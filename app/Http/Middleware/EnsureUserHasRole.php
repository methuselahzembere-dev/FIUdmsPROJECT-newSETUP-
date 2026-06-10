<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. If the user isn't logged in at all, redirect them safely to the standalone login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // 2. Loop through each allowed role passed from your web.php route group definition
        foreach ($roles as $role) {
            
            // Check via custom hasRole method if it exists on the User model
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return $next($request);
            }

            // Fallback: Check direct object property strings if your model uses a simple string field
            if (isset($user->role) && $user->role === $role) {
                return $next($request);
            }
        }

        // 🌟 BREAKS REDIRECT LOOP: Aborting with an explicit 403 stops the infinite bounce cycle instantly!
        abort(403, 'Unauthorized. This dashboard area is strictly reserved for authorized FIU Administrators and Reviewers.');
    }
}