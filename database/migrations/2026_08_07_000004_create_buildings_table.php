<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('status', 50)->default('draft');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->unique(['project_id', 'code']);
            $table->index(['project_id', 'phase_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
