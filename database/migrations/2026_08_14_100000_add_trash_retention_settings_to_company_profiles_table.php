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
            $table->unsignedSmallInteger('trash_retention_days')->default(30)->after('maintenance_percent');
            $table->boolean('auto_purge_enabled')->default(true)->after('trash_retention_days');
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table): void {
            $table->dropColumn(['trash_retention_days', 'auto_purge_enabled']);
        });
    }
};
