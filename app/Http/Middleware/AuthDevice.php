<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AuthDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $staticToken = Cache::get($request->device_uuid);

        if (!$staticToken) {
            $device = Device::where('device_uuid', $request->device_uuid)->first();
            $staticToken = $device?->token;
            Cache::forever('device_uuid', $staticToken);
        }

        $token = $request->header('Authorization');

        if ($token !== 'Bearer ' . $staticToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
