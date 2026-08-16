<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\Crm\ActivityController;
use App\Http\Controllers\Crm\CrmTrashController;
use App\Http\Controllers\Crm\CustomerController;
use App\Http\Controllers\Crm\DealController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\Crm\DocumentController;
use App\Http\Controllers\Crm\FollowUpController;
use App\Http\Controllers\Crm\InstallmentPlanController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\OfferController;
use App\Http\Controllers\Crm\ReportController;
use App\Http\Controllers\Crm\ReservationController;
use App\Http\Controllers\Crm\SearchController;
use App\Http\Controllers\Crm\TaskController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardTrashController;
use App\Http\Controllers\InstallmentCalculatorController;
use App\Http\Controllers\LeadInquiryController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectManagementController;
use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicWebsiteController::class, 'home'])->name('home');

Route::get('/favicon.ico', function () {
    $companyProfile = CompanyProfile::first();
    $path = $companyProfile?->favicon_path ?? '/icons/icon-maskable.svg';

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return redirect($path, 301);
    }

    $publicPath = public_path(ltrim($path, '/\\'));
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }

    return response()->file(public_path('icons/icon-maskable.svg'));
})->name('favicon');

Route::get('/auth/refresh-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('auth.refresh-token');
Route::middleware('track_visitor')->group(function (): void {
    Route::get('/projects', [PublicWebsiteController::class, 'projects'])->name('public.projects.index');
    Route::get('/projects/{slug}', [PublicWebsiteController::class, 'projectShow'])->name('public.projects.show');
    Route::get('/units/{unitNumber}', [PublicWebsiteController::class, 'unitShow'])->name('public.units.show');
    Route::get('/about', [PublicWebsiteController::class, 'about'])->name('public.about');
    Route::get('/contact', [PublicWebsiteController::class, 'contact'])->name('public.contact');
});
Route::post('/inquiries', [LeadInquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.inquiries.store');

Route::get('/calculator', [InstallmentCalculatorController::class, 'index'])->name('installments.index');
Route::post('/calculator', [InstallmentCalculatorController::class, 'calculate'])->name('installments.calculate');
Route::post('/calculator/pdf', [InstallmentCalculatorController::class, 'pdf'])
    ->middleware('auth:customer')
    ->name('installments.pdf');
Route::post('/calculator/save', [InstallmentCalculatorController::class, 'save'])
    ->middleware('auth:customer')
    ->name('installments.save');
Route::post('/calculator/lead', [InstallmentCalculatorController::class, 'quickLead'])
    ->middleware('auth:customer')
    ->name('installments.lead');

Route::post('/locale', LocalizationController::class)->name('locale');

// ===== Admin (dashboard) authentication — separated from the public site =====
Route::middleware('guest')->group(function (): void {
    Route::get('/real-statement-control/login', [LoginController::class, 'create'])->name('login');
    Route::post('/real-statement-control/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::post('/real-statement-control/logout', [LoginController::class, 'destroy'])
    ->middleware(['auth', 'active'])
    ->name('logout');

// ===== Two-Factor Authentication (Google Authenticator) =====
// Challenge is reachable right after login for users with 2FA enabled.
Route::middleware('auth')->group(function (): void {
    Route::get('/real-statement-control/2fa/verify', [TwoFactorController::class, 'showChallenge'])->name('2fa.verify');
    Route::post('/real-statement-control/2fa/verify', [TwoFactorController::class, 'verifyChallenge'])->name('2fa.verify.store');
});

// ===== Customer portal authentication =====
Route::middleware('guest:customer')->group(function (): void {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/login', [CustomerAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('customer.login.store');
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.store');
});

Route::post('/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('auth:customer')
    ->name('customer.logout');

Route::middleware('customer.auth')->prefix('account')->name('customer.')->group(function (): void {
    Route::get('/', [CustomerAccountController::class, 'index'])->name('account');
});

// Legacy dashboard path → new control panel path
Route::redirect('/dashboard', '/real-statement-control');

Route::middleware(['auth', 'active', 'force_logout', '2fa'])->prefix('real-statement-control')->name('dashboard.')->group(function (): void {
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::post('/trash-cleanup/run', [DashboardController::class, 'runCleanup'])
        ->middleware('permission:manage crm')
        ->name('trash-cleanup.run');

    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Accountant|Receptionist|Owner')
        ->name('home');

    Route::get('/calculator', [InstallmentCalculatorController::class, 'index'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Accountant|Receptionist|Owner')
        ->name('installments.index');

    Route::post('/calculator', [InstallmentCalculatorController::class, 'calculate'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Accountant|Receptionist|Owner')
        ->name('installments.calculate');

    Route::post('/calculator/pdf', [InstallmentCalculatorController::class, 'pdf'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Accountant|Receptionist|Owner')
        ->name('installments.pdf');

    Route::post('/calculator/save', [InstallmentCalculatorController::class, 'save'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Accountant|Receptionist|Owner')
        ->name('installments.save');

    Route::post('/calculator/lead', [InstallmentCalculatorController::class, 'quickLead'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Accountant|Receptionist|Owner')
        ->name('installments.lead');

    Route::get('/crm', [CrmController::class, 'index'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Owner')
        ->name('crm.index');

    Route::get('/crm/quick', [CrmController::class, 'quickCreate'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Marketing Manager|Receptionist|Owner')
        ->name('crm.quick');

    Route::post('/crm/leads', [CrmController::class, 'storeLead'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Marketing Manager|Receptionist|Owner')
        ->name('crm.leads.quick-store');

    Route::prefix('crm/leads')->name('crm.leads.')->middleware('permission:view crm dashboard|view all leads|view team leads|view own leads|manage crm')->group(function (): void {
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::post('/', [LeadController::class, 'store'])->name('store')->middleware('permission:create leads|manage crm');
        Route::get('/check-duplicate', [LeadController::class, 'checkDuplicate'])->name('check-duplicate');
        Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
        Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit')->middleware('permission:edit own leads|edit all leads|manage crm');
        Route::put('/{lead}', [LeadController::class, 'update'])->name('update')->middleware('permission:edit own leads|edit all leads|manage crm');
        Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy')->middleware('permission:delete leads|manage crm');
        Route::post('/{lead}/assign', [LeadController::class, 'assign'])->name('assign')->middleware('permission:assign leads|manage crm');
        Route::post('/{lead}/convert', [LeadController::class, 'convert'])->name('convert')->middleware('permission:edit own leads|edit all leads|manage crm');
        Route::patch('/{lead}/stage', [LeadController::class, 'moveStage'])->name('stage.update')->middleware('permission:edit own leads|edit all leads|manage crm');
    });

    Route::prefix('crm/customers')->name('crm.customers.')->middleware('permission:view crm dashboard|view all customers|view own customers|manage crm')->group(function (): void {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create')->middleware('permission:create customers|manage crm');
        Route::post('/', [CustomerController::class, 'store'])->name('store')->middleware('permission:create customers|manage crm');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit')->middleware('permission:edit all customers|edit own customers|manage crm');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update')->middleware('permission:edit all customers|edit own customers|manage crm');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy')->middleware('permission:delete customers|manage crm');
    });

    Route::prefix('crm/activities')->name('crm.activities.')->middleware('permission:view crm dashboard|manage crm|create tasks|edit all leads|edit all customers')->group(function (): void {
        Route::post('/', [ActivityController::class, 'store'])->name('store')->middleware('permission:manage crm|create tasks');
        Route::put('/{activity}', [ActivityController::class, 'update'])->name('update')->middleware('permission:manage crm|edit all leads|edit all customers');
        Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy')->middleware('permission:manage crm|delete customers|delete leads');
        Route::patch('/{activity}/complete', [ActivityController::class, 'complete'])->name('complete')->middleware('permission:manage crm|edit all leads|edit all customers');
    });

    Route::prefix('crm/tasks')->name('crm.tasks.')->middleware('permission:view crm dashboard|manage crm|view all tasks|view own tasks')->group(function (): void {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store')->middleware('permission:create tasks|manage crm');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update')->middleware('permission:edit all tasks|edit own tasks|manage crm');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy')->middleware('permission:delete tasks|manage crm');
        Route::patch('/{task}/complete', [TaskController::class, 'complete'])->name('complete')->middleware('permission:edit all tasks|edit own tasks|manage crm');
    });

    Route::prefix('crm/follow-ups')->name('crm.follow_ups.')->middleware('permission:view crm dashboard|manage crm|view all tasks|view own tasks')->group(function (): void {
        Route::get('/', [FollowUpController::class, 'index'])->name('index');
        Route::post('/', [FollowUpController::class, 'store'])->name('store')->middleware('permission:create tasks|manage crm');
        Route::put('/{followUp}', [FollowUpController::class, 'update'])->name('update')->middleware('permission:edit all tasks|edit own tasks|manage crm');
        Route::delete('/{followUp}', [FollowUpController::class, 'destroy'])->name('destroy')->middleware('permission:delete tasks|manage crm');
        Route::patch('/{followUp}/complete', [FollowUpController::class, 'complete'])->name('complete')->middleware('permission:edit all tasks|edit own tasks|manage crm');
    });

    Route::middleware('role:Administrator|Sales Manager|Sales Executive')->group(function (): void {
        Route::post('/crm/notes', [CrmController::class, 'storeNote'])->name('crm.notes.store');
        Route::delete('/crm/notes/{note}', [CrmController::class, 'destroyNote'])->name('crm.notes.destroy');

        Route::get('/crm/organizations', [CrmController::class, 'indexOrganizations'])->name('crm.organizations.index');
        Route::post('/crm/organizations', [CrmController::class, 'storeOrganization'])->name('crm.organizations.store');
        Route::get('/crm/organizations/{organization}', [CrmController::class, 'showOrganization'])->name('crm.organizations.show');
        Route::get('/crm/organizations/{organization}/edit', [CrmController::class, 'editOrganization'])->name('crm.organizations.edit');
        Route::put('/crm/organizations/{organization}', [CrmController::class, 'updateOrganization'])->name('crm.organizations.update');
        Route::delete('/crm/organizations/{organization}', [CrmController::class, 'destroyOrganization'])->name('crm.organizations.destroy');

        Route::get('/crm/contacts/{contact}/edit', [CrmController::class, 'editContact'])->name('crm.contacts.edit');
        Route::put('/crm/contacts/{contact}', [CrmController::class, 'updateContact'])->name('crm.contacts.update');
        Route::delete('/crm/contacts/{contact}', [CrmController::class, 'destroyContact'])->name('crm.contacts.destroy');
        Route::post('/crm/contacts', [CrmController::class, 'storeContact'])->name('crm.contacts.store');
    });

    Route::get('crm/search', SearchController::class)->name('crm.search')->middleware('permission:view crm dashboard|manage crm|view reports');

    Route::prefix('crm/offers')->name('crm.offers.')->middleware('permission:manage crm|create offers|view reports')->group(function (): void {
        Route::get('/', [OfferController::class, 'index'])->name('index');
        Route::get('/create', [OfferController::class, 'create'])->name('create')->middleware('permission:manage crm|create offers');
        Route::post('/', [OfferController::class, 'store'])->name('store')->middleware('permission:manage crm|create offers');
        Route::get('/{offer}', [OfferController::class, 'show'])->name('show');
        Route::get('/{offer}/edit', [OfferController::class, 'edit'])->name('edit')->middleware('permission:manage crm|create offers');
        Route::put('/{offer}', [OfferController::class, 'update'])->name('update')->middleware('permission:manage crm|create offers');
        Route::delete('/{offer}', [OfferController::class, 'destroy'])->name('destroy')->middleware('permission:manage crm|create offers');
    });

    Route::get('crm/search', SearchController::class)->name('crm.search')->middleware('permission:view crm dashboard|manage crm|view reports');

    Route::prefix('crm/documents')->name('crm.documents.')->middleware('permission:manage crm|view reports')->group(function (): void {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::post('/', [DocumentController::class, 'store'])->name('store')->middleware('permission:manage crm');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy')->middleware('permission:manage crm');
    });

    Route::prefix('crm/reports')->name('crm.reports.')->middleware('permission:view crm dashboard|manage crm|view reports')->group(function (): void {
        Route::get('/', [ReportController::class, 'index'])->name('index');
    });

    Route::prefix('crm/plans')->name('crm.plans.')->middleware('permission:view crm dashboard|manage crm|view reports')->group(function (): void {
        Route::get('/', [InstallmentPlanController::class, 'index'])->name('index');
        Route::get('/trash', [InstallmentPlanController::class, 'trash'])->name('trash');
        Route::post('/{plan}/restore', [InstallmentPlanController::class, 'restore'])->name('restore')->withTrashed()->middleware('permission:manage crm');
        Route::delete('/{plan}/force-delete', [InstallmentPlanController::class, 'forceDelete'])->name('force-delete')->withTrashed()->middleware('permission:manage crm');
        Route::get('/{plan}', [InstallmentPlanController::class, 'show'])->name('show');
        Route::get('/{plan}/pdf', [InstallmentPlanController::class, 'pdf'])->name('pdf');
        Route::patch('/{plan}/items/{item}', [InstallmentPlanController::class, 'updateItem'])->name('items.update')->middleware('permission:manage crm');
        Route::delete('/{plan}', [InstallmentPlanController::class, 'destroy'])->name('destroy')->middleware('permission:manage crm');
    });

    Route::prefix('crm/trash')->name('crm.trash.')->middleware('permission:manage crm')->group(function (): void {
        Route::get('/', [CrmTrashController::class, 'index'])->name('index');

        Route::post('/customers/{customer}/restore', [CrmTrashController::class, 'restoreCustomer'])->name('customers.restore')->withTrashed();
        Route::delete('/customers/{customer}/force-delete', [CrmTrashController::class, 'forceDeleteCustomer'])->name('customers.force-delete')->withTrashed();

        Route::post('/leads/{lead}/restore', [CrmTrashController::class, 'restoreLead'])->name('leads.restore')->withTrashed();
        Route::delete('/leads/{lead}/force-delete', [CrmTrashController::class, 'forceDeleteLead'])->name('leads.force-delete')->withTrashed();

        Route::post('/offers/{offer}/restore', [CrmTrashController::class, 'restoreOffer'])->name('offers.restore')->withTrashed();
        Route::delete('/offers/{offer}/force-delete', [CrmTrashController::class, 'forceDeleteOffer'])->name('offers.force-delete')->withTrashed();
    });

    Route::prefix('crm/reservations')->name('crm.reservations.')->middleware('permission:manage crm|create reservations|view reports')->group(function (): void {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/create', [ReservationController::class, 'create'])->name('create')->middleware('permission:manage crm|create reservations');
        Route::post('/', [ReservationController::class, 'store'])->name('store')->middleware('permission:manage crm|create reservations');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('edit')->middleware('permission:manage crm|create reservations');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->name('update')->middleware('permission:manage crm|create reservations');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('destroy')->middleware('permission:manage crm|create reservations');
    });

    Route::prefix('crm/deals')->name('crm.deals.')->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer')->group(function (): void {
        Route::get('/', [DealController::class, 'index'])->name('index');
        Route::post('/', [DealController::class, 'store'])->name('store')->middleware('role:Administrator|Sales Manager|Sales Executive');
        Route::get('/{deal}', [DealController::class, 'show'])->name('show');
        Route::put('/{deal}', [DealController::class, 'update'])->name('update')->middleware('role:Administrator|Sales Manager|Sales Executive');
        Route::patch('/{deal}/stage', [DealController::class, 'moveStage'])->name('stage.update')->middleware('role:Administrator|Sales Manager|Sales Executive');
        Route::post('/{deal}/activities', [DealController::class, 'storeActivity'])->name('activities.store')->middleware('role:Administrator|Sales Manager|Sales Executive');
        Route::put('/{deal}/activities/{activity}', [DealController::class, 'updateActivity'])->name('activities.update')->middleware('role:Administrator|Sales Manager|Sales Executive');
        Route::delete('/{deal}/activities/{activity}', [DealController::class, 'destroyActivity'])->name('activities.destroy')->middleware('role:Administrator|Sales Manager|Sales Executive');
        Route::delete('/{deal}', [DealController::class, 'destroy'])->name('destroy')->middleware('role:Administrator|Sales Manager|Sales Executive');
    });

    Route::get('/projects', [ProjectManagementController::class, 'index'])
        ->middleware('role:Administrator|Sales Manager|Sales Executive|Viewer|Marketing Manager|Owner')
        ->name('projects.index');

    Route::middleware('role:Administrator|Sales Manager|Sales Executive')->group(function (): void {
        Route::get('/projects/create', [ProjectManagementController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectManagementController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectManagementController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectManagementController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectManagementController::class, 'destroy'])->name('projects.destroy');

        Route::get('/projects/{project}/units/create', [ProjectManagementController::class, 'createUnit'])->name('projects.units.create');
        Route::post('/projects/{project}/units', [ProjectManagementController::class, 'storeUnit'])->name('projects.units.store');
        Route::get('/projects/{project}/units/{unit}/edit', [ProjectManagementController::class, 'editUnit'])->name('projects.units.edit');
        Route::put('/projects/{project}/units/{unit}', [ProjectManagementController::class, 'updateUnit'])->name('projects.units.update');
        Route::delete('/projects/{project}/units/{unit}', [ProjectManagementController::class, 'destroyUnit'])->name('projects.units.destroy');
    });

    Route::prefix('trash')->name('trash.')->middleware('role:Administrator|Sales Manager')->group(function (): void {
        Route::get('/', [DashboardTrashController::class, 'index'])->name('index');
        Route::post('/projects/{project}/restore', [ProjectManagementController::class, 'restoreProject'])->name('projects.restore')->withTrashed();
        Route::delete('/projects/{project}/force-delete', [ProjectManagementController::class, 'forceDeleteProject'])->name('projects.force-delete')->withTrashed();
        Route::post('/units/{unit}/restore', [ProjectManagementController::class, 'restoreUnit'])->name('units.restore')->withTrashed();
        Route::delete('/units/{unit}/force-delete', [ProjectManagementController::class, 'forceDeleteUnit'])->name('units.force-delete')->withTrashed();
        Route::post('/buildings/{building}/restore', [ProjectManagementController::class, 'restoreBuilding'])->name('buildings.restore')->withTrashed();
        Route::delete('/buildings/{building}/force-delete', [ProjectManagementController::class, 'forceDeleteBuilding'])->name('buildings.force-delete')->withTrashed();

        // Generic restore / force-delete for the remaining soft-deleted entities.
        Route::post('/{type}/{id}/restore', [DashboardTrashController::class, 'restore'])
            ->where('type', 'customers|leads|offers|plans|organizations|deals|contacts|documents|conversations|users')
            ->name('restore');
        Route::delete('/{type}/{id}/force-delete', [DashboardTrashController::class, 'forceDelete'])
            ->where('type', 'customers|leads|offers|plans|organizations|deals|contacts|documents|conversations|users')
            ->name('force-delete');
    });

    Route::get('/reports', [ReportsController::class, 'index'])
        ->middleware('permission:view reports')
        ->name('reports.index');

    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('permission:view reports')
        ->name('analytics.index');

    Route::prefix('banners')->name('banners.')->middleware('permission:manage settings')->group(function (): void {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
    });

    Route::get('/settings', [SettingsController::class, 'index'])
        ->middleware('permission:manage settings')
        ->name('settings.index');

    Route::put('/settings', [SettingsController::class, 'update'])
        ->middleware('permission:manage settings')
        ->name('settings.update');

    // ===== Two-Factor Authentication management =====
    Route::get('/security', [TwoFactorController::class, 'showSecurityPage'])
        ->name('security');

    Route::get('/settings/2fa/enable', [TwoFactorController::class, 'showEnableForm'])
        ->name('2fa.enable');
    Route::post('/settings/2fa/enable', [TwoFactorController::class, 'enable'])
        ->name('2fa.enable.store');
    Route::post('/settings/2fa/disable', [TwoFactorController::class, 'disable'])
        ->name('2fa.disable');
    Route::post('/settings/2fa/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])
        ->name('2fa.recovery-codes');

    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:manage users')
        ->name('users.index');

    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->middleware('permission:manage users')
        ->name('users.create');

    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('permission:manage users')
        ->name('users.store');

    Route::post('/users/{user}/disable', [UserManagementController::class, 'disable'])
        ->middleware('permission:manage users')
        ->name('users.disable');

    Route::post('/users/{user}/enable', [UserManagementController::class, 'enable'])
        ->middleware('permission:manage users')
        ->name('users.enable');

    Route::post('/users/{user}/force-logout', [UserManagementController::class, 'forceLogout'])
        ->middleware('permission:manage users')
        ->name('users.force-logout');

    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:manage users')
        ->name('users.destroy');

    Route::post('/users/{user}/role', [UserManagementController::class, 'updateRole'])
        ->middleware('permission:manage users')
        ->name('users.role');

    Route::post('/users/{user}/permissions', [UserManagementController::class, 'updatePermissions'])
        ->middleware('permission:manage users')
        ->name('users.permissions');

    // ===== WhatsApp chat panel =====
    Route::prefix('whatsapp')->name('whatsapp.')->middleware('permission:view whatsapp|view all whatsapp conversations|manage crm')->group(function (): void {
        Route::get('/', [WhatsAppController::class, 'index'])->name('index');
        Route::get('/reports', [WhatsAppController::class, 'reports'])->name('reports');
        Route::get('/conversations', [WhatsAppController::class, 'conversations'])->name('conversations');
        Route::get('/conversations/{conversation}/messages', [WhatsAppController::class, 'messages'])->name('messages');
        Route::get('/conversations/{conversation}/plans', [WhatsAppController::class, 'planOptions'])->name('plans');
        Route::post('/conversations/{conversation}/send-plan', [WhatsAppController::class, 'sendPlan'])->name('send-plan');
        Route::post('/conversations/{conversation}/send', [WhatsAppController::class, 'send'])->name('send');
        Route::get('/media/{message}', [WhatsAppController::class, 'media'])->name('media');
        Route::post('/conversations/{conversation}/assign', [WhatsAppController::class, 'assign'])->name('assign');
        Route::post('/conversations/{conversation}/claim', [WhatsAppController::class, 'claim'])->name('claim');
        Route::post('/conversations/{conversation}/status', [WhatsAppController::class, 'status'])->name('status');
        Route::post('/conversations/{conversation}/link', [WhatsAppController::class, 'link'])->name('link');
        Route::post('/conversations/{conversation}/lead', [WhatsAppController::class, 'createLead'])->name('lead');
        Route::post('/start', [WhatsAppController::class, 'start'])->name('start');
        Route::post('/webhook/register', [WhatsAppController::class, 'registerWebhook'])->name('webhook.register');
        Route::get('/templates', [WhatsAppController::class, 'templates'])->name('templates.index');
        Route::post('/templates', [WhatsAppController::class, 'storeTemplate'])->name('templates.store');
        Route::delete('/templates/{template}', [WhatsAppController::class, 'destroyTemplate'])->name('templates.destroy');
    });
});

// Evolution API WhatsApp webhook (incoming messages)
Route::post('/webhook/whatsapp/evolution', [WhatsAppWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('webhook.whatsapp.evolution');
