<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_plan_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('installment_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_id')->nullable();
            $table->unsignedInteger('installment_number');
            $table->date('due_date')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('balance_after', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_custom')->default(false)->index();
            $table->timestamps();

            $table->unique(['installment_plan_id', 'installment_number'], 'inst_plan_items_plan_num_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_plan_items');
    }
};
