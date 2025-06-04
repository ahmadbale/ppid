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
                // Dapatkan semua level dari database
                $levels = HakAksesModel::where('isDeleted', 0)->get();

                // Gunakan nama level sebagai daftar jenis menu
                $jenisMenuList = $levels->pluck('hak_akses_nama', 'hak_akses_kode')->toArray();

                // Dapatkan menu dikelompokkan berdasarkan level
                $menusByJenis = [];
                foreach ($levels as $level) {
                    $hakAksesId = $level->hak_akses_id;
                    $menusByJenis[$level->hak_akses_kode] = [
                        'nama' => $level->hak_akses_nama,
                        'menus' => WebMenuModel::where('fk_m_hak_akses', $hakAksesId)
                            ->whereNull('wm_parent_id')
                            ->where('isDeleted', 0)
                            ->orderBy('wm_urutan_menu')
                            ->with(['children' => function ($query) use ($hakAksesId) {
                                $query->where('fk_m_hak_akses', $hakAksesId)
                                    ->where('isDeleted', 0)
                                    ->orderBy('wm_urutan_menu');
                            }, 'WebMenuGlobal.WebMenuUrl', 'Level'])
                            ->get()
                    ];
                }

                // Get group menus for dropdown
                $groupMenusGlobal = WebMenuGlobalModel::whereNull('fk_web_menu_url')
                    ->where('isDeleted', 0)
                    ->orderBy('wmg_nama_default')
                    ->get();

                // Get group menus from web_menu for sub menu dropdown
                $groupMenusFromWebMenu = WebMenuModel::whereHas('WebMenuGlobal', function ($query) {
                    $query->whereNull('fk_web_menu_url');
                })
                    ->whereNull('wm_parent_id')
                    ->where('isDeleted', 0)
                    ->where('wm_status_menu', 'aktif')
                    ->with('WebMenuGlobal')
                    ->orderBy('wm_urutan_menu')
                    ->get();

                // Get non-group menus
                $nonGroupMenus = WebMenuGlobalModel::whereNotNull('fk_web_menu_url')
                    ->where('isDeleted', 0)
                    ->with('WebMenuUrl.application')
                    ->orderBy('wmg_nama_default')
                    ->get();

                // Get regular menus
                $menus = WebMenuModel::getMenusWithChildren();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'jenis_menu_list' => $jenisMenuList,
                        'menus_by_jenis' => $menusByJenis,
                        'group_menus_global' => $groupMenusGlobal,
                        'group_menus_from_web_menu' => $groupMenusFromWebMenu,
                        'non_group_menus' => $nonGroupMenus,
                        'menus' => $menus,
                        'levels' => $levels
                    ]
                ]);
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
                    // Use the existing model method to create menu
                    $result = WebMenuModel::createData($request);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            $result['message'] ?? self::SERVER_ERROR,
                            isset($result['errors']) ? $result['errors'] : null,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Menu berhasil dibuat',
                        'data' => $result['data']
                    ]);
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        $e->getMessage(),
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

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthentication(
            function () use ($request, $id) {
                try {
                    // Use the existing model method to update menu
                    $result = WebMenuModel::updateData($request, $id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            $result['message'] ?? self::SERVER_ERROR,
                            isset($result['errors']) ? $result['errors'] : null,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Menu berhasil diperbarui',
                        'data' => $result['data']
                    ]);
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        $e->getMessage(),
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

    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    $result = WebMenuModel::deleteData($id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Gagal menghapus menu',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Menu berhasil dihapus'
                    ]);
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat menghapus menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_DELETE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    $result = WebMenuModel::getDetailData($id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Menu tidak ditemukan',
                            null,
                            self::HTTP_NOT_FOUND
                        );
                    }
                    
                    return response()->json([
                        'success' => true,
                        'data' => $result['menu']
                    ]);
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

    public function editData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    $result = WebMenuModel::getEditData($id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Menu tidak ditemukan',
                            null,
                            self::HTTP_NOT_FOUND
                        );
                    }
                    
                    return response()->json([
                        'success' => true,
                        'data' => $result
                    ]);
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
    
    public function getParentMenus($hakAksesId)
    {
        return $this->executeWithAuthentication(
            function () use ($hakAksesId) {
                try {
                    $excludeId = request()->input('exclude_id');
                    $parentMenus = WebMenuModel::getParentMenusByLevel($hakAksesId, $excludeId);
                    
                    return response()->json([
                        'success' => true,
                        'parentMenus' => $parentMenus
                    ]);
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
    
    public function reorder(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                try {
                    $result = WebMenuModel::reorderMenus($request->input('data', []));
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            $result['message'] ?? 'Gagal menyusun ulang menu',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Urutan menu berhasil disimpan'
                    ]);
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
    
    public function setMenu($hakAksesId)
    {
        return $this->executeWithAuthentication(
            function () use ($hakAksesId) {
                try {
                    $level = HakAksesModel::findOrFail($hakAksesId);
                    $menuGlobal = WebMenuGlobalModel::where('isDeleted', 0)
                        ->orderBy('wmg_urutan_menu')
                        ->get();

                    // Get existing menus for this level
                    $existingMenus = [];
                    $webMenus = WebMenuModel::where('fk_m_hak_akses', $hakAksesId)
                        ->where('isDeleted', 0)
                        ->with('hakAkses')
                        ->get();

                    foreach ($webMenus as $webMenu) {
                        $permissions = [];
                        foreach ($webMenu->hakAkses as $hakAkses) {
                            if ($hakAkses->ha_menu == 1) $permissions[] = 'menu';
                            if ($hakAkses->ha_view == 1) $permissions[] = 'view';
                            if ($hakAkses->ha_create == 1) $permissions[] = 'create';
                            if ($hakAkses->ha_update == 1) $permissions[] = 'update';
                            if ($hakAkses->ha_delete == 1) $permissions[] = 'delete';
                        }

                        $existingMenus[$webMenu->fk_web_menu_global] = [
                            'web_menu_id' => $webMenu->web_menu_id,
                            'alias' => $webMenu->wm_menu_nama,
                            'status' => $webMenu->wm_status_menu,
                            'permissions' => $permissions
                        ];
                    }

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'level' => $level,
                            'menuGlobal' => $menuGlobal,
                            'existingMenus' => $existingMenus
                        ]
                    ]);
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat mengambil data set menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_GET
        );
    }
    
    public function storeSetMenu(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                DB::beginTransaction();
                try {
                    $hakAksesId = $request->input('hak_akses_id');
                    $menus = $request->input('menus');

                    // Get user IDs for this level
                    $userIds = DB::table('set_user_hak_akses')
                        ->where('fk_m_hak_akses', $hakAksesId)
                        ->where('isDeleted', 0)
                        ->pluck('fk_m_user');

                    $parentMapping = [];
                    $modifiedParents = [];

                    // First pass: identify which parent menus need to be created
                    foreach ($menus as $globalId => $menuData) {
                        if (isset($menuData['modified']) && $menuData['modified'] == '1') {
                            // If this is a sub menu and it's modified, mark its parent as needing creation
                            if ($menuData['type'] == 'sub' && isset($menuData['parent_id'])) {
                                $modifiedParents[$menuData['parent_id']] = true;
                            }
                        }
                    }

                    // Second pass: process all menus
                    foreach ($menus as $globalId => $menuData) {
                        // Check if this menu should be processed
                        $shouldProcess = false;

                        // Process if explicitly modified
                        if (isset($menuData['modified']) && $menuData['modified'] == '1') {
                            $shouldProcess = true;
                        }

                        // Process if this is a parent of a modified sub menu
                        if ($menuData['type'] == 'group' && isset($modifiedParents[$globalId])) {
                            $shouldProcess = true;
                        }

                        if (!$shouldProcess) {
                            // Still need to track parent IDs for sub menus
                            if ($menuData['type'] == 'group') {
                                $existingMenu = WebMenuModel::where('fk_web_menu_global', $globalId)
                                    ->where('fk_m_hak_akses', $hakAksesId)
                                    ->where('isDeleted', 0)
                                    ->first();

                                if ($existingMenu) {
                                    $parentMapping[$globalId] = $existingMenu->web_menu_id;
                                }
                            }
                            continue;
                        }

                        $webMenuGlobal = WebMenuGlobalModel::find($globalId);
                        if (!$webMenuGlobal) continue;

                        // Determine parent ID
                        $parentId = null;
                        if ($menuData['type'] == 'sub' && isset($menuData['parent_id'])) {
                            $parentId = $parentMapping[$menuData['parent_id']] ?? null;
                        }

                        // Determine order
                        $order = 1;
                        if ($parentId) {
                            $order = WebMenuModel::where('wm_parent_id', $parentId)
                                ->where('fk_m_hak_akses', $hakAksesId)
                                ->where('isDeleted', 0)
                                ->max('wm_urutan_menu') + 1;
                        } else {
                            $order = WebMenuModel::whereNull('wm_parent_id')
                                ->where('fk_m_hak_akses', $hakAksesId)
                                ->where('isDeleted', 0)
                                ->max('wm_urutan_menu') + 1;
                        }

                        // Create or update menu
                        $webMenu = WebMenuModel::updateOrCreate(
                            [
                                'fk_web_menu_global' => $globalId,
                                'fk_m_hak_akses' => $hakAksesId
                            ],
                            [
                                'wm_parent_id' => $parentId,
                                'wm_menu_nama' => !empty($menuData['alias']) ? $menuData['alias'] : null,
                                'wm_status_menu' => $menuData['status'],
                                'wm_urutan_menu' => $order,
                                'isDeleted' => 0
                            ]
                        );

                        // Track parent mapping for group menus
                        if ($menuData['type'] == 'group') {
                            $parentMapping[$globalId] = $webMenu->web_menu_id;
                        }

                        // Handle permissions (not for group menus)
                        if (isset($menuData['permissions']) && $menuData['type'] != 'group') {
                            foreach ($userIds as $userId) {
                                SetHakAksesModel::updateOrCreate(
                                    [
                                        'ha_pengakses' => $userId,
                                        'fk_web_menu' => $webMenu->web_menu_id
                                    ],
                                    [
                                        'ha_menu' => isset($menuData['permissions']['menu']) ? 1 : 0,
                                        'ha_view' => isset($menuData['permissions']['view']) ? 1 : 0,
                                        'ha_create' => isset($menuData['permissions']['create']) ? 1 : 0,
                                        'ha_update' => isset($menuData['permissions']['update']) ? 1 : 0,
                                        'ha_delete' => isset($menuData['permissions']['delete']) ? 1 : 0,
                                        'isDeleted' => 0
                                    ]
                                );
                            }
                        }
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Menu berhasil disimpan'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat menyimpan menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_CREATE
        );
    }

    public function menuItems()
    {
        return $this->executeWithAuthentication(
            function () {
                try {
                    // Implementasi menu-item endpoint
                    // Bisa digunakan untuk mendapatkan menu dalam format khusus
                    $menus = WebMenuModel::getMenusWithChildren();
                    
                    return response()->json([
                        'success' => true,
                        'data' => $menus
                    ]);
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat mengambil menu items: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu management',
            self::ACTION_GET
        );
    }
}