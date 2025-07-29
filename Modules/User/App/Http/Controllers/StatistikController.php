<?php

namespace Modules\User\App\Http\Controllers;

use App\Services\JwtTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatistikController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

    private function makeAuthenticatedRequest($endpoint)
    {
        try {
            // Get active token
            $tokenData = $this->jwtTokenService->getActiveToken();
            
            // Make request with token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['token']
            ])->get($this->baseUrl . '/api/' . $endpoint);

            // Check if token expired
            if ($response->status() === 401) {
                // Generate new token and retry
                $tokenData = $this->jwtTokenService->generateSystemToken();
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tokenData['token']
                ])->get($this->baseUrl . '/api/' . $endpoint);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

}