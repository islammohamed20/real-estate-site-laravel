<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('currency_code', 10)->default('USD');
            $table->string('default_language', 10)->default('en');
            $table->decimal('maintenance_percent', 8, 2)->default(0);
            $table->string('smtp_from_name')->nullable();
            $table->string('smtp_from_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
