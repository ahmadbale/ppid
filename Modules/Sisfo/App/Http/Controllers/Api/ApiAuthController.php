<?php

namespace Modules\Sisfo\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Modules\Sisfo\App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;

class ApiAuthController extends BaseApiController
{
    public function login(Request $request)
    {
        return $this->execute(
            function () use ($request) {
                // Get app_key from query parameter, default to 'app ppid' if not provided
                $appKey = $request->query('app_key', 'app ppid');
                
                // Validate app_key - FIXED inverted logic
                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                // Validasi input request
                $validator = Validator::make($request->all(), [
                    'username' => 'required|string',
                    'password' => 'required|string|min:5',
                ], [
                    'username.required' => 'Username wajib diisi',
                    'password.required' => 'Password wajib diisi',
                    'password.min' => 'Password minimal 5 karakter',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        $validator->errors()->first(),
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                // Panggil method prosesLogin dari UserModel dengan app_key
                $loginResult = UserModel::prosesLogin($request, $appKey);

                if (!$loginResult['success']) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_CREDENTIALS,
                        $loginResult['message'],
                        self::HTTP_UNAUTHORIZED
                    );
                }

                // User sudah terautentikasi
                $user = $loginResult['user'];

                // Set custom claims untuk user token
                $customClaims = [
                    'type' => 'user',
                    'exp' => now()->addMinutes(config('jwt.ttl.user', 60))->timestamp,
                    'user_id' => $user->user_id,
                    'role' => $user->getRoleName(),
                    'app_key' => $appKey
                ];

                // Generate token dengan custom claims
                $token = JWTAuth::claims($customClaims)->fromUser($user);

                // Set cookie dengan token
                $cookie = cookie(
                    'jwt_token',
                    $token,
                    config('jwt.ttl.user', 60),
                    '/',
                    null,
                    true,
                    true,
                    false,
                    'Strict'
                );

                return response()->json([
                    'success' => true,
                    'message' => $loginResult['message'],
                    'redirect' => $loginResult['redirect'],
                    'multi_level' => $loginResult['multi_level'] ?? false,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl.user', 60) * 60, // dalam detik
                    'user' => UserModel::getDataUser($user),
                    'app_key' => $appKey
                ])->withCookie($cookie);
            },
            'login',
            self::ACTION_LOGIN
        );
    }

    public function logout(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                try {
                    $appKey = $request->query('app_key') ?? JWTAuth::parseToken()->getPayload()->get('app_key') ?? 'app ppid';
                    
                    $token = JWTAuth::getToken();
                    JWTAuth::invalidate($token);

                    // Hapus cookie
                    $cookie = cookie()->forget('jwt_token');

                    return response()->json([
                        'success' => true,
                        'message' => self::AUTH_LOGOUT_SUCCESS,
                        'app_key' => $appKey
                    ])->withCookie($cookie);

                } catch (JWTException $e) {
                    throw new \Exception(self::AUTH_LOGOUT_FAILED);
                }
            },
            'logout',
            self::ACTION_LOGOUT
        );
    }

    public function register(Request $request)
    {
        return $this->execute(
            function () use ($request) {
                // Panggil method prosesRegister dari UserModel
                $registerResult = UserModel::prosesRegister($request);

                if (!$registerResult['success']) {
                    return $this->errorResponse(
                        self::AUTH_REGISTRATION_FAILED,
                        $registerResult['message'],
                        self::HTTP_BAD_REQUEST
                    );
                }

                // Registrasi berhasil - return data yang akan diproses oleh BaseApiController
                return [
                    'message' => $registerResult['message'],
                    'redirect' => $registerResult['data']['redirect'] ?? null
                ];
            },
            'register',
            self::ACTION_REGISTER
        );
    }

    public function getData()
    {
        return $this->executeWithAuthentication(
            function ($user) {
                // Return user data for the logged-in user
                return UserModel::getDataUser($user);
            },
            'user data',
            self::ACTION_GET
        );
    }

    /**
     * Mengubah hak akses aktif user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeActiveLevel(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                // Validasi input
                $validator = Validator::make($request->all(), [
                    'hak_akses_id' => 'required|exists:m_hak_akses,hak_akses_id',
                ], [
                    'hak_akses_id.required' => 'ID hak akses wajib diisi',
                    'hak_akses_id.exists' => 'ID hak akses tidak valid',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        $validator->errors()->first(),
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                // Periksa apakah user memiliki hak akses tersebut
                $hakAksesIds = $user->hakAkses()->pluck('hak_akses_id')->toArray();
                
                if (!in_array($request->hak_akses_id, $hakAksesIds)) {
                    return $this->errorResponse(
                        self::AUTH_UNAUTHORIZED,
                        'Anda tidak memiliki hak akses tersebut',
                        self::HTTP_FORBIDDEN
                    );
                }
                
                // Set hak akses aktif ke session
                session(['active_hak_akses_id' => $request->hak_akses_id]);
                
                // Dapatkan informasi hak akses baru
                $newHakAkses = $user->getActiveHakAkses();
                
                // Generate token baru dengan hak akses yang sudah diubah
                $token = JWTAuth::fromUser($user);
                
                // Return response dengan token baru
                return response()->json([
                    'success' => true,
                    'message' => 'Hak akses berhasil diubah',
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60, // dalam detik
                    'user' => UserModel::getDataUser($user),
                    'active_level' => [
                        'id' => $newHakAkses->hak_akses_id,
                        'kode' => $newHakAkses->hak_akses_kode,
                        'nama' => $newHakAkses->hak_akses_nama
                    ],
                    'redirect' => url('/dashboard' . $newHakAkses->hak_akses_kode)
                ]);
            },
            'change level',
            self::ACTION_UPDATE
        );
    }
}