<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\MenuManagement;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Illuminate\Validation\ValidationException;

class ApiWebMenuGlobalController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $kategori = $request->query('kategori', '');
                
                //Handle filter kategori
                if (!empty($kategori)) {
                    $data = WebMenuGlobalModel::getMenusByKategori($kategori);
                    
                    // Filter search jika ada
                    if (!empty($search)) {
                        $data = $data->filter(function($menu) use ($search) {
                            return stripos($menu->wmg_nama_default, $search) !== false ||
                                   stripos($menu->wmg_kategori_menu, $search) !== false ||
                                   ($menu->WebMenuUrl && stripos($menu->WebMenuUrl->wmu_nama, $search) !== false);
                        });
                    }
                } else {
                    $data = WebMenuGlobalModel::selectData(null, $search);
                }
                
                return [
                    'webMenuGlobals' => $data,
                    'search' => $search,
                    'kategori' => $kategori,
                    'filter_info' => [
                        'applied_kategori' => $kategori ?: 'Semua',
                        'total_results' => $data->count(),
                        'search_term' => $search
                    ],
                    'breadcrumb' => [
                        'title' => 'Manajemen Menu Global',
                        'list' => ['Home', 'Manajemen Menu Global']
                    ],
                    'page' => [
                        'title' => 'Daftar Menu Global'
                    ]
                ];
            },
            'menu global',
            self::ACTION_GET
        );
    }

    //Method getData untuk pagination/filter
    public function getData(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $kategori = $request->query('kategori', '');
                $perPage = $request->query('per_page', 10);
                
                // method yang sudah ada di model
                if (!empty($kategori)) {
                    // Filter berdasarkan kategori spesifik
                    $data = WebMenuGlobalModel::getMenusByKategori($kategori);
                    
                    // Jika ada search, filter lagi berdasarkan search
                    if (!empty($search)) {
                        $data = $data->filter(function($menu) use ($search) {
                            return stripos($menu->wmg_nama_default, $search) !== false ||
                                   stripos($menu->wmg_kategori_menu, $search) !== false ||
                                   ($menu->WebMenuUrl && stripos($menu->WebMenuUrl->wmu_nama, $search) !== false);
                        });
                    }
                } else {
                    // Tanpa filter kategori, gunakan method selectData yang sudah ada
                    $data = WebMenuGlobalModel::selectData(null, $search);
                }
                
                // Convert ke collection jika belum
                if (!$data instanceof \Illuminate\Support\Collection) {
                    $data = collect($data);
                }
                
                // Implementasi pagination manual jika diperlukan
                if ($perPage && $perPage > 0) {
                    $currentPage = $request->query('page', 1);
                    $offset = ($currentPage - 1) * $perPage;
                    
                    $paginatedData = $data->slice($offset, $perPage)->values();
                    
                    return [
                        'data' => $paginatedData,
                        'pagination' => [
                            'current_page' => (int) $currentPage,
                            'per_page' => (int) $perPage,
                            'total' => $data->count(),
                            'last_page' => ceil($data->count() / $perPage),
                            'from' => $offset + 1,
                            'to' => min($offset + $perPage, $data->count())
                        ]
                    ];
                }
                
                return $data;
            },
            'menu global',
            self::ACTION_GET
        );
    }

    //  Method addData untuk form data
    public function addData(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $parentMenus = WebMenuGlobalModel::getParentMenus('Group Menu');
                $menuUrls = WebMenuUrlModel::with('application')
                    ->where('isDeleted', 0)
                    ->get();
                
                return [
                    'form_action' => 'create',
                    'parentMenus' => $parentMenus,
                    'menuUrls' => $menuUrls,
                    'title' => 'Tambah Menu Global Baru'
                ];
            },
            'add menu global form',
            self::ACTION_GET
        );
    }

    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request) {
                try {
                    // Support nested dan flat format
                    $webMenuGlobalData = $request->input('web_menu_global');
                    
                    if ($webMenuGlobalData) {
                        // Format nested dari web form
                        $wmgNamaDefault = $webMenuGlobalData['wmg_nama_default'] ?? null;
                        $wmgKategoriMenu = $webMenuGlobalData['wmg_kategori_menu'] ?? 'Menu Biasa';
                        $fkWebMenuUrl = $webMenuGlobalData['fk_web_menu_url'] ?? null;
                        $wmgParentId = $webMenuGlobalData['wmg_parent_id'] ?? null;
                        $wmgStatusMenu = $webMenuGlobalData['wmg_status_menu'] ?? 'aktif';
                    } else {
                        // Format flat dari API
                        $wmgNamaDefault = $request->input('wmg_nama_default') ?? $request->json('wmg_nama_default');
                        $wmgKategoriMenu = $request->input('wmg_kategori_menu') ?? $request->json('wmg_kategori_menu') ?? 'Menu Biasa';
                        $fkWebMenuUrl = $request->input('fk_web_menu_url') ?? $request->json('fk_web_menu_url');
                        $wmgParentId = $request->input('wmg_parent_id') ?? $request->json('wmg_parent_id');
                        $wmgStatusMenu = $request->input('wmg_status_menu') ?? $request->json('wmg_status_menu') ?? 'aktif';
                    }

                    // Validasi data dasar
                    if (empty($wmgNamaDefault) && $wmgNamaDefault !== '0') {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Nama default menu (wmg_nama_default) harus diisi',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    // Validasi berdasarkan kategori menu
                    if ($wmgKategoriMenu === 'Sub Menu' && empty($wmgParentId)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Sub menu harus memiliki menu induk (wmg_parent_id)',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    if ($wmgKategoriMenu !== 'Group Menu' && empty($fkWebMenuUrl)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Menu URL (fk_web_menu_url) harus diisi untuk ' . $wmgKategoriMenu,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    //  format ke nested untuk model
                    $request->merge([
                        'web_menu_global' => [
                            'wmg_nama_default' => $wmgNamaDefault,
                            'wmg_kategori_menu' => $wmgKategoriMenu,
                            'fk_web_menu_url' => ($wmgKategoriMenu === 'Group Menu') ? null : 
                                (($fkWebMenuUrl === '' || $fkWebMenuUrl === 'null') ? null : $fkWebMenuUrl),
                            'wmg_parent_id' => ($wmgKategoriMenu === 'Sub Menu') ? $wmgParentId : null,
                            'wmg_status_menu' => $wmgStatusMenu
                        ]
                    ]);

                    // Validasi data menggunakan model
                    WebMenuGlobalModel::validasiData($request);

                    // Buat data baru
                    $result = WebMenuGlobalModel::createData($request);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::SERVER_ERROR,
                            $result['message'] ?? 'Gagal membuat menu global',
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }

                    return $result['data'];
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        'Validasi gagal: ' . collect($e->errors())->flatten()->implode(', '),
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $e->errors()
                    );
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat membuat menu global: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu global',
            self::ACTION_CREATE
        );
    }

    //Method editData untuk form edit
    public function editData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($id, $request) {
                try {
                    $webMenuGlobal = WebMenuGlobalModel::with(['WebMenuUrl.application', 'parentMenu'])
                        ->findOrFail($id);
                    
                    $parentMenus = WebMenuGlobalModel::getParentMenus('Group Menu');
                    $menuUrls = WebMenuUrlModel::with('application')
                        ->where('isDeleted', 0)
                        ->get();
                    
                    return [
                        'webMenuGlobal' => $webMenuGlobal,
                        'parentMenus' => $parentMenus,
                        'menuUrls' => $menuUrls,
                        'title' => 'Edit Menu Global'
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::RESOURCE_NOT_FOUND,
                        'Menu global tidak ditemukan',
                        self::HTTP_NOT_FOUND
                    );
                }
            },
            'menu global',
            self::ACTION_GET
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request, $id) {
                try {
                    // Ambil data existing dari database
                    $existingData = WebMenuGlobalModel::find($id);
                    
                    if (!$existingData) {
                        return $this->errorResponse(
                            self::RESOURCE_NOT_FOUND,
                            'Menu global tidak ditemukan',
                            self::HTTP_NOT_FOUND
                        );
                    }

                    //  Support nested dan flat format
                    $webMenuGlobalData = $request->input('web_menu_global');
                    
                    if ($webMenuGlobalData) {
                        // Format nested dari web form
                        $wmgNamaDefault = $webMenuGlobalData['wmg_nama_default'] ?? $existingData->wmg_nama_default;
                        $wmgKategoriMenu = $webMenuGlobalData['wmg_kategori_menu'] ?? $existingData->wmg_kategori_menu;
                        $fkWebMenuUrl = $webMenuGlobalData['fk_web_menu_url'] ?? $existingData->fk_web_menu_url;
                        $wmgParentId = $webMenuGlobalData['wmg_parent_id'] ?? $existingData->wmg_parent_id;
                        $wmgStatusMenu = $webMenuGlobalData['wmg_status_menu'] ?? $existingData->wmg_status_menu;
                    } else {
                        // Format flat dari API - merge dengan data existing
                        $wmgNamaDefault = $request->input('wmg_nama_default') ?? $existingData->wmg_nama_default;
                        $wmgKategoriMenu = $request->input('wmg_kategori_menu') ?? $existingData->wmg_kategori_menu;
                        $fkWebMenuUrl = $request->input('fk_web_menu_url') ?? $existingData->fk_web_menu_url;
                        $wmgParentId = $request->input('wmg_parent_id') ?? $existingData->wmg_parent_id;
                        $wmgStatusMenu = $request->input('wmg_status_menu') ?? $existingData->wmg_status_menu;
                    }

                    // Validasi data dasar
                    if (empty($wmgNamaDefault) && $wmgNamaDefault !== '0') {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Nama default menu (wmg_nama_default) harus diisi',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    // Validasi berdasarkan kategori menu
                    if ($wmgKategoriMenu === 'Sub Menu' && empty($wmgParentId)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Sub menu harus memiliki menu induk (wmg_parent_id)',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    if ($wmgKategoriMenu !== 'Group Menu' && empty($fkWebMenuUrl)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Menu URL (fk_web_menu_url) harus diisi untuk ' . $wmgKategoriMenu,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    //  Selalu format ke nested untuk model
                    $request->merge([
                        'web_menu_global' => [
                            'wmg_nama_default' => $wmgNamaDefault,
                            'wmg_kategori_menu' => $wmgKategoriMenu,
                            'fk_web_menu_url' => ($wmgKategoriMenu === 'Group Menu') ? null : 
                                (($fkWebMenuUrl === '' || $fkWebMenuUrl === 'null') ? null : $fkWebMenuUrl),
                            'wmg_parent_id' => ($wmgKategoriMenu === 'Sub Menu') ? $wmgParentId : null,
                            'wmg_status_menu' => $wmgStatusMenu
                        ]
                    ]);

                    // Validasi menggunakan model
                    WebMenuGlobalModel::validasiData($request);

                    // Update data
                    $result = WebMenuGlobalModel::updateData($request, $id);

                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::SERVER_ERROR,
                            $result['message'] ?? 'Gagal memperbarui menu global',
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }

                    return $result['data'];
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        'Validasi gagal: ' . collect($e->errors())->flatten()->implode(', '),
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $e->errors()
                    );
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat memperbarui menu global: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu global',
            self::ACTION_UPDATE
        );
    }
 
    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    $webMenuGlobal = WebMenuGlobalModel::detailData($id);
                    
                    return [
                        'webMenuGlobal' => $webMenuGlobal,
                        'title' => 'Detail Menu Global'
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::RESOURCE_NOT_FOUND,
                        'Menu global tidak ditemukan',
                        self::HTTP_NOT_FOUND
                    );
                }
            },
            'menu global',
            self::ACTION_GET
        );
    }

    // Method deleteDataView untuk konfirmasi delete
    public function deleteDataView($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($id, $request) {
                try {
                    $webMenuGlobal = WebMenuGlobalModel::with(['WebMenuUrl.application', 'parentMenu', 'children'])
                        ->findOrFail($id);
                    
                    return [
                        'webMenuGlobal' => $webMenuGlobal,
                        'title' => 'Konfirmasi Hapus Menu Global'
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::RESOURCE_NOT_FOUND,
                        'Menu global tidak ditemukan',
                        self::HTTP_NOT_FOUND
                    );
                }
            },
            'menu global',
            self::ACTION_GET
        );
    }
    
    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    $result = WebMenuGlobalModel::deleteData($id);
                    
                    if (!$result['success']) {
                        //  Handle berbagai jenis error delete
                        if (strpos($result['message'], 'memiliki submenu') !== false) {
                            return $this->errorResponse(
                                'HAS_CHILDREN',
                                $result['message'],
                                self::HTTP_BAD_REQUEST
                            );
                        }
                        
                        if (strpos($result['message'], 'sedang digunakan') !== false) {
                            return $this->errorResponse(
                                'MENU_IN_USE',
                                $result['message'],
                                self::HTTP_BAD_REQUEST
                            );
                        }
                        
                        return $this->errorResponse(
                            'DELETE_FAILED',
                            $result['message'],
                            self::HTTP_BAD_REQUEST
                        );
                    }

                    // SUCCESS response
                    return [
                        'message' => $result['message'],
                        'deleted_item' => [
                            'web_menu_global_id' => $result['data']->web_menu_global_id,
                            'wmg_nama_default' => $result['data']->wmg_nama_default,
                            'deleted_at' => $result['data']->deleted_at
                        ]
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        'DELETE_ERROR',
                        'Terjadi kesalahan saat menghapus: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu global',
            self::ACTION_DELETE
        );
    }

    public function getMenuUrl()
    {
        return $this->executeWithAuthentication(
            function () {
                $menuUrls = WebMenuUrlModel::with('application')->where('isDeleted', 0)->get();
                return $menuUrls;
            },
            'menu URL',
            self::ACTION_GET
        );
    }
}