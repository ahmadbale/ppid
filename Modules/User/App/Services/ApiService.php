<?php

namespace Modules\User\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiService
{
    protected static $baseUrl = 'http://ppid-polinema.test/api';
    protected static $tokenRefreshAttempted = false;

    /**
     * Melakukan GET request ke API dengan token
     */
    public static function get($endpoint, $params = [])
    {
        return self::request('get', self::$baseUrl . $endpoint, $params);
    }

    /**
     * Melakukan POST request ke API dengan token
     */
    public static function post($endpoint, $data = [])
    {
        return self::request('post', self::$baseUrl . $endpoint, $data);
    }

    /**
     * Melakukan POST request ke API tanpa token (untuk login)
     */
    public static function postWithoutToken($endpoint, $data = [])
    {
        return self::requestWithoutToken('post', self::$baseUrl . $endpoint, $data);
    }

    /**
     * Request umum tanpa token (untuk login)
     */
    private static function requestWithoutToken($method, $url, $data = [])
    {
        try {
            // Log untuk debugging
            Log::info('API Request Tanpa Token', [
                'url' => $url,
                'method' => $method
            ]);

            // Lakukan request tanpa token
            $response = Http::$method($url, $data);

            // Periksa response
            if ($response->successful()) {
                return $response->json();
            }

            // Log error untuk debugging
            Log::error('API Request Error (tanpa token)', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('API Service Error (tanpa token)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Request umum dengan token
     */
    private static function request($method, $url, $data = [])
    {
        try {
            // Ambil token dari session
            $token = self::getToken();

            // Log untuk debugging
            Log::info('API Request Dengan Token', [
                'url' => $url,
                'method' => $method,
                'token_exists' => !empty($token)
            ]);

            // Jika tidak ada token, kembalikan null
            if (!$token) {
                Log::warning('Token tidak ditemukan di session', ['url' => $url]);
                return null;
            }

            // Lakukan request dengan token
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->$method($url, $data);

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
                        $response = Http::withHeaders([
                            'Authorization' => $token
                        ])->$method($url, $data);

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

                // Hapus data token dari session
                Session::forget('api_token');
                Session::forget('user_data');

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
     * Mendapatkan token dari session
     */
    private static function getToken()
    {
        // Ambil token hanya dari session
        $token = Session::get('api_token');

        // Log untuk debugging
        if (empty($token)) {
            Log::warning('Token tidak ditemukan di session');
        }

        return $token;
    }

    /**
     * Set token ke session
     */
    // public static function setToken($token)
    // {
    //     // Pastikan format token sudah benar
    //     if (!empty($token)) {
    //         // Tambahkan prefix 'Bearer ' jika belum ada
    //         if (strpos($token, 'Bearer ') !== 0) {
    //             $token = 'Bearer ' . $token;
    //         }

    //         // Simpan ke session
    //         Session::put('api_token', $token);
    //         Log::info('Token baru disimpan ke session');
    //         return true;
    //     }

    //     return false;
    // }
    public static function setToken($token)
    {
        // Pastikan format token sudah benar
        if (!empty($token)) {
            // Tambahkan prefix 'Bearer ' jika belum ada
            if (strpos($token, 'Bearer ') !== 0) {
                $token = 'Bearer ' . $token;
            }

            // Simpan ke session
            Session::put('api_token', $token);

            // TAMBAHKAN: Simpan ke session database untuk persistensi
            // Pastikan menggunakan driver session 'database'
            session()->save();

            Log::info('Token baru disimpan ke session');
            return true;
        }

        return false;
    }
    public static function validateToken($token)
{
    try {
        if (empty($token)) {
            return false;
        }
        
        // Cek apakah token valid dengan ping ke endpoint yang memerlukan autentikasi
        $response = Http::withHeaders([
            'Authorization' => $token
        ])->get(self::$baseUrl . '/auth/user'); // gunakan endpoint yang mengembalikan user data
        
        return $response->successful();
    } catch (\Exception $e) {
        Log::error('Token Validation Error', [
            'message' => $e->getMessage()
        ]);
        return false;
    }
}

public static function refreshToken()
{
    try {
        // Ambil token yang ada
        $token = self::getToken();
        
        if (!$token) {
            return false;
        }
        
        // Lakukan request untuk refresh token
        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post(self::$baseUrl . '/auth/refresh');

        if ($response->successful()) {
            $data = $response->json();
            $newToken = $data['token'] ?? ($data['data']['token'] ?? null);
            
            if ($newToken) {
                // Simpan token baru di session dengan format bearer token
                return self::setToken($newToken);
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

    /**
     * Validate token
     */
    // public static function validateToken($token)
    // {
    //     try {
    //         if (empty($token)) {
    //             return false;
    //         }

    //         // Cek apakah token valid dengan ping ke endpoint yang memerlukan autentikasi
    //         $response = Http::withHeaders([
    //             'Authorization' => $token
    //         ])->get(self::$baseUrl . '/auth/validate-token');

    //         return $response->successful();
    //     } catch (\Exception $e) {
    //         Log::error('Token Validation Error', [
    //             'message' => $e->getMessage()
    //         ]);
    //         return false;
    //     }
    // }

    // /**
    //  * Refresh token
    //  */
    // public static function refreshToken()
    // {
    //     try {
    //         // Ambil token yang ada
    //         $token = self::getToken();

    //         if (!$token) {
    //             return false;
    //         }

    //         // Lakukan request untuk refresh token
    //         $response = Http::withHeaders([
    //             'Authorization' => $token
    //         ])->post(self::$baseUrl . '/auth/refresh-token');

    //         if ($response->successful()) {
    //             $data = $response->json();
    //             $newToken = $data['token'] ?? ($data['data']['token'] ?? null);

    //             if ($newToken) {
    //                 // Simpan token baru di session dengan format bearer token
    //                 return self::setToken($newToken);
    //             }
    //         }

    //         Log::warning('Refresh token gagal', [
    //             'status' => $response->status(),
    //             'response' => $response->json()
    //         ]);

    //         return false;
    //     } catch (\Exception $e) {
    //         Log::error('Refresh Token Error', [
    //             'message' => $e->getMessage()
    //         ]);
    //         return false;
    //     }
    // }
}