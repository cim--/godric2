<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class CheckApiIpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        $whitelist = config('api_access.ip_whitelist');

        if (
            empty($whitelist) ||
            !IpUtils::checkIp($request->ip(), $whitelist)
        ) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
