<?php

namespace Modules\Sisfo\App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Models\TraitsModel;

/**
 * Model WebMenuFieldConfigModel
 * 
 * Model untuk mengelola konfigurasi field pada menu master (Template Master System).
 * Setiap menu master memiliki konfigurasi field yang disimpan di tabel ini.
 * 
 * @package Modules\Sisfo\App\Models\Website
 * @author Development Team
 * @version 1.0.0
 * 
 * @property int $web_menu_field_config_id
 * @property int $fk_web_menu_url
 * @property string $wmfc_column_name
 * @property string $wmfc_field_label
 * @property string $wmfc_field_type
 * @property array|null $wmfc_criteria
 * @property array|null $wmfc_validation
 * @property string|null $wmfc_fk_table
 * @property string|null $wmfc_fk_pk_column
 * @property array|null $wmfc_fk_display_columns
 * @property int $wmfc_order
 * @property int $wmfc_is_primary_key
 * @property int $wmfc_is_auto_increment
 * @property int $wmfc_is_visible
 */
class WebMenuFieldConfigModel extends Model
{
    use TraitsModel;

    /**
     * Table name
     */
    protected $table = 'web_menu_field_config';

    /**
     * Primary key
     */
    protected $primaryKey = 'web_menu_field_config_id';

    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Fillable attributes
     */
    protected $fillable = [
        'fk_web_menu_url',
        'wmfc_column_name',
        'wmfc_field_label',
        'wmfc_field_type',
        'wmfc_criteria',
        'wmfc_validation',
        'wmfc_fk_table',
        'wmfc_fk_pk_column',
        'wmfc_fk_display_columns',
        'wmfc_order',
        'wmfc_is_primary_key',
        'wmfc_is_auto_increment',
        'wmfc_is_visible',
    ];

    /**
     * Attributes that should be cast
     */
    protected $casts = [
        'wmfc_criteria' => 'array',
        'wmfc_validation' => 'array',
        'wmfc_fk_display_columns' => 'array',
        'wmfc_order' => 'integer',
        'wmfc_is_primary_key' => 'boolean',
        'wmfc_is_auto_increment' => 'boolean',
        'wmfc_is_visible' => 'boolean',
        'isDeleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Constructor
     * 
     * Merge fillable dengan common fields dari trait
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // ==========================================
    // RELASI
    // ==========================================

    /**
     * Relasi ke WebMenuUrlModel
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function webMenuUrl()
    {
        return $this->belongsTo(
            WebMenuUrlModel::class,
            'fk_web_menu_url',
            'web_menu_url_id'
        );
    }

    // ==========================================
    // CRUD METHODS
    // ==========================================

    /**
     * Select Data (dengan pagination)
     * 
     * Query untuk mengambil data field config dengan filter & pagination
     * 
     * @param array $params Parameter filter (web_menu_url_id, visible, etc)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function selectData(array $params = [])
    {
        $query = self::query()
            ->select(
                'web_menu_field_config.*',
                'web_menu_url.wmu_nama',
                'web_menu_url.wmu_akses_tabel'
            )
            ->leftJoin('web_menu_url', 'web_menu_field_config.fk_web_menu_url', '=', 'web_menu_url.web_menu_url_id')
            ->where('web_menu_field_config.isDeleted', 0);

        // Filter by web_menu_url_id
        if (isset($params['web_menu_url_id']) && !empty($params['web_menu_url_id'])) {
            $query->where('web_menu_field_config.fk_web_menu_url', $params['web_menu_url_id']);
        }

        // Filter by visible
        if (isset($params['visible'])) {
            $query->where('web_menu_field_config.wmfc_is_visible', $params['visible']);
        }

        // Filter by field type
        if (isset($params['field_type']) && !empty($params['field_type'])) {
            $query->where('web_menu_field_config.wmfc_field_type', $params['field_type']);
        }

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('web_menu_field_config.wmfc_column_name', 'like', "%{$search}%")
                    ->orWhere('web_menu_field_config.wmfc_field_label', 'like', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $params['order_by'] ?? 'wmfc_order';
        $orderDir = $params['order_dir'] ?? 'ASC';
        $query->orderBy("web_menu_field_config.{$orderBy}", $orderDir);

        // Pagination
        $perPage = $params['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Create Data
     * 
     * Insert field config baru ke database
     * 
     * @param array $data Data field config
     * @return self
     * @throws \Exception
     */
    public static function createData(array $data)
    {
        try {
            $fieldConfig = new self();
            $fieldConfig->fill($data);
            $fieldConfig->created_by = auth()->user()->user_id ?? null;
            $fieldConfig->save();

            return $fieldConfig;
        } catch (\Exception $e) {
            throw new \Exception("Gagal menyimpan field config: " . $e->getMessage());
        }
    }

    /**
     * Update Data
     * 
     * Update field config berdasarkan ID
     * 
     * @param int $id ID field config
     * @param array $data Data yang akan diupdate
     * @return self
     * @throws \Exception
     */
    public static function updateData(int $id, array $data)
    {
        try {
            $fieldConfig = self::findOrFail($id);
            $fieldConfig->fill($data);
            $fieldConfig->updated_by = auth()->user()->user_id ?? null;
            $fieldConfig->save();

            return $fieldConfig;
        } catch (\Exception $e) {
            throw new \Exception("Gagal update field config: " . $e->getMessage());
        }
    }

    /**
     * Delete Data (Soft Delete)
     * 
     * Soft delete field config berdasarkan ID
     * 
     * @param int $id ID field config
     * @return bool
     * @throws \Exception
     */
    public static function deleteData(int $id)
    {
        try {
            $fieldConfig = self::findOrFail($id);
            $fieldConfig->isDeleted = 1;
            $fieldConfig->deleted_by = auth()->user()->user_id ?? null;
            $fieldConfig->deleted_at = now();
            $fieldConfig->save();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Gagal delete field config: " . $e->getMessage());
        }
    }

    /**
     * Validasi Data (Server-side)
     * 
     * Rules validasi untuk form input
     * 
     * @param int|null $id ID untuk update (exclude unique check)
     * @return array
     */
    public static function validasiData(?int $id = null): array
    {
        return [
            'fk_web_menu_url' => 'required|integer|exists:web_menu_url,web_menu_url_id',
            'wmfc_column_name' => 'required|string|max:100',
            'wmfc_field_label' => 'required|string|max:255',
            'wmfc_field_type' => 'required|string|in:text,textarea,number,date,date2,dropdown,radio,search',
            'wmfc_criteria' => 'nullable|json',
            'wmfc_validation' => 'nullable|json',
            'wmfc_fk_table' => 'nullable|string|max:100',
            'wmfc_fk_pk_column' => 'nullable|string|max:100',
            'wmfc_fk_display_columns' => 'nullable|json',
            'wmfc_order' => 'required|integer|min:0',
            'wmfc_is_primary_key' => 'required|boolean',
            'wmfc_is_auto_increment' => 'required|boolean',
            'wmfc_is_visible' => 'required|boolean',
        ];
    }

    // ==========================================
    // CUSTOM METHODS
    // ==========================================

    /**
     * Get Field Configs by Menu URL ID
     * 
     * Ambil semua field config untuk menu tertentu (ordered)
     * 
     * @param int $menuUrlId ID menu URL
     * @param bool $visibleOnly Ambil hanya yang visible
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByMenuUrlId(int $menuUrlId, bool $visibleOnly = true)
    {
        $query = self::where('fk_web_menu_url', $menuUrlId)
            ->where('isDeleted', 0);

        if ($visibleOnly) {
            $query->where('wmfc_is_visible', 1);
        }

        return $query->orderBy('wmfc_order', 'ASC')->get();
    }

    /**
     * Get Field Config by Column Name
     * 
     * Ambil field config berdasarkan nama kolom
     * 
     * @param int $menuUrlId ID menu URL
     * @param string $columnName Nama kolom
     * @return self|null
     */
    public static function getByColumnName(int $menuUrlId, string $columnName)
    {
        return self::where('fk_web_menu_url', $menuUrlId)
            ->where('wmfc_column_name', $columnName)
            ->where('isDeleted', 0)
            ->first();
    }

    /**
     * Get Primary Key Field
     * 
     * Ambil field yang merupakan Primary Key
     * 
     * @param int $menuUrlId ID menu URL
     * @return self|null
     */
    public static function getPrimaryKeyField(int $menuUrlId)
    {
        return self::where('fk_web_menu_url', $menuUrlId)
            ->where('wmfc_is_primary_key', 1)
            ->where('isDeleted', 0)
            ->first();
    }

    /**
     * Get Foreign Key Fields
     * 
     * Ambil semua field yang merupakan Foreign Key
     * 
     * @param int $menuUrlId ID menu URL
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForeignKeyFields(int $menuUrlId)
    {
        return self::where('fk_web_menu_url', $menuUrlId)
            ->where('wmfc_field_type', 'search')
            ->whereNotNull('wmfc_fk_table')
            ->where('isDeleted', 0)
            ->orderBy('wmfc_order', 'ASC')
            ->get();
    }

    /**
     * Bulk Create Field Configs
     * 
     * Insert multiple field configs sekaligus (untuk saat create menu master)
     * 
     * @param int $menuUrlId ID menu URL
     * @param array $fieldsData Array of field data
     * @return bool
     * @throws \Exception
     */
    public static function bulkCreate(int $menuUrlId, array $fieldsData)
    {
        try {
            DB::beginTransaction();

            foreach ($fieldsData as $fieldData) {
                $fieldData['fk_web_menu_url'] = $menuUrlId;
                self::createData($fieldData);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal bulk create field configs: " . $e->getMessage());
        }
    }

    /**
     * Reorder Fields
     * 
     * Update urutan field configs
     * 
     * @param array $orderedIds Array of field IDs in new order
     * @return bool
     * @throws \Exception
     */
    public static function reorderFields(array $orderedIds)
    {
        try {
            DB::beginTransaction();

            foreach ($orderedIds as $index => $id) {
                $fieldConfig = self::findOrFail($id);
                $fieldConfig->wmfc_order = $index + 1;
                $fieldConfig->updated_by = auth()->user()->user_id ?? null;
                $fieldConfig->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal reorder fields: " . $e->getMessage());
        }
    }

    /**
     * Get Validation Rules Array
     * 
     * Build Laravel validation rules dari wmfc_validation JSON
     * 
     * @param int $menuUrlId ID menu URL
     * @return array
     */
    public static function getValidationRulesArray(int $menuUrlId): array
    {
        $fields = self::getByMenuUrlId($menuUrlId, true);
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];
            $validation = $field->wmfc_validation ?? [];

            // Required
            if (!empty($validation['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Max length
            if (!empty($validation['max'])) {
                $fieldRules[] = 'max:' . $validation['max'];
            }

            // Min length
            if (!empty($validation['min'])) {
                $fieldRules[] = 'min:' . $validation['min'];
            }

            // Email
            if (!empty($validation['email'])) {
                $fieldRules[] = 'email';
            }

            // Unique (dari criteria)
            $criteria = $field->wmfc_criteria ?? [];
            if (!empty($criteria['unique'])) {
                // Ambil table name dari relasi
                $tableName = $field->webMenuUrl->wmu_akses_tabel ?? '';
                if ($tableName) {
                    $fieldRules[] = 'unique:' . $tableName . ',' . $field->wmfc_column_name;
                }
            }

            $rules[$field->wmfc_column_name] = implode('|', $fieldRules);
        }

        return $rules;
    }

    /**
     * Apply Field Criteria to Value
     * 
     * Terapkan kriteria (uppercase/lowercase) ke nilai field
     * 
     * @param string $columnName Nama kolom
     * @param mixed $value Nilai field
     * @param int $menuUrlId ID menu URL
     * @return mixed
     */
    public static function applyFieldCriteria(string $columnName, $value, int $menuUrlId)
    {
        $field = self::getByColumnName($menuUrlId, $columnName);
        
        if (!$field || !$value) {
            return $value;
        }

        $criteria = $field->wmfc_criteria ?? [];

        // Apply case transformation
        if (!empty($criteria['case'])) {
            switch ($criteria['case']) {
                case 'uppercase':
                    $value = strtoupper($value);
                    break;
                case 'lowercase':
                    $value = strtolower($value);
                    break;
            }
        }

        return $value;
    }
}
