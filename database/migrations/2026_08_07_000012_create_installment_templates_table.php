<?php

declare(strict_types=1);

use App\Enums\InstallmentFrequency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('down_payment_percent', 8, 2)->nullable();
            $table->decimal('down_payment_amount', 14, 2)->nullable();
            $table->unsignedInteger('installment_count');
            $table->string('installment_frequency', 50)->default(InstallmentFrequency::Monthly->value);
            $table->decimal('maintenance_percent', 8, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->unsignedInteger('first_installment_offset_months')->default(0);
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('defaults_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_templates');
    }
};
