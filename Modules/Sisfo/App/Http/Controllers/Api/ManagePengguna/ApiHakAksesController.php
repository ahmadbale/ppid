<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiHakAksesController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                // Ambil app_key dari token JWT atau request
                $appKey = $this->getAppKeyFromToken() ?? $request->query('app_key', 'app ppid');
                
                // Validasi app_key
                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                
                return HakAksesModel::selectData($perPage, $search, $appKey);
            },
            'hak akses',
            self::ACTION_GET
        );
    }

    
    public function getData(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $appKey = $this->getAppKeyFromToken() ?? $request->query('app_key', 'app ppid');
                
                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                
                $result = HakAksesModel::selectData($perPage, $search, $appKey);
                
                return [
                    'level' => $result,
                    'search' => $search,
                    'currentPage' => $result->currentPage(),
                    'lastPage' => $result->lastPage(),
                    'total' => $result->total()
                ];
            },
            'data hak akses',
            self::ACTION_GET
        );
    }

    
    public function addData(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                // Return struktur data yang diperlukan untuk form tambah
                return [
                    'form_data' => [
                        'action' => 'create',
                        'method' => 'POST',
                        'fields' => [
                            'hak_akses_kode' => [
                                'type' => 'text',
                                'required' => true,
                                'maxlength' => 50,
                                'label' => 'Kode Level'
                            ],
                            'hak_akses_nama' => [
                                'type' => 'text', 
                                'required' => true,
                                'maxlength' => 255,
                                'label' => 'Nama Level'
                            ]
                        ]
                    ]
                ];
            },
            'form tambah hak akses',
            self::ACTION_GET
        );
    }


    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request) {
                // Ambil data dari request, baik JSON maupun form-data
                $hakAksesKode = $request->input('hak_akses_kode') ?? $request->json('hak_akses_kode');
                $hakAksesNama = $request->input('hak_akses_nama') ?? $request->json('hak_akses_nama');
    
                // Validasi data dasar
                if (empty($hakAksesKode) || empty($hakAksesNama)) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Data hak_akses_kode dan hak_akses_nama harus diisi',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
    
                // Struktur ulang data untuk m_hak_akses format
                $request->merge([
                    'm_hak_akses' => [
                        'hak_akses_kode' => $hakAksesKode,
                        'hak_akses_nama' => $hakAksesNama
                    ]
                ]);
    
                // Validasi menggunakan model
                HakAksesModel::validasiData($request);
                
                // Create data
                $result = HakAksesModel::createData($request);
                
                if (!$result || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal membuat hak akses';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $errors
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'hak akses',
            self::ACTION_CREATE
        );
    }

    
    public function editData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                $level = HakAksesModel::detailData($id);
                
                return [
                    'level' => $level,
                    'form_data' => [
                        'action' => 'update',
                        'method' => 'POST',
                        'current_values' => [
                            'hak_akses_kode' => $level->hak_akses_kode,
                            'hak_akses_nama' => $level->hak_akses_nama
                        ],
                        'fields' => [
                            'hak_akses_kode' => [
                                'type' => 'text',
                                'required' => true,
                                'maxlength' => 50,
                                'label' => 'Kode Level'
                            ],
                            'hak_akses_nama' => [
                                'type' => 'text',
                                'required' => true,
                                'maxlength' => 255,
                                'label' => 'Nama Level'
                            ]
                        ]
                    ]
                ];
            },
            'form edit hak akses',
            self::ACTION_GET
        );
    }


    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                // Handle different request formats
                if ($request->has('hak_akses_kode') && $request->has('hak_akses_nama')) {
                    // Format API langsung
                    $request->merge([
                        'm_hak_akses' => [
                            'hak_akses_kode' => $request->input('hak_akses_kode'),
                            'hak_akses_nama' => $request->input('hak_akses_nama')
                        ]
                    ]);
                } elseif (!$request->has('m_hak_akses')) {
                    // Format tidak valid
                    return $this->errorResponse(
                        self::INVALID_REQUEST_FORMAT,
                        'Harap sertakan hak_akses_kode dan hak_akses_nama',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                // Validasi data
                HakAksesModel::validasiData($request);

                // Update data
                $result = HakAksesModel::updateData($request, $id);

                if (!$result || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal memperbarui hak akses';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $errors
                    );
                }

                return $result['data'] ?? $result;
            },
            'hak akses',
            self::ACTION_UPDATE
        );
    }


    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                $level = HakAksesModel::detailData($id);
                
                return [
                    'level' => $level,
                    'formatted_data' => [
                        'hak_akses_id' => $level->hak_akses_id,
                        'hak_akses_kode' => $level->hak_akses_kode,
                        'hak_akses_nama' => $level->hak_akses_nama,
                        'created_at' => $level->created_at ? $level->created_at->format('d-m-Y H:i:s') : null,
                        'created_by' => $level->created_by,
                        'updated_at' => $level->updated_at ? $level->updated_at->format('d-m-Y H:i:s') : null,
                        'updated_by' => $level->updated_by
                    ]
                ];
            },
            'detail hak akses',
            self::ACTION_GET
        );
    }


    public function deleteDataView($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                $level = HakAksesModel::detailData($id);
                
                return [
                    'level' => $level,
                    'confirmation' => [
                        'title' => 'Konfirmasi Hapus Level',
                        'message' => "Apakah Anda yakin ingin menghapus level '{$level->hak_akses_nama}'?",
                        'warning' => 'Data yang dihapus tidak dapat dikembalikan',
                        'action' => 'delete',
                        'method' => 'DELETE'
                    ]
                ];
            },
            'konfirmasi hapus hak akses',
            self::ACTION_GET
        );
    }
    

    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                $result = HakAksesModel::deleteData($id);
                
                if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                    return $this->errorResponse(
                        $result['message'] ?? 'Gagal menghapus hak akses',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'hak akses',
            self::ACTION_DELETE
        );
    }

    // Helper method untuk mengambil app_key dari token
    protected function getAppKeyFromToken()
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            return $payload->get('app_key');
        } catch (\Exception $e) {
            return null;
        }
    }
}