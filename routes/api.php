<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Site Routes
use App\Http\Controllers\Api\PublicSiteController;

Route::prefix('public')->group(function () {
    Route::get('/vehicles', [PublicSiteController::class, 'getVehicles']);
    Route::get('/vehicles/{id}', [PublicSiteController::class, 'getVehicleDetails']);
    Route::get('/categories', [PublicSiteController::class, 'getCategories']);
    Route::get('/branches', [PublicSiteController::class, 'getBranches']);
});
