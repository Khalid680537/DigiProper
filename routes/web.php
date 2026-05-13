<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\PropertyPhotoController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::resource('properties', PropertyController::class);

    Route::post('properties/{property}/documents', [PropertyDocumentController::class, 'store'])
        ->name('properties.documents.store');
    Route::get('properties/{property}/documents/{document}', [PropertyDocumentController::class, 'show'])
        ->name('properties.documents.show');
    Route::delete('properties/{property}/documents/{document}', [PropertyDocumentController::class, 'destroy'])
        ->name('properties.documents.destroy');

    Route::post('properties/{property}/photos', [PropertyPhotoController::class, 'store'])
        ->name('properties.photos.store');
    Route::get('properties/{property}/photos/{photo}', [PropertyPhotoController::class, 'show'])
        ->scopeBindings()
        ->name('properties.photos.show');
    Route::patch('properties/{property}/photos/{photo}/primary', [PropertyPhotoController::class, 'makePrimary'])
        ->scopeBindings()
        ->name('properties.photos.makePrimary');
    Route::delete('properties/{property}/photos/{photo}', [PropertyPhotoController::class, 'destroy'])
        ->scopeBindings()
        ->name('properties.photos.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
