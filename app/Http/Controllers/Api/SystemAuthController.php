<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
use Illuminate\Http\Request;

class SystemAuthController extends Controller
{
    protected $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    public function getToken()
    {
        $tokenData = $this->jwtTokenService->getActiveToken();
        return response()->json($tokenData);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->refresh_token;

        if (!$refreshToken) {
            return response()->json(['error' => 'Refresh token tidak ditemukan'], 400);
        }

        $newToken = $this->jwtTokenService->refreshToken($refreshToken);

        if (!$newToken) {
            return response()->json(['error' => 'Refresh token tidak valid'], 401);
        }

        return response()->json($newToken);
    }
}