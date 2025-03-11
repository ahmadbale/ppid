<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Sisfo\App\Models\UserModel;

class UserController extends Controller
{
    public function showLoginForm()
    {
        return view(view: 'user::login');
    }

    public function login(Request $request)
    {
        try {
            // Kirim request login ke API backend
            $response = Http::post('http://ppid-polinema.test/api/auth/login', [
                'username' => $request->username,
                'password' => $request->password
            ]);

            $data = $response->json();
            
            // Log full response for debugging
            Log::info('Login API Response', ['response' => $data]);

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
                    session(['api_token' => $token]);
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
            // Kirim request logout ke backend API
            $token = session('api_token');
            
            $response = Http::withToken($token)->post('http://ppid-polinema.test/api/auth/logout');
            
            // Hapus token dari session
            session()->forget('api_token');

            // Logout dari frontend
            Auth::logout();

            // Redirect ke halaman login
            return redirect()->route('login-ppid');
        } catch (\Exception $e) {
            // Tetap logout meskipun ada kesalahan
            Auth::logout();
            session()->forget('api_token');
            return redirect()->route('login-ppid');
        }
    }
}