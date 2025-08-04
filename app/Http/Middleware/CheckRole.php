<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. بررسی آیا کاربر لاگین کرده است
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated. Please login first.'
            ], 401);
        }

        // 2. بررسی آیا کاربر دارای یکی از نقش‌های مورد نیاز است
        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Unauthorized. You don\'t have permission to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
