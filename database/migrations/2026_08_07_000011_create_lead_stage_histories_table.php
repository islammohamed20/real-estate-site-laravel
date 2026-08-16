<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_stage_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('stage_from', 50)->nullable()->index();
            $table->string('stage_to', 50)->index();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->index();
            $table->timestamps();

            $table->index(['lead_id', 'stage_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_stage_histories');
    }
};
