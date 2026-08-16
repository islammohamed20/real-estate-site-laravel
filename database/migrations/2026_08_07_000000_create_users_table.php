<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 50)->nullable()->index();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('force_logout_at')->nullable()->index();
            $table->boolean('two_factor_enabled')->default(false)->index();
            $table->text('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();
            $table->timestamp('last_login_at')->nullable()->index();
            $table->string('last_login_ip', 45)->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
