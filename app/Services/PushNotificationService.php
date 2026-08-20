<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private ?WebPush $webPush = null;

    private function getWebPush(): WebPush
    {
        if ($this->webPush === null) {
            $this->webPush = new WebPush(config('webpush', []));
        }
        return $this->webPush;
    }

    /**
     * Send a push notification to specific users by role.
     */
    public function sendToRoles(array $roles, string $title, string $body, string $url = '/real-statement-control/dashboard', array $extra = []): int
    {
        $users = User::whereHas('roles', fn ($q) => $q->whereIn('name', $roles))->get();
        return $this->sendToUsers($users, $title, $body, $url, $extra);
    }

    /**
     * Send a push notification to specific users.
     */
    public function sendToUsers($users, string $title, string $body, string $url = '/real-statement-control/dashboard', array $extra = []): int
    {
        $sent = 0;
        $subscriptions = PushSubscription::whereIn('user_id', $users->pluck('id'))->get();

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => '/icons/icon.svg',
            'badge' => '/icons/icon.svg',
            'tag' => $extra['tag'] ?? 'estateflow',
            'renotify' => $extra['renotify'] ?? true,
            'data' => array_merge(['url' => $url], $extra),
        ], JSON_THROW_ON_ERROR);

        foreach ($subscriptions as $sub) {
            try {
                $webPushSub = new Subscription(
                    $sub->endpoint,
                    $sub->public_key,
                    $sub->auth_token,
                    $sub->content_encoding ?? 'aes128gcm'
                );
                $this->getWebPush()->queueNotification($webPushSub, $payload);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Push notification failed', [
                    'user_id' => $sub->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Flush all queued notifications
        if ($sent > 0) {
            $reports = $this->getWebPush()->flush();
            foreach ($reports as $report) {
                if ($report->isSuccess()) continue;
                // Remove stale/expired subscriptions
                $endpoint = $report->getEndpoint();
                PushSubscription::where('endpoint', $endpoint)->delete();
                Log::info('Removed stale push subscription', ['endpoint' => substr($endpoint, 0, 60)]);
            }
        }

        return $sent;
    }

    /**
     * Notify about a new WhatsApp message.
     */
    public function notifyNewWhatsAppMessage(string $customerName, string $preview, int $conversationId): int
    {
        return $this->sendToRoles(
            ['Administrator', 'Owner', 'Sales Manager'],
            'New WhatsApp message',
            $customerName . ': ' . mb_substr($preview, 0, 100),
            '/real-statement-control/whatsapp?conversation=' . $conversationId,
            ['tag' => 'whatsapp-' . $conversationId]
        );
    }

    /**
     * Notify about a CRM event (lead, customer, offer).
     */
    public function notifyCrmEvent(string $title, string $body, string $url = '/real-statement-control/crm'): int
    {
        return $this->sendToRoles(
            ['Administrator', 'Owner', 'Sales Manager'],
            $title,
            $body,
            $url
        );
    }
}
