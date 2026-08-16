<?php

declare(strict_types=1);

use App\Enums\UnitStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->string('unit_number');
            $table->string('unit_type')->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->decimal('area', 12, 2)->default(0);
            $table->decimal('garden_area', 12, 2)->default(0);
            $table->decimal('roof_area', 12, 2)->default(0);
            $table->decimal('balcony_area', 12, 2)->default(0);
            $table->decimal('price_per_meter', 14, 2)->default(0);
            $table->decimal('garden_price', 14, 2)->default(0);
            $table->decimal('roof_price', 14, 2)->default(0);
            $table->decimal('current_price', 14, 2)->default(0)->index();
            $table->string('status', 50)->default(UnitStatus::Available->value)->index();
            $table->boolean('featured')->default(false)->index();
            $table->boolean('hidden_from_website')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['floor_id', 'unit_number']);
            $table->index(['project_id', 'phase_id', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
