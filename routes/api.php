<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\VoteController;

// Public
Route::get('/posts', [PostController::class,'index']);
Route::get('/posts/{slug}', [PostController::class,'showBySlug']);
Route::get('/categories', [CategoryController::class,'index']);
Route::get('/posts/{post}/comments', [CommentController::class,'index']);
Route::get('/posts/{post}/votes/summary', [VoteController::class,'summary']);

// Auth
Route::post('/auth/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/auth/me', [AuthController::class,'me']);
    Route::post('/auth/logout', [AuthController::class,'logout']);

    // Posts
    Route::post('/posts', [PostController::class,'store']);
    Route::put('/posts/{post}', [PostController::class,'update']);
    Route::delete('/posts/{post}', [PostController::class,'destroy']);

    // Publish (admin)
    Route::post('/posts/{post}/publish', [PostController::class,'publish'])->middleware('role:admin');
    Route::post('/posts/{post}/unpublish', [PostController::class,'unpublish'])->middleware('role:admin');

    // Categories (admin)
    Route::post('/categories', [CategoryController::class,'store'])->middleware('role:admin');
    Route::put('/categories/{category}', [CategoryController::class,'update'])->middleware('role:admin');
    Route::delete('/categories/{category}', [CategoryController::class,'destroy'])->middleware('role:admin');

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class,'store']);
    Route::post('/comments/{comment}/moderate', [CommentController::class,'moderate'])->middleware('role:admin');

    // Votes
    Route::post('/posts/{post}/votes', [VoteController::class,'store']);
});
