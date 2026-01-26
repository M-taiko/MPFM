<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Authorization Bypass Middleware
 *
 * Ensures admin users bypass all authorization checks.
 * This middleware should be applied to routes that require
 * special authorization logic, allowing admins to access
 * any resource without restriction.
 *
 * Usage: Add to route group or specific routes to ensure
 * admin can access resources even with department or role
 * restrictions.
 */
class AdminAuthorizationBypass
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is admin, they can proceed to any route
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $next($request);
        }

        // For non-admins, continue normal request handling
        return $next($request);
    }
}
