<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('otp_code_hash')->nullable()->after('email_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code_hash');
            $table->timestamp('otp_sent_at')->nullable()->after('otp_expires_at');
            $table->unsignedTinyInteger('otp_attempts')->default(0)->after('otp_sent_at');
        });

        // Accounts that existed before email verification was introduced keep working.
        DB::table('customers')
            ->whereNotNull('password')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['otp_code_hash', 'otp_expires_at', 'otp_sent_at', 'otp_attempts']);
        });
    }
};
