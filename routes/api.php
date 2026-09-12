<?php

use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NoteSearchController;
use App\Http\Controllers\Api\NoteSummaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Notes Management API with AI Semantic Search & Summarization
|
*/

Route::middleware('throttle:60,1')->group(function () {
    // Semantic Search API (Placed before resource to prevent route collision)
    Route::get('/notes/search', [NoteSearchController::class, 'search'])->name('notes.search');

    // AI Summary API Endpoint
    Route::post('/notes/{id}/summary', [NoteSummaryController::class, 'summary'])->name('notes.summary');

    // Notes CRUD APIs (Define named route aliases including 'notes')
    Route::get('/notes', [NoteController::class, 'index'])->name('notes');
    Route::apiResource('notes', NoteController::class)->except(['index']);
});
