<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FacebookSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\FacebookMessengerService;
use App\Services\PushNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacebookWebhookController extends Controller
{
    public function __construct(
        protected FacebookMessengerService $facebook
    ) {
    }

    /**
     * Handle GET verification request from Facebook.
     */
    public function verify(Request $request): JsonResponse
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $result = $this->facebook->verifyWebhook($mode, $token, $challenge);

        if ($result !== null) {
            Log::info('Facebook webhook verified successfully');

            return response()->json((int) $result);
        }

        Log::warning('Facebook webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
        ]);

        return response()->json(['error' => 'Verification failed'], 403);
    }

    /**
     * Handle incoming messages from Facebook Messenger.
     */
    public function handle(Request $request): JsonResponse
    {
        // Verify signature
        $signature = $request->header('X-Hub-Signature-256', '');
        $payload = $request->getContent();

        if (! $this->facebook->verifySignature($payload, $signature)) {
            Log::warning('Facebook webhook: Invalid signature');

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data = $request->json()->all();

        // Process each entry
        foreach (data_get($data, 'entry', []) as $entry) {
            $pageId = (string) data_get($entry, 'id');
            $events = data_get($entry, 'messaging', []);

            foreach ($events as $event) {
                $this->processEvent($event, $pageId);
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Process a single messaging event.
     */
    protected function processEvent(array $event, string $pageId): void
    {
        $senderId = (string) data_get($event, 'sender.id');
        $timestamp = data_get($event, 'timestamp');

        if ($senderId === '') {
            return;
        }

        // Handle message
        $message = data_get($event, 'message');
        if (is_array($message)) {
            $this->handleMessage($senderId, $message, $pageId, $timestamp);
        }

        // Handle postback (button clicks)
        $postback = data_get($event, 'postback');
        if (is_array($postback)) {
            $this->handlePostback($senderId, $postback, $pageId, $timestamp);
        }

        // Handle delivery confirmation
        $delivery = data_get($event, 'delivery');
        if (is_array($delivery)) {
            $this->handleDelivery($delivery, $pageId);
        }
    }

    /**
     * Handle an incoming text message.
     */
    protected function handleMessage(string $senderId, array $message, string $pageId, ?int $timestamp): void
    {
        $messageId = (string) data_get($message, 'mid', '');
        $text = (string) data_get($message, 'text', '');

        // Skip echo messages (our own messages sent via API)
        if (data_get($message, 'is_echo', false)) {
            // Mark as delivered for outgoing messages
            if ($messageId !== '') {
                $this->markDelivered($messageId, $pageId);
            }

            return;
        }

        if ($text === '' && empty($message['attachments'])) {
            return;
        }

        // Handle attachments (images, files, etc.)
        if ($text === '' && ! empty($message['attachments'])) {
            $text = $this->extractAttachmentText($message);
        }

        $occurredAt = $timestamp
            ? CarbonImmutable::createFromTimestampUTC($timestamp)
            : now();

        try {
            DB::transaction(function () use ($senderId, $text, $messageId, $pageId, $occurredAt) {
                // Get or create conversation
                $conversation = WhatsAppConversation::query()
                    ->withTrashed()
                    ->firstOrCreate(
                        [
                            'platform_user_id' => $senderId,
                            'platform' => 'facebook',
                        ],
                        [
                            'customer_phone' => 'fb_'.$senderId,
                            'customer_name' => $this->fetchUserName($senderId),
                            'platform_page_id' => $pageId,
                            'status' => WhatsAppConversation::STATUS_NEW,
                            'last_message_at' => $occurredAt,
                        ]
                    );

                if ($conversation->trashed()) {
                    $conversation->restore();
                }

                // Deduplicate
                $exists = $messageId !== ''
                    && $conversation->messages()->where('platform_message_id', $messageId)->exists();

                if ($exists) {
                    return;
                }

                $messageRecord = $conversation->messages()->create([
                    'direction' => WhatsAppMessage::DIRECTION_INCOMING,
                    'body' => trim($text),
                    'message_type' => 'text',
                    'platform_message_id' => $messageId !== '' ? $messageId : null,
                ]);

                $storedAt = $occurredAt->setTimezone(config('app.timezone'));
                $messageRecord->forceFill([
                    'created_at' => $storedAt,
                    'updated_at' => $storedAt,
                ])->save();

                $conversation->update([
                    'unread_count' => (int) $conversation->unread_count + 1,
                    'last_message_at' => $occurredAt,
                    'last_seen_at' => null,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Facebook webhook message failed', [
                'sender' => $senderId,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        // Push notification
        $conversation = WhatsAppConversation::query()
            ->where('platform_user_id', $senderId)
            ->where('platform', 'facebook')
            ->first();

        if ($conversation) {
            // Mark as seen automatically
            $this->facebook->markSeen($senderId);

            try {
                app(PushNotificationService::class)->notifyNewWhatsAppMessage(
                    ($conversation->customer_name ?? 'Facebook User').' [FB]',
                    trim($text),
                    (int) $conversation->id
                );
            } catch (\Throwable $e) {
                Log::warning('Push notification failed', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Handle postback events (button clicks).
     */
    protected function handlePostback(string $senderId, array $postback, string $pageId, ?int $timestamp): void
    {
        $title = (string) data_get($postback, 'title', '');
        $payload = (string) data_get($postback, 'payload', '');

        if ($title !== '') {
            $this->handleMessage($senderId, ['text' => $title, 'mid' => 'postback_'.md5($payload)], $pageId, $timestamp);
        }
    }

    /**
     * Update delivery status for outgoing messages.
     */
    protected function handleDelivery(array $delivery, string $pageId): void
    {
        $mids = data_get($delivery, 'mids', []);

        foreach ($mids as $mid) {
            $this->markDelivered((string) $mid, $pageId);
        }
    }

    /**
     * Mark a Facebook message as delivered in our database.
     */
    protected function markDelivered(string $messageId, string $pageId): void
    {
        WhatsAppMessage::query()
            ->where('platform_message_id', $messageId)
            ->update(['delivery_status' => 'delivered']);
    }

    /**
     * Extract text from attachments.
     */
    protected function extractAttachmentText(array $message): string
    {
        $attachments = data_get($message, 'attachments', []);

        foreach ($attachments as $attachment) {
            $type = data_get($attachment, 'type');
            $payload = data_get($attachment, 'payload', []);

            return match ($type) {
                'image' => '📷 [Image]',
                'video' => '🎬 [Video]',
                'audio' => '🎵 [Audio]',
                'file' => '📎 [File: '.data_get($payload, 'name', 'file').']',
                'location' => '📍 [Location]',
                default => '['.$type.']',
            };
        }

        return '';
    }

    /**
     * Fetch the user's name from Facebook Graph API.
     */
    protected function fetchUserName(string $psid): string
    {
        $profile = $this->facebook->getUserProfile($psid);

        if ($profile) {
            $first = data_get($profile, 'first_name', '');
            $last = data_get($profile, 'last_name', '');

            return trim($first.' '.$last) ?: 'Facebook User';
        }

        return 'Facebook User';
    }
}
