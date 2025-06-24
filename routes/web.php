<?php

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
