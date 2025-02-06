<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please log in.'
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'status' => 'error',
                'message' => "Unauthorized. {$role} access required."
            ], 403);
        }

        return $next($request);
    }
}
