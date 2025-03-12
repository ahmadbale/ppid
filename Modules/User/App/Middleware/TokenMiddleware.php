<?php

namespace Modules\User\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Services\ApiService;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class TokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil token
        $token = $request->cookie('api_token') ?? session('api_token');
        
        if ($token) {
            try {
                
                $payload = JWTAuth::setToken($token)->getPayload();
                $expTime = $payload->get('exp');
                
                if (time() > ($expTime - 300)) {
                    ApiService::refreshToken();
                }
            } catch (TokenExpiredException $e) {
                // Token sudah expired, coba refresh
                ApiService::refreshToken();
            } catch (\Exception $e) {
                // Error lain, log saja
                Log::warning('Token check error', ['error' => $e->getMessage()]);
            }
        }
        
        return $next($request);
    }
}