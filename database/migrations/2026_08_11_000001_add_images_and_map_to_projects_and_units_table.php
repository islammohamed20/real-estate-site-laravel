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
        Schema::table('projects', function (Blueprint $table) {
            $table->json('images')->nullable()->after('description');
            $table->decimal('map_lat', 10, 7)->nullable()->after('country');
            $table->decimal('map_lng', 10, 7)->nullable()->after('map_lat');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->json('images')->nullable()->after('unit_type');
            $table->decimal('map_lat', 10, 7)->nullable()->after('unit_number');
            $table->decimal('map_lng', 10, 7)->nullable()->after('map_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['images', 'map_lat', 'map_lng']);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['images', 'map_lat', 'map_lng']);
        });
    }
};
