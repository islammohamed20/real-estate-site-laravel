<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_incoming_message_with_lid_jid_uses_remote_jid_alt_phone(): void
    {
        // A rep already started a conversation with the customer's real number.
        WhatsAppConversation::query()->create([
            'customer_phone' => '201008643035',
            'customer_name' => 'Princess Ooda',
            'status' => WhatsAppConversation::STATUS_ASSIGNED,
        ]);

        // Evolution sends the LID jid; the real phone lives in remoteJidAlt.
        $this->postJson('/webhook/whatsapp/evolution', [
            'event' => 'messages.upsert',
            'data' => [
                'key' => [
                    'id' => 'MSG-LID-1',
                    'fromMe' => false,
                    'remoteJid' => '95541883105445@lid',
                    'remoteJidAlt' => '201008643035@s.whatsapp.net',
                ],
                'pushName' => 'Princess Ooda',
                'message' => ['conversation' => 'Hello'],
                'messageTimestamp' => 1786908606,
            ],
        ])->assertOk();

        // The message lands in the existing conversation — no duplicate created.
        $this->assertSame(1, WhatsAppConversation::query()->count());
        $this->assertDatabaseHas('whatsapp_conversations', [
            'customer_phone' => '201008643035',
        ]);
        $this->assertDatabaseMissing('whatsapp_conversations', [
            'customer_phone' => '95541883105445',
        ]);

        $message = WhatsAppMessage::query()->firstOrFail();
        $this->assertSame('Hello', $message->body);
        $this->assertSame('incoming', $message->direction);
        $this->assertSame(1786908606, $message->created_at->utc()->timestamp);
    }

    public function test_outgoing_message_sent_from_phone_is_stored_in_conversation(): void
    {
        WhatsAppConversation::query()->create([
            'customer_phone' => '201008643035',
            'customer_name' => 'Princess Ooda',
            'status' => WhatsAppConversation::STATUS_ASSIGNED,
        ]);

        $this->postJson('/webhook/whatsapp/evolution', [
            'event' => 'messages.upsert',
            'data' => [
                'key' => [
                    'id' => 'MSG-OUTGOING-1',
                    'fromMe' => true,
                    'remoteJid' => '201008643035@s.whatsapp.net',
                ],
                'message' => ['conversation' => 'Sent from the phone'],
                'messageTimestamp' => 1786908606,
            ],
        ])->assertOk();

        $message = WhatsAppMessage::query()->firstOrFail();
        $this->assertSame(WhatsAppMessage::DIRECTION_OUTGOING, $message->direction);
        $this->assertSame('Sent from the phone', $message->body);
        $this->assertSame('MSG-OUTGOING-1', $message->faalwa_message_id);
        $this->assertSame('sent', $message->delivery_status);
        $this->assertSame(0, (int) $message->conversation->unread_count);
    }

    public function test_dashboard_outgoing_message_is_linked_when_from_me_webhook_arrives(): void
    {
        $conversation = WhatsAppConversation::query()->create([
            'customer_phone' => '201008643035',
            'customer_name' => 'Princess Ooda',
            'status' => WhatsAppConversation::STATUS_ASSIGNED,
        ]);

        $message = $conversation->messages()->create([
            'direction' => WhatsAppMessage::DIRECTION_OUTGOING,
            'body' => 'Sent from the dashboard',
            'message_type' => 'text',
            'delivery_status' => 'sent',
        ]);

        $this->postJson('/webhook/whatsapp/evolution', [
            'event' => 'messages.upsert',
            'data' => [
                'key' => [
                    'id' => 'MSG-DASHBOARD-1',
                    'fromMe' => true,
                    'remoteJid' => '201008643035@s.whatsapp.net',
                ],
                'message' => ['conversation' => 'Sent from the dashboard'],
                'messageTimestamp' => now()->timestamp,
            ],
        ])->assertOk();

        $this->assertSame(1, WhatsAppMessage::query()->count());
        $this->assertSame('MSG-DASHBOARD-1', $message->fresh()->faalwa_message_id);
    }
    public function test_incoming_message_with_lid_jid_and_no_alt_is_skipped(): void
    {
        $this->postJson('/webhook/whatsapp/evolution', [
            'event' => 'messages.upsert',
            'data' => [
                'key' => [
                    'id' => 'MSG-LID-2',
                    'fromMe' => false,
                    'remoteJid' => '233474472599725@lid',
                ],
                'pushName' => 'Hamza',
                'message' => ['conversation' => 'Hi'],
                'messageTimestamp' => 1786885697,
            ],
        ])->assertOk();

        // Without remoteJidAlt the LID cannot be mapped to a real phone —
        // storing it would create a fake conversation that can never be
        // replied to.
        $this->assertSame(0, WhatsAppConversation::query()->count());
        $this->assertSame(0, WhatsAppMessage::query()->count());
    }
}
