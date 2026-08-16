<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table): void {
            // Per-unit excellence/premium percentage used by the calculator
            // (set from the dashboard, read-only for customers).
            $table->decimal('excellence_percent', 5, 2)->default(0)->after('roof_price');

            // Number of terraces for the unit.
            $table->unsignedTinyInteger('terrace_count')->default(0)->after('balcony_area');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table): void {
            $table->dropColumn(['excellence_percent', 'terrace_count']);
        });
    }
};
