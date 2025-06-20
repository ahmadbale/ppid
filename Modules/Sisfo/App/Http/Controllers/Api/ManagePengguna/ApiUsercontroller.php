<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Sisfo\App\Models\UserModel;
use Modules\Sisfo\App\Models\HakAksesModel;
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

    
    public function getData(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                $levelId = $request->query('hak_akses_id', null);
                
                if ($levelId) {
                    $users = UserModel::getUsersByLevel($levelId, $perPage, $search);
                    $level = HakAksesModel::findOrFail($levelId);
                    return [
                        'users' => $users,
                        'currentLevel' => $level
                    ];
                } else {
                    return [
                        'users' => UserModel::selectData($perPage, $search),
                        'currentLevel' => null
                    ];
                }
            },
            'pengguna',
            self::ACTION_GET
        );
    }

    
    public function addData(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $levelId = $request->query('hak_akses_id', null);

                // Ambil semua level
                $hakAkses = HakAksesModel::where('isDeleted', 0)
                    ->when(Auth::user()->level->hak_akses_kode !== 'SAR', function ($query) {
                        return $query->where('hak_akses_kode', '!=', 'SAR');
                    })
                    ->get();

                $selectedLevel = null;
                if ($levelId) {
                    $selectedLevel = HakAksesModel::findOrFail($levelId);

                    // Jika user bukan SAR dan mencoba menambah user ke level SAR
                    if (Auth::user()->level->hak_akses_kode !== 'SAR' && $selectedLevel->hak_akses_kode === 'SAR') {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk menambahkan pengguna ke level Super Administrator',
                            self::HTTP_FORBIDDEN
                        );
                    }
                }

                return [
                    'hakAkses' => $hakAkses,
                    'selectedLevel' => $selectedLevel
                ];
            },
            'form tambah pengguna',
            self::ACTION_GET
        );
    }

    public function editData($id)
    {
        return $this->executeWithAuthentication(
            function ($authenticatedUser) use ($id) { 
                $targetUser = UserModel::detailData($id);
                
                // $currentUserLevel = $authenticatedUser->level ?? null;
                
                // Cek apakah user yang diedit memiliki hak akses SAR
                $targetUserIsSAR = $targetUser->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if ($targetUserIsSAR) {
                    // Cek apakah user yang login memiliki akses SAR
                    $currentUserHasSAR = $authenticatedUser->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                    
                    if (!$currentUserHasSAR) {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk mengedit pengguna dengan level Super Administrator',
                            self::HTTP_FORBIDDEN
                        );
                    }
                }
    
                // Ambil semua hak akses yang bisa ditambahkan ke user
                $availableHakAkses = HakAksesModel::where('isDeleted', 0)
                    ->when(!$currentUserHasSAR, function ($query) {
                        return $query->where('hak_akses_kode', '!=', 'SAR');
                    })
                    ->whereNotIn('hak_akses_id', $targetUser->hakAkses->pluck('hak_akses_id'))
                    ->get();
    
                return [
                    'user' => $targetUser,
                    'availableHakAkses' => $availableHakAkses,
                    'debug' => [
                        'current_user_levels' => $authenticatedUser->hakAkses->pluck('hak_akses_kode')->toArray(),
                        'current_user_has_sar' => $currentUserHasSAR ?? false
                    ]
                ];
            },
            'form edit pengguna',
            self::ACTION_GET
        );
    }

    
    public function deleteDataView($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $user = UserModel::detailData($id);

                // Cek apakah user yang dihapus memiliki hak akses SAR
                $isSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Anda tidak memiliki izin untuk menghapus pengguna dengan level Super Administrator',
                        self::HTTP_FORBIDDEN
                    );
                }

                return $user;
            },
            'konfirmasi hapus pengguna',
            self::ACTION_GET
        );
    }

    
    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request) {
                // Validasi apakah pengguna mencoba menambahkan ke level SAR
                if ($request->has('hak_akses_id')) {
                    $level = HakAksesModel::findOrFail($request->hak_akses_id);
                    if (Auth::user()->level->hak_akses_kode !== 'SAR' && $level->hak_akses_kode === 'SAR') {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk menambahkan pengguna ke level Super Administrator',
                            self::HTTP_FORBIDDEN
                        );
                    }
                }
                
                // Buat data baru
                $result = UserModel::createData($request);
                
                // Cek jika result adalah array dan memiliki kunci 'success' yang false
                if (!$result || isset($result['error']) || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    // Jika ada errors, sertakan dalam response
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal membuat pengguna';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $errors
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'pengguna',
            self::ACTION_CREATE
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($authenticatedUser) use ($request, $id) {
                // Cek apakah user yang diedit memiliki hak akses SAR
                $userToUpdate = UserModel::findOrFail($id);
                $isSAR = $userToUpdate->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if ($isSAR) {
                    $currentUserHasSAR = $authenticatedUser->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                    
                    if (!$currentUserHasSAR) {
                        return $this->errorResponse(
                            self::AUTH_FORBIDDEN,
                            'Anda tidak memiliki izin untuk mengedit pengguna dengan level Super Administrator',
                            self::HTTP_FORBIDDEN
                        );
                    }
                }
                
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
                    
                    // Update dengan data yang sudah dimergre
                    $result = UserModel::updateData($finalRequest, $id);
                } else {
                    return $this->errorResponse(
                        self::INVALID_REQUEST_FORMAT,
                        'Tidak ada data yang dikirim untuk update',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                // Check hasil update
                if (!$result || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal memperbarui pengguna';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
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
            },
            'pengguna',
            self::ACTION_UPDATE
        );
    }

    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                // Cek apakah user yang dihapus memiliki hak akses SAR
                $userToDelete = UserModel::findOrFail($id);
                $isSAR = $userToDelete->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Anda tidak memiliki izin untuk menghapus pengguna dengan level Super Administrator',
                        self::HTTP_FORBIDDEN
                    );
                }
                
                $result = UserModel::deleteData($id);
                
                // Cek jika result adalah array dan memiliki kunci 'success' yang false
                if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                    return $this->errorResponse(
                        $result['message'] ?? 'Gagal menghapus pengguna',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'pengguna',
            self::ACTION_DELETE
        );
    }


    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                return UserModel::detailData($id);
            },
            'pengguna',
            self::ACTION_GET
        );
    }
    
    
    public function addHakAkses(Request $request, $userId)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $userId) {
                if (!$request->has('hak_akses_id')) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'ID hak akses harus disediakan',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                $hakAksesId = $request->hak_akses_id;
                
                // Cek apakah mencoba menambah hak akses SAR
                $hakAkses = HakAksesModel::findOrFail($hakAksesId);
                if (Auth::user()->level->hak_akses_kode !== 'SAR' && $hakAkses->hak_akses_kode === 'SAR') {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Anda tidak memiliki izin untuk menambahkan hak akses Super Administrator',
                        self::HTTP_FORBIDDEN
                    );
                }
                
                $result = UserModel::addHakAkses($userId, $hakAksesId);
                
                // Cek jika result adalah array dan memiliki kunci 'success' yang false
                if (!$result || !isset($result['success']) || $result['success'] === false) {
                    return $this->errorResponse(
                        $result['message'] ?? 'Gagal menambahkan hak akses',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'hak akses pengguna',
            self::ACTION_CREATE
        );
    }
    
    
    public function removeHakAkses(Request $request, $userId)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $userId) {
                if (!$request->has('hak_akses_id')) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'ID hak akses harus disediakan',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                $hakAksesId = $request->hak_akses_id;
                
                // Cek apakah mencoba menghapus hak akses SAR
                $hakAkses = HakAksesModel::findOrFail($hakAksesId);
                if (Auth::user()->level->hak_akses_kode !== 'SAR' && $hakAkses->hak_akses_kode === 'SAR') {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Anda tidak memiliki izin untuk menghapus hak akses Super Administrator',
                        self::HTTP_FORBIDDEN
                    );
                }
                
                $result = UserModel::removeHakAkses($userId, $hakAksesId);
                
                // Cek jika result adalah array dan memiliki kunci 'success' yang false
                if (!$result || !isset($result['success']) || $result['success'] === false) {
                    return $this->errorResponse(
                        $result['message'] ?? 'Gagal menghapus hak akses',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'hak akses pengguna',
            self::ACTION_DELETE
        );
    }

    public function getAvailableHakAkses($userId)
    {
        return $this->executeWithAuthentication(
            function () use ($userId) {
                $user = UserModel::detailData($userId);
                
                // Ambil semua hak akses yang bisa ditambahkan ke user
                $availableHakAkses = HakAksesModel::where('isDeleted', 0)
                    ->when(Auth::user()->level->hak_akses_kode !== 'SAR', function ($query) {
                        return $query->where('hak_akses_kode', '!=', 'SAR');
                    })
                    ->whereNotIn('hak_akses_id', $user->hakAkses->pluck('hak_akses_id'))
                    ->get();
                
                return $availableHakAkses;
            },
            'hak akses tersedia',
            self::ACTION_GET
        );
    }
}