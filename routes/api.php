<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\InstallmentController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\UnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);
    Route::get('/units', [UnitController::class, 'index']);
    Route::post('/leads', [LeadController::class, 'store']);
    Route::post('/installments/calculate', [InstallmentController::class, 'calculate']);
    Route::get('/search', SearchController::class);
});
