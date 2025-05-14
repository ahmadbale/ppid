<?php

namespace App\Http\Controllers\Api;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use App\Services\JwtTokenService;
use Illuminate\Http\Request;

class SystemAuthController extends BaseApiController
{
    protected $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    public function getToken()
    {
        return $this->execute(
            function() {
                $tokenData = $this->jwtTokenService->getActiveToken();
                return $tokenData;
            },
            'token',
            self::ACTION_GET
        );
    }

    public function refreshToken(Request $request)
    {
        return $this->executeWithValidation(
            function() use ($request) {
                // Validasi input
                return validator($request->all(), [
                    'refresh_token' => 'required|string'
                ]);
            },
            function() use ($request) {
                $refreshToken = $request->refresh_token;
                $newToken = $this->jwtTokenService->refreshToken($refreshToken);
                
                if (!$newToken) {
                    return $this->errorResponse(self::AUTH_TOKEN_INVALID, null, 401);
                }
                
                return $newToken;
            },
            self::ACTION_UPDATE
        );
    }
}