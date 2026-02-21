<?php

namespace Modules\Sisfo\App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Modules\Sisfo\App\Services\MasterMenuService;
use Modules\Sisfo\App\Services\DatabaseSchemaService;
use Modules\Sisfo\App\Models\Website\WebMenuFieldConfigModel;
use Modules\Sisfo\App\Helpers\ValidationHelper;

/**
 * Master Controller
 * 
 * Template controller untuk semua menu master (Menu Tanpa Ngoding)
 * Controller ini dinamis: membaca config dari database dan handle CRUD otomatis
 * 
 * @package Modules\Sisfo\App\Http\Controllers\Template
 * @author Development Team
 * @version 1.0.0
 */
class MasterController extends Controller
{
    /**
     * Current menu configuration (loaded from DB)
     */
    protected $menuConfig;

    /**
     * Field configurations (loaded from DB)
     */
    protected $fieldConfigs;

    /**
     * Table name yang sedang diakses
     */
    protected $tableName;

    /**
     * Primary key column name
     */
    protected $pkColumn;

    /**
     * Constructor - Load menu config berdasarkan URL saat ini
     */
    public function __construct()
    {
        // Detect current URL dari request
        $currentUrl = request()->segment(1); // Get first segment
        
        // Load menu config
        $this->menuConfig = MasterMenuService::getMenuConfigByUrl($currentUrl);
        
        if (!$this->menuConfig) {
            abort(404, "Menu '{$currentUrl}' tidak ditemukan atau bukan menu master");
        }

        // Set table name
        $this->tableName = $this->menuConfig->wmu_akses_tabel;
        
        if (!$this->tableName) {
            abort(500, "Konfigurasi tabel tidak ditemukan untuk menu ini");
        }

        // Check table exists
        if (!DatabaseSchemaService::tableExists($this->tableName)) {
            abort(500, "Tabel '{$this->tableName}' tidak ditemukan di database");
        }

        // Load field configs
        $this->fieldConfigs = MasterMenuService::getFieldConfigs(
            $this->menuConfig->web_menu_url_id,
            false // Load semua fields (visible & hidden)
        );

        // Get primary key column
        $pkField = WebMenuFieldConfigModel::getPrimaryKeyField(
            $this->menuConfig->web_menu_url_id
        );
        
        $this->pkColumn = $pkField ? $pkField->wmfc_column_name : 'id';
    }

    // ==========================================
    // 1. INDEX - Halaman Utama
    // ==========================================

    /**
     * Display list page dengan DataTable
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = [
            'pageTitle' => $this->generatePageTitle(),
            'breadcrumb' => $this->generateBreadcrumb(),
            'menuConfig' => $this->menuConfig,
            'tableName' => $this->tableName,
            'pkColumn' => $this->pkColumn,
            'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1),
        ];

        return view('sisfo::Template.Master.index', $data);
    }

    // ==========================================
    // 2. GET DATA - AJAX DataTable
    // ==========================================

    /**
     * Get data untuk DataTable (AJAX)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            // Build query dengan JOIN untuk FK
            $query = MasterMenuService::buildSelectQuery(
                $this->tableName,
                $this->menuConfig->web_menu_url_id
            );

            // Filter soft delete
            $query->where("{$this->tableName}.isDeleted", 0);

            // Search
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $visibleFields = $this->fieldConfigs->where('wmfc_is_visible', 1);
                
                $query->where(function($q) use ($searchValue, $visibleFields) {
                    foreach ($visibleFields as $field) {
                        $q->orWhere(
                            "{$this->tableName}.{$field->wmfc_column_name}",
                            'like',
                            "%{$searchValue}%"
                        );
                    }
                });
            }

            // Total records before filter
            $totalRecords = DB::table($this->tableName)
                ->where('isDeleted', 0)
                ->count();

            // Total records after filter
            $totalFiltered = $query->count();

            // Order
            if ($request->has('order')) {
                $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                $orderDir = $request->order[0]['dir'];
                $query->orderBy("{$this->tableName}.{$orderColumn}", $orderDir);
            } else {
                $query->orderBy("{$this->tableName}.created_at", 'desc');
            }

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $data = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->draw ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // 3. ADD DATA - Show Form
    // ==========================================

    /**
     * Show form untuk tambah data
     * 
     * @return \Illuminate\View\View
     */
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
            'tableName' => $this->tableName,
            'pkColumn' => $this->pkColumn,
            'action' => 'create',
        ];

        return view('sisfo::Template.Master.create', $data);
    }

    // ==========================================
    // 4. CREATE DATA - Process Insert
    // ==========================================

    /**
     * Process create data (insert ke database)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createData(Request $request)
    {
        try {
            // Build validation rules
            $rules = MasterMenuService::buildValidationRules(
                $this->menuConfig->web_menu_url_id,
                $this->tableName
            );

            // Custom messages
            $messages = ValidationHelper::buildCustomMessages(
                $this->fieldConfigs->toArray()
            );

            // Validate
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get only field columns (exclude non-field inputs)
            $fieldColumns = $this->fieldConfigs
                ->where('wmfc_is_auto_increment', 0)
                ->pluck('wmfc_column_name')
                ->toArray();
            
            $data = $request->only($fieldColumns);

            // Insert data
            DB::beginTransaction();
            
            $insertedId = MasterMenuService::insertData(
                $this->tableName,
                $data,
                $this->menuConfig->web_menu_url_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan',
                'id' => $insertedId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // 5. EDIT DATA - Show Edit Form
    // ==========================================

    /**
     * Show form untuk edit data
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editData($id)
    {
        try {
            // Get existing data
            $existingData = MasterMenuService::getDetailData(
                $this->tableName,
                $id,
                $this->menuConfig->web_menu_url_id,
                $this->pkColumn
            );

            if (!$existingData) {
                abort(404, 'Data tidak ditemukan');
            }

            // Build form fields with existing data
            $formFields = MasterMenuService::buildFormFields(
                $this->menuConfig->web_menu_url_id,
                $existingData
            );

            $data = [
                'pageTitle' => 'Edit ' . $this->generatePageTitle(),
                'breadcrumb' => $this->generateBreadcrumb('Edit'),
                'formFields' => $formFields,
                'existingData' => $existingData,
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

    // ==========================================
    // 6. UPDATE DATA - Process Update
    // ==========================================

    /**
     * Process update data
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateData(Request $request, $id)
    {
        try {
            // Build validation rules (dengan exclude ID untuk unique check)
            $rules = MasterMenuService::buildValidationRules(
                $this->menuConfig->web_menu_url_id,
                $this->tableName,
                $id
            );

            // Custom messages
            $messages = ValidationHelper::buildCustomMessages(
                $this->fieldConfigs->toArray()
            );

            // Validate
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get only field columns
            $fieldColumns = $this->fieldConfigs
                ->where('wmfc_is_auto_increment', 0)
                ->where('wmfc_is_primary_key', 0) // Exclude PK
                ->pluck('wmfc_column_name')
                ->toArray();
            
            $data = $request->only($fieldColumns);

            // Update data
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
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil diupdate',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau tidak ada perubahan',
                ], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // 7. DETAIL DATA - Show Detail
    // ==========================================

    /**
     * Show detail data
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function detailData($id)
    {
        try {
            // Get detail data
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
                'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1),
                'tableName' => $this->tableName,
                'pkColumn' => $this->pkColumn,
                'id' => $id,
            ];

            return view('sisfo::Template.Master.detail', $data);

        } catch (\Exception $e) {
            abort(500, 'Error: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 8 & 9. DELETE DATA - Confirm & Process
    // ==========================================

    /**
     * Show delete confirmation atau process delete
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function deleteData(Request $request, $id)
    {
        // GET: Show confirmation
        if ($request->isMethod('GET')) {
            try {
                // Get data untuk konfirmasi
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
                    'fields' => $this->fieldConfigs->where('wmfc_is_visible', 1),
                    'tableName' => $this->tableName,
                    'pkColumn' => $this->pkColumn,
                    'id' => $id,
                ];

                return view('sisfo::Template.Master.delete', $data);

            } catch (\Exception $e) {
                abort(500, 'Error: ' . $e->getMessage());
            }
        }

        // DELETE: Process deletion
        try {
            DB::beginTransaction();
            
            $deleted = MasterMenuService::deleteData(
                $this->tableName,
                $id,
                $this->pkColumn
            );

            DB::commit();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil dihapus',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Generate page title dari menu config
     * 
     * @return string
     */
    protected function generatePageTitle(): string
    {
        // Ambil dari keterangan menu, atau generate dari nama tabel
        if (!empty($this->menuConfig->wmu_keterangan)) {
            return $this->menuConfig->wmu_keterangan;
        }

        // Generate dari nama tabel
        $title = str_replace('_', ' ', $this->tableName);
        $title = preg_replace('/^m_/', '', $title); // Remove prefix m_
        return ucwords($title);
    }

    /**
     * Generate breadcrumb
     * 
     * @param string|null $action Action name (Tambah, Edit, Detail, Hapus)
     * @return array
     */
    protected function generateBreadcrumb(?string $action = null): array
    {
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => $this->generatePageTitle(), 'url' => url($this->menuConfig->wmu_nama)],
        ];

        if ($action) {
            $breadcrumb[] = ['title' => $action, 'url' => '#'];
        }

        return $breadcrumb;
    }

    // ==========================================
    // 9. GET FK DATA - AJAX untuk FK Search Modal
    // ==========================================

    /**
     * Get FK data untuk search modal
     * Dipanggil dari create.blade.php & update.blade.php saat user klik search FK
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFkData(Request $request)
    {
        try {
            $fkTable = $request->input('fk_table');
            $fkPkColumn = $request->input('fk_pk_column');
            $fkDisplayColumns = $request->input('fk_display_columns', []);
            
            // Validasi parameter
            if (!$fkTable || !$fkPkColumn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap. fk_table dan fk_pk_column wajib diisi.',
                ], 400);
            }

            // Check tabel exists
            if (!DatabaseSchemaService::tableExists($fkTable)) {
                return response()->json([
                    'success' => false,
                    'message' => "Tabel FK '{$fkTable}' tidak ditemukan di database",
                ], 404);
            }

            // Build select columns
            $selectColumns = [$fkPkColumn];
            if (!empty($fkDisplayColumns)) {
                $selectColumns = array_merge($selectColumns, $fkDisplayColumns);
            }

            // Get FK data dengan filter soft delete
            $query = DB::table($fkTable)
                ->select($selectColumns);
            
            // Check jika ada kolom isDeleted
            if (Schema::hasColumn($fkTable, 'isDeleted')) {
                $query->where('isDeleted', 0);
            }

            // Limit untuk performa (max 1000 records)
            $data = $query->limit(1000)->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'pk_column' => $fkPkColumn,
                'display_columns' => $fkDisplayColumns,
                'total' => $data->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data FK: ' . $e->getMessage(),
            ], 500);
        }
    }
}
