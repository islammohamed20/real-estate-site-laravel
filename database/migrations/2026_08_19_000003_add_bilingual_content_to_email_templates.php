<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table): void {
            $table->string('subject_ar')->nullable()->after('subject');
            $table->longText('body_ar')->nullable()->after('body');
            $table->string('subject_en')->nullable()->after('subject_ar');
            $table->longText('body_en')->nullable()->after('body_ar');
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table): void {
            $table->dropColumn(['subject_ar', 'body_ar', 'subject_en', 'body_en']);
        });
    }
};
