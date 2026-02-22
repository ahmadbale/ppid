<?php

namespace Modules\Sisfo\App\Models\Website;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\ApplicationModel;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Services\DatabaseSchemaService;

class WebMenuUrlModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu_url';
    protected $primaryKey = 'web_menu_url_id';
    protected $fillable = [
        'fk_m_application',
        'wmu_nama',
        'controller_name',
        'module_type',
        'wmu_keterangan',
        'wmu_kategori_menu',
        'wmu_akses_tabel',
        'wmu_parent_id',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Relationship with Application model
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'fk_m_application', 'application_id');
    }

    // Relationship with WebMenuGlobal
    public function webMenuGlobal()
    {
        return $this->hasMany(WebMenuGlobalModel::class, 'fk_web_menu_url', 'web_menu_url_id');
    }

    // Relationship with WebMenuFieldConfig
    public function fieldConfigs()
    {
        return $this->hasMany(WebMenuFieldConfigModel::class, 'fk_web_menu_url', 'web_menu_url_id');
    }

    // Relationship parent-child menu
    public function parent()
    {
        return $this->belongsTo(self::class, 'wmu_parent_id', 'web_menu_url_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'wmu_parent_id', 'web_menu_url_id');
    }

    public static function selectData($perPage = null, $search = '', $appKey = null)
    {
        $query = self::query()
            ->where('isDeleted', 0)
            ->with('application');

        //  Filter berdasarkan app_key jika diberikan
        if ($appKey) {
            $query->whereHas('application', function ($q) use ($appKey) {
                $q->where('app_key', $appKey);
            });
        }

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('wmu_nama', 'like', "%{$search}%")
                    ->orWhereHas('application', function ($app) use ($search) {
                        $app->where('app_nama', 'like', "%{$search}%");
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $kategoriMenu = $request->input('web_menu_url.wmu_kategori_menu', 'custom');
            $data = $request->web_menu_url;

            // ✅ CHECK: Jika ini adalah update (tabel sudah terdaftar dengan perubahan)
            $existingMenuId = $request->input('existing_menu_id');
            $isUpdate = $request->input('is_update', false);

            if ($isUpdate && $existingMenuId) {
                // Soft delete menu lama dan semua field configs
                $oldMenu = self::find($existingMenuId);
                if ($oldMenu) {
                    $oldMenu->update(['isDeleted' => 1]);
                    
                    // Soft delete semua field configs
                    WebMenuFieldConfigModel::where('fk_web_menu_url', $existingMenuId)
                        ->update(['isDeleted' => 1]);
                }
            }

            // Set default values berdasarkan kategori
            if ($kategoriMenu === 'master') {
                $data['controller_name'] = 'Template\MasterController';
                $data['wmu_parent_id'] = null; // Master selalu parent
                $data['module_type'] = 'sisfo'; // Master hanya untuk sisfo
            } elseif ($kategoriMenu === 'pengajuan') {
                // Untuk pengajuan (future development)
                throw new \Exception('Kategori menu "pengajuan" masih dalam tahap pengembangan');
            } elseif ($kategoriMenu === 'custom') {
                // Custom: user input manual
                $data['wmu_akses_tabel'] = null; // Custom tidak pakai tabel otomatis
            }

            // Create web menu url
            $webMenuUrl = self::create($data);

            // Jika kategori master, create field configs otomatis
            if ($kategoriMenu === 'master' && !empty($data['wmu_akses_tabel'])) {
                $fieldsConfig = $request->input('field_configs', []);
                
                if (empty($fieldsConfig)) {
                    throw new \Exception('Konfigurasi field wajib diisi untuk menu master');
                }

                // Loop create field configs satu per satu
                foreach ($fieldsConfig as $fieldConfig) {
                    $fieldConfig['fk_web_menu_url'] = $webMenuUrl->web_menu_url_id;
                    
                    // Process FK display columns dari checkbox array ke JSON
                    if (!empty($fieldConfig['fk_display_cols']) && is_array($fieldConfig['fk_display_cols'])) {
                        $fieldConfig['wmfc_fk_display_columns'] = json_encode($fieldConfig['fk_display_cols']);
                        unset($fieldConfig['fk_display_cols']);
                    }
                    
                    // Build criteria JSON
                    $criteria = [];
                    if (!empty($fieldConfig['criteria_unique'])) {
                        $criteria['unique'] = true;
                    }
                    if (!empty($fieldConfig['criteria_uppercase'])) {
                        $criteria['case'] = 'uppercase';
                    } elseif (!empty($fieldConfig['criteria_lowercase'])) {
                        $criteria['case'] = 'lowercase';
                    }
                    $fieldConfig['wmfc_criteria'] = !empty($criteria) ? json_encode($criteria) : null;
                    
                    // Build validation JSON
                    $validation = [];
                    if (!empty($fieldConfig['validation_required'])) {
                        $validation['required'] = true;
                    }
                    if (!empty($fieldConfig['validation_unique'])) {
                        $validation['unique'] = true;
                    }
                    if (!empty($fieldConfig['validation_email'])) {
                        $validation['email'] = true;
                    }
                    if (!empty($fieldConfig['validation_max'])) {
                        $validation['max'] = (int)$fieldConfig['validation_max'];
                    }
                    if (!empty($fieldConfig['validation_min'])) {
                        $validation['min'] = (int)$fieldConfig['validation_min'];
                    }
                    $fieldConfig['wmfc_validation'] = !empty($validation) ? json_encode($validation) : null;
                    
                    // Remove checkbox individual keys
                    unset($fieldConfig['criteria_unique'], $fieldConfig['criteria_uppercase'], $fieldConfig['criteria_lowercase']);
                    unset($fieldConfig['validation_required'], $fieldConfig['validation_unique'], $fieldConfig['validation_email'], $fieldConfig['validation_max'], $fieldConfig['validation_min']);
                    
                    WebMenuFieldConfigModel::createData($fieldConfig);
                }
            }

            // Log transaction
            $transactionType = $isUpdate ? 'UPDATED' : 'CREATED';
            TransactionModel::createData(
                $transactionType,
                $webMenuUrl->web_menu_url_id,
                $webMenuUrl->wmu_nama
            );

            DB::commit();

            $message = $isUpdate 
                ? 'Konfigurasi menu berhasil diperbarui (menu lama telah di-archive)' 
                : 'URL menu berhasil dibuat';

            return self::responFormatSukses($webMenuUrl, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat URL menu');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $webMenuUrl = self::findOrFail($id);
            $kategoriMenu = $request->input('web_menu_url.wmu_kategori_menu', $webMenuUrl->wmu_kategori_menu);
            $data = $request->web_menu_url;

            // Set default values berdasarkan kategori
            if ($kategoriMenu === 'master') {
                $data['controller_name'] = 'Template\MasterController';
                $data['wmu_parent_id'] = null;
                $data['module_type'] = 'sisfo';
            } elseif ($kategoriMenu === 'pengajuan') {
                throw new \Exception('Kategori menu "pengajuan" masih dalam tahap pengembangan');
            } elseif ($kategoriMenu === 'custom') {
                $data['wmu_akses_tabel'] = null;
            }

            // ✅ UPDATE: Selalu TRUE UPDATE (tidak delete-create)
            // Bahkan jika struktur tabel berubah, tetap UPDATE existing records
            $webMenuUrl->update($data);

            // Jika kategori master dan ada perubahan field configs
            if ($kategoriMenu === 'master' && $request->has('field_configs')) {
                $fieldsConfig = $request->input('field_configs', []);

                // ✅ TRUE UPDATE: Update existing field configs (tidak delete-create)
                if (!empty($fieldsConfig)) {
                    foreach ($fieldsConfig as $fieldConfig) {
                        // Parse criteria & validation dari checkbox
                        $fieldConfig['wmfc_criteria'] = self::parseCriteriaFromRequest($fieldConfig);
                        $fieldConfig['wmfc_validation'] = self::parseValidationFromRequest($fieldConfig);
                        
                        // Check apakah field config sudah ada (UPDATE) atau baru (CREATE)
                        if (!empty($fieldConfig['web_menu_field_config_id'])) {
                            // UPDATE existing field config
                            $existingField = WebMenuFieldConfigModel::find($fieldConfig['web_menu_field_config_id']);
                            
                            if ($existingField) {
                                // Update data field config
                                $existingField->wmfc_field_label = $fieldConfig['wmfc_field_label'];
                                $existingField->wmfc_field_type = $fieldConfig['wmfc_field_type'];
                                $existingField->wmfc_criteria = $fieldConfig['wmfc_criteria'];
                                $existingField->wmfc_validation = $fieldConfig['wmfc_validation'];
                                $existingField->wmfc_order = $fieldConfig['wmfc_order'] ?? $existingField->wmfc_order;
                                $existingField->wmfc_is_visible = $fieldConfig['wmfc_is_visible'] ?? 1;
                                
                                // Update FK display columns jika ada
                                if (!empty($fieldConfig['fk_display_cols']) && is_array($fieldConfig['fk_display_cols'])) {
                                    $existingField->wmfc_fk_display_columns = json_encode($fieldConfig['fk_display_cols']);
                                }
                                
                                $existingField->save();
                            }
                        } else {
                            // CREATE new field config (jika ada field baru ditambahkan)
                            $fieldConfig['fk_web_menu_url'] = $webMenuUrl->web_menu_url_id;
                            WebMenuFieldConfigModel::createData($fieldConfig);
                        }
                    }
                }
            }

            // Log transaction
            TransactionModel::createData(
                'UPDATED',
                $webMenuUrl->web_menu_url_id,
                $webMenuUrl->wmu_nama
            );

            DB::commit();

            return self::responFormatSukses($webMenuUrl, 'URL menu berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui URL menu');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $webMenuUrl = self::findOrFail($id);

            // Check if the URL is being used by a menu
            $menuUsage = DB::table('web_menu_global')->where('fk_web_menu_url', $id)->where('isDeleted', 0)->count();

            if ($menuUsage > 0) {
                // Tampilkan pesan tanpa menggunakan exception yang akan digabung dengan pesan lain
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Gagal menghapus URL ({$webMenuUrl->wmu_nama}) dikarenakan sedang digunakan pada tabel web_menu_global"
                ];
            }

            $webMenuUrl->delete();

            TransactionModel::createData(
                'DELETED',
                $webMenuUrl->web_menu_url_id,
                $webMenuUrl->wmu_nama
            );

            DB::commit();

            return self::responFormatSukses($webMenuUrl, 'URL menu berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus URL menu: ' . $e->getMessage());
        }
    }

    public static function detailData($id)
    {
        return self::with('application')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $kategoriMenu = $request->input('web_menu_url.wmu_kategori_menu', 'custom');

        // Base rules
        $rules = [
            'web_menu_url.fk_m_application' => 'required|exists:m_application,application_id',
            'web_menu_url.wmu_nama' => 'required|max:255',
            'web_menu_url.wmu_keterangan' => 'nullable|max:1000',
            'web_menu_url.wmu_kategori_menu' => 'required|in:master,pengajuan,custom',
        ];

        $messages = [
            'web_menu_url.fk_m_application.required' => 'Aplikasi wajib dipilih',
            'web_menu_url.fk_m_application.exists' => 'Aplikasi tidak valid',
            'web_menu_url.wmu_nama.required' => 'Nama URL menu wajib diisi',
            'web_menu_url.wmu_nama.max' => 'Nama URL menu maksimal 255 karakter',
            'web_menu_url.wmu_keterangan.max' => 'Keterangan maksimal 1000 karakter',
            'web_menu_url.wmu_kategori_menu.required' => 'Kategori menu wajib dipilih',
            'web_menu_url.wmu_kategori_menu.in' => 'Kategori menu tidak valid',
        ];

        // Conditional rules berdasarkan kategori
        if ($kategoriMenu === 'master') {
            $rules['web_menu_url.wmu_akses_tabel'] = 'required|max:100';
            $rules['field_configs'] = 'required|array|min:1';

            $messages['web_menu_url.wmu_akses_tabel.required'] = 'Nama tabel wajib diisi untuk menu master';
            $messages['web_menu_url.wmu_akses_tabel.max'] = 'Nama tabel maksimal 100 karakter';
            $messages['field_configs.required'] = 'Konfigurasi field wajib diisi untuk menu master';
            $messages['field_configs.min'] = 'Minimal harus ada 1 field yang dikonfigurasi';
        } elseif ($kategoriMenu === 'custom') {
            $rules['web_menu_url.controller_name'] = 'required|max:255';
            $rules['web_menu_url.module_type'] = 'required|in:sisfo,user';

            $messages['web_menu_url.controller_name.required'] = 'Controller name wajib diisi untuk menu custom';
            $messages['web_menu_url.controller_name.max'] = 'Controller name maksimal 255 karakter';
            $messages['web_menu_url.module_type.required'] = 'Module type wajib dipilih';
            $messages['web_menu_url.module_type.in'] = 'Module type harus sisfo atau user';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi field configs jika kategori master
        if ($kategoriMenu === 'master') {
            WebMenuFieldConfigModel::validasiData($request);
        }

        return true;
    }
    public static function validateAppKey($appKey)
    {
        return ApplicationModel::where('app_key', $appKey)->exists();
    }

    public function scopeByAppKey($query, $app_key)
    {
        return $query->whereHas('application', function ($q) use ($app_key) {
            $q->where('app_key', $app_key);
        });
    }

    public static function getApplications()
    {
        return ApplicationModel::where('isDeleted', 0)
            ->orderBy('app_nama')
            ->get();
    }

    /**
     * Validasi apakah tabel ada di database
     * 
     * @param string $tableName Nama tabel
     * @param int|null $menuUrlId ID menu URL untuk update mode (exclude from duplicate check)
     * @return array [success, message, tableStructure, data]
     */
    public static function validateTable(string $tableName, ?int $menuUrlId = null)
    {
        try {
            // Check tabel exists
            if (!DatabaseSchemaService::tableExists($tableName)) {
                return [
                    'success' => false,
                    'message' => "Tabel yang anda inputkan <strong>tidak ada</strong> atau anda <strong>typo</strong> silahkan check ketersediaan tabel didatabase",
                    'tableStructure' => null,
                    'isDuplicate' => false,
                    'hasChanges' => false,
                ];
            }

            // Get table structure
            $tableStructure = DatabaseSchemaService::getTableStructure($tableName);
            
            // Check common fields (7 required fields)
            $requiredCommonFields = ['isDeleted', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
            $existingColumns = array_column($tableStructure, 'column_name');
            $missingFields = array_diff($requiredCommonFields, $existingColumns);
            
            if (!empty($missingFields)) {
                $missingFieldsStr = implode(', ', $missingFields);
                return [
                    'success' => false,
                    'message' => "Tabel ini tidak memiliki common fields: <strong>{$missingFieldsStr}</strong>. Tidak bisa melanjutkan config fields karena tabel tidak memenuhi standar struktur.",
                    'tableStructure' => $tableStructure,
                    'isDuplicate' => false,
                    'hasChanges' => false,
                ];
            }

            // ✅ UPDATE MODE: Jika menuUrlId ada, check changes untuk menu tersebut
            if ($menuUrlId) {
                $changes = self::detectTableChanges($tableName, $menuUrlId);
                
                // ✅ FIX: Get existing field configs dari database
                $existingConfigs = WebMenuFieldConfigModel::where('fk_web_menu_url', $menuUrlId)
                    ->where('isDeleted', 0)
                    ->get()
                    ->keyBy('wmfc_column_name');
                
                // Auto-generate field configs dari struktur tabel terbaru
                $generatedFields = self::autoGenerateFieldConfigs($tableName);
                
                // ✅ MERGE: Combine generated fields dengan existing configs
                $fields = [];
                foreach ($generatedFields as $generatedField) {
                    $columnName = $generatedField['wmfc_column_name'];
                    $existing = $existingConfigs->get($columnName);
                    
                    if ($existing) {
                        // ✅ Field sudah ada - pakai data existing (validation, criteria, label, etc)
                        $fields[] = [
                            'web_menu_field_config_id' => $existing->web_menu_field_config_id,
                            'wmfc_column_name' => $existing->wmfc_column_name,
                            'wmfc_column_type' => $generatedField['wmfc_column_type'], // Update dari struktur terbaru
                            'wmfc_field_label' => $existing->wmfc_field_label, // Keep user's label
                            'wmfc_field_type' => $existing->wmfc_field_type, // Keep user's choice
                            'wmfc_field_type_options' => $generatedField['wmfc_field_type_options'],
                            'wmfc_max_length' => $generatedField['wmfc_max_length'],
                            'wmfc_criteria' => $existing->wmfc_criteria, // ✅ Keep existing criteria
                            'wmfc_validation' => $existing->wmfc_validation, // ✅ Keep existing validation
                            'wmfc_fk_table' => $existing->wmfc_fk_table ?? $generatedField['wmfc_fk_table'],
                            'wmfc_fk_pk_column' => $existing->wmfc_fk_pk_column ?? $generatedField['wmfc_fk_pk_column'],
                            'wmfc_fk_display_columns' => $existing->wmfc_fk_display_columns ?? $generatedField['wmfc_fk_display_columns'],
                            'wmfc_order' => $existing->wmfc_order,
                            'wmfc_is_primary_key' => $generatedField['wmfc_is_primary_key'],
                            'wmfc_is_auto_increment' => $generatedField['wmfc_is_auto_increment'],
                            'wmfc_is_visible' => $existing->wmfc_is_visible,
                            'wmfc_is_foreign_key' => $generatedField['wmfc_is_foreign_key'],
                        ];
                    } else {
                        // ✅ Field baru - pakai generated config
                        $fields[] = $generatedField;
                    }
                }
                
                // Count non-common fields
                $nonCommonFields = array_diff($existingColumns, $requiredCommonFields);
                $totalColumns = count($nonCommonFields);
                
                if (!empty($changes['added']) || !empty($changes['removed']) || !empty($changes['modified'])) {
                    // Ada perubahan struktur
                    return [
                        'success' => true,
                        'isDuplicate' => false,
                        'hasChanges' => true,
                        'table_name' => $tableName,
                        'total_columns' => $totalColumns,
                        'changes' => $changes,
                        'fields' => $fields,
                        'message' => "Terdeteksi perubahan struktur tabel!",
                        'data' => [
                            'isDuplicate' => false,
                            'hasChanges' => true,
                            'table_name' => $tableName,
                            'total_columns' => $totalColumns,
                            'changes' => $changes,
                            'fields' => $fields
                        ]
                    ];
                } else {
                    // Tidak ada perubahan - struktur sama
                    return [
                        'success' => true,
                        'isDuplicate' => false,
                        'hasChanges' => false,
                        'table_name' => $tableName,
                        'total_columns' => $totalColumns,
                        'fields' => $fields,
                        'message' => "Tidak ada perubahan struktur tabel",
                        'data' => [
                            'isDuplicate' => false,
                            'hasChanges' => false, // Explicit boolean false
                            'table_name' => $tableName,
                            'total_columns' => $totalColumns,
                            'fields' => $fields
                        ]
                    ];
                }
            }

            // ✅ CREATE MODE: CHECK DUPLICATE - Cek apakah tabel sudah terdaftar di menu master
            $existingMenu = self::where('wmu_akses_tabel', $tableName)
                ->where('wmu_kategori_menu', 'master')
                ->where('isDeleted', 0)
                ->first();

            // ✅ JIKA TABEL SUDAH TERDAFTAR: Detect changes
            if ($existingMenu) {
                $changes = self::detectTableChanges($tableName, $existingMenu->web_menu_url_id);
                
                if (!empty($changes)) {
                    // Ada perubahan struktur tabel
                    return [
                        'success' => true,
                        'isDuplicate' => true,
                        'hasChanges' => true,
                        'existingMenuId' => $existingMenu->web_menu_url_id,
                        'existingMenuName' => $existingMenu->wmu_nama,
                        'changes' => $changes,
                        'message' => "Tabel '<strong>{$tableName}</strong>' sudah terdaftar sebagai menu master, namun terdeteksi ada <strong>perubahan struktur</strong>. Silakan klik <strong>Detail</strong> untuk melihat perubahan dan konfigurasikan ulang.",
                        'tableStructure' => $tableStructure
                    ];
                } else {
                    // Tidak ada perubahan - BLOCK
                    return [
                        'success' => false,
                        'isDuplicate' => true,
                        'hasChanges' => false,
                        'existingMenuId' => $existingMenu->web_menu_url_id,
                        'existingMenuName' => $existingMenu->wmu_nama,
                        'message' => "Tabel '<strong>{$tableName}</strong>' sudah terdaftar sebagai menu master dengan URL '<strong>{$existingMenu->wmu_nama}</strong>'. Tidak dapat mendaftarkan tabel yang sama dua kali. Silakan edit menu yang sudah ada jika ingin melakukan perubahan.",
                        'tableStructure' => null
                    ];
                }
            }
            
            // Count non-common fields
            $nonCommonFields = array_diff($existingColumns, $requiredCommonFields);
            $fieldCount = count($nonCommonFields);

            return [
                'success' => true,
                'isDuplicate' => false,
                'hasChanges' => false,
                'message' => "Tabel '{$tableName}' ditemukan dengan {$fieldCount} kolom (exclude common fields)",
                'tableStructure' => $tableStructure
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error validasi tabel: ' . $e->getMessage(),
                'tableStructure' => null,
                'isDuplicate' => false,
                'hasChanges' => false,
            ];
        }
    }

    /**
     * Auto generate field configs dari struktur tabel
     * 
     * @param string $tableName Nama tabel
     * @return array Field configs
     */
    public static function autoGenerateFieldConfigs(string $tableName)
    {
        try {
            $schemaService = app(DatabaseSchemaService::class);

            // Get table structure
            $tableStructure = $schemaService::getTableStructure($tableName);
            $primaryKey = $schemaService::getPrimaryKey($tableName);
            $foreignKeys = $schemaService::getForeignKeys($tableName);

            $fieldConfigs = [];
            $order = 1;

            foreach ($tableStructure as $column) {
                $columnName = $column['column_name'];

                // Skip common fields (audit trail)
                $commonFields = ['isDeleted', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
                if (in_array($columnName, $commonFields)) {
                    continue;
                }

                // Detect field type
                $fieldType = $schemaService::suggestFieldType(
                    $column['data_type'],
                    $column['is_foreign_key'], // ✅ FIX: Use is_foreign_key instead of is_nullable
                    $columnName
                );

                // Generate label
                $fieldLabel = $schemaService::generateFieldLabel($columnName);

                // Detect criteria
                $criteria = $schemaService::detectDefaultCriteria($column);

                // Parse max length dari data_type_full terlebih dahulu
                $maxLength = null;
                if (!empty($column['data_type_full'])) {
                    if (preg_match('/\(([0-9]+)/', $column['data_type_full'], $matches)) {
                        // Hanya untuk varchar, char yang butuh max length validation
                        if (in_array(strtolower($column['data_type']), ['varchar', 'char'])) {
                            $maxLength = (int)$matches[1];
                        }
                    }
                }

                // Build validation - Default required TRUE untuk semua kolom
                $validation = [];
                // ✅ Default required = true untuk semua kolom (kecuali PK auto increment - dihandle di frontend)
                $validation['required'] = true;
                
                if ($maxLength) {
                    $validation['max'] = $maxLength;
                }

                // Check if FK
                $fkInfo = collect($foreignKeys)->firstWhere('column_name', $columnName);
                $fkTable = null;
                $fkPkColumn = null;
                $fkDisplayColumns = [];

                if ($fkInfo) {
                    $fieldType = 'search';
                    $fkTable = $fkInfo['referenced_table'];
                    $fkPkColumn = $fkInfo['referenced_column'];

                    // Get displayable columns dari FK table
                    $fkDisplayColumnsObjects = $schemaService::getFKDisplayableColumns($fkTable);
                    
                    // Extract hanya column_name (bukan object) untuk disimpan
                    $fkDisplayColumns = array_map(function($col) {
                        return $col['column_name'];
                    }, $fkDisplayColumnsObjects);
                }

                // Determine length display untuk UI (e.g., VARCHAR(200), INT(11))
                $lengthDisplay = '';
                if (!empty($column['data_type_full'])) {
                    // Extract length dari tipe data untuk display
                    if (preg_match('/\(([0-9,]+)\)/', $column['data_type_full'], $matches)) {
                        $lengthDisplay = "({$matches[1]})";
                    }
                }
                
                $fieldConfigs[] = [
                    'wmfc_column_name' => $columnName,
                    'wmfc_column_type' => strtoupper($column['data_type']) . $lengthDisplay,
                    'wmfc_field_label' => $fieldLabel,
                    'wmfc_field_type' => $fieldType,
                    'wmfc_field_type_options' => self::getFieldTypeOptions($column['data_type'], (bool)$fkInfo),
                    'wmfc_max_length' => $maxLength,
                    'wmfc_criteria' => !empty($criteria) ? json_encode($criteria) : null,
                    'wmfc_validation' => !empty($validation) ? json_encode($validation) : null,
                    'wmfc_fk_table' => $fkTable,
                    'wmfc_fk_pk_column' => $fkPkColumn,
                    'wmfc_fk_display_columns' => !empty($fkDisplayColumns) ? json_encode($fkDisplayColumns) : null,
                    'wmfc_order' => $order++,
                    'wmfc_is_primary_key' => ($columnName === $primaryKey) ? 1 : 0,
                    'wmfc_is_auto_increment' => ($column['extra'] === 'auto_increment') ? 1 : 0,
                    'wmfc_is_visible' => ($columnName === $primaryKey && $column['extra'] === 'auto_increment') ? 0 : 1,
                    'wmfc_is_foreign_key' => $fkInfo ? 1 : 0,
                ];
            }

            return $fieldConfigs;
        } catch (\Exception $e) {
            throw new \Exception('Gagal generate field configs: ' . $e->getMessage());
        }
    }

    /**
     * Get field type options berdasarkan MySQL data type
     * 
     * @param string $dataType MySQL data type
     * @param bool $isFk Apakah kolom ini foreign key
     * @return array
     */
    private static function getFieldTypeOptions(string $dataType, bool $isFk = false): array
    {
        $dataType = strtolower($dataType);
        
        // Jika FK, buka semua opsi untuk fleksibilitas
        if ($isFk) {
            return ['search', 'dropdown', 'radio'];
        }
        
        // String types
        if (in_array($dataType, ['varchar', 'char', 'tinytext'])) {
            return ['text', 'textarea'];
        }
        
        // Long text types
        if (in_array($dataType, ['text', 'mediumtext', 'longtext'])) {
            return ['textarea'];
        }
        
        // Numeric types
        if (in_array($dataType, ['int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'decimal', 'float', 'double'])) {
            return ['number'];
        }
        
        // Date types
        if (in_array($dataType, ['date', 'datetime', 'timestamp'])) {
            return ['date', 'date2'];
        }
        
        // Enum type
        if ($dataType === 'enum') {
            return ['dropdown', 'radio'];
        }
        
        // Default: text & textarea
        return ['text', 'textarea'];
    }

    /**
     * Get detail data with field configs
     * 
     * @param int $id ID web menu url
     * @return object
     */
    public static function detailDataWithConfigs($id)
    {
        $menuUrl = self::with(['application', 'fieldConfigs' => function ($query) {
            $query->where('isDeleted', 0)->orderBy('wmfc_order', 'ASC');
        }])->findOrFail($id);

        return $menuUrl;
    }

    /**
     * Detect changes antara current table structure dengan field config yang tersimpan
     * 
     * @param string $tableName Nama tabel
     * @param int $menuUrlId ID menu URL yang sudah ada
     * @return array Array of changes
     */
    public static function detectTableChanges(string $tableName, int $menuUrlId): array
    {
        try {
            // Get current table structure
            $currentColumns = DatabaseSchemaService::getTableStructure($tableName);
            
            // Get saved field configs
            $savedConfigs = WebMenuFieldConfigModel::where('fk_web_menu_url', $menuUrlId)
                ->where('isDeleted', 0)
                ->get()
                ->keyBy('wmfc_column_name');
            
            $changes = [
                'added' => [],      // Kolom baru yang ditambahkan
                'removed' => [],    // Kolom yang dihapus
                'modified' => [],   // Kolom yang berubah (type, length, dll)
            ];
            
            // Common fields yang di-skip
            $commonFields = ['isDeleted', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
            
            // Check for added and modified columns
            foreach ($currentColumns as $column) {
                $columnName = $column['column_name'];
                
                // Skip common fields
                if (in_array($columnName, $commonFields)) {
                    continue;
                }
                
                if (!isset($savedConfigs[$columnName])) {
                    // Kolom baru ditambahkan
                    $changes['added'][] = [
                        'column_name' => $columnName,
                        'data_type' => strtoupper($column['data_type_full']),
                        'is_nullable' => $column['is_nullable'],
                        'is_primary_key' => $column['is_primary_key'],
                    ];
                } else {
                    // Check if modified
                    $savedConfig = $savedConfigs[$columnName];
                    $currentType = strtoupper($column['data_type_full']);
                    $savedType = $savedConfig->wmfc_column_type;
                    
                    if ($currentType !== $savedType) {
                        $changes['modified'][] = [
                            'column_name' => $columnName,
                            'old_type' => $savedType,
                            'new_type' => $currentType,
                        ];
                    }
                }
            }
            
            // Check for removed columns
            $currentColumnNames = array_column($currentColumns, 'column_name');
            foreach ($savedConfigs as $savedConfig) {
                if (!in_array($savedConfig->wmfc_column_name, $currentColumnNames)) {
                    $changes['removed'][] = [
                        'column_name' => $savedConfig->wmfc_column_name,
                        'data_type' => $savedConfig->wmfc_column_type,
                    ];
                }
            }
            
            // Return only if there are actual changes
            if (empty($changes['added']) && empty($changes['removed']) && empty($changes['modified'])) {
                return [];
            }
            
            return $changes;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Parse criteria data dari request checkboxes ke JSON format
     * 
     * @param array $fieldConfig Field config data dari request
     * @return string|null JSON string atau null jika tidak ada criteria
     */
    private static function parseCriteriaFromRequest(array $fieldConfig): ?string
    {
        $criteria = [];
        
        // Check unique criteria
        if (!empty($fieldConfig['criteria_unique'])) {
            $criteria['unique'] = true;
        }
        
        // Check case criteria (uppercase/lowercase)
        if (!empty($fieldConfig['criteria_uppercase'])) {
            $criteria['case'] = 'uppercase';
        } elseif (!empty($fieldConfig['criteria_lowercase'])) {
            $criteria['case'] = 'lowercase';
        }
        
        // Return JSON atau null
        return !empty($criteria) ? json_encode($criteria) : null;
    }

    /**
     * Parse validation data dari request checkboxes ke JSON format
     * 
     * @param array $fieldConfig Field config data dari request
     * @return string|null JSON string atau null jika tidak ada validation
     */
    private static function parseValidationFromRequest(array $fieldConfig): ?string
    {
        $validation = [];
        
        // Check required validation
        if (!empty($fieldConfig['validation_required'])) {
            $validation['required'] = true;
        }
        
        // Check unique validation
        if (!empty($fieldConfig['validation_unique'])) {
            $validation['unique'] = true;
        }
        
        // Check email validation
        if (!empty($fieldConfig['validation_email'])) {
            $validation['email'] = true;
        }
        
        // Check max length validation
        if (!empty($fieldConfig['validation_max'])) {
            $validation['max'] = (int)$fieldConfig['validation_max'];
        }
        
        // Check min length validation
        if (!empty($fieldConfig['validation_min'])) {
            $validation['min'] = (int)$fieldConfig['validation_min'];
        }
        
        // Return JSON atau null
        return !empty($validation) ? json_encode($validation) : null;
    }
}
