<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table): void {
            $table->string('media_name')->nullable()->after('message_type');
            $table->string('media_path')->nullable()->after('media_name');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table): void {
            $table->dropColumn(['media_name', 'media_path']);
        });
    }
};
