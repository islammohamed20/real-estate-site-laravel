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
            $table->decimal('excellence_percent', 5, 2)->default(0)->after('base_price');
            $table->decimal('excellence_amount', 14, 2)->default(0)->after('excellence_percent');
            $table->decimal('base_price_with_excellence', 14, 2)->default(0)->after('excellence_amount');
            $table->decimal('discount_percent', 8, 2)->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('installment_plans', function (Blueprint $table): void {
            $table->dropColumn([
                'excellence_percent',
                'excellence_amount',
                'base_price_with_excellence',
                'discount_percent',
            ]);
        });
    }
};
