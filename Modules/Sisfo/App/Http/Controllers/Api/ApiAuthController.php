<?php

namespace Modules\Sisfo\App\Http\Controllers\Api;

use Modules\Sisfo\App\Models\UserModel;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthController extends BaseApiController
{
    public function login(Request $request)
    {
        return $this->execute(
            function () use ($request) {
                $loginResult = UserModel::prosesLogin($request);

                if (!$loginResult['success']) {
                    return $this->errorResponse(self::AUTH_INVALID_CREDENTIALS, $loginResult['message'], 401);
                }

                $user = UserModel::where('nik_pengguna', $request->username)
                    ->orWhere('email_pengguna', $request->username)
                    ->orWhere('no_hp_pengguna', $request->username)
                    ->first();

                $token = JWTAuth::fromUser($user);

                return $this->successResponse([
                    'message' => $loginResult['message'],
                    'redirect' => $loginResult['redirect'],
                    'token' => $token,
                    'user' => UserModel::getDataUser($user)
                ]);
            },
            'user',
            self::ACTION_LOGIN
        );
    }

    public function logout()
    {
        return $this->execute(
            function () {
                $token = JWTAuth::getToken();
                JWTAuth::invalidate($token);
                return $this->successResponse(null, self::AUTH_LOGOUT_SUCCESS);
            },
            'user',
            self::ACTION_LOGOUT
        );
    }

    public function register(Request $request)
    {
        return $this->executeWithValidation(
            function () use ($request) {
                return validator($request->all(), [
                    'username' => 'required|string|unique:users',
                    'password' => 'required|min:6',
                    'email' => 'required|email|unique:users'
                ]);
            },
            function () use ($request) {
                $registerResult = UserModel::prosesRegister($request);

                if (!$registerResult['success']) {
                    return $this->errorResponse(self::AUTH_REGISTRATION_FAILED, $registerResult['message'], 400);
                }

                return $this->successResponse([
                    'message' => $registerResult['message'],
                    'redirect' => $registerResult['redirect']
                ], self::AUTH_REGISTER_SUCCESS);
            },
            self::ACTION_REGISTER
        );
    }

    public function getData()
    {
        return $this->executeWithAuthentication(
            function ($user) {
                return UserModel::getDataUser($user);
            },
            'user'
        );
    }

    public function refreshToken()
    {
        return $this->execute(
            function () {
                try {
                    $oldToken = JWTAuth::getToken();
                    if (!$oldToken) {
                        throw new JWTException(self::AUTH_TOKEN_NOT_FOUND);
                    }
                    $token = JWTAuth::refresh($oldToken);
                    return $this->successResponse([
                        'token' => $token,
                        'expires_in' => config('jwt.ttl') * 60
                    ]);
                } catch (JWTException $e) {
                    return $this->errorResponse(self::AUTH_TOKEN_INVALID, $e->getMessage(), 401);
                }
            },
            'token',
            self::ACTION_UPDATE
        );
    }
}
