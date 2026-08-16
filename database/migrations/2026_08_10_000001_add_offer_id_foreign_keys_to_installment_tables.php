<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_plans', function (Blueprint $table): void {
            $table->foreign('offer_id')->references('id')->on('offers')->nullOnDelete();
        });

        Schema::table('installment_plan_items', function (Blueprint $table): void {
            $table->foreign('offer_id')->references('id')->on('offers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('installment_plan_items', function (Blueprint $table): void {
            $table->dropForeign('installment_plan_items_offer_id_foreign');
        });

        Schema::table('installment_plans', function (Blueprint $table): void {
            $table->dropForeign('installment_plans_offer_id_foreign');
        });
    }
};
