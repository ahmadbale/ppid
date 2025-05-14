<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Log;

class VerifyUserToken
{
    public function handle($request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();

            // Verifikasi tipe token
            if (!isset($payload['type']) || $payload['type'] !== 'user') {
                return response()->json(['error' => 'Invalid token type'], 401);
            }

            // Verifikasi exp time sesuai user TTL
            $expiration = $payload['exp'];
            if (time() > $expiration) {
                return response()->json(['error' => 'Token has expired'], 401);
            }

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Exception $e) {
            Log::error('Token verification error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}