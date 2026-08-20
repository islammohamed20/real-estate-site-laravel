<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\EvolutionApiService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncWhatsAppMessages extends Command
{
    protected $signature = 'whatsapp:sync {--limit=50}';

    protected $description = 'Poll recent messages from the Evolution API as a webhook fallback and store new incoming messages.';

    public function handle(EvolutionApiService $evolution): int
    {
        if (! $evolution->isConfigured()) {
            $this->warn('Evolution API is not configured — nothing to sync.');

            return self::SUCCESS;
        }

        // This Evolution build ignores paging/filtering on /chat/findMessages
        // and always returns the recent global message list — so process it in
        // one pass. The webhook remains the primary path; this is the fallback.
        $messages = $evolution->getChatMessages(null, (int) $this->option('limit'));
        $synced = 0;

        foreach ($messages as $msg) {
            $key = data_get($msg, 'key', []);
            $fromMe = (bool) data_get($key, 'fromMe', false);

            // LID jids ("...@lid") carry the real phone in remoteJidAlt.
            $remoteJid = (string) (data_get($key, 'remoteJidAlt', '') ?: data_get($key, 'remoteJid', ''));
            if ($remoteJid === '' || str_contains($remoteJid, '@g.us')) {
                continue;
            }

            // Without remoteJidAlt the LID is unusable — skip rather than
            // creating a fake conversation that can never be replied to.
            if (str_contains($remoteJid, '@lid')) {
                continue;
            }

            $phone = EvolutionApiService::jidToPhone($remoteJid);
            if ($phone === '') {
                continue;
            }

            $body = data_get($msg, 'message.conversation')
                ?? data_get($msg, 'message.extendedTextMessage.text');

            if ($body === null || trim((string) $body) === '') {
                continue;
            }

            $messageId = (string) data_get($key, 'id', '');

            $occurredAt = is_numeric(data_get($msg, 'messageTimestamp'))
                ? CarbonImmutable::createFromTimestampUTC((int) data_get($msg, 'messageTimestamp'))
                : now();

            $saved = DB::transaction(function () use ($phone, $body, $messageId, $msg, $occurredAt, $fromMe): bool {
                $conversation = WhatsAppConversation::query()->firstOrCreate(
                    ['customer_phone' => $phone],
                    [
                        'customer_name' => data_get($msg, 'pushName'),
                        'status' => WhatsAppConversation::STATUS_NEW,
                        'last_message_at' => $occurredAt,
                    ]
                );

                if ($messageId !== '' && $conversation->messages()->where('faalwa_message_id', $messageId)->exists()) {
                    return false;
                }

                if ($fromMe) {
                    $localMessage = $conversation->messages()
                        ->where('direction', WhatsAppMessage::DIRECTION_OUTGOING)
                        ->where('body', trim((string) $body))
                        ->whereNull('faalwa_message_id')
                        ->where('created_at', '>=', now()->subMinutes(2))
                        ->latest('id')
                        ->first();

                    if ($localMessage instanceof WhatsAppMessage) {
                        $localMessage->forceFill([
                            'faalwa_message_id' => $messageId !== '' ? $messageId : null,
                            'delivery_status' => 'sent',
                        ])->save();

                        return false;
                    }
                }

                $message = $conversation->messages()->create([
                    'direction' => $fromMe ? WhatsAppMessage::DIRECTION_OUTGOING : WhatsAppMessage::DIRECTION_INCOMING,
                    'body' => trim((string) $body),
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
                ]);

                return true;
            });

            if ($saved) {
                $synced++;
                $this->info("Synced message from {$phone}");
            }
        }

        $this->info("WhatsApp sync complete — {$synced} new message(s).");

        return self::SUCCESS;
    }
}
