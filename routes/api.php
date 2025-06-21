<?php

use App\Http\Controllers\Api\DocumentApiController;
use Illuminate\Support\Facades\Route;

Route::post('/documents', [DocumentApiController::class, 'store'])->name('api.document.store');
Route::get('/documents/{document:id}', [DocumentApiController::class, 'status'])->name('api.document.status');
