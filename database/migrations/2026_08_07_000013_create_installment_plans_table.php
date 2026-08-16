<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('installment_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('building_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('offer_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('status', 50)->default('draft')->index();
            $table->string('currency_code', 10)->default('USD');
            $table->decimal('base_price', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('final_price', 14, 2)->default(0);
            $table->decimal('maintenance_deposit', 14, 2)->default(0);
            $table->decimal('down_payment', 14, 2)->default(0);
            $table->decimal('remaining_amount', 14, 2)->default(0);
            $table->decimal('installment_amount', 14, 2)->default(0);
            $table->unsignedInteger('installment_count')->default(0);
            $table->string('installment_type', 50)->default('monthly');
            $table->json('schedule_json')->nullable();
            $table->date('starts_at')->nullable()->index();
            $table->decimal('last_installment_adjustment', 14, 2)->default(0);
            $table->boolean('saved_from_calculator')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};
