<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('sales_manager_whatsapp')->nullable()->after('smtp_from_email');
            $table->string('evolution_api_url')->nullable()->after('sales_manager_whatsapp');
            $table->string('evolution_api_key')->nullable()->after('evolution_api_url');
            $table->string('evolution_instance_name')->nullable()->after('evolution_api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'sales_manager_whatsapp',
                'evolution_api_url',
                'evolution_api_key',
                'evolution_instance_name',
            ]);
        });
    }
};
