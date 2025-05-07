<?php

namespace App\Services;

use App\Models\ApiToken;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class JwtTokenService
{
    // Konstanta untuk masa berlaku token
    private const TOKEN_LIFETIME_DAYS = 2;

    /**
     * Periksa apakah token mendekati masa expired
     * 
     * @return bool
     */
    public function isTokenNearExpiration()
    {
        try {
            $apiToken = ApiToken::find(1);

            if (!$apiToken) {
                // Jika tidak ada token, kembalikan true agar token dibuat
                return true;
            }

            $expiresAt = Carbon::parse($apiToken->expires_at);
            $now = Carbon::now();

            // Periksa apakah token akan expire dalam 1-2 hari ke depan
            $isNearExpiration = $now->diffInDays($expiresAt) <= 1;

            Log::info('Token Expiration Check', [
                'current_time' => $now,
                'expires_at' => $expiresAt,
                'days_until_expiry' => $now->diffInDays($expiresAt),
                'near_expiration' => $isNearExpiration
            ]);

            return $isNearExpiration;
        } catch (\Exception $e) {
            Log::error('Error checking token expiration', [
                'error' => $e->getMessage()
            ]);

            // Jika ada kesalahan, anggap token perlu diperbarui
            return true;
        }
    }

    public function generateSystemToken()
    {
        try {
            // Create claims array properly
            $customClaims = [
                'sub' => 'system_api',
                'type' => 'system',
                'jti' => Str::uuid()->toString(),
            ];

            try {
                // Use JWTFactory to create the payload with custom claims
                $payload = JWTFactory::customClaims($customClaims)->make();
                
                // Encode token
                $token = JWTAuth::encode($payload)->get();
            } catch (\Exception $tokenException) {
                Log::error('Token Generation Detailed Error', [
                    'message' => $tokenException->getMessage(),
                    'trace' => $tokenException->getTraceAsString(),
                    'claims' => $customClaims
                ]);
                throw $tokenException;
            }
            
            $refreshToken = $this->generateRefreshToken();

            // Simpan token ke database
            $apiToken = ApiToken::updateOrCreate(
                ['id' => 1],
                [
                    'token' => $token,
                    'refresh_token' => $refreshToken,
                    'expires_at' => Carbon::now()->addDays(self::TOKEN_LIFETIME_DAYS),
                ]
            );

            Log::info('System token generated successfully', [
                'token_id' => $apiToken->id,
                'expires_at' => $apiToken->expires_at
            ]);

            return [
                'token' => $token,
                'refresh_token' => $refreshToken,
                'expires_at' => $apiToken->expires_at,
            ];
        } catch (\Exception $e) {
            Log::error('Complete system token generation failure', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function generateRefreshToken()
    {
        return hash('sha256', Str::uuid() . microtime(true));
    }

    public function refreshToken($refreshToken)
    {
        try {
            $apiToken = ApiToken::where('refresh_token', $refreshToken)->first();

            if (!$apiToken) {
                Log::warning('Invalid refresh token attempt', [
                    'refresh_token' => $refreshToken
                ]);
                return null;
            }

            // Generate token baru
            return $this->generateSystemToken();
        } catch (\Exception $e) {
            Log::error('Token refresh failed', [
                'error' => $e->getMessage(),
                'refresh_token' => $refreshToken
            ]);

            return null;
        }
    }

    public function getActiveToken()
    {
        try {
            $apiToken = ApiToken::find(1);

            // Jika tidak ada token atau akan expire
            if (!$apiToken || $this->isTokenNearExpiration()) {
                Log::info('Generating new token', [
                    'reason' => !$apiToken ? 'No existing token' : 'Token near expiration'
                ]);
                return $this->generateSystemToken();
            }

            return [
                'token' => $apiToken->token,
                'refresh_token' => $apiToken->refresh_token,
                'expires_at' => $apiToken->expires_at,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting active token', [
                'error' => $e->getMessage()
            ]);

            // Fallback to generate new token
            return $this->generateSystemToken();
        }
    }
}