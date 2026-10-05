<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\LeadStage;
use App\Enums\UnitStatus;
use App\Http\Requests\InstallmentCalculatorRequest;
use App\Models\Building;
use App\Models\CompanyProfile;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPlanItem;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Unit;
use App\Notifications\CrmActivityNotification;
use App\Repositories\Interfaces\InstallmentTemplateRepositoryInterface;
use App\Services\Installments\InstallmentCalculatorService;
use App\Services\PdfRendererService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstallmentCalculatorController extends Controller
{
    public function index(InstallmentTemplateRepositoryInterface $templates, Request $request): View
    {
        $isDashboard = $request->routeIs('dashboard.*');
        $filterPublicUnits = static function ($query): void {
            $query->where('hidden_from_website', false)
                ->where('status', UnitStatus::Available->value)
                ->whereHas('project', fn ($projectQuery) => $projectQuery->where('status', 'active'));
        };
        $unitsQuery = Unit::query()
            ->with(['project', 'floor', 'building'])
            ->when(! $isDashboard, $filterPublicUnits)
            ->orderBy('unit_number');
        $units = $unitsQuery->limit(200)->get();
        $buildings = Building::query()
            ->with('project')
            ->when(! $isDashboard, fn ($q) => $q->where('hidden_from_website', false))
            ->orderBy('name')
            ->get();
        $floors = Floor::query()
            ->whereHas('units')
            ->with('building:id,name,project_id')
            ->orderBy('sort_order')
            ->orderBy('number')
            ->get();

        $unitId = (int) old('unit_id', $request->integer('unit_id'));

        if ($unitId > 0 && $units->doesntContain('id', $unitId)) {
            $unitQuery = Unit::query()
                ->with(['project', 'floor', 'building'])
                ->whereKey($unitId)
                ->when(! $isDashboard, $filterPublicUnits);
            $unit = $unitQuery->first();

            if ($unit !== null) {
                $units->prepend($unit);
            }
        }

        $companyProfile = CompanyProfile::query()->first() ?? new CompanyProfile(['maintenance_percent' => 7]);

        $preselectedUnit = $units->firstWhere('id', $unitId);
        $requestedUnitUnavailable = ! $isDashboard && $unitId > 0 && $preselectedUnit === null;
        $preselectedProjectId = old('project_id', $request->input('project_id', $preselectedUnit?->project_id));
        $preselectedBuildingId = old('building_id', $request->input('building_id', $preselectedUnit?->building_id));
        $projects = Project::query()->orderBy('name')->get();
        $preselectedProject = $projects->firstWhere('id', (int) $preselectedProjectId);

        $allTemplates = $templates->all();
        $template = $allTemplates->first();
        $defaultInstallmentYears = ($preselectedProject && $preselectedProject->max_installment_years > 0)
            ? $preselectedProject->max_installment_years
            : ($template ? $template->installment_count * $template->installment_frequency->monthsPerInstallment() / 12 : 5);

        $calcNum = fn (string $field, mixed $default): float => is_numeric(old($field)) ? (float) old($field) : (float) ($default ?? 0);

        $calcDefaults = [
            'area' => $calcNum('area', $preselectedUnit?->area ?? 0),
            'price_per_meter' => $calcNum('price_per_meter', $preselectedUnit?->price_per_meter ?? 0),
            'garden_price' => $calcNum('garden_price', $preselectedUnit?->garden_price ?? 0),
            'roof_price' => $calcNum('roof_price', $preselectedUnit?->roof_price ?? 0),
            'roof_area' => $calcNum('roof_area', $preselectedUnit?->roof_area ?? 0),
            'excellence_percent' => $calcNum('excellence_percent', $preselectedUnit?->excellence_percent ?? $template?->defaults()['excellence_percent'] ?? 0),
            'down_payment_percent' => $calcNum('down_payment_percent', $template?->down_payment_percent ?? 25),
            'down_payment' => $calcNum('down_payment', 0),
            'maintenance_percent' => $calcNum('maintenance_percent', $template?->maintenance_percent ?? $companyProfile?->maintenance_percent ?? 7),
            'installment_years' => $calcNum('installment_years', $defaultInstallmentYears),
            'installment_type' => (fn (): string => in_array(old('installment_type', $template?->installment_frequency?->value ?? 'quarterly'), ['monthly', 'quarterly'], true)
                ? (string) old('installment_type', $template?->installment_frequency?->value ?? 'quarterly')
                : 'quarterly')(),
        ];

        $templateId = old('installment_template_id', $template?->id);

        return view('installments.index', [
            'layout' => $isDashboard ? 'layouts.dashboard' : 'layouts.public',
            'isDashboard' => $isDashboard,
            'calculatorRoutes' => $this->calculatorRoutes($isDashboard),
            'templates' => $allTemplates,
            'projects' => $projects,
            'buildings' => $buildings,
            'floors' => $floors,
            'units' => $units,
            'companyProfile' => $companyProfile,
            'preselectedUnit' => $preselectedUnit,
            'selectedUnitId' => $preselectedUnit?->id ?? '',
            'requestedUnitUnavailable' => $requestedUnitUnavailable,
            'preselectedProject' => $preselectedProject,
            'preselectedProjectId' => $preselectedProjectId,
            'preselectedBuildingId' => $preselectedBuildingId,
            'defaultInstallmentYears' => $defaultInstallmentYears,
            'calcDefaults' => $calcDefaults,
            'templateId' => $templateId,
        ]);
    }

    public function calculate(InstallmentCalculatorRequest $request, InstallmentCalculatorService $calculator): View|RedirectResponse
    {
        $isDashboard = $request->routeIs('dashboard.*');
        $validated = $request->validated();

        $unitError = $this->unavailableUnitError($validated);
        if ($unitError !== null) {
            return back()->withErrors(['unit_id' => $unitError])->withInput();
        }

        $result = $calculator->calculate($validated);

        // In the customer portal, only the customer's own record and own offers
        // are visible (privacy) — dashboard staff still get the full lists,
        // even in a browser where a customer session is also present.
        $customerPortal = ! $request->routeIs('dashboard.*') && auth('customer')->check();
        $customerId = $customerPortal ? (int) auth('customer')->id() : null;

        // In the customer portal the customer is pre-selected automatically.
        // On the dashboard a customer session must NEVER auto-select a customer
        // (staff picks the recipient manually) — otherwise a browser with both
        // an admin and a customer session would pre-fill a random customer.
        if ($customerPortal) {
            $validated['customer_id'] = $customerId;
        }

        $customersQuery = $customerPortal
            ? Customer::query()->whereKey($customerId)
            : Customer::query()->orderBy('name');

        return view('installments.result', [
            'layout' => $isDashboard ? 'layouts.dashboard' : 'layouts.public',
            'calculatorRoutes' => $this->calculatorRoutes($isDashboard),
            'result' => $result,
            'input' => $validated,
            'unit' => ! empty($validated['unit_id'])
                ? Unit::query()->with(['project', 'building', 'floor'])->find($validated['unit_id'])
                : null,
            'customerPortal' => $customerPortal,
            'customers' => $customersQuery->pluck('name', 'id'),
            'customersInfo' => $customersQuery->get(['id', 'name', 'phone']),
            'offers' => Offer::query()
                ->with('customer')
                ->where('unit_id', $validated['unit_id'] ?? null)
                ->when($customerPortal, fn ($query) => $query->where('customer_id', $customerId))
                ->orderByDesc('id')
                ->get()
                ->mapWithKeys(fn (Offer $offer) => [$offer->id => $offer->offer_number.' · '.($offer->customer?->name ?? __('No customer'))]),
            'pdfOffers' => Offer::query()
                ->when($customerPortal, fn ($query) => $query->where('customer_id', $customerId))
                ->orderByDesc('id')
                ->get(['id', 'offer_number', 'customer_id']),
        ]);
    }

    /**
     * Persist the calculated plan into the CRM, linked to a customer and optionally an offer.
     */
    public function save(InstallmentCalculatorRequest $request, InstallmentCalculatorService $calculator): RedirectResponse
    {
        $validated = $request->validated();

        $unitError = $this->unavailableUnitError($validated);
        if ($unitError !== null) {
            return back()->withErrors(['unit_id' => $unitError])->withInput();
        }

        $result = $calculator->calculate($validated);

        if (empty($validated['customer_id'])) {
            return back()->withErrors([
                'customer_id' => __('Please select a customer to save the plan.'),
            ]);
        }

        // In the customer portal a plan can only be saved to the customer's own
        // account, even if the request was tampered with.
        if (! $request->routeIs('dashboard.*') && (int) $validated['customer_id'] !== (int) auth('customer')->id()) {
            return back()->withErrors([
                'customer_id' => __('You can only save plans to your own account.'),
            ])->withInput();
        }

        $unit = $validated['unit_id'] ? Unit::query()->find($validated['unit_id']) : null;
        $offerId = ! empty($validated['offer_id']) ? $validated['offer_id'] : null;

        $plan = InstallmentPlan::query()->create([
            'customer_id' => $validated['customer_id'],
            'lead_id' => Lead::query()->where('customer_id', $validated['customer_id'])->latest('id')->value('id'),
            'offer_id' => $offerId,
            'project_id' => $unit?->project_id ?? $validated['project_id'] ?? null,
            'building_id' => $unit?->building_id,
            'floor_id' => $unit?->floor_id,
            'unit_id' => $unit?->id,
            // Use the admin guard explicitly — after the customer portal, the
            // `auth:customer` middleware switches the default guard to `customer`,
            // so a bare auth()->id() would store the CUSTOMER id in created_by.
            'created_by' => auth('web')->id(),
            'name' => $this->planName($unit, (int) $validated['customer_id']),
            'status' => 'active',
            'base_price' => $result['base_price'],
            'excellence_percent' => $result['excellence_percent'],
            'excellence_amount' => $result['excellence_amount'],
            'base_price_with_excellence' => $result['base_price_with_excellence'],
            // Store the total discount so the saved plan reconciles with its final price.
            'discount_amount' => $result['discount_amount'],
            'discount_percent' => $result['discount_percent'],
            'final_price' => $result['final_price'],
            'maintenance_deposit' => $result['maintenance_deposit'],
            'down_payment' => $result['down_payment'],
            'remaining_amount' => $result['remaining'],
            'installment_amount' => $result['installment_amount'],
            'installment_count' => count($result['schedule']),
            'installment_type' => $validated['installment_type'] ?? 'quarterly',
            'schedule_json' => $result['schedule'],
            'starts_at' => $validated['first_installment_date'] ?? now(),
            'saved_from_calculator' => true,
        ]);

        foreach ($result['schedule'] as $row) {
            InstallmentPlanItem::query()->create([
                'installment_plan_id' => $plan->id,
                'offer_id' => $offerId,
                'installment_number' => $row['installment_number'],
                'due_date' => $row['due_date'],
                'amount' => $row['amount'],
                'balance_after' => $row['balance_after'],
            ]);
        }

        if (! empty($validated['offer_id'])) {
            Offer::query()->where('id', $validated['offer_id'])->update(['installment_plan_id' => $plan->id]);
        }

        CrmActivityNotification::notifyRelevant(new CrmActivityNotification(
            'plan',
            [
                'customer_name' => Customer::query()->find($validated['customer_id'])?->name ?? __('Unknown customer'),
                'unit_number' => $unit?->unit_number,
                'amount' => $result['final_price'],
                'action_url' => route('dashboard.crm.plans.show', $plan),
            ],
            auth('web')->user()?->name,
        ), auth('web')->user());

        $message = __('Installment plan saved to CRM successfully.');

        if ($request->routeIs('dashboard.*')) {
            if (! empty($validated['offer_id'])) {
                return redirect()->route('dashboard.crm.offers.show', $validated['offer_id'])->with('status', $message);
            }

            return redirect()->route('dashboard.crm.index')->with('status', $message);
        }

        // Public calculator — the customer returns to their own portal page.
        return redirect()->route('customer.account')->with('status', $message);
    }

    /**
     * Quick-create a lead (and its linked customer) straight from the calculator result page.
     * The lead shows up in CRM Leads and its customer can be used for the PDF / plan save.
     */
    public function quickLead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            [$customer, $lead] = DB::transaction(function () use ($validated): array {
                $customer = Customer::query()->firstOrCreate(
                    ['phone' => $validated['phone']],
                    [
                        'name' => $validated['name'],
                        'email' => $validated['email'] ?? null,
                        'whatsapp' => $validated['whatsapp'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                        'source' => 'calculator',
                    ]
                );

                $lead = Lead::query()->where('phone', $validated['phone'])->latest('id')->first();

                if ($lead === null) {
                    $lead = Lead::query()->create([
                        'customer_id' => $customer->id,
                        'name' => $validated['name'],
                        'phone' => $validated['phone'],
                        'whatsapp' => $validated['whatsapp'] ?? $validated['phone'],
                        'email' => $validated['email'] ?? null,
                        'stage' => LeadStage::New,
                        'status' => 'active',
                        'source' => 'calculator',
                        'notes' => $validated['notes'] ?? null,
                    ]);
                }

                return [$customer, $lead];
            });

            return response()->json([
                'ok' => true,
                'customer_id' => $customer->id,
                'lead_id' => $lead->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => __('Failed to save the lead.'),
            ], 422);
        }
    }

    private function unavailableUnitError(array $validated): ?string
    {
        if (empty($validated['unit_id'])) {
            return null;
        }

        $unit = Unit::query()->find($validated['unit_id']);

        if ($unit === null || $unit->status?->value === UnitStatus::Available->value) {
            return null;
        }

        return __('Unit :unit is not available for sale — it is currently :status.', [
            'unit' => $unit->unit_number,
            'status' => __($unit->status->label()),
        ]);
    }

    private function planName(?Unit $unit, int $customerId): string
    {
        $customerName = Customer::query()->find($customerId)?->name;
        $unitLabel = $unit ? ($unit->unit_number.' · '.($unit->project?->name ?? '')) : __('Custom plan');

        return __('Plan').' — '.trim($customerName.' · '.$unitLabel, ' ·');
    }

    /**
     * Build route names for the calculator pages depending on whether they are
     * being rendered from the public site or from the dashboard.
     */
    private function calculatorRoutes(bool $dashboard): array
    {
        return [
            'index' => $dashboard ? 'dashboard.installments.index' : 'installments.index',
            'calculate' => $dashboard ? 'dashboard.installments.calculate' : 'installments.calculate',
            'pdf' => $dashboard ? 'dashboard.installments.pdf' : 'installments.pdf',
            'save' => $dashboard ? 'dashboard.installments.save' : 'installments.save',
            'lead' => $dashboard ? 'dashboard.installments.lead' : 'installments.lead',
        ];
    }

    public function pdf(InstallmentCalculatorRequest $request, InstallmentCalculatorService $calculator, PdfRendererService $renderer): Response
    {
        $input = $request->validated();

        // A logged-in customer (on the public calculator) can only generate a
        // PDF addressed to themselves. Dashboard staff keep full access.
        if (! $request->routeIs('dashboard.*')
            && auth('customer')->check()
            && ! empty($input['customer_id'])
            && (int) $input['customer_id'] !== (int) auth('customer')->id()) {
            abort(403);
        }

        $result = $calculator->calculate($input);

        $company = CompanyProfile::query()->first();
        $unit = ! empty($input['unit_id']) ? Unit::query()->with(['project', 'building', 'floor'])->find($input['unit_id']) : null;
        $customer = ! empty($input['customer_id']) ? Customer::query()->find($input['customer_id']) : null;
        $offer = ! empty($input['offer_id']) ? Offer::query()->find($input['offer_id']) : null;

        $reference = 'CALC-'.now()->format('Ymd-His').'-'.strtoupper(substr(md5(serialize($input).now()->format('YmdHis')), 0, 6));

        $html = view('installments.pdf', [
            'input' => $input,
            'result' => $result,
            'company' => $company,
            'reference' => $reference,
            'logoDataUri' => $renderer->imageDataUri($company?->logo_dark_path ?? $company?->logo_path),
            'stampDataUri' => $renderer->imageDataUri($company?->stamp_path),
            'unit' => $unit,
            'customer' => $customer,
            'offer' => $offer,
        ])->render();

        $filename = 'installment-plan-'.now()->format('Ymd-His').'.pdf';

        return $renderer->renderResponse($html, $filename, inline: true);
    }
}
