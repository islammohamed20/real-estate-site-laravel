<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The unique index conflicts with soft deletes: a soft-deleted row keeps
        // occupying the key, blocking creation of a unit with the same number.
        // Uniqueness is now enforced in the controller among active units only.
        Schema::table('units', function ($table) {
            // The floors FK currently leans on the unique index (leftmost column).
            // Give it a dedicated index first so the unique one can be dropped.
            $table->index('floor_id', 'units_floor_id_index');
            $table->dropUnique('units_floor_id_unit_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('units', function ($table) {
            $table->unique(['floor_id', 'unit_number'], 'units_floor_id_unit_number_unique');
            $table->dropIndex('units_floor_id_index');
        });
    }
};
