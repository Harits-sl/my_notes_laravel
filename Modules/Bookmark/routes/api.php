<?php

use Illuminate\Support\Facades\Route;
use Modules\Bookmark\Http\Controllers\BookmarkController;
use Modules\Bookmark\Http\Controllers\CategoryController;
use Modules\Bookmark\Http\Controllers\LinkPreviewController;

Route::get('/preview', [LinkPreviewController::class, 'preview']);
Route::resource('/bookmarks', BookmarkController::class);
Route::resource('/categories', CategoryController::class);
