<?php

namespace AuroraWebSoftware\ASetting\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BearerTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        if (! $this->validateToken($token)) {
            return response()->json([
                'message' => 'Invalid Token',
            ], 401);
        }

        return $next($request);
    }

    protected function validateToken($token)
    {
        $validTokens = config('asetting.api_token');

        return in_array($token, $validTokens);
    }
}
