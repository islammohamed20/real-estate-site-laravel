<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (EmailTemplate::defaultDefinitions() as $key => $definition) {
            EmailTemplate::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $definition['name'],
                    'subject' => $definition['subject_en'],
                    'body' => $definition['body_en'],
                    'subject_en' => $definition['subject_en'],
                    'body_en' => $definition['body_en'],
                    'subject_ar' => $definition['subject_ar'],
                    'body_ar' => $definition['body_ar'],
                    'is_active' => true,
                ],
            );
        }
    }
}
