<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->integer('number');
            $table->string('name')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->unique(['building_id', 'number']);
            $table->index(['project_id', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
