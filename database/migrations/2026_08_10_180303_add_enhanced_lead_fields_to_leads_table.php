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
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete()->after('customer_id');
            $table->string('whatsapp', 50)->nullable()->index()->after('phone');
            $table->string('priority', 20)->nullable()->default('normal')->index()->after('stage');
            $table->string('campaign', 100)->nullable()->index()->after('source');
            $table->string('unit_type', 100)->nullable()->after('occupation');
            $table->unsignedTinyInteger('bedrooms')->nullable()->after('unit_type');
            $table->decimal('required_area', 10, 2)->nullable()->after('bedrooms');
            $table->string('preferred_payment_plan', 100)->nullable()->after('required_area');
            $table->string('status', 30)->nullable()->default('active')->index()->after('preferred_payment_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'lead_source_id',
                'whatsapp',
                'priority',
                'campaign',
                'unit_type',
                'bedrooms',
                'required_area',
                'preferred_payment_plan',
                'status',
            ]);
        });
    }
};
