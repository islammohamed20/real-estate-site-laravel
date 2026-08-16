<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()?->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', __('All notifications marked as read.'));
    }

    public function markRead(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        if ($request->user()?->id === $notification->notifiable_id) {
            $notification->markAsRead();
        }

        return back();
    }
}
