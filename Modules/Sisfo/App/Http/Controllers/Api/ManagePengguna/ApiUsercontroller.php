<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\Sisfo\App\Models\UserModel;
use Modules\Sisfo\App\Models\HakAksesModel;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiUserController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                $levelId = $request->query('hak_akses_id', null);
                
                if ($levelId) {
                    return UserModel::getUsersByLevel($levelId, $perPage, $search);
                } else {
                    return UserModel::selectData($perPage, $search);
                }
            },
            'pengguna',
            self::ACTION_GET
        );
    }

    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request) {
                try {
                    // Validasi apakah pengguna mencoba menambahkan ke level SAR
                    if ($request->has('hak_akses_id')) {
                        $level = HakAksesModel::findOrFail($request->hak_akses_id);
                        if (Auth::user()->level->hak_akses_kode !== 'SAR' && $level->hak_akses_kode === 'SAR') {
                            return $this->errorResponse(
                                self::AUTH_FORBIDDEN,
                                'Anda tidak memiliki izin untuk menambahkan pengguna ke level Super Administrator',
                                403
                            );
                        }
                    }
                    
                    // Buat data baru
                    $result = UserModel::createData($request);
                    
                    // PERBAIKAN: Cek jika result adalah array dan memiliki kunci 'success' yang false
                    if (!$result || isset($result['error']) || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                        // Jika ada errors, sertakan dalam response
                        $errors = isset($result['errors']) ? $result['errors'] : null;
                        $message = isset($result['message']) ? $result['message'] : 'Gagal membuat pengguna';
                        
                        return $this->errorResponse(
                            $message,
                            null,
                            422,
                            $errors
                        );
                    }
                    
                    return $result['data'] ?? $result;
                    
                } catch (ValidationException $e) {
                    return $this->jsonValidationError($e);
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'pengguna',
            self::ACTION_CREATE
        );
    }


public function updateData(Request $request, $id)
{
    return $this->executeWithAuthAndValidation(
        function ($user) use ($request, $id) {
            try {
                // Cek apakah user yang diedit memiliki hak akses SAR
                $userToUpdate = UserModel::findOrFail($id);
                $isSAR = $userToUpdate->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Anda tidak memiliki izin untuk mengedit pengguna dengan level Super Administrator',
                        403
                    );
                }
                
                // DEBUGGING: Log data request yang diterima
               Log::info('Request update data: ', $request->all());
                
                // Check if we have partial update
                $requestData = $request->all();
                $isPartialUpdate = !isset($requestData['m_user']) || 
                                  (isset($requestData['m_user']) && count($requestData['m_user']) < 5);
                
                $result = null;
                
                if ($isPartialUpdate) {
                    // Get existing data
                    $existingUser = UserModel::find($id);
                   Log::info('Data existing user: ', $existingUser->toArray());
                    
                    // Create a new request with merged data
                    $mergedRequest = new Request();
                    
                    // Create proper m_user structure
                    $m_user = [
                        'nama_pengguna' => $existingUser->nama_pengguna,
                        'email_pengguna' => $existingUser->email_pengguna,
                        'no_hp_pengguna' => $existingUser->no_hp_pengguna,
                        'alamat_pengguna' => $existingUser->alamat_pengguna,
                        'pekerjaan_pengguna' => $existingUser->pekerjaan_pengguna,
                        'nik_pengguna' => $existingUser->nik_pengguna
                    ];
                    
                    // Merge with the data from request
                    if (isset($requestData['m_user'])) {
                        // PERBAIKAN: Pastikan data dari request masuk ke m_user
                       Log::info('Merging with request data: ', $requestData['m_user']);
                        foreach ($requestData['m_user'] as $key => $value) {
                            $m_user[$key] = $value;
                        }
                    }
                    
                    // Build the final request data
                    $mergedData = [
                        'm_user' => $m_user
                    ];
                    
                    // Add password if present in request
                    if (isset($requestData['password'])) {
                        $mergedData['password'] = $requestData['password'];
                    }
                    if (isset($requestData['password_confirmation'])) {
                        $mergedData['password_confirmation'] = $requestData['password_confirmation'];
                    }
                    
                    // Add uploaded file if present
                    if ($request->hasFile('upload_nik_pengguna')) {
                        $mergedRequest->files->set('upload_nik_pengguna', $request->file('upload_nik_pengguna'));
                    }
                    
                    // Replace request with merged data
                    $mergedRequest->replace($mergedData);
                    
                    // DEBUGGING: Log merged data yang akan diupdate
                   Log::info('Merged data sebelum update: ', $mergedRequest->all());
                    
                    // Update with merged data
                    $result = UserModel::updateData($mergedRequest, $id);
                } else {
                    // Jika semua field sudah ada, langsung update
                    $result = UserModel::updateData($request, $id);
                }
                
                // DEBUGGING: Log hasil update
               Log::info('Hasil update: ', $result ?: ['null']);
                
                // PERBAIKAN: Cek jika result adalah array dan memiliki kunci 'success' yang false
                if (!$result || isset($result['error']) || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    // Jika ada errors, sertakan dalam response
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal mengupdate pengguna';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        422,
                        $errors
                    );
                }
                
                // PERBAIKAN: Ambil data terbaru dari database untuk response
                // Refresh dari database untuk mendapatkan data yang benar-benar terbaru
                $updatedUser = UserModel::findOrFail($id);
               Log::info('Data user setelah update: ', $updatedUser->toArray());
                
                return [
                    'success' => true,
                    'message' => 'Data pengguna berhasil diperbarui.',
                    'data' => $updatedUser
                ];
                
            } catch (ValidationException $e) {
               Log::error('Validation Exception: ' . $e->getMessage());
                return $this->jsonValidationError($e);
            } catch (\Exception $e) {
               Log::error('Exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                return $this->errorResponse(
                    self::SERVER_ERROR,
                    $e->getMessage(),
                    500
                );
            }
        },
        'pengguna',
        self::ACTION_UPDATE
    );
}

    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    // Cek apakah user yang dihapus memiliki hak akses SAR
                    $userToDelete = UserModel::findOrFail($id);
                    $isSAR = $userToDelete->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                    
                    if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk menghapus pengguna dengan level Super Administrator',
                            403
                        );
                    }
                    
                    $result = UserModel::deleteData($id);
                    
                    // PERBAIKAN: Cek jika result adalah array dan memiliki kunci 'success' yang false
                    if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Gagal menghapus pengguna',
                            null,
                            422
                        );
                    }
                    
                    return $result['data'] ?? $result;
                    
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'pengguna',
            self::ACTION_DELETE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    return UserModel::detailData($id);
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'pengguna',
            self::ACTION_GET
        );
    }
    
    public function addHakAkses(Request $request, $userId)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $userId) {
                try {
                    if (!$request->has('hak_akses_id')) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'ID hak akses harus disediakan',
                            422
                        );
                    }
                    
                    $hakAksesId = $request->hak_akses_id;
                    
                    // Cek apakah mencoba menambah hak akses SAR
                    $hakAkses = HakAksesModel::findOrFail($hakAksesId);
                    if (Auth::user()->level->hak_akses_kode !== 'SAR' && $hakAkses->hak_akses_kode === 'SAR') {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk menambahkan hak akses Super Administrator',
                            403
                        );
                    }
                    
                    $result = UserModel::addHakAkses($userId, $hakAksesId);
                    
                    // PERBAIKAN: Cek jika result adalah array dan memiliki kunci 'success' yang false
                    if (!$result || !isset($result['success']) || $result['success'] === false) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Gagal menambahkan hak akses',
                            null,
                            422
                        );
                    }
                    
                    return $result['data'] ?? $result;
                    
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'hak akses pengguna',
            self::ACTION_CREATE
        );
    }
    
    public function removeHakAkses(Request $request, $userId)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $userId) {
                try {
                    if (!$request->has('hak_akses_id')) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'ID hak akses harus disediakan',
                            422
                        );
                    }
                    
                    $hakAksesId = $request->hak_akses_id;
                    
                    // Cek apakah mencoba menghapus hak akses SAR
                    $hakAkses = HakAksesModel::findOrFail($hakAksesId);
                    if (Auth::user()->level->hak_akses_kode !== 'SAR' && $hakAkses->hak_akses_kode === 'SAR') {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk menghapus hak akses Super Administrator',
                            403
                        );
                    }
                    
                    $result = UserModel::removeHakAkses($userId, $hakAksesId);
                    
                    // PERBAIKAN: Cek jika result adalah array dan memiliki kunci 'success' yang false
                    if (!$result || !isset($result['success']) || $result['success'] === false) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Gagal menghapus hak akses',
                            null,
                            422
                        );
                    }
                    
                    return $result['data'] ?? $result;
                    
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'hak akses pengguna',
            self::ACTION_DELETE
        );
    }

    public function getAvailableHakAkses($userId)
    {
        return $this->executeWithAuthentication(
            function () use ($userId) {
                try {
                    $user = UserModel::detailData($userId);
                    
                    // Ambil semua hak akses yang bisa ditambahkan ke user
                    $availableHakAkses = HakAksesModel::where('isDeleted', 0)
                        ->when(Auth::user()->level->hak_akses_kode !== 'SAR', function ($query) {
                            return $query->where('hak_akses_kode', '!=', 'SAR');
                        })
                        ->whereNotIn('hak_akses_id', $user->hakAkses->pluck('hak_akses_id'))
                        ->get();
                    
                    return $availableHakAkses;
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'hak akses tersedia',
            self::ACTION_GET
        );
    }
}