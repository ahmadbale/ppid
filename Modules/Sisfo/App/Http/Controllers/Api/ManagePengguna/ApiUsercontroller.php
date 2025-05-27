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
                    
                    // DEBUGGING: Log semua data request
                    Log::info('All Request Data: ', [
                        'all' => $request->all(),
                        'input' => $request->input(),
                        'keys' => $request->keys(),
                        'method' => $request->method(),
                        'content_type' => $request->header('Content-Type')
                    ]);
                    
                    // Handle form-data format yang benar
                    $formDataKeys = $request->keys();
                    $isFormDataFormat = false;
                    $formDataValues = [];
                    
                    // Deteksi dan ekstrak form-data
                    foreach ($formDataKeys as $key) {
                        if (strpos($key, 'm_user[') === 0 && substr($key, -1) === ']') {
                            $isFormDataFormat = true;
                            $fieldName = substr($key, 7, -1);
                            $formDataValues[$fieldName] = $request->input($key);
                        }
                    }
                    
                    Log::info('Form data detection: ', [
                        'is_form_data' => $isFormDataFormat,
                        'form_values' => $formDataValues
                    ]);
                    
                    // Restrukturisasi request jika form-data
                    if ($isFormDataFormat && !empty($formDataValues)) {
                        $restructuredData = ['m_user' => $formDataValues];
                        
                        // Tambahkan field tambahan
                        if ($request->has('password')) {
                            $restructuredData['password'] = $request->input('password');
                        }
                        if ($request->has('password_confirmation')) {
                            $restructuredData['password_confirmation'] = $request->input('password_confirmation');
                        }
                        if ($request->has('hak_akses_id')) {
                            $restructuredData['hak_akses_id'] = $request->input('hak_akses_id');
                        }
                        
                        // Create new request
                        $newRequest = new Request();
                        $newRequest->replace($restructuredData);
                        
                        // Handle file uploads
                        if ($request->hasFile('upload_nik_pengguna')) {
                            $newRequest->files->set('upload_nik_pengguna', $request->file('upload_nik_pengguna'));
                        }
                        
                        $request = $newRequest;
                        Log::info('Restructured request: ', $request->all());
                    }
                    
                    // Jika tidak ada data m_user sama sekali, buat dari form-data langsung
                    $requestData = $request->all();
                    if (!isset($requestData['m_user']) && !$isFormDataFormat) {
                        // Mungkin data dikirim langsung tanpa struktur m_user
                        $possibleFields = [
                            'nama_pengguna', 'email_pengguna', 'no_hp_pengguna', 
                            'alamat_pengguna', 'pekerjaan_pengguna', 'nik_pengguna'
                        ];
                        
                        $directData = [];
                        foreach ($possibleFields as $field) {
                            if ($request->has($field)) {
                                $directData[$field] = $request->input($field);
                            }
                        }
                        
                        if (!empty($directData)) {
                            $requestData['m_user'] = $directData;
                            $request->replace($requestData);
                            Log::info('Direct field mapping: ', $request->all());
                        }
                    }
                    
                    // PARTIAL UPDATE IMPLEMENTATION
                    $requestData = $request->all();
                    
                    // Selalu lakukan partial update untuk PUT method
                    if (isset($requestData['m_user']) && !empty($requestData['m_user'])) {
                        // Get existing user data
                        $existingUser = UserModel::findOrFail($id);
                        
                        // Merge existing data dengan data baru
                        $mergedUserData = [
                            'nama_pengguna' => $existingUser->nama_pengguna,
                            'email_pengguna' => $existingUser->email_pengguna,
                            'no_hp_pengguna' => $existingUser->no_hp_pengguna,
                            'alamat_pengguna' => $existingUser->alamat_pengguna,
                            'pekerjaan_pengguna' => $existingUser->pekerjaan_pengguna,
                            'nik_pengguna' => $existingUser->nik_pengguna
                        ];
                        
                        // Override dengan data dari request
                        foreach ($requestData['m_user'] as $key => $value) {
                            if (!empty($value)) { // Hanya update jika value tidak kosong
                                $mergedUserData[$key] = $value;
                            }
                        }
                        
                        // Create final request dengan data lengkap
                        $finalRequest = new Request();
                        $finalData = [
                            'm_user' => $mergedUserData
                        ];
                        
                        // Tambahkan field lain jika ada
                        if (isset($requestData['password'])) {
                            $finalData['password'] = $requestData['password'];
                        }
                        if (isset($requestData['password_confirmation'])) {
                            $finalData['password_confirmation'] = $requestData['password_confirmation'];
                        }
                        if (isset($requestData['hak_akses_id'])) {
                            $finalData['hak_akses_id'] = $requestData['hak_akses_id'];
                        }
                        
                        $finalRequest->replace($finalData);
                        
                        // Handle file upload
                        if ($request->hasFile('upload_nik_pengguna')) {
                            $finalRequest->files->set('upload_nik_pengguna', $request->file('upload_nik_pengguna'));
                        }
                        
                        Log::info('Final merged request: ', $finalRequest->all());
                        
                        // Update dengan data yang sudah dimergre
                        $result = UserModel::updateData($finalRequest, $id);
                    } else {
                        return $this->errorResponse(
                            'Data tidak valid',
                            'Tidak ada data yang dikirim untuk update',
                            422
                        );
                    }
                    
                    // Log hasil update
                    Log::info('Update result: ', $result ?: ['null']);
                    
                    // Check hasil update
                    if (!$result || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                        $errors = isset($result['errors']) ? $result['errors'] : null;
                        $message = isset($result['message']) ? $result['message'] : 'Gagal memperbarui pengguna';
                        
                        return $this->errorResponse(
                            $message,
                            null,
                            422,
                            $errors
                        );
                    }
                    
                    // Ambil data user terbaru
                    $updatedUser = UserModel::findOrFail($id);
                    
                    return [
                        'success' => true,
                        'message' => 'Data pengguna berhasil diperbarui.',
                        'data' => $updatedUser
                    ];
                    
                } catch (ValidationException $e) {
                    Log::error('Validation Exception: ', $e->errors());
                    return $this->jsonValidationError($e);
                } catch (\Exception $e) {
                    Log::error('Exception in updateData: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
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