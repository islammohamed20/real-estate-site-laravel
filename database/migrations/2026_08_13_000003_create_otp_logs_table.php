<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('phone', 30)->nullable()->index();
            $table->string('purpose', 60)->nullable();
            $table->string('status', 30)->default('sent');
            $table->string('channel', 20)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('sent_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_logs');
    }
};
