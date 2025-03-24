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

                // Checking if login was successful
                if (!$loginResult['success']){
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
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);

            // Successfully invalidated the token
            return $this->successResponse(null, self::AUTH_LOGOUT_SUCCESS);
        } catch (JWTException $e) {
            // If error occurs during token invalidation
            return $this->errorResponse(self::AUTH_LOGOUT_FAILED, $e->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        return $this->execute(
            function () use ($request) {
                // Calling the processRegister method from UserModel to handle user registration
                $registerResult = UserModel::prosesRegister($request);

                // If registration is successful, return the success message
                if (!$registerResult['success']) {
                    return $this->errorResponse(self::AUTH_REGISTRATION_FAILED, $registerResult['message'], 400);
                }

                // Successful registration response
                return response()->json([
                    'success' => true,
                    'message' => $registerResult['message'],
                    'redirect' => $registerResult['redirect']  // Optional: include a redirect URL
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
