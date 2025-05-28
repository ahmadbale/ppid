<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;

class PengumumanController extends Controller {
     protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

    private function makeAuthenticatedRequest($endpoint)
    {
        try {
            $response = Http::get($this->baseUrl . '/api/' . $endpoint);
            return $response;
        } catch (\Exception $e) {
            Log::error('API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
public function index(Request $request){
       
    }


}

