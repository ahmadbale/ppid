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
        try {
            // Handle AJAX requests dari function tambahan (validateTable, autoGenerateFields)
            $handled = $this->handle(request());
            if ($handled !== null) {
                return $handled;
            }

            $applications = ApplicationModel::where('isDeleted', 0)->get();

            return view("sisfo::AdminWeb.WebMenuUrl.create", [
                'applications' => $applications,
                'mode' => 'create',
            ]);
        } catch (\Exception $e) {
            Log::error('Error in addData(): ' . $e->getMessage());
            throw $e;
        }
    }

    public function createData(Request $request)
    {
        // ===== LOGGING: Entry point =====
        Log::debug('WebMenuUrlController::createData() CALLED', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'action_param' => $request->input('action'),
            'table_name' => $request->input('table_name'),
            'all_inputs' => $request->all(),
        ]);

        try {
            // Handle AJAX requests dari function tambahan
            $handled = $this->handle($request);
            if ($handled !== null) {
                Log::debug('WebMenuUrlController::createData() - AJAX handled', [
                    'action' => $request->input('action'),
                ]);
                return $handled;
            }

            Log::debug('WebMenuUrlController::createData() - Processing normal form submission');

            // Jika create-ulang (is_update=1), exclude existing_menu_id dari unique check wmu_nama
            $existingMenuId = $request->input('is_update') ? $request->input('existing_menu_id') : null;

            // Process form submission
            WebMenuUrlModel::validasiData($request, $existingMenuId);
            $result = WebMenuUrlModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'URL menu berhasil dibuat'
            );
        } catch (ValidationException $e) {
            Log::error('WebMenuUrlController::createData() - Validation error', [
                'errors' => $e->errors(),
            ]);
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            Log::error('WebMenuUrlController::createData() - Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat URL menu');
        }
    }

    public function editData($id)
    {
        // Handle AJAX requests dari function tambahan (validateTable, autoGenerateFields)
        $handled = $this->handle(request());
        if ($handled !== null) {
            return $handled;
        }

        $webMenuUrl = WebMenuUrlModel::detailDataWithConfigs($id);
        $applications = ApplicationModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.WebMenuUrl.update", [
            'webMenuUrl' => $webMenuUrl,
            'applications' => $applications,
            'mode' => 'update',
        ]);
    }

    public function updateData(Request $request, $id)
    {
        // ===== LOGGING: Entry point =====
        Log::debug('WebMenuUrlController::updateData() CALLED', [
            'id' => $id,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'action_param' => $request->input('action'),
            'table_name' => $request->input('table_name'),
            'all_inputs' => $request->all(),
        ]);

        try {
            // Handle AJAX requests dari function tambahan
            $handled = $this->handle($request);
            if ($handled !== null) {
                Log::debug('WebMenuUrlController::updateData() - AJAX handled', [
                    'action' => $request->input('action'),
                ]);
                return $handled;
            }

            Log::debug('WebMenuUrlController::updateData() - Processing normal form submission');

            WebMenuUrlModel::validasiData($request, $id);
            $result = WebMenuUrlModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'URL menu berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            Log::error('WebMenuUrlController::updateData() - Validation error', [
                'errors' => $e->errors(),
            ]);
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            Log::error('WebMenuUrlController::updateData() - Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui URL menu');
        }
    }

    public function detailData($id)
    {
        $webMenuUrl = WebMenuUrlModel::detailDataWithConfigs($id);
        
        return view("sisfo::AdminWeb.WebMenuUrl.detail", [
            'webMenuUrl' => $webMenuUrl,
            'title' => 'Detail URL Menu'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $webMenuUrl = WebMenuUrlModel::detailDataWithConfigs($id);
            
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
        
        Log::debug('WebMenuUrlController::handle() CALLED', [
            'action_param' => $action,
            'has_action' => !empty($action),
        ]);
        
        // Action: validateTable
        if ($action === 'validateTable') {
            Log::debug('WebMenuUrlController::handle() - Routing to validateTable()');
            return $this->validateTable($request);
        }
        
        // Action: autoGenerateFields
        if ($action === 'autoGenerateFields') {
            Log::debug('WebMenuUrlController::handle() - Routing to autoGenerateFields()');
            return $this->autoGenerateFields($request);
        }
        
        Log::debug('WebMenuUrlController::handle() - No matching action, return null');
        return null; // Bukan AJAX request, lanjut ke normal flow
    }

    /**
     * Function tambahan: Validasi tabel (AJAX)
     */
    private function validateTable(Request $request)
    {
        Log::debug('WebMenuUrlController::validateTable() CALLED', [
            'table_name' => $request->input('table_name'),
            'menu_url_id' => $request->input('menu_url_id'),
        ]);

        try {
            $tableName = $request->input('table_name');
            $menuUrlId = $request->input('menu_url_id');
            
            if (empty($tableName)) {
                Log::warning('WebMenuUrlController::validateTable() - Table name empty');
                return response()->json([
                    'success' => false,
                    'message' => 'Nama tabel tidak boleh kosong'
                ], 422);
            }

            Log::debug('WebMenuUrlController::validateTable() - Calling Model::validateTable()', [
                'table_name' => $tableName,
                'menu_url_id' => $menuUrlId,
            ]);

            if ($menuUrlId) {
                $result = WebMenuUrlModel::validateTable($tableName, $menuUrlId);
            } else {
                $result = WebMenuUrlModel::validateTable($tableName);
            }
            
            Log::debug('WebMenuUrlController::validateTable() - Model result', [
                'result' => $result,
            ]);

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
            Log::error('WebMenuUrlController::validateTable() - Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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
        Log::debug('WebMenuUrlController::autoGenerateFields() CALLED', [
            'table_name' => $request->input('table_name'),
        ]);

        try {
            $tableName = $request->input('table_name');
            
            if (empty($tableName)) {
                Log::warning('WebMenuUrlController::autoGenerateFields() - Table name empty');
                return $this->jsonError(
                    new \Exception('Nama tabel tidak boleh kosong'),
                    'Generate field gagal'
                );
            }

            Log::debug('WebMenuUrlController::autoGenerateFields() - Validating table first');
            $validation = WebMenuUrlModel::validateTable($tableName);
            
            if (!$validation['success']) {
                Log::warning('WebMenuUrlController::autoGenerateFields() - Table validation failed', [
                    'message' => $validation['message'],
                ]);
                return $this->jsonError(
                    new \Exception($validation['message']),
                    'Tabel tidak ditemukan'
                );
            }

            Log::debug('WebMenuUrlController::autoGenerateFields() - Calling Model::autoGenerateFieldConfigs()');
            $fieldConfigs = WebMenuUrlModel::autoGenerateFieldConfigs($tableName);
            
            Log::debug('WebMenuUrlController::autoGenerateFields() - Field configs generated', [
                'count' => count($fieldConfigs),
                'first_field_sample' => count($fieldConfigs) > 0 ? [
                    'wmfc_column_name' => $fieldConfigs[0]['wmfc_column_name'],
                    'wmfc_label_keterangan' => $fieldConfigs[0]['wmfc_label_keterangan'],
                    'wmfc_display_list' => $fieldConfigs[0]['wmfc_display_list'],
                    'wmfc_ukuran_max' => $fieldConfigs[0]['wmfc_ukuran_max'],
                ] : 'No fields',
            ]);

            return $this->jsonSuccess(
                $fieldConfigs,
                'Field configs berhasil di-generate dari tabel ' . $tableName
            );
        } catch (\Exception $e) {
            Log::error('WebMenuUrlController::autoGenerateFields() - Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->jsonError($e, 'Terjadi kesalahan saat generate field configs');
        }
    }
}