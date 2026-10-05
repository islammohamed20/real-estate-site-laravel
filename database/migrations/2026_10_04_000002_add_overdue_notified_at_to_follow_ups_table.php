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
            $table->timestamp('overdue_notified_at')->nullable()->after('reminded_at');
        });
    }

    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table): void {
            $table->dropColumn('overdue_notified_at');
        });
    }
};
