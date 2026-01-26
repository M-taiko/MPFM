<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartmentAccess
{
    /**
     * Handle an incoming request.
     *
     * Middleware to ensure users only access resources in their department.
     * Accepts department codes as parameters.
     *
     * Usage: Route::middleware('department:MPFM,WIRELINE')->group(...)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $departments  Comma-separated list of allowed department codes
     */
    public function handle(Request $request, Closure $next, ...$departments): Response
    {
        $user = $request->user();

        // If no departments specified, allow everyone (endpoint accessible to all)
        if (empty($departments)) {
            return $next($request);
        }

        // Admin can access any department
        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        // Surveillance Supervisor can access all departments (read-only)
        if ($user && $user->hasRole('surveillance_supervisor')) {
            return $next($request);
        }

        // Department Manager can only access their own department
        if ($user && $user->hasRole('department_manager')) {
            if ($user->department && in_array($user->department->code, $departments)) {
                return $next($request);
            }

            abort(403, 'You do not have access to this department.');
        }

        // For other roles, grant access to specified departments
        // (this handles scenarios where multiple users across departments might need access)
        if ($user && $user->department && in_array($user->department->code, $departments)) {
            return $next($request);
        }

        abort(403, 'You do not have access to this department.');
    }
}
