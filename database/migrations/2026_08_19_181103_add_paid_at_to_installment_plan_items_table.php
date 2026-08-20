<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_plan_items', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
            $table->string('paid_by')->nullable()->after('paid_at')->comment('Who recorded the payment');
            $table->string('payment_method')->nullable()->after('paid_by')->comment('cash, bank_transfer, visa, etc');
            $table->text('payment_notes')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('installment_plan_items', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'paid_by', 'payment_method', 'payment_notes']);
        });
    }
};
