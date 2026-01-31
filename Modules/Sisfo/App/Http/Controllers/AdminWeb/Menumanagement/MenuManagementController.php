<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $result = WebMenuModel::selectData();

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return view('sisfo::adminweb.MenuManagement.index', $result['data']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading menu management page: ' . $e->getMessage());
        }
    }

    // API Gateway - handle multiple actions via query parameter
    public function getData(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->back();
        }

        $action = $request->query('action', 'list');
        
        switch ($action) {
            case 'parent-menus':
                return $this->handleGetParentMenus($request);
                
            case 'reorder':
                return $this->handleReorder($request);
                
            case 'list':
            default:
                return $this->getMenuList();
        }
    }

    public function addData($hakAksesId)
    {
        try {
            $result = WebMenuModel::getAddData($hakAksesId);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            // Return full page create view
            return view('sisfo::AdminWeb.MenuManagement.create', $result['data']);
        } catch (\Exception $e) {
            Log::error('Error in addData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Error loading create menu page: ' . $e->getMessage());
        }
    }

    public function createData(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::createData($request);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function editData($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::getEditData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function updateData(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::updateData($request, $id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function detailData($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::detailData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->ajax()) {
            $result = WebMenuModel::deleteData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    // Private: Handle reorder request via getData API Gateway
    private function handleReorder(Request $request)
    {
        $result = WebMenuModel::reorderMenus($request->input('data'));
        return response()->json($result);
    }

    // Private: Handle get parent menus via getData API Gateway
    private function handleGetParentMenus(Request $request)
    {
        $hakAksesId = $request->query('hak_akses_id');
        $excludeId = $request->query('exclude_id');
        
        $result = WebMenuModel::getParentMenusData($hakAksesId, $excludeId);
        return response()->json($result);
    }

    // Private: Default getData - return menu list
    private function getMenuList()
    {
        $result = WebMenuModel::selectData();
        return response()->json($result);
    }
}
