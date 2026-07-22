<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;

class CheckApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');

        if (!str_starts_with($header, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $plaintext = substr($header, 7);
        $apiToken = ApiToken::findByPlaintext($plaintext);

        if (!$apiToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $apiToken->update(['last_used_at' => now()]);

        return $next($request);
    }
}
