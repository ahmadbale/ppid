<?php 

namespace Modules\Sisfo\App\Http\Controllers\Api\MenuManagement;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\HakAksesModel;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiMenuManagementController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $result = WebMenuModel::selectData();

                if (!$result['success']) {
                    return $this->errorResponse(
                        'LOAD_DATA_FAILED',
                        $result['message'],
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                return [
                    'breadcrumb' => $result['data']['breadcrumb'],
                    'page' => $result['data']['page'],
                    'menus' => $result['data']['menus'],
                    'activeMenu' => $result['data']['activeMenu'],
                    'menusByJenis' => $result['data']['menusByJenis'],
                    'jenisMenuList' => $result['data']['jenisMenuList'],
                    'levels' => $result['data']['levels'],
                    'groupMenusGlobal' => $result['data']['groupMenusGlobal'],
                    'groupMenusFromWebMenu' => $result['data']['groupMenusFromWebMenu'],
                    'nonGroupMenus' => $result['data']['nonGroupMenus'],
                    'userHakAksesKode' => $result['data']['userHakAksesKode'] ?? null
                ];
            },
            'menu management',
            self::ACTION_GET
        );
    }

    public function addData($hakAksesId, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($hakAksesId, $request) {
                try {
                    $result = WebMenuModel::getAddData($hakAksesId);

                    if (!$result['success']) {
                        return $this->errorResponse(
                            'LOAD_DATA_FAILED',
                            $result['message'],
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }

                    return [
                        'breadcrumb' => $result['data']['breadcrumb'],
                        'page' => $result['data']['page'],
                        'activeMenu' => $result['data']['activeMenu'],
                        'level' => $result['data']['level'],
                        'menuGlobal' => $result['data']['menuGlobal'],
                        'existingMenus' => $result['data']['existingMenus'],
                        'createdMenusCount' => count($result['data']['existingMenus']),
                        'percentage' => $result['data']['menuGlobal']->count() > 0 
                            ? round((count($result['data']['existingMenus']) / $result['data']['menuGlobal']->count()) * 100, 1) 
                            : 0
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        'LOAD_SET_MENU_FAILED',
                        'Error loading set menu data: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_GET
        );
    }
    
    public function createData(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                try {
                    $result = WebMenuModel::createData($request);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            'CREATE_FAILED',
                            $result['message'] ?? 'Gagal membuat menu',
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            $result['errors'] ?? null
                        );
                    }
                    
                    return [
                        'message' => $result['message'],
                        'data' => $result['data'] ?? null
                    ];
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
                        'Terjadi kesalahan saat membuat menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_CREATE
        );
    }

    public function editData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($id, $request) {
                try {
                    $result = WebMenuModel::getEditData($id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::RESOURCE_NOT_FOUND,
                            $result['message'] ?? 'Menu tidak ditemukan',
                            self::HTTP_NOT_FOUND
                        );
                    }
                    
                    return [
                        'menu' => $result['menu'],
                        'parentMenus' => $result['parentMenus']
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat mengambil data edit menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_GET
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthentication(
            function () use ($request, $id) {
                try {
                    $result = WebMenuModel::updateData($request, $id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            'UPDATE_FAILED',
                            $result['message'] ?? 'Gagal memperbarui menu',
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            $result['errors'] ?? null
                        );
                    }
                    
                    return [
                        'message' => $result['message'],
                        'data' => $result['data']
                    ];
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
                        'Terjadi kesalahan saat memperbarui menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_UPDATE
        );
    }

    public function detailData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($id, $request) {
                try {
                    $result = WebMenuModel::detailData($id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::RESOURCE_NOT_FOUND,
                            $result['message'] ?? 'Menu tidak ditemukan',
                            self::HTTP_NOT_FOUND
                        );
                    }
                    
                    return [
                        'menu' => $result['menu']
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat mengambil detail menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_GET
        );
    }

    public function deleteData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($id, $request) {
                try {
                    $result = WebMenuModel::deleteData($id);
                    
                    if (!$result['success']) {
                        // Handle berbagai jenis error delete
                        if (strpos($result['message'], 'SAR') !== false) {
                            return $this->errorResponse(
                                'PERMISSION_DENIED',
                                $result['message'],
                                self::HTTP_FORBIDDEN
                            );
                        }
                        
                        if (strpos($result['message'], 'submenu yang masih aktif') !== false) {
                            return $this->errorResponse(
                                'HAS_ACTIVE_CHILDREN',
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

                    return [
                        'message' => $result['message'],
                        'deleted_item' => [
                            'web_menu_id' => $result['data']->web_menu_id,
                            'menu_name' => $result['data']->getDisplayName(),
                            'deleted_at' => now()
                        ]
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        'DELETE_ERROR',
                        'Terjadi kesalahan saat menghapus menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_DELETE
        );
    }

    public function reorder(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                try {
                    $data = $request->input('data', []);
                    
                    if (empty($data)) {
                        return $this->errorResponse(
                            'INVALID_DATA',
                            'Data reorder tidak valid atau kosong',
                            self::HTTP_BAD_REQUEST
                        );
                    }

                    $result = WebMenuModel::reorderMenus($data);
                    
                    if (!$result['success']) {
                        // Handle error SAR permission
                        if (strpos($result['message'], 'SAR') !== false) {
                            return $this->errorResponse(
                                'PERMISSION_DENIED',
                                $result['message'],
                                self::HTTP_FORBIDDEN
                            );
                        }
                        
                        return $this->errorResponse(
                            'REORDER_FAILED',
                            $result['message'] ?? 'Gagal menyusun ulang menu',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }
                    
                    return [
                        'message' => $result['message']
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat menyusun ulang menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_UPDATE
        );
    }
    
    public function getParentMenus($hakAksesId, Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($hakAksesId, $request) {
                try {
                    $excludeId = $request->input('exclude_id');
                    $result = WebMenuModel::getParentMenusData($hakAksesId, $excludeId);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            'LOAD_PARENT_MENUS_FAILED',
                            $result['message'] ?? 'Gagal mengambil parent menu',
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }
                    
                    return [
                        'parentMenus' => $result['parentMenus']
                    ];
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat mengambil parent menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_GET
        );
    }

}