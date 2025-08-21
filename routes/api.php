<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SuggestionController;

Route::prefix('v1')->group(function () {
    // Health (optional)
    Route::get('/health', fn () => response()->json(['ok' => true]));

    // ---- Suggestions ----
    Route::get('/suggestions',        [SuggestionController::class, 'index']);
    Route::get('/suggestions/{id}',   [SuggestionController::class, 'show'])->whereNumber('id');
    Route::post('/suggestions',       [SuggestionController::class, 'store']);

    // ---- Attachments (DELETE marks as removed; PATCH changes status) ----
    Route::delete('/attachments/{id}', [SuggestionController::class, 'deactivateAttachment'])
        ->whereNumber('id');
    Route::patch('/attachments/{id}/status', [SuggestionController::class, 'setAttachmentStatus'])
        ->whereNumber('id');

    // ---- Categories / Departments (if you use them) ----
    Route::get('/categories',  [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/departments',  [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
});
