<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\UserModel;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
                    
                    if (!$result || isset($result['error'])) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Gagal membuat pengguna',
                            422
                        );
                    }
                    
                    return $result['data'] ?? $result;
                    
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        $e->getMessage(),
                        422
                    );
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
                    
                    // Update data
                    $result = UserModel::updateData($request, $id);
                    
                    if (!$result || isset($result['error'])) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Gagal mengupdate pengguna',
                            422
                        );
                    }
                    
                    return $result['data'] ?? $result;
                    
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        $e->getMessage(),
                        422
                    );
                } catch (\Exception $e) {
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
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            $result['message'] ?? 'Gagal menambahkan hak akses',
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
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            $result['message'] ?? 'Gagal menghapus hak akses',
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