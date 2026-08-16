<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'documentable_type' => Customer::class,
            'documentable_id' => Customer::factory(),
            'uploaded_by' => User::factory(),
            'name' => $this->faker->word().'.pdf',
            'file_path' => 'documents/'.$this->faker->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1000, 100000),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
