<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('deleted_at');
        });

        Schema::table('buildings', function (Blueprint $table): void {
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('deleted_at');
        });

        Schema::table('units', function (Blueprint $table): void {
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('deleted_by');
        });

        Schema::table('buildings', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('deleted_by');
        });

        Schema::table('units', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('deleted_by');
        });
    }
};
