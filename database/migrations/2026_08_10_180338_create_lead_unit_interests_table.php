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
        Schema::create('lead_unit_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->string('priority', 20)->nullable()->default('normal');
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('interested');
            $table->timestamps();

            $table->unique(['lead_id', 'unit_id']);
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_unit_interests');
    }
};
