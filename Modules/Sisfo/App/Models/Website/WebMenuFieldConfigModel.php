<?php

namespace Modules\Sisfo\App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\TraitsModel;

class WebMenuFieldConfigModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu_field_config';
    protected $primaryKey = 'web_menu_field_config_id';
    
    protected $fillable = [
        'fk_web_menu_url',
        'wmfc_column_name',
        'wmfc_column_type',
        'wmfc_field_label',
        'wmfc_field_type',
        'wmfc_criteria',
        'wmfc_validation',
        'wmfc_fk_table',
        'wmfc_fk_pk_column',
        'wmfc_fk_display_columns',
        'wmfc_max_length',
        'wmfc_order',
        'wmfc_is_primary_key',
        'wmfc_is_auto_increment',
        'wmfc_is_visible',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // RELASI

    public function webMenuUrl()
    {
        return $this->belongsTo(
            WebMenuUrlModel::class,
            'fk_web_menu_url',
            'web_menu_url_id'
        );
    }

    // MAIN METHODS

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

    public static function createData(array $data)
    {
        try {
            DB::beginTransaction();

            $allowedTypes = ['text', 'textarea', 'number', 'date', 'date2', 'dropdown', 'radio', 'search'];
            if (!in_array($data['wmfc_field_type'], $allowedTypes)) {
                throw new \Exception("Tipe field '{$data['wmfc_field_type']}' tidak valid");
            }

            $fieldConfig = new self();
            $fieldConfig->fill($data);
            $fieldConfig->save();

            DB::commit();
            return $fieldConfig;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal menyimpan field config: " . $e->getMessage());
        }
    }

    public static function updateData(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $fieldConfig = self::findOrFail($id);
            $fieldConfig->fill($data);
            $fieldConfig->save();

            DB::commit();
            return $fieldConfig;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal update field config: " . $e->getMessage());
        }
    }

    public static function deleteData(int $id)
    {
        try {
            DB::beginTransaction();

            $fieldConfig = self::findOrFail($id);
            $fieldConfig->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal delete field config: " . $e->getMessage());
        }
    }

    public static function detailData($id)
    {
        return self::with('webMenuUrl')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        Log::info('====================================');
        Log::info('🔍 FIELD CONFIG VALIDATION - START');
        Log::info('====================================');
        Log::info('Field Configs Count: ' . count($request->input('field_configs', [])));
        
        foreach ($request->input('field_configs', []) as $index => $fieldConfig) {
            Log::info("Field Config [{$index}]:", ['config' => $fieldConfig]);
        }
        
        $rules = [
            'field_configs.*.wmfc_column_name' => 'required|max:100',
            'field_configs.*.wmfc_field_label' => 'required|max:255',
            'field_configs.*.wmfc_field_type' => 'required|in:text,textarea,number,date,date2,dropdown,radio,search',
            'field_configs.*.wmfc_order' => 'required|integer|min:0',
            'field_configs.*.wmfc_is_primary_key' => 'required|boolean',
            'field_configs.*.wmfc_is_auto_increment' => 'required|boolean',
            'field_configs.*.wmfc_is_visible' => 'required|boolean',
        ];

        $messages = [
            'field_configs.*.wmfc_column_name.required' => 'Nama kolom wajib diisi',
            'field_configs.*.wmfc_column_name.max' => 'Nama kolom maksimal 100 karakter',
            'field_configs.*.wmfc_field_label.required' => 'Label field wajib diisi',
            'field_configs.*.wmfc_field_label.max' => 'Label field maksimal 255 karakter',
            'field_configs.*.wmfc_field_type.required' => 'Tipe field wajib dipilih',
            'field_configs.*.wmfc_field_type.in' => 'Tipe field tidak valid',
            'field_configs.*.wmfc_order.required' => 'Urutan field wajib diisi',
            'field_configs.*.wmfc_order.integer' => 'Urutan field harus berupa angka',
            'field_configs.*.wmfc_order.min' => 'Urutan field minimal 0',
            'field_configs.*.wmfc_is_primary_key.required' => 'Status primary key wajib diisi',
            'field_configs.*.wmfc_is_primary_key.boolean' => 'Status primary key harus boolean',
            'field_configs.*.wmfc_is_auto_increment.required' => 'Status auto increment wajib diisi',
            'field_configs.*.wmfc_is_auto_increment.boolean' => 'Status auto increment harus boolean',
            'field_configs.*.wmfc_is_visible.required' => 'Status visible wajib diisi',
            'field_configs.*.wmfc_is_visible.boolean' => 'Status visible harus boolean',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            Log::error('❌ FIELD CONFIG VALIDATION FAILED');
            Log::error('Errors:', ['errors' => $validator->errors()->toArray()]);
            Log::error('====================================');
            
            throw new ValidationException($validator);
        }

        Log::info('✅ FIELD CONFIG VALIDATION PASSED');
        Log::info('====================================');

        return true;
    }

    // CUSTOM METHODS

    public static function getByMenuUrlId(int $menuUrlId, bool $visibleOnly = true)
    {
        $query = self::where('fk_web_menu_url', $menuUrlId)
            ->where('isDeleted', 0);

        if ($visibleOnly) {
            $query->where('wmfc_is_visible', 1);
        }

        return $query->orderBy('wmfc_order', 'ASC')->get();
    }

    public static function getByColumnName(int $menuUrlId, string $columnName)
    {
        return self::where('fk_web_menu_url', $menuUrlId)
            ->where('wmfc_column_name', $columnName)
            ->where('isDeleted', 0)
            ->first();
    }

    public static function getPrimaryKeyField(int $menuUrlId)
    {
        return self::where('fk_web_menu_url', $menuUrlId)
            ->where('wmfc_is_primary_key', 1)
            ->where('isDeleted', 0)
            ->first();
    }

    public static function getForeignKeyFields(int $menuUrlId)
    {
        return self::where('fk_web_menu_url', $menuUrlId)
            ->where('wmfc_field_type', 'search')
            ->whereNotNull('wmfc_fk_table')
            ->where('isDeleted', 0)
            ->orderBy('wmfc_order', 'ASC')
            ->get();
    }

    public static function reorderFields(array $orderedIds)
    {
        try {
            DB::beginTransaction();

            foreach ($orderedIds as $index => $id) {
                $fieldConfig = self::findOrFail($id);
                $fieldConfig->wmfc_order = $index + 1;
                $fieldConfig->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal reorder fields: " . $e->getMessage());
        }
    }

    public static function getValidationRulesArray(int $menuUrlId): array
    {
        $fields = self::getByMenuUrlId($menuUrlId, true);
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];
            $validation = $field->wmfc_validation ?? [];

            if (!empty($validation['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if (!empty($validation['max'])) {
                $fieldRules[] = 'max:' . $validation['max'];
            }

            if (!empty($validation['min'])) {
                $fieldRules[] = 'min:' . $validation['min'];
            }

            if (!empty($validation['email'])) {
                $fieldRules[] = 'email';
            }

            $criteria = $field->wmfc_criteria ?? [];
            if (!empty($criteria['unique'])) {
                $tableName = $field->webMenuUrl->wmu_akses_tabel ?? '';
                if ($tableName) {
                    $fieldRules[] = 'unique:' . $tableName . ',' . $field->wmfc_column_name;
                }
            }

            $rules[$field->wmfc_column_name] = implode('|', $fieldRules);
        }

        return $rules;
    }

    public static function applyFieldCriteria(string $columnName, $value, int $menuUrlId)
    {
        $field = self::getByColumnName($menuUrlId, $columnName);
        
        if (!$field || !$value) {
            return $value;
        }

        $criteria = $field->wmfc_criteria ?? [];

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
