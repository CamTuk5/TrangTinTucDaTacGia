<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\Registrar;

class PermissionAliasServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(Registrar $router): void
    {
        $router->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
}
