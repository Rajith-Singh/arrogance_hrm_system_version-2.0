<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

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
    public function handle($request, Closure $next, $role)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // If the user is not authenticated, redirect to the login page
            return redirect()->route('login');
        }

        // Check if the authenticated user's usertype matches the required role
        if (Auth::user()->usertype !== $role) {
            // If the user's role does not match, redirect to the home page with an error message
            return redirect('/home')->with('error', 'You do not have access to this page.');
        }

        // If the usertype matches, allow the request to proceed
        return $next($request);
    }
}
