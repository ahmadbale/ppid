<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Log;

class VerifyApiToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Ambil token dari header Authorization
            if (!$token = $request->bearerToken()) {
                Log::warning('No token provided', [
                    'ip' => $request->ip(),
                    'path' => $request->path()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }

            // Set token dan decode payload
            $payload = JWTAuth::setToken($token)->getPayload();

            // Validasi khusus untuk token sistem
            if ($payload->get('sub') !== 'system_api' || $payload->get('type') !== 'system') {
                Log::warning('Invalid system token', [
                    'subject' => $payload->get('sub'),
                    'type' => $payload->get('type')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token sistem tidak valid'
                ], 401);
            }

        } catch (TokenExpiredException $e) {
            Log::warning('Expired system token', [
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Token sistem telah expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            Log::warning('Invalid system token', [
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Token sistem tidak valid'
            ], 401);
        } catch (JWTException $e) {
            Log::error('System token verification error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi token sistem gagal'
            ], 500);
        }

        return $next($request);
    }
}