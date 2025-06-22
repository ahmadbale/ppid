<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\HakAkses;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiSetHakAksesController extends BaseApiController
{
    public function index()
    {
        return $this->executeWithAuthentication(
            function() {
                //  Validasi SAR
                $user = auth()->user();
                $hasSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if (!$hasSAR) {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Hanya Super Administrator yang dapat mengakses pengaturan hak akses',
                        self::HTTP_FORBIDDEN
                    );
                }

                // Mengambil data dari model
                $result = SetHakAksesModel::selectData();
                $levelUsers = $result['data'];

                return [
                    'success' => true,
                    'message' => 'Data pengaturan hak akses berhasil diambil',
                    'data' => [
                        'levelUsers' => $levelUsers,
                        'breadcrumb' => [
                            'title' => 'Pengaturan Hak Akses',
                            'list' => ['Home', 'Hak Akses']
                        ],
                        'page' => [
                            'title' => 'Pengaturan Hak Akses'
                        ]
                    ]
                ];
            },
            'pengaturan hak akses',
            self::ACTION_GET
        );
    }

    public function getData()
    {
        return $this->executeWithAuthentication(
            function() {
                
                $user = auth()->user();
                $hasSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if (!$hasSAR) {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Hanya Super Administrator yang dapat mengakses pengaturan hak akses',
                        self::HTTP_FORBIDDEN
                    );
                }

            
                $result = SetHakAksesModel::selectData();
                
                return [
                    'success' => true,
                    'message' => 'Data berhasil diambil',
                    'data' => $result['data']
                ];
            },
            'data pengaturan hak akses',
            self::ACTION_GET
        );
    }

    public function addData()
    {
        return $this->executeWithAuthentication(
            function() {
                //  Validasi SAR
                $user = auth()->user();
                $hasSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if (!$hasSAR) {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Hanya Super Administrator yang dapat mengakses pengaturan hak akses',
                        self::HTTP_FORBIDDEN
                    );
                }

                // 
                return [
                    'form_action' => 'create',
                    'available_levels' => HakAksesModel::where('isDeleted', 0)->get(),
                    'available_menus' => WebMenuModel::where('isDeleted', 0)->get()
                ];
            },
            'form tambah hak akses',
            self::ACTION_GET
        );
    }

    public function editData($param1, $param2 = null)
    {
        return $this->executeWithAuthentication(
            function() use ($param1, $param2) {
                // Validasi SAR
                $user = auth()->user();
                $hasSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if (!$hasSAR) {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Hanya Super Administrator yang dapat mengakses pengaturan hak akses',
                        self::HTTP_FORBIDDEN
                    );
                }

              
                // Jika param2 tidak ada, maka ini adalah permintaan hak akses berdasarkan level
                if ($param2 === null) {
                    $hak_akses_kode = $param1;
                    $menuData = SetHakAksesModel::getHakAksesData($hak_akses_kode);
                    
                    return [
                        'success' => true,
                        'message' => 'Data hak akses berdasarkan level berhasil diambil',
                        'data' => $menuData
                    ];
                }
                // Jika param2 ada, maka ini adalah permintaan hak akses spesifik user dan menu
                else {
                    $pengakses_id = $param1;
                    $menu_id = $param2;
                    $hakAkses = SetHakAksesModel::getHakAksesData($pengakses_id, $menu_id);
                    
                    return [
                        'success' => true,
                        'message' => 'Data hak akses spesifik berhasil diambil',
                        'data' => $hakAkses
                    ];
                }
            },
            'edit hak akses',
            self::ACTION_GET
        );
    }

    public function updateData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request) {
                $hasSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if (!$hasSAR) {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Hanya Super Administrator yang dapat mengakses pengaturan hak akses',
                        self::HTTP_FORBIDDEN
                    );
                }

                try {
                    $isLevel = $request->has('hak_akses_kode');
                    $result = SetHakAksesModel::updateData($request->all(), $isLevel);

                
                    return [
                        'result' => $result,
                        'update_type' => $isLevel ? 'Hak akses Kode' : 'individu(user_id)',
                    ];

                } catch (\Exception $e) {
                    return $this->errorResponse(
                        'UPDATE_ERROR',
                        'Terjadi kesalahan: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'pengaturan hak akses',
            self::ACTION_UPDATE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function() use ($id) {
                // Validasi SAR
                $user = auth()->user();
                $hasSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
                
                if (!$hasSAR) {
                    return $this->errorResponse(
                        self::AUTH_FORBIDDEN,
                        'Hanya Super Administrator yang dapat mengakses pengaturan hak akses',
                        self::HTTP_FORBIDDEN
                    );
                }

                try {
                    $hakAkses = SetHakAksesModel::find($id);
                    
                    if (!$hakAkses) {
                        return $this->errorResponse(
                            'DATA_NOT_FOUND',
                            'Data hak akses tidak ditemukan',
                            self::HTTP_NOT_FOUND
                        );
                    }

                    return [
                        'success' => true,
                        'message' => 'Detail hak akses berhasil diambil',
                        'data' => $hakAkses
                    ];

                } catch (\Exception $e) {
                    return $this->errorResponse(
                        'DETAIL_ERROR',
                        'Terjadi kesalahan: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'detail hak akses',
            self::ACTION_GET
        );
    }
}