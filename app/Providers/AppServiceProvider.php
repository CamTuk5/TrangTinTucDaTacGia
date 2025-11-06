<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nơi đăng ký service container bindings
    }

    public function boot(): void
    {
        // Nơi chạy khi app khởi động
    }
}
