<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('instagram_url');
            $table->string('seo_description', 500)->nullable()->after('seo_title');
            $table->string('seo_image_path')->nullable()->after('seo_description');
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'seo_image_path']);
        });
    }
};
