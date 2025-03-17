<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiService
{
    // Base URL API backend
    protected static $baseUrl = 'http://ppid-polinema.test/api';

    /**
     * Melakukan GET request ke API
     */
    public static function get($endpoint, $params = [])
    {
        return self::request('get', self::$baseUrl . $endpoint, $params);
    }

    /**
     * Melakukan POST request ke API
     */
    public static function post($endpoint, $data = [])
    {
        return self::request('post', self::$baseUrl . $endpoint, $data);
    }

    /**
     * Request umum dengan token
     */
    private static function request($method, $url, $data = [])
    {
        try {
            // Ambil token dari session
            $token = Session::get('api_token');

            // Jika tidak ada token, kembalikan null
            if (!$token) {
                return null;
            }

            // Lakukan request dengan token
            $response = Http::withToken($token)->$method($url, $data);

            // Periksa response
            if ($response->successful()) {
                return $response->json();
            }

            // Handle unauthorized (token expired/invalid)
            if ($response->status() == 401) {
                // Logout user
                Auth::logout();
                Session::flush();
                
                // Redirect ke login
                return redirect()->route('login-ppid')
                    ->withErrors(['session' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            // Log error untuk debugging
            Log::error('API Request Error', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('API Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Refresh token
     */
    public static function refreshToken()
    {
        try {
            $response = Http::withToken(Session::get('api_token'))
                ->post(self::$baseUrl . '/auth/refresh');

            if ($response->successful()) {
                $data = $response->json();
                Session::put('api_token', $data['token']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}