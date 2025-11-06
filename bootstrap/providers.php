<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,             // ← THÊM
    App\Providers\PermissionAliasServiceProvider::class,
];
