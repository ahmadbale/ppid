<?php

namespace Modules\Sisfo\App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class BaseApiController extends Controller
{
    
    // =============================
    // HTTP STATUS CODE CONSTANTS
    // =============================
    
    // Success Status Codes (2xx)
    protected const HTTP_OK = 200;                    // Request berhasil
    protected const HTTP_CREATED = 201;              // Resource berhasil dibuat
    protected const HTTP_ACCEPTED = 202;             // Request diterima untuk processing
    protected const HTTP_NO_CONTENT = 204;           // Request berhasil tanpa content
    
    // Client Error Status Codes (4xx)
    protected const HTTP_BAD_REQUEST = 400;          // Request tidak valid
    protected const HTTP_UNAUTHORIZED = 401;         // Authentication diperlukan
    protected const HTTP_FORBIDDEN = 403;            // Access ditolak
    protected const HTTP_NOT_FOUND = 404;            // Resource tidak ditemukan
    protected const HTTP_METHOD_NOT_ALLOWED = 405;   // Method tidak diizinkan
    protected const HTTP_NOT_ACCEPTABLE = 406;       // Format response tidak acceptable
    protected const HTTP_REQUEST_TIMEOUT = 408;      // Request timeout
    protected const HTTP_CONFLICT = 409;             // Conflict dengan state saat ini
    protected const HTTP_GONE = 410;                 // Resource sudah tidak tersedia
    protected const HTTP_UNPROCESSABLE_ENTITY = 422; // Validation error
    protected const HTTP_TOO_MANY_REQUESTS = 429;    // Rate limit exceeded
    
    // Server Error Status Codes (5xx)
    protected const HTTP_INTERNAL_SERVER_ERROR = 500; // Server error
    protected const HTTP_NOT_IMPLEMENTED = 501;       // Feature belum diimplementasi
    protected const HTTP_BAD_GATEWAY = 502;           // Bad gateway
    protected const HTTP_SERVICE_UNAVAILABLE = 503;   // Service tidak tersedia
    protected const HTTP_GATEWAY_TIMEOUT = 504;       // Gateway timeout
    
    // =============================
    // ACTION TYPE CONSTANTS
    // =============================
    
    protected const ACTION_GET = 'get';
    protected const ACTION_CREATE = 'create';
    protected const ACTION_UPDATE = 'update';
    protected const ACTION_DELETE = 'delete';
    protected const ACTION_LOGIN = 'login';
    protected const ACTION_REGISTER = 'register';
    protected const ACTION_LOGOUT = 'logout';
    
    // =============================
    // MESSAGE CONSTANTS
    // =============================
    
    protected const AUTH_TOKEN_NOT_FOUND = 'Token tidak ditemukan';
    protected const AUTH_USER_NOT_FOUND = 'User tidak ditemukan';
    protected const AUTH_SYSTEM_NOT_FOUND = 'System tidak ditemukan';
    protected const AUTH_TOKEN_EXPIRED = 'Token telah kadaluarsa';
    protected const AUTH_TOKEN_INVALID = 'Token tidak valid';
    protected const AUTH_TOKEN_ERROR = 'Terjadi kesalahan pada token';
    protected const AUTH_INVALID_CREDENTIALS = 'Login gagal. Pastikan username dan password yang Anda masukkan benar.';
    protected const AUTH_INVALID_INPUT = 'Data yang dimasukkan tidak valid';
    protected const AUTH_UNAUTHORIZED = 'Anda tidak memiliki izin untuk mengakses resource ini';
    protected const AUTH_LOGIN_SUCCESS = 'Login berhasil';
    protected const AUTH_REGISTER_SUCCESS = 'Registrasi berhasil';
    protected const AUTH_LOGOUT_SUCCESS = 'Logout berhasil, token telah dihapus';
    protected const AUTH_LOGOUT_FAILED = 'Gagal melakukan logout';
    protected const AUTH_REGISTRATION_FAILED = 'Registrasi gagal';
    protected const VALIDATION_FAILED = 'Validasi gagal';
    protected const SERVER_ERROR = 'Terjadi kesalahan pada server';
    protected const RESOURCE_NOT_FOUND = 'Data tidak ditemukan';
    protected const INVALID_REQUEST_FORMAT = 'Format request tidak valid';
    protected const AUTH_FORBIDDEN = 'Anda tidak memiliki izin untuk melakukan aksi ini';
    protected const SUCCESS_OPERATION = 'Operasi berhasil dilakukan';
    protected const RATE_LIMIT_EXCEEDED = 'Terlalu banyak request, silakan coba lagi nanti';
    protected const MAINTENANCE_MODE = 'Sistem sedang dalam maintenance';
    protected const DUPLICATE_ENTRY = 'Data sudah ada, tidak dapat menambah duplikat';
    protected const DEPENDENCY_CONFLICT = 'Data tidak dapat dihapus karena masih digunakan';
    
    // =============================
    // AUTH TYPE CONSTANTS
    // =============================
    
    protected const AUTH_TYPE_USER = 'user';
    protected const AUTH_TYPE_SYSTEM = 'system';
   
    // Template pesan untuk setiap aksi
    protected $messageTemplates = [
        self::ACTION_GET => [
            'success' => 'Data %s berhasil diambil.',
            'error' => 'Gagal mengambil data %s. Silakan coba lagi.'
        ],
        self::ACTION_CREATE => [
            'success' => 'Data %s berhasil dibuat.',
            'error' => 'Gagal membuat data %s. Silakan coba lagi.'
        ],
        self::ACTION_UPDATE => [
            'success' => 'Data %s berhasil diperbarui.',
            'error' => 'Gagal memperbarui data %s. Silakan coba lagi.'
        ],
        self::ACTION_DELETE => [
            'success' => 'Data %s berhasil dihapus.',
            'error' => 'Gagal menghapus data %s. Silakan coba lagi.'
        ],
        self::ACTION_LOGIN => [
            'success' => self::AUTH_LOGIN_SUCCESS,
            'error' => self::AUTH_INVALID_CREDENTIALS
        ],
        self::ACTION_REGISTER => [
            'success' => self::AUTH_REGISTER_SUCCESS,
            'error' => self::VALIDATION_FAILED
        ],
        self::ACTION_LOGOUT => [
            'success' => self::AUTH_LOGOUT_SUCCESS,
            'error' => self::AUTH_LOGOUT_FAILED
        ],
    ];

    /**
     * Menjalankan aksi API tanpa autentikasi (untuk endpoint publik)
     * 
     * @param callable $action Fungsi yang akan dieksekusi
     * @param string $resourceName Nama resource (contoh: 'menu', 'user')
     * @param string $actionType Jenis aksi (get, create, update, delete, dll)
     * @return JsonResponse
     */
    protected function execute(callable $action, string $resourceName, string $actionType = self::ACTION_GET): JsonResponse
    {
        try {
            // Eksekusi aksi
            $result = $action();

            // Jika hasil adalah instance JsonResponse, langsung kembalikan
            if ($result instanceof JsonResponse) {
                return $result;
            }
            
            // Definisi pesan error untuk hasil null
            $notFoundMessages = [
                self::ACTION_GET => '%s tidak ditemukan.',
                self::ACTION_UPDATE => 'Data %s yang akan diupdate tidak ditemukan.',
                self::ACTION_DELETE => 'Data %s yang akan dihapus tidak ditemukan.',
                self::ACTION_CREATE => 'Gagal membuat data %s.',
            ];

            // Cek hasil null dengan pesan sesuai tipe aksi
            if ($result === null) {
                $errorMessage = isset($notFoundMessages[$actionType]) 
                    ? sprintf($notFoundMessages[$actionType], $resourceName)
                    : sprintf('%s tidak ditemukan.', $resourceName);
                
                return $this->errorResponse($errorMessage, null, self::HTTP_NOT_FOUND);
            }

            // Pastikan action type valid, jika tidak gunakan pesan default
            $message = self::RESOURCE_NOT_FOUND;
            if (isset($this->messageTemplates[$actionType])) {
                $message = sprintf($this->messageTemplates[$actionType]['success'], $resourceName);
            }
            
            // Tentukan status code berdasarkan action type
            $statusCode = match($actionType) {
                self::ACTION_CREATE => self::HTTP_CREATED,
                self::ACTION_DELETE => self::HTTP_NO_CONTENT,
                default => self::HTTP_OK
            };
            
            return $this->successResponse($result, $message, $statusCode);

        } catch (\Exception $e) {
            $this->logError('Execution error: ' . $resourceName, $e);
            
            // Tentukan pesan error berdasarkan action type
            $message = isset($this->messageTemplates[$actionType]) 
                ? sprintf($this->messageTemplates[$actionType]['error'], $resourceName)
                : sprintf('Terjadi kesalahan saat memproses %s.', $resourceName);
            
            return $this->errorResponse($message, $e->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Menjalankan aksi API dengan autentikasi sistem (jwt.system)
     * Digunakan untuk endpoint API publik yang dilindungi middleware jwt.system
     * 
     * @param callable $action Fungsi yang akan dieksekusi
     * @param string $resourceName Nama resource (contoh: 'menu', 'berita')
     * @param string $actionType Jenis aksi (get, create, update, delete, dll)
     * @return JsonResponse
     */
    protected function executeWithSystemAuth(callable $action, string $resourceName, string $actionType = self::ACTION_GET): JsonResponse
    {
        try {
            // Get active system token using JwtTokenService
            $jwtService = new \App\Services\JwtTokenService();
            $tokenData = $jwtService->getActiveToken();

            if (!$tokenData || !isset($tokenData['token'])) {
                return $this->errorResponse(self::AUTH_SYSTEM_NOT_FOUND, null, self::HTTP_UNAUTHORIZED);
            }

            // Set token for request
            JWTAuth::setToken($tokenData['token']);

            // Execute action without passing system parameter
            $result = $action();
                
            // Handle JsonResponse result
            if ($result instanceof JsonResponse) {
                return $result;
            }

            // Handle null result
            if ($result === null) {
                $errorMessage = match($actionType) {
                    self::ACTION_GET => sprintf('%s tidak ditemukan.', $resourceName),
                    self::ACTION_UPDATE => sprintf('Data %s yang akan diupdate tidak ditemukan.', $resourceName),
                    self::ACTION_DELETE => sprintf('Data %s yang akan dihapus tidak ditemukan.', $resourceName),
                    self::ACTION_CREATE => sprintf('Gagal membuat data %s.', $resourceName),
                    default => sprintf('%s tidak ditemukan.', $resourceName)
                };
                
                return $this->errorResponse($errorMessage, null, self::HTTP_NOT_FOUND);
            }

            // Get success message from templates
            $message = isset($this->messageTemplates[$actionType]) 
                ? sprintf($this->messageTemplates[$actionType]['success'], $resourceName)
                : sprintf('Data %s berhasil diproses.', $resourceName);
            
            // Tentukan status code berdasarkan action type
            $statusCode = match($actionType) {
                self::ACTION_CREATE => self::HTTP_CREATED,
                self::ACTION_DELETE => self::HTTP_NO_CONTENT,
                default => self::HTTP_OK
            };
                
            return $this->successResponse($result, $message, $statusCode);
                
        } catch (TokenExpiredException $e) {
            // Generate new token if expired
            try {
                $newToken = $jwtService->generateSystemToken();
                JWTAuth::setToken($newToken['token']);
                return $this->execute($action, $resourceName, $actionType);
            } catch (\Exception $e) {
                return $this->errorResponse(self::AUTH_TOKEN_EXPIRED, null, self::HTTP_UNAUTHORIZED);
            }
        } catch (TokenInvalidException $e) {
            $this->logError('System token invalid', $e);
            return $this->errorResponse(self::AUTH_TOKEN_INVALID, null, self::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            $this->logError('JWT Error: ' . $resourceName, $e);
            return $this->errorResponse(self::AUTH_TOKEN_ERROR, $e->getMessage(), self::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            $this->logError('System auth error: ' . $resourceName, $e);
            return $this->errorResponse(
                sprintf($this->messageTemplates[$actionType]['error'] ?? 'Terjadi kesalahan saat memproses %s.', $resourceName),
                config('app.debug') ? $e->getMessage() : null,
                self::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    

/**
 * Menjalankan aksi API yang memerlukan autentikasi user (jwt.user)
 * 
 * @param callable $action Fungsi yang akan dieksekusi, menerima user terautentikasi
 * @param string $resourceName Nama resource (contoh: 'menu', 'user')
 * @param string $actionType Jenis aksi (get, create, update, delete, dll)
 * @return JsonResponse
 */
protected function executeWithAuthentication(callable $action, string $resourceName, string $actionType = self::ACTION_GET): JsonResponse
{
    try {
        // Periksa keberadaan token
        if (!$token = JWTAuth::getToken()) {
            return $this->errorResponse(self::AUTH_TOKEN_NOT_FOUND, null, self::HTTP_UNAUTHORIZED);
        }

        // Autentikasi token dan dapatkan user
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return $this->errorResponse(self::AUTH_USER_NOT_FOUND, null, self::HTTP_UNAUTHORIZED);
        }
        
        // Eksekusi aksi dengan user terautentikasi
        $result = $action($user);

        // Jika hasil adalah instance JsonResponse, langsung kembalikan
        if ($result instanceof JsonResponse) {
            return $result;
        }

        // Definisi pesan error untuk hasil null
        $notFoundMessages = [
            self::ACTION_GET => '%s tidak ditemukan.',
            self::ACTION_UPDATE => 'Data %s yang akan diupdate tidak ditemukan.',
            self::ACTION_DELETE => 'Data %s yang akan dihapus tidak ditemukan.',
            self::ACTION_CREATE => 'Gagal membuat data %s.',
        ];

        // Cek hasil null dengan pesan sesuai tipe aksi
        if ($result === null) {
            $errorMessage = isset($notFoundMessages[$actionType]) 
                ? sprintf($notFoundMessages[$actionType], $resourceName)
                : sprintf('%s tidak ditemukan.', $resourceName);
            
            return $this->errorResponse($errorMessage, null, self::HTTP_NOT_FOUND);
        }

        // Pastikan action type valid, jika tidak gunakan pesan default
        $message = self::RESOURCE_NOT_FOUND;
        if (isset($this->messageTemplates[$actionType])) {
            $message = sprintf($this->messageTemplates[$actionType]['success'], $resourceName);
        }
        
    
        $statusCode = match($actionType) {
            self::ACTION_CREATE => self::HTTP_CREATED,
            self::ACTION_DELETE => self::HTTP_OK, 
            default => self::HTTP_OK
        };
        
        // PERBAIKAN: Untuk DELETE, jangan kirim data dalam response
        if ($actionType === self::ACTION_DELETE) {
            return $this->successResponse(null, $message, $statusCode);
        }
        
        return $this->successResponse($result, $message, $statusCode);
        
    } catch (TokenExpiredException $e) {
        $this->logError('Token expired', $e);
        return $this->errorResponse(self::AUTH_TOKEN_EXPIRED, null, self::HTTP_UNAUTHORIZED);
    } catch (TokenInvalidException $e) {
        $this->logError('Token invalid', $e);
        return $this->errorResponse(self::AUTH_TOKEN_INVALID, null, self::HTTP_UNAUTHORIZED);
    } catch (JWTException $e) {
        $this->logError('JWT Error: ' . $resourceName, $e);
        return $this->errorResponse(self::AUTH_TOKEN_ERROR, $e->getMessage(), self::HTTP_UNAUTHORIZED);
    } catch (\Exception $e) {
        $this->logError('Authentication action error: ' . $resourceName, $e);
        $message = isset($this->messageTemplates[$actionType]) 
            ? sprintf($this->messageTemplates[$actionType]['error'], $resourceName)
            : sprintf('Terjadi kesalahan saat memproses %s.', $resourceName);
        return $this->errorResponse($message, $e->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
    }
}
    /**
     * Menjalankan aksi API yang memerlukan validasi dan autentikasi sekaligus
     * 
     * @param callable $action Fungsi yang akan dieksekusi jika autentikasi berhasil
     * @param string $resourceName Nama resource
     * @param string $actionType Jenis aksi
     * @return JsonResponse
     */
    protected function executeWithAuthAndValidation(callable $action, string $resourceName, string $actionType): JsonResponse
    {
        return $this->executeWithAuthentication(
            function ($user) use ($action, $resourceName, $actionType) {
                try {
                    // Jalankan action dengan user
                    $result = $action($user);
                    
                    // Jika result adalah response, kembalikan langsung
                    if ($result instanceof JsonResponse) {
                        return $result;
                    }
                    
                    return $result;
                } catch (\Exception $e) {
                    $this->logError('Validation error in authenticated context: ' . $resourceName, $e);
                    return $this->errorResponse(self::VALIDATION_FAILED, $e->getMessage(), self::HTTP_UNPROCESSABLE_ENTITY);
                }
            },
            $resourceName,
            $actionType
        );
    }
    
    protected function executeWithValidation(callable $validator, callable $action, string $resourceName, string $actionType): JsonResponse
    {
        try {
            // Jalankan validator
            $validationResult = $validator();
            
            // Jika validasi memberikan response error, kembalikan
            if ($validationResult instanceof JsonResponse) {
                return $validationResult;
            }
            
            // Jika validator mengembalikan pesan error string
            if (is_string($validationResult)) {
                return $this->errorResponse($validationResult, null, self::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Jalankan aksi sukses
            return $this->execute(
                $action,
                $resourceName,
                $actionType
            );
            
        } catch (\Exception $e) {
            $this->logError('Validation error: ' . $resourceName, $e);
            return $this->errorResponse(self::VALIDATION_FAILED, $e->getMessage(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    // app key validation
    protected function validateUserAccess($user, $appKey, $resource = null)
{
    // Validasi bahwa user memiliki akses ke aplikasi ini
    $hasAccess = DB::table('set_user_hak_akses')
        ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
        ->join('web_menu', 'web_menu.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
        ->join('web_menu_global', 'web_menu.fk_web_menu_global', '=', 'web_menu_global.web_menu_global_id')
        ->join('web_menu_url', 'web_menu_global.fk_web_menu_url', '=', 'web_menu_url.web_menu_url_id')
        ->join('m_application', 'web_menu_url.fk_m_application', '=', 'm_application.application_id')
        ->where('set_user_hak_akses.fk_m_user', $user->user_id)
        ->where('m_application.app_key', $appKey)
        ->where('set_user_hak_akses.isDeleted', 0)
        ->exists();
        
    if (!$hasAccess) {
        throw new \Exception("User tidak memiliki akses ke aplikasi '{$appKey}'");
    }
    
    return true;
}

    /**
     * Return success response in JSON format.
     * 
     * @param mixed $data Data yang akan dikembalikan
     * @param string $message Pesan sukses
     * @param int $statusCode HTTP status code
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Operation successful', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Hanya tambahkan data jika tidak null
        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return error response in JSON format.
     * 
     * @param string $message Pesan error
     * @param mixed $error Detail error (hanya tampil jika debug mode aktif)
     * @param int $statusCode HTTP status code
     * @param array|null $validationErrors Error validasi yang akan ditampilkan
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'An error occurred', $error = null, int $statusCode = 500, ?array $validationErrors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        // Tambahkan validation errors jika ada
        if ($validationErrors !== null) {
            $response['errors'] = $validationErrors;
        }
        
        // Tambahkan detail error jika debug mode aktif
        if ($error !== null && config('app.debug')) {
            $errorDetail = $error;
            // Jika error adalah Exception, ambil pesan dan stacktrace
            if ($error instanceof \Exception) {
                $errorDetail = [
                    'message' => $error->getMessage(),
                    'code' => $error->getCode(),
                    'file' => $error->getFile(),
                    'line' => $error->getLine()
                ];
            }
            $response['error_detail'] = $errorDetail;
        }
        
        return response()->json($response, $statusCode);
    }

    /**
     * Log error ke system log.
     * 
     * @param string $context Konteks error
     * @param \Exception $exception Exception yang terjadi
     * @return void
     */
    protected function logError(string $context, \Exception $exception): void
    {
        if (class_exists('Log')) {
            Log::error($context, [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }
}