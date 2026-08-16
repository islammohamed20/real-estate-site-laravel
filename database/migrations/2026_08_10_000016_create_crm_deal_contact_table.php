<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deal_contact', function (Blueprint $table): void {
            $table->foreignId('deal_id')->constrained('crm_deals')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->primary(['deal_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deal_contact');
    }
};
