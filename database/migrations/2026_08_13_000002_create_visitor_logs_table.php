<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type', 20)->nullable()->index();
            $table->string('country', 100)->nullable()->index();
            $table->string('city', 100)->nullable();
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamp('visited_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
