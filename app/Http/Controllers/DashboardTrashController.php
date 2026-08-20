<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Customer;
use App\Models\Document;
use App\Models\InstallmentPlan;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\User;
use App\Models\WhatsAppConversation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Unified trash for the dashboard: every soft-deleted entity with
 * restore / permanent-delete actions.
 */
class DashboardTrashController extends Controller
{
    /**
     * Type key (used in routes and the UI) => model class.
     */
    private const TYPES = [
        'customers' => Customer::class,
        'leads' => Lead::class,
        'offers' => Offer::class,
        'plans' => InstallmentPlan::class,
        'organizations' => CrmOrganization::class,
        'deals' => CrmDeal::class,
        'contacts' => CrmContact::class,
        'documents' => Document::class,
        'conversations' => WhatsAppConversation::class,
        'users' => User::class,
    ];

    public function index(): View
    {
        $user = auth()->user();
        $canViewAllTrash = $user->hasAnyRole(['Administrator', 'Sales Manager']);

        if ($canViewAllTrash) {
            return view('dashboard.trash.index', [
                'trashedProjects' => \App\Models\Project::onlyTrashed()
                    ->withCount([
                        'units' => fn ($query) => $query->withTrashed(),
                        'buildings' => fn ($query) => $query->withTrashed(),
                    ])
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedUnits' => \App\Models\Unit::onlyTrashed()
                    ->with(['project', 'building'])
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedBuildings' => \App\Models\Building::onlyTrashed()
                    ->with('project')
                    ->orderByDesc('deleted_at')
                    ->get(),

                'trashedCustomers' => Customer::onlyTrashed()
                    ->withCount(['offers', 'leads'])
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedLeads' => Lead::onlyTrashed()
                    ->with(['customer'])
                    ->withCount(['offers'])
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedOffers' => Offer::onlyTrashed()
                    ->with(['customer', 'lead', 'project', 'unit'])
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedPlans' => InstallmentPlan::onlyTrashed()
                    ->with(['customer', 'unit.project'])
                    ->withCount('items')
                    ->orderByDesc('deleted_at')
                    ->get(),

                'trashedOrganizations' => CrmOrganization::onlyTrashed()
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedDeals' => CrmDeal::onlyTrashed()
                    ->with(['customer', 'lead'])
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedContacts' => CrmContact::onlyTrashed()
                    ->with('organization')
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedDocuments' => Document::onlyTrashed()
                    ->orderByDesc('deleted_at')
                    ->get(),
                'trashedConversations' => WhatsAppConversation::onlyTrashed()
                    ->orderByDesc('deleted_at')
                    ->get(),

                'trashedUsers' => User::onlyTrashed()
                    ->orderByDesc('deleted_at')
                    ->get(),
            ]);
        }

        // Data Entry and similar roles can only see the project/unit/building
        // records that they personally deleted.
        return view('dashboard.trash.index', [
            'trashedProjects' => \App\Models\Project::onlyTrashed()
                ->where('deleted_by', $user->id)
                ->withCount([
                    'units' => fn ($query) => $query->withTrashed(),
                    'buildings' => fn ($query) => $query->withTrashed(),
                ])
                ->orderByDesc('deleted_at')
                ->get(),
            'trashedUnits' => \App\Models\Unit::onlyTrashed()
                ->where('deleted_by', $user->id)
                ->with(['project', 'building'])
                ->orderByDesc('deleted_at')
                ->get(),
            'trashedBuildings' => \App\Models\Building::onlyTrashed()
                ->where('deleted_by', $user->id)
                ->with('project')
                ->orderByDesc('deleted_at')
                ->get(),

            'trashedCustomers' => collect(),
            'trashedLeads' => collect(),
            'trashedOffers' => collect(),
            'trashedPlans' => collect(),
            'trashedOrganizations' => collect(),
            'trashedDeals' => collect(),
            'trashedContacts' => collect(),
            'trashedDocuments' => collect(),
            'trashedConversations' => collect(),
            'trashedUsers' => collect(),
        ]);
    }

    public function restore(string $type, int $id): RedirectResponse
    {
        $this->authorizeTrashAction($type, $id);

        $this->trashedModel($type, $id)->restore();

        return back()->with('status', __('Restored successfully.'));
    }

    public function forceDelete(string $type, int $id): RedirectResponse
    {
        $this->authorizeTrashAction($type, $id);

        $model = $this->trashedModel($type, $id);

        if ($model instanceof User && $this->isProtectedUser($model)) {
            return back()->withErrors([
                'delete' => __('The main administrator account cannot be deleted.'),
            ]);
        }

        $model->forceDelete();

        return back()->with('status', __('Permanently deleted.'));
    }

    private function trashedModel(string $type, int $id): Model
    {
        $class = self::TYPES[$type] ?? abort(404);

        return $class::onlyTrashed()->find($id) ?? abort(404);
    }

    private function authorizeTrashAction(string $type, int $id): void
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['Administrator', 'Sales Manager'])) {
            return;
        }

        // Data Entry may only restore/force-delete project-related records
        // that they personally deleted.
        if (! in_array($type, ['projects', 'units', 'buildings'], true)) {
            abort(403);
        }

        $class = match ($type) {
            'projects' => \App\Models\Project::class,
            'units' => \App\Models\Unit::class,
            'buildings' => \App\Models\Building::class,
            default => null,
        };

        if ($class === null || ! $class::onlyTrashed()->where('id', $id)->where('deleted_by', $user->id)->exists()) {
            abort(403);
        }
    }

    private function isProtectedUser(User $user): bool
    {
        return strtolower($user->email) === 'admin@venecia-dev.com';
    }
}
