<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table): void {
            $table->id();
            $table->string('offer_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('installment_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('installment_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date')->index();
            $table->date('valid_until')->nullable()->index();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('qr_code_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 50)->default('draft')->index();
            $table->string('stamp_text')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'lead_id', 'sales_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
