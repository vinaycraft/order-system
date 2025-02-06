<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();
        
        // Check if user has any of the required roles
        $userRoles = explode(',', $roles[0]); // Convert comma-separated roles to array
        if (!in_array($user->role, $userRoles)) {
            return response()->json(['error' => 'Unauthorized. Required roles: ' . implode(', ', $userRoles)], 403);
        }

        return $next($request);
    }
}
