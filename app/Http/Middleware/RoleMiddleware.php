<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Dùng: ->middleware('role:admin') hoặc ->middleware('role:admin,editor')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Chưa đăng nhập
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Nếu dùng Sanctum abilities và token có '*' thì cho qua luôn
        if (method_exists($user, 'tokenCan') && $user->tokenCan('*')) {
            return $next($request);
        }

        // Chuẩn hoá role
        $userRole = strtolower((string) ($user->role ?? ''));
        $roles    = array_map('strtolower', $roles ?? []);

        // Nếu không truyền tham số, mặc định yêu cầu 'admin'
        if (empty($roles)) {
            $roles = ['admin'];
        }

        if (!in_array($userRole, $roles, true)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
