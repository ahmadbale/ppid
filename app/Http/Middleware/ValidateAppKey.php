<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;

class ValidateAppKey
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Ambil app_key dari token JWT
            $payload = JWTAuth::parseToken()->getPayload();
            $tokenAppKey = $payload->get('app_key');
            
            // Ambil app_key dari request (query parameter atau header)
            $requestAppKey = $request->query('app_key') ?? $request->header('app-key');
            
            // Jika ada app_key di request, pastikan sama dengan token
            if ($requestAppKey && $requestAppKey !== $tokenAppKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'App key tidak sesuai dengan token'
                ], 403);
            }
            
            // Validasi app_key exists di database
            if (!WebMenuUrlModel::validateAppKey($tokenAppKey)) {
                return response()->json([
                    'success' => false,
                    'message' => "App key '{$tokenAppKey}' tidak valid"
                ], 400);
            }
            
            // Set app_key ke request untuk digunakan di controller
            $request->merge(['validated_app_key' => $tokenAppKey]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau tidak ditemukan'
            ], 401);
        }

        return $next($request);
    }
}