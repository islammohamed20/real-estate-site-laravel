<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\EvolutionApiService;
use App\Services\PushNotificationService;
use Carbon\CarbonImmutable;
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

        // Reactions are updates to an existing message, not standalone chat
        // messages. Process them before ignoring fromMe webhooks.
        if ($this->storeReaction($data)) {
            return response()->json(['received' => true]);
        }

        // Newer WhatsApp accounts use LID jids ("...@lid") for contacts; the
        // real phone number lives in remoteJidAlt — using the LID would create
        // duplicate conversations with a wrong number.
        $remoteJid = (string) (data_get($key, 'remoteJidAlt', '') ?: data_get($key, 'remoteJid', ''));
        $fromMe = (bool) data_get($key, 'fromMe', false);
        $messageId = (string) data_get($key, 'id', '');

        // Group chats are not part of the CRM inbox. Messages with fromMe=true
        // are outgoing messages sent from the connected phone and must be stored
        // so the dashboard shows the complete conversation history.
        if ($remoteJid === '' || str_contains($remoteJid, '@g.us')) {
            return response()->json(['received' => true]);
        }

        // If the only identifier left is a LID jid (no remoteJidAlt), we cannot
        // map it to a real phone — storing it would create a fake conversation
        // that can never be replied to.
        if (str_contains($remoteJid, '@lid')) {
            return response()->json(['received' => true]);
        }

        $body = $this->extractText($data);
        if ($body === null || trim($body) === '') {
            // Non-text messages (media, reactions...) are acknowledged but not stored.
            return response()->json(['received' => true]);
        }

        $phone = EvolutionApiService::jidToPhone($remoteJid);

        // Use the real WhatsApp message timestamp instead of the arrival time.
        $occurredAt = is_numeric(data_get($data, 'messageTimestamp'))
            ? CarbonImmutable::createFromTimestampUTC((int) data_get($data, 'messageTimestamp'))
            : now();

        try {
            DB::transaction(function () use ($phone, $body, $messageId, $data, $remoteJid, $occurredAt, $fromMe): void {
                $conversation = WhatsAppConversation::query()
                    ->withTrashed()
                    ->firstOrCreate(
                        ['customer_phone' => $phone],
                        [
                            'customer_name' => $this->extractName($data, $remoteJid),
                            'status' => WhatsAppConversation::STATUS_NEW,
                            'last_message_at' => $occurredAt,
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

                // A message sent from the dashboard also produces a fromMe
                // webhook. Link that webhook to the locally-created message
                // instead of inserting a duplicate.
                if ($fromMe) {
                    $localMessage = $conversation->messages()
                        ->where('direction', WhatsAppMessage::DIRECTION_OUTGOING)
                        ->where('body', trim($body))
                        ->whereNull('faalwa_message_id')
                        ->where('created_at', '>=', now()->subMinutes(2))
                        ->latest('id')
                        ->first();

                    if ($localMessage instanceof WhatsAppMessage) {
                        $localMessage->forceFill([
                            'faalwa_message_id' => $messageId !== '' ? $messageId : null,
                            'delivery_status' => 'sent',
                        ])->save();

                        $conversation->update(['last_message_at' => $occurredAt]);

                        return;
                    }
                }

                $message = $conversation->messages()->create([
                    'direction' => $fromMe ? WhatsAppMessage::DIRECTION_OUTGOING : WhatsAppMessage::DIRECTION_INCOMING,
                    'body' => trim($body),
                    'message_type' => 'text',
                    'delivery_status' => $fromMe ? 'sent' : null,
                    'faalwa_message_id' => $messageId !== '' ? $messageId : null,
                ]);

                // created_at is not mass-assignable — stamp the real WhatsApp
                // message time explicitly.
                // MySQL DATETIME has no timezone. Store the instant in the
                // application's timezone so Eloquent reads it consistently.
                $storedAt = $occurredAt->setTimezone(config('app.timezone'));
                $message->forceFill([
                    'created_at' => $storedAt,
                    'updated_at' => $storedAt,
                ])->save();

                $conversation->update([
                    'unread_count' => $fromMe ? (int) $conversation->unread_count : (int) $conversation->unread_count + 1,
                    'last_message_at' => $occurredAt,
                    'last_seen_at' => $fromMe ? $conversation->last_seen_at : null,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook failed', [
                'remote_jid' => $remoteJid,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['received' => false, 'error' => $e->getMessage()], 500);
        }

        // Send a push notification about the new WhatsApp message.
        $conversation = WhatsAppConversation::query()
            ->where('customer_phone', $phone)
            ->first();

        // Only incoming customer messages should create unread/push alerts.
        if (! $fromMe) {
            try {
                if ($conversation instanceof WhatsAppConversation) {
                    app(PushNotificationService::class)->notifyNewWhatsAppMessage(
                        $conversation->customer_name ?? 'Unknown',
                        trim($body),
                        (int) $conversation->id
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Push notification failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Store an Evolution reaction on the original message's `reactions` JSON.
     * Returns true when the event contains a reaction, even if the target
     * message is not found, so it is never inserted as a fake new message.
     */
    protected function storeReaction(array $data): bool
    {
        $reaction = data_get($data, 'message.reactionMessage');
        if (! is_array($reaction)) {
            return false;
        }

        $targetId = (string) (data_get($reaction, 'key.id') ?: data_get($reaction, 'key.remoteJid'));
        $emoji = trim((string) data_get($reaction, 'text', ''));
        $remoteJid = (string) (data_get($data, 'key.remoteJidAlt', '') ?: data_get($data, 'key.remoteJid', ''));
        $phone = EvolutionApiService::jidToPhone($remoteJid);

        if ($targetId === '' || $phone === '') {
            return true;
        }

        $conversation = WhatsAppConversation::query()
            ->where('customer_phone', $phone)
            ->first();

        $message = $conversation?->messages()
            ->where('faalwa_message_id', $targetId)
            ->first();

        if (! $message instanceof WhatsAppMessage) {
            return true;
        }

        $reactions = collect($message->reactions ?? []);
        $direction = (bool) data_get($data, 'key.fromMe', false) ? 'outgoing' : 'incoming';
        $reactions = $reactions->reject(fn (array $item) => ($item['direction'] ?? null) === $direction);

        if ($emoji !== '') {
            $reactions->push([
                'emoji' => $emoji,
                'direction' => $direction,
                'created_at' => now()->toIso8601String(),
            ]);
        }

        $message->forceFill(['reactions' => $reactions->values()->all()])->save();

        return true;
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

    protected function extractName(array $data, string $remoteJid): ?string
    {
        $pushName = data_get($data, 'pushName');
        if (! empty($pushName)) {
            return (string) $pushName;
        }

        if ($remoteJid !== '') {
            return EvolutionApiService::jidToPhone($remoteJid);
        }

        return null;
    }
}
