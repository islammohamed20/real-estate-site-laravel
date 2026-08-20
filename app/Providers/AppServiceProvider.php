<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Crm\CrmDeal;
use App\Models\Reservation;
use App\Observers\CrmDealObserver;
use App\Observers\ReservationObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The framework auto-registers the base EventServiceProvider with event
        // discovery ON, which would register every listener in app/Listeners a
        // second time (Class@handle) and duplicate queued notifications.
        BaseEventServiceProvider::disableEventDiscovery();
    }

    public function boot(): void
    {
        CrmDeal::observe(CrmDealObserver::class);
        Reservation::observe(ReservationObserver::class);

        Paginator::defaultView('vendor.pagination.app');
        Paginator::defaultSimpleView('vendor.pagination.app');

        // Share the latest notifications with the dashboard header bell.
        View::composer('layouts.dashboard', function (ViewContract $view): void {
            $user = auth()->user();

            $view->with('dashboardNotifications', $user ? $user->notifications()->latest()->take(8)->get()->map(function ($notification) {
                $data = $notification->data ?? [];
                $locale = app()->getLocale();

                return (object) [
                    'id' => $notification->id,
                    'title' => $data['title_'.$locale] ?? $data['title_en'] ?? __('Notification'),
                    'message' => $data['message_'.$locale] ?? $data['message_en'] ?? '',
                    'url' => $data['action_url'] ?? '#',
                    'unread' => $notification->read_at === null,
                    'created_at' => $notification->created_at,
                ];
            }) : collect());
            $view->with('dashboardUnreadCount', $user ? $user->unreadNotifications()->count() : 0);
        });

        if (app()->runningInConsole()) {
            return;
        }

        $request = request();

        if ($request->isSecure() || $request->header('x-forwarded-proto') === 'https' || str_contains($request->getHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
    }
}
