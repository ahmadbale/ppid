<?php

namespace Modules\Sisfo\App\Services;

use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\Website\WebMenuFieldConfigModel;

/**
 * Master Menu Service
 * 
 * Service untuk business logic menu master template
 * Handle operasi CRUD dinamis, validation, dan query building
 * 
 * @package Modules\Sisfo\App\Services
 * @author Development Team
 * @version 1.0.0
 */
class MasterMenuService
{
    /**
     * Get menu config by URL
     * 
     * @param string $menuUrl URL menu (wmu_nama)
     * @return object|null
     */
    public static function getMenuConfigByUrl(string $menuUrl): ?object
    {
        return DB::table('web_menu_url')
            ->where('wmu_nama', $menuUrl)
            ->where('module_type', 'sisfo')
            ->where('wmu_kategori_menu', 'master')
            ->where('isDeleted', 0)
            ->first();
    }

    /**
     * Get field configs untuk menu tertentu
     * 
     * @param int $menuUrlId ID menu URL
     * @param bool $visibleOnly Hanya visible fields
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFieldConfigs(int $menuUrlId, bool $visibleOnly = true)
    {
        return WebMenuFieldConfigModel::getByMenuUrlId($menuUrlId, $visibleOnly);
    }

    /**
     * Build Laravel validation rules dari field configs
     * 
     * @param int $menuUrlId ID menu URL
     * @param string $tableName Nama tabel (untuk unique check)
     * @param int|null $excludeId ID yang di-exclude (untuk update)
     * @return array
     */
    public static function buildValidationRules(
        int $menuUrlId, 
        string $tableName,
        ?int $excludeId = null
    ): array {
        $fields = self::getFieldConfigs($menuUrlId, true);
        $rules = [];

        foreach ($fields as $field) {
            $columnName = $field->wmfc_column_name;
            $fieldRules = [];
            
            // Skip auto increment
            if ($field->wmfc_is_auto_increment) {
                continue;
            }

            $validation = $field->wmfc_validation ?? [];
            $criteria = $field->wmfc_criteria ?? [];

            // Required
            if (!empty($validation['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Data type validation
            switch ($field->wmfc_field_type) {
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                    
                case 'date':
                case 'date2':
                    $fieldRules[] = 'date';
                    break;
                    
                case 'search':
                    // FK validation
                    if ($field->wmfc_fk_table && $field->wmfc_fk_pk_column) {
                        $fieldRules[] = "exists:{$field->wmfc_fk_table},{$field->wmfc_fk_pk_column}";
                    }
                    break;
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

            // Unique
            if (!empty($criteria['unique'])) {
                if ($excludeId) {
                    $pkColumn = WebMenuFieldConfigModel::getPrimaryKeyField($menuUrlId);
                    $pkName = $pkColumn ? $pkColumn->wmfc_column_name : 'id';
                    $fieldRules[] = "unique:{$tableName},{$columnName},{$excludeId},{$pkName}";
                } else {
                    $fieldRules[] = "unique:{$tableName},{$columnName}";
                }
            }

            $rules[$columnName] = implode('|', $fieldRules);
        }

        return $rules;
    }

    /**
     * Apply field criteria to data (uppercase/lowercase)
     * 
     * @param array $data Input data
     * @param int $menuUrlId ID menu URL
     * @return array
     */
    public static function applyFieldCriteria(array $data, int $menuUrlId): array
    {
        $fields = self::getFieldConfigs($menuUrlId, false);
        $processed = $data;

        foreach ($fields as $field) {
            $columnName = $field->wmfc_column_name;
            
            if (!isset($processed[$columnName])) {
                continue;
            }

            $criteria = $field->wmfc_criteria ?? [];
            $value = $processed[$columnName];

            // Apply case transformation
            if (!empty($criteria['case']) && is_string($value)) {
                switch ($criteria['case']) {
                    case 'uppercase':
                        $processed[$columnName] = strtoupper($value);
                        break;
                    case 'lowercase':
                        $processed[$columnName] = strtolower($value);
                        break;
                }
            }
        }

        return $processed;
    }

    /**
     * Build SELECT query dinamis dengan JOIN untuk FK
     * 
     * @param string $tableName Nama tabel utama
     * @param int $menuUrlId ID menu URL
     * @return \Illuminate\Database\Query\Builder
     */
    public static function buildSelectQuery(string $tableName, int $menuUrlId)
    {
        $fields = self::getFieldConfigs($menuUrlId, true);
        $query = DB::table($tableName);
        
        // Select columns
        $selectColumns = ["{$tableName}.*"];
        
        // JOIN untuk FK fields
        foreach ($fields as $field) {
            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_table) {
                $fkTable = $field->wmfc_fk_table;
                $fkPkColumn = $field->wmfc_fk_pk_column;
                $columnName = $field->wmfc_column_name;
                $displayColumns = $field->wmfc_fk_display_columns ?? [];

                // LEFT JOIN
                $query->leftJoin(
                    $fkTable,
                    "{$tableName}.{$columnName}",
                    '=',
                    "{$fkTable}.{$fkPkColumn}"
                );

                // Select display columns with alias
                foreach ($displayColumns as $displayCol) {
                    $selectColumns[] = "{$fkTable}.{$displayCol} as {$columnName}_{$displayCol}";
                }
            }
        }

        $query->select($selectColumns);

        return $query;
    }

    /**
     * Build form fields data untuk view
     * 
     * @param int $menuUrlId ID menu URL
     * @param object|null $existingData Data untuk edit (optional)
     * @return array
     */
    public static function buildFormFields(int $menuUrlId, ?object $existingData = null): array
    {
        $fields = self::getFieldConfigs($menuUrlId, true);
        $formFields = [];

        foreach ($fields as $field) {
            // Skip auto increment
            if ($field->wmfc_is_auto_increment) {
                continue;
            }

            $fieldData = [
                'column' => $field->wmfc_column_name,
                'label' => $field->wmfc_field_label,
                'type' => $field->wmfc_field_type,
                'value' => $existingData->{$field->wmfc_column_name} ?? null,
                'validation' => $field->wmfc_validation ?? [],
                'criteria' => $field->wmfc_criteria ?? [],
                'is_pk' => $field->wmfc_is_primary_key,
                'is_auto_increment' => $field->wmfc_is_auto_increment,
                'is_required' => $field->wmfc_validation['required'] ?? false,
            ];

            // FK options
            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_table) {
                $fieldData['fk_table'] = $field->wmfc_fk_table;
                $fieldData['fk_pk_column'] = $field->wmfc_fk_pk_column;
                $fieldData['fk_display_columns'] = $field->wmfc_fk_display_columns ?? [];
            }

            // Dropdown options (untuk ENUM)
            if ($field->wmfc_field_type === 'dropdown') {
                $fieldData['options'] = self::getEnumOptions($field->wmfc_column_name, $menuUrlId);
            }

            $formFields[] = $fieldData;
        }

        return $formFields;
    }

    /**
     * Get ENUM options dari column
     * 
     * @param string $columnName Nama kolom
     * @param int $menuUrlId ID menu URL
     * @return array
     */
    public static function getEnumOptions(string $columnName, int $menuUrlId): array
    {
        $menuConfig = WebMenuUrlModel::find($menuUrlId);
        if (!$menuConfig || !$menuConfig->wmu_akses_tabel) {
            return [];
        }

        $tableName = $menuConfig->wmu_akses_tabel;
        $result = DB::select("SHOW COLUMNS FROM {$tableName} WHERE Field = ?", [$columnName]);
        
        if (empty($result)) {
            return [];
        }

        $type = $result[0]->Type;
        
        // Parse ENUM values
        if (preg_match('/^enum\((.*)\)$/', $type, $matches)) {
            $values = explode(',', $matches[1]);
            return array_map(function($value) {
                return trim($value, "'\"");
            }, $values);
        }

        return [];
    }

    /**
     * Insert data ke tabel dinamis
     * 
     * @param string $tableName Nama tabel
     * @param array $data Data yang akan diinsert
     * @param int $menuUrlId ID menu URL (untuk apply criteria)
     * @return int Insert ID
     */
    public static function insertData(string $tableName, array $data, int $menuUrlId): int
    {
        // Apply criteria
        $processedData = self::applyFieldCriteria($data, $menuUrlId);
        
        // Add common fields
        $processedData['created_by'] = auth()->user()->user_id ?? null;
        $processedData['created_at'] = now();
        
        return DB::table($tableName)->insertGetId($processedData);
    }

    /**
     * Update data di tabel dinamis
     * 
     * @param string $tableName Nama tabel
     * @param int $id Primary key value
     * @param array $data Data yang akan diupdate
     * @param int $menuUrlId ID menu URL (untuk apply criteria)
     * @param string $pkColumn Nama kolom PK
     * @return bool
     */
    public static function updateData(
        string $tableName, 
        int $id, 
        array $data, 
        int $menuUrlId,
        string $pkColumn = 'id'
    ): bool {
        // Apply criteria
        $processedData = self::applyFieldCriteria($data, $menuUrlId);
        
        // Add common fields
        $processedData['updated_by'] = auth()->user()->user_id ?? null;
        $processedData['updated_at'] = now();
        
        return DB::table($tableName)
            ->where($pkColumn, $id)
            ->update($processedData) > 0;
    }

    /**
     * Soft delete data di tabel dinamis
     * 
     * @param string $tableName Nama tabel
     * @param int $id Primary key value
     * @param string $pkColumn Nama kolom PK
     * @return bool
     */
    public static function deleteData(
        string $tableName, 
        int $id, 
        string $pkColumn = 'id'
    ): bool {
        return DB::table($tableName)
            ->where($pkColumn, $id)
            ->update([
                'isDeleted' => 1,
                'deleted_by' => auth()->user()->user_id ?? null,
                'deleted_at' => now(),
            ]) > 0;
    }

    /**
     * Get detail data untuk view
     * 
     * @param string $tableName Nama tabel
     * @param int $id Primary key value
     * @param int $menuUrlId ID menu URL (untuk JOIN FK)
     * @param string $pkColumn Nama kolom PK
     * @return object|null
     */
    public static function getDetailData(
        string $tableName, 
        int $id, 
        int $menuUrlId,
        string $pkColumn = 'id'
    ): ?object {
        $query = self::buildSelectQuery($tableName, $menuUrlId);
        
        return $query
            ->where("{$tableName}.{$pkColumn}", $id)
            ->where("{$tableName}.isDeleted", 0)
            ->first();
    }
}
