<?php

use App\Http\Controllers\LinkPreviewController;
use App\Http\Controllers\API\NoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/preview', [LinkPreviewController::class, 'preview']);
Route::resource('/notes', NoteController::class);
