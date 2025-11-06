<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (method_exists($user, 'tokenCan') && $user->tokenCan('*')) {
            return $next($request);
        }

        $userRole = strtolower((string)($user->role ?? ''));
        $roles = array_map('strtolower', $roles ?: ['admin']);

        if (!in_array($userRole, $roles, true)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
