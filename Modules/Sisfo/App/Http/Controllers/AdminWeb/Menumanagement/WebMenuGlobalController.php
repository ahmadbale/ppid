<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class WebMenuGlobalController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Menu Global Management';
    public $pagename = 'AdminWeb/WebMenuGlobal';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Menu Global Management',
            'list' => ['Home', 'Menu Global Management']
        ];

        $page = (object) [
            'title' => 'Daftar Menu Global'
        ];

        $activeMenu = 'menumanagement';
        
        // Gunakan pagination dan pencarian
        $webMenuGlobals = WebMenuGlobalModel::selectData(10, $search);
        
        // Ambil menu URLs untuk dropdown di form create/update
        $menuUrls = WebMenuUrlModel::with('application')->where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.WebMenuGlobal.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'webMenuGlobals' => $webMenuGlobals,
            'search' => $search,
            'menuUrls' => $menuUrls
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $webMenuGlobals = WebMenuGlobalModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::AdminWeb.WebMenuGlobal.data', compact('webMenuGlobals', 'search'))->render();
        }
        
        return redirect()->back();
    }

    public function addData()
    {
        // Ambil daftar URL menu untuk dropdown
        $menuUrls = WebMenuUrlModel::with('application')->where('isDeleted', 0)->get();
        
        return view("sisfo::AdminWeb.WebMenuGlobal.create", [
            'menuUrls' => $menuUrls
        ]);
    }

    public function createData(Request $request)
    {
        try {
            WebMenuGlobalModel::validasiData($request);
            $result = WebMenuGlobalModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Menu global berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat menu global');
        }
    }

    public function editData($id)
    {
        // Ambil data menu global
        $webMenuGlobal = WebMenuGlobalModel::detailData($id);
        
        // Ambil daftar URL menu untuk dropdown
        $menuUrls = WebMenuUrlModel::with('application')->where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.WebMenuGlobal.update", [
            'webMenuGlobal' => $webMenuGlobal,
            'menuUrls' => $menuUrls
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            WebMenuGlobalModel::validasiData($request);
            $result = WebMenuGlobalModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Menu global berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui menu global');
        }
    }

    public function detailData($id)
    {
        $webMenuGlobal = WebMenuGlobalModel::detailData($id);
        
        return view("sisfo::AdminWeb.WebMenuGlobal.detail", [
            'webMenuGlobal' => $webMenuGlobal,
            'title' => 'Detail Menu Global'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $webMenuGlobal = WebMenuGlobalModel::detailData($id);
            
            return view("sisfo::AdminWeb.WebMenuGlobal.delete", [
                'webMenuGlobal' => $webMenuGlobal
            ]);
        }
        
        try {
            $result = WebMenuGlobalModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Menu global berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}