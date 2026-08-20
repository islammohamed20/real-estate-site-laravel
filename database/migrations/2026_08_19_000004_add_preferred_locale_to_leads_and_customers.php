<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->string('preferred_locale', 8)->default('ar')->after('notes');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->string('preferred_locale', 8)->default('ar')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropColumn('preferred_locale');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn('preferred_locale');
        });
    }
};
