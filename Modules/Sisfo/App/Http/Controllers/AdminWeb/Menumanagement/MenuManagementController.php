<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;

class MenuManagementController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Menu Management';
    public $pagename = 'AdminWeb/MenuManagement';

    public function index()
    {
        try {
            $breadcrumb = (object)[
                'title' => 'Menu Management',
                'list' => ['Home', 'Menu Management'],
            ];

            $page = (object)[
                'title' => 'Menu Management System'
            ];

            $activeMenu = 'menumanagement';

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
                        }, 'WebMenuGlobal', 'Level'])
                        ->get()
                ];
            }

            // Untuk dropdown di form - data dari web_menu_global
            $groupMenusGlobal = WebMenuGlobalModel::whereNull('fk_web_menu_url')
                ->where('isDeleted', 0)
                ->orderBy('wmg_nama_default')
                ->get();

            // PERUBAHAN: Untuk dropdown nama group menu pada form submenu - data dari web_menu
            $groupMenusFromWebMenu = WebMenuModel::whereHas('WebMenuGlobal', function ($query) {
                $query->whereNull('fk_web_menu_url');
            })
                ->whereNull('wm_parent_id')  // Pastikan ini adalah menu utama/parent
                ->where('isDeleted', 0)
                ->where('wm_status_menu', 'aktif')
                ->with('WebMenuGlobal')
                ->orderBy('wm_urutan_menu')
                ->get();

            // Get non-group menus (fk_web_menu_url != NULL)
            $nonGroupMenus = WebMenuGlobalModel::whereNotNull('fk_web_menu_url')
                ->where('isDeleted', 0)
                ->with('WebMenuUrl.application')
                ->orderBy('wmg_nama_default')
                ->get();

            // Untuk dropdown - data dari web_menu biasa
            $menus = WebMenuModel::getMenusWithChildren();

            return view('sisfo::adminweb.MenuManagement.index', compact(
                'breadcrumb',
                'page',
                'menus',
                'activeMenu',
                'menusByJenis',
                'jenisMenuList',
                'levels',
                'groupMenusGlobal',     // Group menus dari web_menu_global untuk set sebagai group menu
                'groupMenusFromWebMenu', // Group menus dari web_menu untuk set sebagai sub menu
                'nonGroupMenus'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading menu management page: ' . $e->getMessage());
        }
    }

    // Method lain tetap sama
    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::createData($request);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::getEditData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::updateData($request, $id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function delete($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::deleteData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function detail_menu($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::getDetailData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function reorder(Request $request)
    {
        if ($request->ajax()) {
            $result = WebMenuModel::reorderMenus($request->get('data'));
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function getParentMenus($hakAksesId)
    {
        $excludeId = request()->input('exclude_id');
        $parentMenus = WebMenuModel::getParentMenusByLevel($hakAksesId, $excludeId);
        return response()->json([
            'success' => true,
            'parentMenus' => $parentMenus
        ]);
    }

    public function setMenu($hakAksesId)
    {
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

        $breadcrumb = (object)[
            'title' => 'Set Menu',
            'list' => ['Home', 'Menu Management', 'Set Menu'],
        ];

        $page = (object)[
            'title' => 'Pembuatan Menu untuk Hak Akses'
        ];

        $activeMenu = 'menumanagement';

        return view('sisfo::adminweb.MenuManagement.set-menu', compact(
            'breadcrumb',
            'page',
            'activeMenu',
            'level',
            'menuGlobal',
            'existingMenus'
        ));
    }

    public function storeSetMenu(Request $request)
    {
        DB::beginTransaction();
        try {
            $hakAksesId = $request->hak_akses_id;
            $menus = $request->menus;

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
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
