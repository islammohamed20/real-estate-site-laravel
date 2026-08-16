<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Document;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPlanItem;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CrmTrashController extends Controller
{
    public function index(): View
    {
        $trashedCustomers = Customer::onlyTrashed()
            ->withCount(['offers', 'leads', 'deals', 'reservations'])
            ->orderByDesc('deleted_at')
            ->paginate(10);

        $trashedLeads = Lead::onlyTrashed()
            ->with(['customer'])
            ->withCount(['offers'])
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'leads_page');

        $trashedOffers = Offer::onlyTrashed()
            ->with(['customer', 'lead', 'project', 'unit'])
            ->withCount(['installments'])
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'offers_page');

        $trashedPlans = InstallmentPlan::onlyTrashed()
            ->with(['customer', 'unit.project'])
            ->withCount('items')
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'plans_page');

        return view('crm.trash.index', [
            'trashedCustomers' => $trashedCustomers,
            'trashedLeads' => $trashedLeads,
            'trashedOffers' => $trashedOffers,
            'trashedPlans' => $trashedPlans,
            'customersCount' => Customer::onlyTrashed()->count(),
            'leadsCount' => Lead::onlyTrashed()->count(),
            'offersCount' => Offer::onlyTrashed()->count(),
            'plansCount' => InstallmentPlan::onlyTrashed()->count(),
        ]);
    }

    public function restoreCustomer(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        $customer->restore();

        $this->log('customer.restored', $customer::class, $customer->id, ['deleted_at' => $customer->deleted_at?->toDateTimeString()], ['deleted_at' => null], ['name' => $customer->name, 'phone' => $customer->phone]);

        return back()->with('status', __('Customer restored successfully.'));
    }

    public function forceDeleteCustomer(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        if ($this->customerHasRelatedRecords($customer)) {
            return back()->withErrors([
                'delete' => __('This customer cannot be permanently deleted because it has related offers, leads, deals, reservations, or documents.'),
            ]);
        }

        $this->log('customer.force_deleted', $customer::class, $customer->id, ['deleted_at' => $customer->deleted_at?->toDateTimeString()], [], ['name' => $customer->name, 'phone' => $customer->phone]);

        $customer->forceDelete();

        return back()->with('status', __('Customer permanently deleted.'));
    }

    public function restoreLead(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        $lead->restore();

        $this->log('lead.restored', $lead::class, $lead->id, ['deleted_at' => $lead->deleted_at?->toDateTimeString()], ['deleted_at' => null], ['name' => $lead->name, 'phone' => $lead->phone]);

        return back()->with('status', __('Lead restored successfully.'));
    }

    public function forceDeleteLead(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        if ($lead->offers()->exists() || $lead->documents()->exists()) {
            return back()->withErrors([
                'delete' => __('This lead cannot be permanently deleted because it has related offers or documents.'),
            ]);
        }

        $this->log('lead.force_deleted', $lead::class, $lead->id, ['deleted_at' => $lead->deleted_at?->toDateTimeString()], [], ['name' => $lead->name, 'phone' => $lead->phone]);

        $lead->forceDelete();

        return back()->with('status', __('Lead permanently deleted.'));
    }

    public function restoreOffer(Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $offer->restore();

        $this->log('offer.restored', $offer::class, $offer->id, ['deleted_at' => $offer->deleted_at?->toDateTimeString()], ['deleted_at' => null], ['offer_number' => $offer->offer_number, 'total' => $offer->total_amount]);

        return back()->with('status', __('Offer restored successfully.'));
    }

    public function forceDeleteOffer(Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        if ($offer->installments()->exists() || $offer->documents()->exists()) {
            return back()->withErrors([
                'delete' => __('This offer cannot be permanently deleted because it has related installment items or documents.'),
            ]);
        }

        $this->log('offer.force_deleted', $offer::class, $offer->id, ['deleted_at' => $offer->deleted_at?->toDateTimeString()], [], ['offer_number' => $offer->offer_number, 'total' => $offer->total_amount]);

        // Detach references from installment tables before hard deleting.
        DB::transaction(function () use ($offer): void {
            InstallmentPlan::query()->where('offer_id', $offer->id)->update(['offer_id' => null]);
            InstallmentPlanItem::query()->where('offer_id', $offer->id)->update(['offer_id' => null]);
            $offer->forceDelete();
        });

        return back()->with('status', __('Offer permanently deleted.'));
    }

    private function customerHasRelatedRecords(Customer $customer): bool
    {
        return $customer->offers()->exists()
            || $customer->leads()->exists()
            || $customer->deals()->exists()
            || $customer->reservations()->exists()
            || $customer->documents()->exists()
            || $customer->recordedNotes()->exists()
            || $customer->followUps()->exists()
            || InstallmentPlan::query()->where('customer_id', $customer->id)->exists();
    }

    /**
     * Record a trash operation in the activity log.
     *
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     * @param  array<string, mixed>  $properties
     */
    private function log(string $event, string $auditableType, int $auditableId, array $oldValues, array $newValues, array $properties): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'properties' => $properties,
        ]);
    }
}
