<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('email', 'customers_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_email_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'email_verified_at']);
        });
    }
};
