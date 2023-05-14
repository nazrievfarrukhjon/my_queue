<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::guest()) {
            return redirect()->back(ResponseAlias::HTTP_NOT_FOUND);
        }

        if (! $request->user()->can($permission)) {
            abort(403, "Permission denied!");
        }

        return $next($request);
    }
}
