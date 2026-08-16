<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\EvolutionApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncWhatsAppMessages extends Command
{
    protected $signature = 'whatsapp:sync {--limit=10}';

    protected $description = 'Poll recent chats from the Evolution API as a webhook fallback and store new incoming messages.';

    public function handle(EvolutionApiService $evolution): int
    {
        if (! $evolution->isConfigured()) {
            $this->warn('Evolution API is not configured — nothing to sync.');

            return self::SUCCESS;
        }

        $chats = $evolution->getChats((int) $this->option('limit'));
        $synced = 0;

        foreach ($chats as $chat) {
            $jid = (string) data_get($chat, 'id', '');
            if ($jid === '' || str_contains($jid, '@g.us')) {
                continue;
            }

            $phone = EvolutionApiService::jidToPhone($jid);
            $messages = $evolution->getChatMessages($jid, 15);

            foreach ($messages as $msg) {
                $key = data_get($msg, 'key', []);
                if ((bool) data_get($key, 'fromMe', false)) {
                    continue;
                }

                $body = data_get($msg, 'message.conversation')
                    ?? data_get($msg, 'message.extendedTextMessage.text');

                if ($body === null || trim((string) $body) === '') {
                    continue;
                }

                $messageId = (string) data_get($key, 'id', '');

                $saved = DB::transaction(function () use ($phone, $body, $messageId, $chat): bool {
                    $conversation = WhatsAppConversation::query()->firstOrCreate(
                        ['customer_phone' => $phone],
                        [
                            'customer_name' => data_get($chat, 'name') ?: data_get($chat, 'pushName'),
                            'status' => WhatsAppConversation::STATUS_NEW,
                            'last_message_at' => now(),
                        ]
                    );

                    if ($messageId !== '' && $conversation->messages()->where('faalwa_message_id', $messageId)->exists()) {
                        return false;
                    }

                    $conversation->messages()->create([
                        'direction' => WhatsAppMessage::DIRECTION_INCOMING,
                        'body' => trim((string) $body),
                        'message_type' => 'text',
                        'faalwa_message_id' => $messageId !== '' ? $messageId : null,
                    ]);

                    $conversation->update([
                        'unread_count' => (int) $conversation->unread_count + 1,
                        'last_message_at' => now(),
                    ]);

                    return true;
                });

                if ($saved) {
                    $synced++;
                    $this->info("Synced message from {$phone}");
                }
            }
        }

        $this->info("WhatsApp sync complete — {$synced} new message(s).");

        return self::SUCCESS;
    }
}
