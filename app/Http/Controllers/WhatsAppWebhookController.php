<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\EvolutionApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Evolution API pushes "messages.upsert" events here once the instance
     * webhook URL is registered (see WhatsAppController::registerWebhook).
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        $event = data_get($payload, 'event', '');

        if ($event !== 'messages.upsert') {
            // Ack unrelated events silently (status updates, etc.).
            return response()->json(['received' => true]);
        }

        $data = data_get($payload, 'data', []);
        $key = data_get($data, 'key', []);
        $remoteJid = (string) data_get($key, 'remoteJid', '');
        $fromMe = (bool) data_get($key, 'fromMe', false);
        $messageId = (string) data_get($key, 'id', '');

        // Ignore group chats and our own outgoing messages (webhooks fire for both).
        if ($remoteJid === '' || str_contains($remoteJid, '@g.us') || $fromMe) {
            return response()->json(['received' => true]);
        }

        $body = $this->extractText($data);
        if ($body === null || trim($body) === '') {
            // Non-text messages (media, reactions...) are acknowledged but not stored.
            return response()->json(['received' => true]);
        }

        $phone = EvolutionApiService::jidToPhone($remoteJid);

        try {
            DB::transaction(function () use ($phone, $body, $messageId, $data): void {
                $conversation = WhatsAppConversation::query()
                    ->withTrashed()
                    ->firstOrCreate(
                        ['customer_phone' => $phone],
                        [
                            'customer_name' => $this->extractName($data),
                            'status' => WhatsAppConversation::STATUS_NEW,
                            'last_message_at' => now(),
                        ]
                    );

                if ($conversation->trashed()) {
                    $conversation->restore();
                }

                $exists = $messageId !== ''
                    && $conversation->messages()->where('faalwa_message_id', $messageId)->exists();

                if ($exists) {
                    return;
                }

                $conversation->messages()->create([
                    'direction' => WhatsAppMessage::DIRECTION_INCOMING,
                    'body' => trim($body),
                    'message_type' => 'text',
                    'faalwa_message_id' => $messageId !== '' ? $messageId : null,
                ]);

                $conversation->update([
                    'unread_count' => (int) $conversation->unread_count + 1,
                    'last_message_at' => now(),
                    'last_seen_at' => null,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook failed', [
                'remote_jid' => $remoteJid,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['received' => false, 'error' => $e->getMessage()], 500);
        }

        return response()->json(['received' => true]);
    }

    protected function extractText(array $data): ?string
    {
        $message = data_get($data, 'message', []);

        return (string) (data_get($message, 'conversation')
            ?? data_get($message, 'extendedTextMessage.text')
            ?? data_get($message, 'imageMessage.caption')
            ?? data_get($message, 'videoMessage.caption'))
            ?: null;
    }

    protected function extractName(array $data): ?string
    {
        $pushName = data_get($data, 'pushName');
        if (! empty($pushName)) {
            return (string) $pushName;
        }

        $notify = data_get($data, 'key.remoteJid');
        if ($notify !== null) {
            return EvolutionApiService::jidToPhone((string) $notify);
        }

        return null;
    }
}
