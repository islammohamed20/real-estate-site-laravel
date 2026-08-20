<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FacebookSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookMessengerService
{
    private const GRAPH_API = 'https://graph.facebook.com/v19.0';

    /**
     * Send a text message to a Facebook user via the Messenger Platform.
     */
    public function sendText(string $recipientId, string $text, ?FacebookSetting $settings = null): bool
    {
        $settings ??= FacebookSetting::active();

        if (! $settings || empty($settings->access_token)) {
            Log::warning('Facebook Messenger: No active settings or access token');

            return false;
        }

        try {
            $response = Http::timeout(15)->post(self::GRAPH_API.'/'.$settings->page_id.'/messages', [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $text],
                'messaging_type' => 'RESPONSE',
                'access_token' => $settings->access_token,
            ]);

            if ($response->failed()) {
                Log::error('Facebook Messenger send failed', [
                    'recipient' => $recipientId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Facebook Messenger send error', [
                'recipient' => $recipientId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send a quick reply (text with suggested actions).
     */
    public function sendQuickReply(string $recipientId, string $text, array $replies, ?FacebookSetting $settings = null): bool
    {
        $settings ??= FacebookSetting::active();

        if (! $settings) {
            return false;
        }

        $quickReplies = array_map(fn ($reply) => [
            'content_type' => 'text',
            'title' => $reply['title'],
            'payload' => $reply['payload'] ?? $reply['title'],
        ], $replies);

        try {
            $response = Http::timeout(15)->post(self::GRAPH_API.'/'.$settings->page_id.'/messages', [
                'recipient' => ['id' => $recipientId],
                'message' => [
                    'text' => $text,
                    'quick_replies' => $quickReplies,
                ],
                'messaging_type' => 'RESPONSE',
                'access_token' => $settings->access_token,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Facebook Messenger quick reply error', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Mark a message as seen (sends read receipt).
     */
    public function markSeen(string $recipientId, ?FacebookSetting $settings = null): bool
    {
        $settings ??= FacebookSetting::active();

        if (! $settings) {
            return false;
        }

        try {
            $response = Http::timeout(10)->post(self::GRAPH_API.'/'.$settings->page_id('/messages'), [
                'recipient' => ['id' => $recipientId],
                'sender_action' => 'mark_seen',
                'access_token' => $settings->access_token,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Send typing indicator.
     */
    public function sendTypingOn(string $recipientId, ?FacebookSetting $settings = null): bool
    {
        $settings ??= FacebookSetting::active();

        if (! $settings) {
            return false;
        }

        try {
            $response = Http::timeout(10)->post(self::GRAPH_API.'/'.$settings->page_id.'/messages', [
                'recipient' => ['id' => $recipientId],
                'sender_action' => 'typing_on',
                'access_token' => $settings->access_token,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get user profile from Facebook Graph API.
     */
    public function getUserProfile(string $psid, ?FacebookSetting $settings = null): ?array
    {
        $settings ??= FacebookSetting::active();

        if (! $settings) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get(self::GRAPH_API.'/'.$psid, [
                'fields' => 'first_name,last_name,profile_pic',
                'access_token' => $settings->access_token,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Verify the webhook hub verification token from Facebook.
     */
    public function verifyWebhook(string $mode, string $token, string $challenge): ?string
    {
        if ($mode === 'subscribe' && $token === config('services.facebook.verify_token', 'venecia_fb_verify')) {
            return $challenge;
        }

        return null;
    }

    /**
     * Verify the X-Hub-Signature for incoming webhooks.
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        $settings = FacebookSetting::active();

        if (! $settings || empty($settings->app_secret)) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $payload, $settings->app_secret);

        return hash_equals($expected, $signature);
    }
}
