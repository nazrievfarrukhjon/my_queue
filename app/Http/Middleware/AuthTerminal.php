<?php

namespace App\Http\Middleware;

use App\Models\Terminal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AuthTerminal
{
    public function handle(Request $request, Closure $next): Response
    {
        $staticToken = Cache::get($request->terminal_uuid);

        if (!$staticToken) {
            $terminal = Terminal::where('terminal_uuid', $request->terminal_uuid)->first();
            $staticToken = $terminal?->token;
            Cache::forever('terminal_uuid', $staticToken);
        }

        $token = $request->header('Authorization');

        if ($token !== 'Bearer ' . $staticToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
