<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('follow_ups', function (Blueprint $table): void {
            $table->foreignId('deal_id')->nullable()->constrained('crm_deals')->nullOnDelete()->after('customer_id');
            $table->string('type', 50)->default('phone_call')->after('channel');
            $table->string('priority', 20)->default('normal')->after('type');
            $table->boolean('reminder')->default(false)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table): void {
            $table->dropColumn(['deal_id', 'type', 'priority', 'reminder']);
        });
    }
};
