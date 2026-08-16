<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_phone', 50)->index();
            $table->string('customer_name')->nullable();
            $table->string('faalwa_user_ns')->nullable();
            $table->string('status', 20)->default('new')->index(); // new | assigned | closed
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('linked_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('linked_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->string('direction', 10)->default('incoming')->index(); // incoming | outgoing
            $table->text('body');
            $table->string('message_type', 20)->default('text'); // text | template
            $table->string('delivery_status', 20)->nullable(); // sent | failed (outgoing only)
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->string('faalwa_message_id', 120)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('message_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('namespace')->nullable();
            $table->string('template_name')->nullable();
            $table->string('lang', 10)->default('ar');
            $table->text('body');
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
