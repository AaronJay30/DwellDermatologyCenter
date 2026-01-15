<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user role matches the required role
        if ($user->role !== $role) {
            abort(403, 'Unauthorized access. Your role (' . ($user->role ?? 'none') . ') does not have permission to access this resource. Required role: ' . $role);
        }

        return $next($request);
    }
}
