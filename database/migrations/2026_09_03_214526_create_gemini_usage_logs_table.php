<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gemini_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->string('push_name')->nullable();
            $table->text('customer_message')->nullable();
            $table->text('ai_reply')->nullable();
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('thoughts_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->string('service_tier')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gemini_usage_logs');
    }
};
