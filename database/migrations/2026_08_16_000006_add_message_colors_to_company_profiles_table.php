<?php

declare(strict_types=1);

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
        Schema::table('company_profiles', function (Blueprint $table): void {
            $table->string('evolution_outgoing_color', 9)->nullable()->after('evolution_dashboard_url');
            $table->string('evolution_incoming_color', 9)->nullable()->after('evolution_outgoing_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table): void {
            $table->dropColumn(['evolution_outgoing_color', 'evolution_incoming_color']);
        });
    }
};
