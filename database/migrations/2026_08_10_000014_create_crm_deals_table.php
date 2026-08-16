<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->foreignId('pipeline_id')->constrained('crm_pipelines');
            $table->foreignId('stage_id')->constrained('crm_stages');
            $table->foreignId('organization_id')->nullable()->constrained('crm_organizations')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('value', 16, 2)->default(0);
            $table->string('currency_code', 10)->default('USD');
            $table->date('expected_close_date')->nullable();
            $table->string('priority', 20)->default('medium');
            $table->string('source', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('stage_changed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 30)->default('open')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pipeline_id', 'stage_id']);
            $table->index(['assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};
