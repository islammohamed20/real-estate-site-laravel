<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('deal_id')->constrained('crm_deals')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->morphs('activityable');
            $table->string('type', 50); // call, email, meeting, note, task, whatsapp, sms, follow_up
            $table->string('subject', 255)->nullable();
            $table->text('body')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('outcome', 100)->nullable();
            $table->string('duration', 30)->nullable();
            $table->timestamps();

            $table->index(['deal_id', 'type']);
            $table->index(['due_at', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
};
