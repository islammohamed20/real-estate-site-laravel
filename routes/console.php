<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:health', function (): int {
    $this->info('Application bootstrap is healthy.');

    return self::SUCCESS;
})->purpose('Check application bootstrap health.');
