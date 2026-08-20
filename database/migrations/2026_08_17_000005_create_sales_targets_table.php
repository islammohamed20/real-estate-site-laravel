<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table): void {
            $table->id();
            // A target belongs to exactly one entity: a user OR a team.
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('sales_team_id')->nullable()->constrained('sales_teams')->cascadeOnDelete();
            $table->string('period', 7); // YYYY-MM
            $table->unsignedInteger('leads_target')->default(0);
            $table->unsignedInteger('offers_target')->default(0);
            $table->unsignedInteger('reservations_target')->default(0);
            $table->unsignedInteger('deals_target')->default(0);
            $table->decimal('deal_value_target', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'sales_team_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
