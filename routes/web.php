<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'status' => 'ok',
        'docs' => [
            'login' => '/api/auth/login',
            'posts' => '/api/posts',
            'categories' => '/api/categories',
        ],
    ]);
});
