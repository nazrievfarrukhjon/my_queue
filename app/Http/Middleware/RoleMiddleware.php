<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
    // TODO::fix redirects
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            return redirect('/admin/login');
        }

        if (! $request->user()->hasRole($role)) {
            abort(403);
        }

        return $next($request);
    }
}
