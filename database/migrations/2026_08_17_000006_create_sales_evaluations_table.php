<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_evaluations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('period', 7); // YYYY-MM
            $table->decimal('score', 10, 2)->default(0);
            $table->char('grade', 1)->nullable(); // A | B | C | D
            $table->json('metrics')->nullable(); // snapshot of the underlying KPIs
            $table->timestamps();

            $table->unique(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_evaluations');
    }
};
