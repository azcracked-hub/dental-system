<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array(Auth::user()->role, $roles, true)) {
            $dashboardRoute = match (Auth::user()->role) {
                'admin' => 'admin.dashboard',
                'staff' => 'staff.dashboard',
                'patient' => 'patient.dashboard',
                default => null,
            };

            if ($dashboardRoute) {
                return redirect()->route($dashboardRoute)
                    ->with('error', 'You do not have permission to access that page.');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
