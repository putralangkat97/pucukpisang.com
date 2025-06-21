<?php

use App\Http\Controllers\AudioController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('index');
});

Route::get('/document', [DocumentController::class, 'index'])
    ->name('document.index');
Route::get('/document/results/{document:id}', [DocumentController::class, 'show'])
    ->name('document.show');
Route::get('/document/results/{document:id}/{type}/download', [DocumentController::class, 'download']);

Route::get('/audio', [AudioController::class, 'index']);
Route::post('/audio/process', [AudioController::class, 'process']);

// Route::prefix('documents')->group(function () {
//     Route::get('/create', [DocumentController::class, 'index'])->name('document.form');
//     Route::post('/create', [DocumentController::class, 'process'])->name('document.process');
// });

// Route::prefix('audio')->group(function () {
//     Route::get('/create', [AudioController::class, 'index'])->name('audio.form');
//     Route::post('/create', [AudioController::class, 'process'])->name('audio.process');
// });
