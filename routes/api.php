<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\PropertyDocumentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');

        Route::middleware('throttle:api')->group(function () {
            Route::apiResource('properties', PropertyController::class);

            Route::get('properties/{property}/documents', [PropertyDocumentController::class, 'index'])
                ->name('properties.documents.index');
            Route::get('properties/{property}/documents/{document}', [PropertyDocumentController::class, 'show'])
                ->name('properties.documents.show');
            Route::delete('properties/{property}/documents/{document}', [PropertyDocumentController::class, 'destroy'])
                ->name('properties.documents.destroy');

            Route::post('properties/{property}/documents', [PropertyDocumentController::class, 'store'])
                ->middleware('throttle:documents')
                ->name('properties.documents.store');
        });
    });

    // Signed download URL (embedded in PropertyDocumentResource). The signature
    // grants authorization for a short window; no auth:sanctum required here so
    // the URL can be used by clients without re-attaching the bearer token.
    Route::get('properties/{property}/documents/{document}/download', [PropertyDocumentController::class, 'download'])
        ->middleware('signed')
        ->name('properties.documents.download');
});
