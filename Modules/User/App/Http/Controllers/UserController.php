<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Modules\Sisfo\App\Models\UserModel;
use Modules\User\App\Services\ApiService;
use App\Services\JwtTokenService; 

class UserController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

    public function showLoginForm()
    {
        return view(view: 'user::login');
    }

    public function login(Request $request)
    {
        try {
             // Ambil token JWT aktif
            $tokenData = $this->jwtTokenService->getActiveToken();

            // Kirim request login ke API backend dengan token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['token']
            ])->post($this->baseUrl . '/api/auth/login', [
                'username' => $request->username,
                'password' => $request->password
            ]);

            $data = $response->json();


            // Cek apakah login berhasil dengan berbagai struktur response
            $success = $data['success'] ?? false;
            $message = $data['message'] ?? 'Login gagal';

            // Ambil token dari berbagai kemungkinan lokasi
            $token = $data['token'] ??
                     ($data['data']['token'] ??
                     ($data['data']['original']['token'] ?? null));

            // Ambil user data
            $user = $data['user'] ??
                    ($data['data']['user'] ??
                    ($data['data']['original']['user'] ?? null));

            if ($success && $user) {
                // Cari atau buat user di frontend
                $authUser = UserModel::firstOrCreate(
                    ['email_pengguna' => $user['email_pengguna']],
                    [
                        'nama_pengguna' => $user['nama_pengguna'] ?? 'Unknown',
                        'password' => bcrypt($request->password),
                        'fk_m_level' => $user['fk_m_level'] ?? null,
                        // Tambahkan field lain sesuai kebutuhan
                    ]
                );

                // Login user di frontend
                Auth::login($authUser);

                // Simpan token ke session jika ada
                if ($token) {
                    // Gunakan ApiService untuk menyimpan token
                    ApiService::setToken($token);

                    // Simpan user data ke session jika diperlukan
                    session(['user_data' => $user]);
                }

                // Tentukan redirect URL
                $redirectUrl = $data['redirect'] ??
                               ($data['data']['redirect'] ??
                               ($data['data']['original']['redirect'] ?? route('beranda')));

                // Redirect ke dashboard sesuai level
                return redirect($redirectUrl);
            }

            // Jika login gagal
            return back()->withErrors([
                'username' => $message
            ]);

        } catch (\Exception $e) {
            // Log error lengkap
            Log::error('Login Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Tangani error koneksi atau parsing
            return back()->withErrors([
                'username' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

public function logout()
{
    try {
        $tokenData = $this->jwtTokenService->getActiveToken();

            if ($tokenData && isset($tokenData['token'])) {
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tokenData['token']
                ])->post($this->baseUrl . '/api/auth/logout');
            }

        // Hapus semua data session yang terkait
        session()->forget(['api_token', 'user_data']);

        // Logout dari frontend
        Auth::logout();

        // Redirect ke halaman login
        return redirect()->route('login-ppid');
    } catch (\Exception $e) {
        // Log error
        Log::error('Logout Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Tetap logout meskipun ada kesalahan
        Auth::logout();
        session()->forget(['api_token', 'user_data']);
        return redirect()->route('login-ppid');
    }
}
}
