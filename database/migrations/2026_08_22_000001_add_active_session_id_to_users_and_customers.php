<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('active_session_id', 255)->nullable()->index()->after('remember_token');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->string('active_session_id', 255)->nullable()->index()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn('active_session_id');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('active_session_id');
        });
    }
};
