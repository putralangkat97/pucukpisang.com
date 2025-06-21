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

Route::get('/audio', [AudioController::class, 'index'])
    ->name('audio.index');
Route::get('/audio/results/{audio:id}', [AudioController::class, 'show'])
    ->name('audio.show');
Route::get('/audio/results/{audio:id}/{type}/download', [AudioController::class, 'download']);
