<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
}
