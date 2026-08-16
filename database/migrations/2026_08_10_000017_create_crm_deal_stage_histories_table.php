<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deal_stage_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('deal_id')->constrained('crm_deals')->cascadeOnDelete();
            $table->foreignId('from_stage_id')->nullable()->constrained('crm_stages')->nullOnDelete();
            $table->foreignId('to_stage_id')->constrained('crm_stages');
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deal_stage_histories');
    }
};
