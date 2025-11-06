<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\VoteController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('logout-all', [AuthController::class, 'logoutAll'])->name('api.auth.logout_all');
    });
});

Route::get('posts', [PostController::class, 'index'])->name('api.posts.index');
Route::get('posts/{slug}', [PostController::class, 'showBySlug'])->name('api.posts.show_by_slug');
Route::get('posts/{post}/comments', [CommentController::class, 'index'])->name('api.comments.index');
Route::get('posts/{post}/votes/summary', [VoteController::class, 'summary'])->name('api.votes.summary');
Route::get('categories', [CategoryController::class, 'index'])->name('api.categories.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('posts', [PostController::class, 'store'])->name('api.posts.store');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('api.posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('api.posts.destroy');

    Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('api.comments.store');
    Route::post('posts/{post}/votes', [VoteController::class, 'store'])->name('api.votes.store');

    Route::middleware('role:admin')->group(function () {
        Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('api.posts.publish');
        Route::post('posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('api.posts.unpublish');

        Route::post('comments/{comment}/moderate', [CommentController::class, 'moderate'])->name('api.comments.moderate');

        Route::post('categories', [CategoryController::class, 'store'])->name('api.categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');
    });
});
