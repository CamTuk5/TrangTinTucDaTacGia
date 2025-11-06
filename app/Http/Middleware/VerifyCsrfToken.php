<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Với API dùng Bearer token, ta loại trừ toàn bộ đường dẫn api/*
     * khỏi kiểm tra CSRF để tránh 419 khi test bằng Postman/mobile.
     */
    protected $except = [
        'api/*',
    ];
}
