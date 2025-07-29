<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
            $result = WebMenuModel::selectData();

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return view('sisfo::adminweb.MenuManagement.index', $result['data']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading menu management page: ' . $e->getMessage());
        }
    }

    public function addData($hakAksesId)
    {
        try {
            $result = WebMenuModel::getAddData($hakAksesId);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return view('sisfo::adminweb.MenuManagement.set-menu', $result['data']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading set menu page: ' . $e->getMessage());
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

    public function deleteData($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::deleteData($id);
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
        if (request()->ajax()) {
            $result = WebMenuModel::getParentMenusData($hakAksesId, request()->input('exclude_id'));
            return response()->json($result);
        }
        return redirect()->back();
    }
}
