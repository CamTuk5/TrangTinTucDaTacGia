<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Đăng ký providers “chuẩn” (đừng alias middleware ở đây)
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,             // ← THÊM
        App\Providers\PermissionAliasServiceProvider::class,  // alias Spatie (đặt tên riêng trong Provider)
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Không alias middleware Spatie ở đây để tránh đụng với Kernel/Provider khác
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
