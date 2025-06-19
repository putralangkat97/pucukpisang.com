<?php

use App\Http\Controllers\AudioController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::prefix('documents')->group(function () {
    Route::get('/create', [DocumentController::class, 'index'])->name('document.form');
    Route::post('/create', [DocumentController::class, 'process'])->name('document.process');
});

Route::prefix('audio')->group(function () {
    Route::get('/create', [AudioController::class, 'index'])->name('audio.form');
    Route::post('/create', [AudioController::class, 'process'])->name('audio.process');
});
