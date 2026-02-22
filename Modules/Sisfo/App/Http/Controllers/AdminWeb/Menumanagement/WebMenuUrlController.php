<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\ApplicationModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\Website\WebMenuFieldConfigModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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

    public function addData($id = null)
    {
        // Handle AJAX requests dari function tambahan (validateTable, autoGenerateFields)
        $handled = $this->handle(request());
        if ($handled !== null) {
            return $handled; // Return AJAX response
        }
        
        // Return form view (bukan AJAX)
        $applications = ApplicationModel::where('isDeleted', 0)->get();
        
        return view("sisfo::AdminWeb.WebMenuUrl.create", [
            'applications' => $applications
        ]);
    }

    public function createData(Request $request)
    {
        try {
            // Handle AJAX requests dari function tambahan
            $handled = $this->handle($request);
            if ($handled !== null) {
                return $handled;
            }

            // Process form submission
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
        // Handle AJAX requests dari function tambahan (validateTable, autoGenerateFields)
        $handled = $this->handle(request());
        if ($handled !== null) {
            return $handled; // Return AJAX response
        }
        
        // Return form view (bukan AJAX)
        $webMenuUrl = WebMenuUrlModel::detailDataWithConfigs($id);
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

    /**
     * PRIVATE HELPER METHODS
     * Dipanggil dari addData() berdasarkan query parameter
     */
    
    /**
     * Handle function untuk memanggil function tambahan
     * Digunakan untuk validateTable() dan autoGenerateFields()
     */
    private function handle(Request $request)
    {
        $action = $request->input('action');
        
        // Action: validateTable
        if ($action === 'validateTable') {
            return $this->validateTable($request);
        }
        
        // Action: autoGenerateFields
        if ($action === 'autoGenerateFields') {
            return $this->autoGenerateFields($request);
        }
        
        return null; // Bukan AJAX request, lanjut ke normal flow
    }

    /**
     * Function tambahan: Validasi tabel (AJAX)
     */
    private function validateTable(Request $request)
    {
        try {
            $tableName = $request->input('table_name');
            $menuUrlId = $request->input('menu_url_id');
            
            if (empty($tableName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama tabel tidak boleh kosong'
                ], 422);
            }

            if ($menuUrlId) {
                $result = WebMenuUrlModel::validateTable($tableName, $menuUrlId);
            } else {
                $result = WebMenuUrlModel::validateTable($tableName);
            }
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? $result
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi tabel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Function tambahan: Auto generate field configs (AJAX)
     */
    private function autoGenerateFields(Request $request)
    {
        try {
            $tableName = $request->input('table_name');
            
            if (empty($tableName)) {
                return $this->jsonError(
                    new \Exception('Nama tabel tidak boleh kosong'),
                    'Generate field gagal'
                );
            }

            $validation = WebMenuUrlModel::validateTable($tableName);
            
            if (!$validation['success']) {
                return $this->jsonError(
                    new \Exception($validation['message']),
                    'Tabel tidak ditemukan'
                );
            }

            $fieldConfigs = WebMenuUrlModel::autoGenerateFieldConfigs($tableName);
            
            return $this->jsonSuccess(
                $fieldConfigs,
                'Field configs berhasil di-generate dari tabel ' . $tableName
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat generate field configs');
        }
    }
}