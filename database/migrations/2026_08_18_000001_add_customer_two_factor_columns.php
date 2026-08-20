<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->boolean('whatsapp_two_factor_enabled')->default(false)->after('otp_attempts');
            $table->boolean('authenticator_two_factor_enabled')->default(false)->after('whatsapp_two_factor_enabled');
            $table->text('two_factor_secret')->nullable()->after('authenticator_two_factor_enabled');
            $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn([
                'whatsapp_two_factor_enabled',
                'authenticator_two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
            ]);
        });
    }
};
