<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type', 30)->nullable()->index();
            $table->string('location')->nullable();
            $table->boolean('is_successful')->default(false)->index();
            $table->timestamp('logged_in_at')->index();
            $table->timestamp('logged_out_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
