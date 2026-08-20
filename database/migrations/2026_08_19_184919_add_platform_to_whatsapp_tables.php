<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add platform column to conversations
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->string('platform', 20)->default('whatsapp')->after('customer_phone')->comment('whatsapp, facebook, instagram');
            $table->string('platform_user_id')->nullable()->after('platform')->comment('Facebook PSID or Instagram IGID');
            $table->string('platform_page_id')->nullable()->after('platform_user_id')->comment('Facebook Page ID');
        });

        // Add platform column to messages
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->string('platform_message_id')->nullable()->after('faalwa_message_id')->comment('Facebook message mid');
        });

        // Facebook settings table
        Schema::create('facebook_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->nullable()->comment('Facebook Page ID');
            $table->text('access_token')->nullable()->comment('Long-lived Page Access Token');
            $table->string('verify_token', 64)->nullable()->comment('Webhook verify token');
            $table->string('app_secret', 64)->nullable()->comment('Facebook App Secret');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_settings');
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn('platform_message_id');
        });
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropColumn(['platform', 'platform_user_id', 'platform_page_id']);
        });
    }
};
