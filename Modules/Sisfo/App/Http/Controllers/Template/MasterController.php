<?php

namespace Modules\Sisfo\App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Services\MasterMenuService;
use Modules\Sisfo\App\Services\DatabaseSchemaService;
use Modules\Sisfo\App\Models\Website\WebMenuFieldConfigModel;
use Modules\Sisfo\App\Helpers\ValidationHelper;
use Modules\Sisfo\App\Http\Controllers\TraitsController;

class MasterController extends Controller
{
    use TraitsController;

    protected $menuConfig;
    protected $fieldConfigs;
    protected $tableName;
    protected $pkColumn;

    public function __construct()
    {
        $currentUrl = request()->segment(1);
        
        $this->menuConfig = MasterMenuService::getMenuConfigByUrl($currentUrl);
        
        if (!$this->menuConfig) {
            abort(404, "Menu '{$currentUrl}' tidak ditemukan atau bukan menu master");
        }

        $this->tableName = $this->menuConfig->wmu_akses_tabel;
        
        if (!$this->tableName) {
            abort(500, "Konfigurasi tabel tidak ditemukan untuk menu ini");
        }

        if (!DatabaseSchemaService::tableExists($this->tableName)) {
            abort(500, "Tabel '{$this->tableName}' tidak ditemukan di database");
        }

        $this->fieldConfigs = MasterMenuService::getFieldConfigs(
            $this->menuConfig->web_menu_url_id,
            false
        );

        $pkField = WebMenuFieldConfigModel::getPrimaryKeyField(
            $this->menuConfig->web_menu_url_id
        );
        
        $this->pkColumn = $pkField ? $pkField->wmfc_column_name : 'id';
    }

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $query = MasterMenuService::buildSelectQuery(
            $this->tableName,
            $this->menuConfig->web_menu_url_id
        );

        $query->where("{$this->tableName}.isDeleted", 0);

        if (!empty($search)) {
            $visibleFields = $this->fieldConfigs->where('wmfc_is_visible', 1);
            
            $query->where(function($q) use ($search, $visibleFields) {
                foreach ($visibleFields as $field) {
                    $q->orWhere(
                        "{$this->tableName}.{$field->wmfc_column_name}",
                        'like',
                        "%{$search}%"
                    );
                }
            });
        }

        $query->orderBy("{$this->tableName}.created_at", 'desc');

        $dataResult = $query->paginate(10);

        $data = [
            'pageTitle' => $this->generatePageTitle(),
            'breadcrumb' => $this->generateBreadcrumb(),
            'menuConfig' => $this->menuConfig,
            'tableName' => $this->tableName,
            'pkColumn' => $this->pkColumn,
            'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1),
            'data' => $dataResult,
            'search' => $search,
        ];

        return view('sisfo::Template.Master.index', $data);
    }

    public function getData(Request $request)
    {
        try {
            $search = $request->query('search', '');

            $query = MasterMenuService::buildSelectQuery(
                $this->tableName,
                $this->menuConfig->web_menu_url_id
            );

            $query->where("{$this->tableName}.isDeleted", 0);

            if (!empty($search)) {
                $visibleFields = $this->fieldConfigs->where('wmfc_is_visible', 1);
                
                $query->where(function($q) use ($search, $visibleFields) {
                    foreach ($visibleFields as $field) {
                        $q->orWhere(
                            "{$this->tableName}.{$field->wmfc_column_name}",
                            'like',
                            "%{$search}%"
                        );
                    }
                });
            }

            $query->orderBy("{$this->tableName}.created_at", 'desc');

            $data = $query->paginate(10);

            $viewData = [
                'data' => $data,
                'menuConfig' => $this->menuConfig,
                'pkColumn' => $this->pkColumn,
                'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1),
                'search' => $search,
            ];

            if ($request->ajax()) {
                return response()->view('sisfo::Template.Master.data', $viewData);
            }

            return redirect()->back();

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response('<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>', 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function addData()
    {
        $formFields = MasterMenuService::buildFormFields(
            $this->menuConfig->web_menu_url_id,
            null
        );

        $data = [
            'pageTitle' => 'Tambah ' . $this->generatePageTitle(),
            'breadcrumb' => $this->generateBreadcrumb('Tambah'),
            'formFields' => $formFields,
            'menuConfig' => $this->menuConfig,
            'tableName' => $this->tableName,
            'pkColumn' => $this->pkColumn,
            'action' => 'create',
        ];

        return view('sisfo::Template.Master.create', $data);
    }

    public function createData(Request $request)
    {
        try {
            $rules = MasterMenuService::buildValidationRules(
                $this->menuConfig->web_menu_url_id,
                $this->tableName
            );

            $messages = ValidationHelper::buildCustomMessages(
                $this->fieldConfigs->toArray()
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $fieldColumns = $this->fieldConfigs
                ->where('wmfc_is_auto_increment', 0)
                ->pluck('wmfc_column_name')
                ->toArray();
            
            $data = $request->only($fieldColumns);

            DB::beginTransaction();
            
            $insertedId = MasterMenuService::insertData(
                $this->tableName,
                $data,
                $this->menuConfig->web_menu_url_id
            );

            DB::commit();

            return $this->jsonSuccess(
                ['id' => $insertedId],
                'Data berhasil ditambahkan'
            );

        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e, 'Gagal menambahkan data');
        }
    }

    public function editData($id)
    {
        try {
            $existingData = MasterMenuService::getDetailData(
                $this->tableName,
                $id,
                $this->menuConfig->web_menu_url_id,
                $this->pkColumn
            );

            if (!$existingData) {
                abort(404, 'Data tidak ditemukan');
            }

            $formFields = MasterMenuService::buildFormFields(
                $this->menuConfig->web_menu_url_id,
                $existingData
            );

            $data = [
                'pageTitle' => 'Edit ' . $this->generatePageTitle(),
                'breadcrumb' => $this->generateBreadcrumb('Edit'),
                'formFields' => $formFields,
                'existingData' => $existingData,
                'menuConfig' => $this->menuConfig,
                'tableName' => $this->tableName,
                'pkColumn' => $this->pkColumn,
                'id' => $id,
                'action' => 'update',
            ];

            return view('sisfo::Template.Master.update', $data);

        } catch (\Exception $e) {
            abort(500, 'Error: ' . $e->getMessage());
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            $rules = MasterMenuService::buildValidationRules(
                $this->menuConfig->web_menu_url_id,
                $this->tableName,
                $id
            );

            $messages = ValidationHelper::buildCustomMessages(
                $this->fieldConfigs->toArray()
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $fieldColumns = $this->fieldConfigs
                ->where('wmfc_is_auto_increment', 0)
                ->where('wmfc_is_primary_key', 0)
                ->pluck('wmfc_column_name')
                ->toArray();
            
            $data = $request->only($fieldColumns);

            DB::beginTransaction();
            
            $updated = MasterMenuService::updateData(
                $this->tableName,
                $id,
                $data,
                $this->menuConfig->web_menu_url_id,
                $this->pkColumn
            );

            DB::commit();

            if ($updated) {
                return $this->jsonSuccess(null, 'Data berhasil diupdate');
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau tidak ada perubahan',
            ], 404);

        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e, 'Gagal mengupdate data');
        }
    }

    public function detailData($id)
    {
        try {
            $detailData = MasterMenuService::getDetailData(
                $this->tableName,
                $id,
                $this->menuConfig->web_menu_url_id,
                $this->pkColumn
            );

            if (!$detailData) {
                abort(404, 'Data tidak ditemukan');
            }

            $data = [
                'pageTitle' => 'Detail ' . $this->generatePageTitle(),
                'breadcrumb' => $this->generateBreadcrumb('Detail'),
                'detailData' => $detailData,
                'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1)->where('wmfc_is_primary_key', 0),
                'menuConfig' => $this->menuConfig,
                'tableName' => $this->tableName,
                'pkColumn' => $this->pkColumn,
                'id' => $id,
            ];

            return view('sisfo::Template.Master.detail', $data);

        } catch (\Exception $e) {
            abort(500, 'Error: ' . $e->getMessage());
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('GET')) {
            try {
                $detailData = MasterMenuService::getDetailData(
                    $this->tableName,
                    $id,
                    $this->menuConfig->web_menu_url_id,
                    $this->pkColumn
                );

                if (!$detailData) {
                    abort(404, 'Data tidak ditemukan');
                }

                $data = [
                    'pageTitle' => 'Hapus ' . $this->generatePageTitle(),
                    'breadcrumb' => $this->generateBreadcrumb('Hapus'),
                    'detailData' => $detailData,
                    'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1)->where('wmfc_is_primary_key', 0),
                    'menuConfig' => $this->menuConfig,
                    'tableName' => $this->tableName,
                    'pkColumn' => $this->pkColumn,
                    'id' => $id,
                ];

                return view('sisfo::Template.Master.delete', $data);

            } catch (\Exception $e) {
                abort(500, 'Error: ' . $e->getMessage());
            }
        }

        try {
            DB::beginTransaction();
            
            $deleted = MasterMenuService::deleteData(
                $this->tableName,
                $id,
                $this->pkColumn
            );

            DB::commit();

            if ($deleted) {
                return $this->jsonSuccess(null, 'Data berhasil dihapus');
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e, 'Gagal menghapus data');
        }
    }

    public function getFkData(Request $request)
    {
        try {
            $fkTable = $request->input('table');
            $displayColumns = $request->input('columns', []);

            if (!$fkTable || !is_array($displayColumns) || empty($displayColumns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap',
                ], 400);
            }

            if (!DatabaseSchemaService::tableExists($fkTable)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabel tidak ditemukan',
                ], 404);
            }

            $pkColumn = DatabaseSchemaService::getPrimaryKey($fkTable);
            if (!$pkColumn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Primary key tidak ditemukan',
                ], 500);
            }

            $selectColumns = array_merge([$pkColumn], $displayColumns);
            $query = DB::table($fkTable)->select($selectColumns);

            if (Schema::hasColumn($fkTable, 'isDeleted')) {
                $query->where('isDeleted', 0);
            }

            $query->limit(100);

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'pkColumn' => $pkColumn,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function generatePageTitle(): string
    {
        if (!empty($this->menuConfig->wmu_keterangan)) {
            return $this->menuConfig->wmu_keterangan;
        }

        $title = str_replace('_', ' ', $this->tableName);
        $title = preg_replace('/^m_/', '', $title);
        return ucwords($title);
    }

    protected function generateBreadcrumb(?string $action = null): object
    {
        $pageTitle = $this->generatePageTitle();
        
        $list = [
            'Home',
            $pageTitle,
        ];

        if ($action) {
            $list[] = $action;
        }

        return (object) [
            'title' => $action ? "{$action} {$pageTitle}" : $pageTitle,
            'list' => $list,
        ];
    }
}
