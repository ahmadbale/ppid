<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ApiService
{
    protected static $baseUrl = 'http://ppid-polinema.test/api';
    protected static $tokenRefreshAttempted = false;

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
            // Ambil token dari berbagai sumber (prioritas)
            $token = self::getToken();
            
            // Log untuk debugging
            Log::info('API Request', [
                'url' => $url,
                'method' => $method,
                'token_exists' => !empty($token)
            ]);

            // Jika tidak ada token, kembalikan null
            if (!$token) {
                Log::warning('Token tidak ditemukan', ['url' => $url]);
                return null;
            }

            // Lakukan request dengan token
            $response = Http::withToken($token)->$method($url, $data);

            // Periksa response
            if ($response->successful()) {
                return $response->json();
            }

            // Handle unauthorized (token expired/invalid)
            if ($response->status() == 401 && !self::$tokenRefreshAttempted) {
                Log::info('Token expired, mencoba refresh token');
                
                // Set flag agar tidak terjadi infinite loop
                self::$tokenRefreshAttempted = true;
                
                // Coba refresh token
                if (self::refreshToken()) {
                    // Ambil token baru dan coba lagi
                    $token = self::getToken();
                    
                    if ($token) {
                        $response = Http::withToken($token)->$method($url, $data);
                        
                        if ($response->successful()) {
                            // Reset flag setelah berhasil
                            self::$tokenRefreshAttempted = false;
                            return $response->json();
                        }
                    }
                }
                
                // Jika refresh gagal atau request dengan token baru gagal
                self::$tokenRefreshAttempted = false;
                Log::warning('Refresh token gagal, user akan logout');
                
                // Logout user
                Auth::logout();
                Session::forget('api_token');
                Cookie::queue(Cookie::forget('api_token'));
                
                return null;
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
     * Mendapatkan token dari berbagai sumber (cookie atau session)
     */
    private static function getToken()
    {
        // // Coba ambil dari cookie dulu (lebih aman)
        // $token = request()->cookie('api_token');
        
        // Jika tidak ada di cookie, coba ambil dari session
        // if (empty($token)) 
            $token = Session::get('api_token');
        
        
        return $token;
    }

    /**
     * Refresh token
     */
    public static function refreshToken()
    {
        try {
            // Ambil token yang ada
            $token = self::getToken();
            
            if (!$token) {
                return false;
            }
            
            $response = Http::withToken($token)
                ->post(self::$baseUrl . '/auth/refresh');

            if ($response->successful()) {
                $data = $response->json();
                $newToken = $data['token'] ?? ($data['data']['token'] ?? null);
                
                if ($newToken) {
                    // Simpan token baru di session
                    Session::put('api_token', $newToken);
                    
                    // Perbarui cookie
                    Cookie::queue('api_token', $newToken, 60, null, null, true, true);
                    
                    Log::info('Token berhasil diperbarui');
                    return true;
                }
            }
            
            Log::warning('Refresh token gagal', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('Refresh Token Error', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }
}