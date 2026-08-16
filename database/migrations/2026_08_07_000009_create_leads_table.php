<?php

declare(strict_types=1);

use App\Enums\LeadStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('phone', 50)->index();
            $table->string('email')->nullable()->index();
            $table->string('address')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('budget', 14, 2)->nullable();
            $table->string('stage', 50)->default(LeadStage::New->value)->index();
            $table->string('source', 100)->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable()->index();
            $table->timestamp('follow_up_at')->nullable()->index();
            $table->timestamp('converted_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
