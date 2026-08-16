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
            $table->unsignedSmallInteger('logo_height_desktop')->default(40)->after('logo_dark_path');
            $table->unsignedSmallInteger('logo_height_mobile')->default(36)->after('logo_height_desktop');
            $table->json('available_features')->nullable()->after('default_language');
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table): void {
            $table->dropColumn(['logo_height_desktop', 'logo_height_mobile', 'available_features']);
        });
    }
};
