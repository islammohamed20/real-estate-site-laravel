<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\Document;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->user = User::factory()->create(['is_active' => true]);
        $this->user->assignRole('Administrator');

        Storage::fake('public');
    }

    public function test_documents_index_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard.crm.documents.index'))
            ->assertOk()
            ->assertViewIs('crm.documents.index');
    }

    public function test_document_can_be_uploaded(): void
    {
        $customer = Customer::factory()->create();
        $file = UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf');

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.documents.store'), [
                'documentable_type' => 'App\\Models\\Customer',
                'documentable_id' => $customer->id,
                'file' => $file,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'documentable_type' => 'App\\Models\\Customer',
            'documentable_id' => $customer->id,
            'uploaded_by' => $this->user->id,
        ]);

        Storage::disk('public')->assertExists('documents/'.$file->hashName());
    }

    public function test_document_can_be_deleted(): void
    {
        $file = UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf');
        $path = $file->store('documents', 'public');

        $document = Document::factory()->create([
            'file_path' => $path,
            'mime_type' => 'application/pdf',
            'size' => $file->getSize(),
        ]);

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.documents.destroy', $document))
            ->assertRedirect();

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }
}
