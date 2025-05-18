<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\ApplicationModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class WebMenuUrlController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Manajemen URL Menu';
    public $pagename = 'AdminWeb/WebMenuUrl';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Manajemen URL Menu',
            'list' => ['Home', 'Manajemen URL Menu']
        ];

        $page = (object) [
            'title' => 'Daftar URL Menu'
        ];

        $activeMenu = 'webmenuurl';
        
        // Gunakan pagination dan pencarian
        $webMenuUrls = WebMenuUrlModel::selectData(10, $search);
        
        // Ambil aplikasi untuk dropdown di form create
        $applications = ApplicationModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.WebMenuUrl.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'webMenuUrls' => $webMenuUrls,
            'search' => $search,
            'applications' => $applications
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $webMenuUrls = WebMenuUrlModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::AdminWeb.WebMenuUrl.data', compact('webMenuUrls', 'search'))->render();
        }
        
        return redirect()->back();
    }

    public function addData()
    {
        // Ambil daftar aplikasi untuk dropdown
        $applications = ApplicationModel::where('isDeleted', 0)->get();
        
        return view("sisfo::AdminWeb.WebMenuUrl.create", [
            'applications' => $applications
        ]);
    }

    public function createData(Request $request)
    {
        try {
            WebMenuUrlModel::validasiData($request);
            $result = WebMenuUrlModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'URL menu berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat URL menu');
        }
    }

    public function editData($id)
    {
        // Ambil data URL menu
        $webMenuUrl = WebMenuUrlModel::detailData($id);
        
        // Ambil daftar aplikasi untuk dropdown
        $applications = ApplicationModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.WebMenuUrl.update", [
            'webMenuUrl' => $webMenuUrl,
            'applications' => $applications
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            WebMenuUrlModel::validasiData($request);
            $result = WebMenuUrlModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'URL menu berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui URL menu');
        }
    }

    public function detailData($id)
    {
        $webMenuUrl = WebMenuUrlModel::detailData($id);
        
        return view("sisfo::AdminWeb.WebMenuUrl.detail", [
            'webMenuUrl' => $webMenuUrl,
            'title' => 'Detail URL Menu'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $webMenuUrl = WebMenuUrlModel::detailData($id);
            
            return view("sisfo::AdminWeb.WebMenuUrl.delete", [
                'webMenuUrl' => $webMenuUrl
            ]);
        }
        
        try {
            $result = WebMenuUrlModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'URL menu berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}