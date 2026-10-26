<?php

namespace App\Http\Middleware;

use App\Utilities\UserAccess;
use Closure;
use Illuminate\Http\Request;

class UserPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $key = null, $menu = null)
    {
        if (UserAccess::can($key, $menu)) {
            return $next($request);
        }

        if (UserAccess::parent($key)) {
            return $next($request);
        }

        if (auth()->user()->is_superadmin) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Akses anda terbatas!');
    }
}
