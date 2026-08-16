<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table): void {
            if (Schema::hasColumn('company_profiles', 'tax_number')) {
                $table->dropColumn('tax_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table): void {
            $table->string('tax_number')->nullable()->after('website');
        });
    }
};
