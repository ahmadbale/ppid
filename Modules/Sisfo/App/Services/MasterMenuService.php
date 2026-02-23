<?php

namespace Modules\Sisfo\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\Website\WebMenuFieldConfigModel;
use Tymon\JWTAuth\Facades\JWTAuth;

class MasterMenuService
{
    public static function getMenuConfigByUrl(string $menuUrl): ?object
    {
        return DB::table('web_menu_url')
            ->where('wmu_nama', $menuUrl)
            ->where('module_type', 'sisfo')
            ->where('wmu_kategori_menu', 'master')
            ->where('isDeleted', 0)
            ->first();
    }

    public static function getFieldConfigs(int $menuUrlId, bool $visibleOnly = true)
    {
        return WebMenuFieldConfigModel::getByMenuUrlId($menuUrlId, $visibleOnly);
    }

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
            
            if ($field->wmfc_is_auto_increment) {
                continue;
            }

            $validation = $field->wmfc_validation ?? [];
            $criteria = $field->wmfc_criteria ?? [];

            if (!empty($validation['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field->wmfc_field_type) {
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                case 'date2':
                    $fieldRules[] = 'date';
                    break;
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

            if (!empty($validation['unique']) || !empty($criteria['unique'])) {
                $uniqueRule = "unique:{$tableName},{$columnName}";
                if ($excludeId) {
                    $primaryKey = WebMenuFieldConfigModel::getPrimaryKeyField($menuUrlId);
                    $pkColumn = $primaryKey ? $primaryKey->wmfc_column_name : 'id';
                    $uniqueRule .= ",{$excludeId},{$pkColumn}";
                }
                $fieldRules[] = $uniqueRule;
            }

            if (!empty($fieldRules)) {
                $rules[$columnName] = $fieldRules;
            }
        }

        return $rules;
    }

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

            if (!empty($criteria['case'])) {
                if ($criteria['case'] === 'uppercase') {
                    $processed[$columnName] = strtoupper($processed[$columnName]);
                } elseif ($criteria['case'] === 'lowercase') {
                    $processed[$columnName] = strtolower($processed[$columnName]);
                }
            }
        }

        return $processed;
    }

    public static function buildSelectQuery(string $tableName, int $menuUrlId)
    {
        $fields = self::getFieldConfigs($menuUrlId, true);
        $query = DB::table($tableName);
        
        $selectColumns = ["{$tableName}.*"];
        
        foreach ($fields as $field) {
            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_table) {
                $fkTable = $field->wmfc_fk_table;
                $columnName = $field->wmfc_column_name;
                $displayColumns = $field->wmfc_fk_display_columns ?? [];

                if (empty($displayColumns)) {
                    continue;
                }

                $fkPkColumn = DB::table('web_menu_field_config as wmfc')
                    ->join('web_menu_url as wmu', 'wmfc.fk_web_menu_url', '=', 'wmu.web_menu_url_id')
                    ->where('wmu.wmu_akses_tabel', $fkTable)
                    ->where('wmfc.wmfc_is_primary_key', 1)
                    ->where('wmfc.isDeleted', 0)
                    ->value('wmfc.wmfc_column_name');

                if (!$fkPkColumn) {
                    continue;
                }

                $query->leftJoin(
                    $fkTable,
                    "{$tableName}.{$columnName}",
                    '=',
                    "{$fkTable}.{$fkPkColumn}"
                );

                foreach ($displayColumns as $displayCol) {
                    $selectColumns[] = "{$fkTable}.{$displayCol} as {$columnName}_{$displayCol}";
                }
            }
        }

        $query->select($selectColumns);

        return $query;
    }

    public static function buildFormFields(int $menuUrlId, ?object $existingData = null): array
    {
        $fields = self::getFieldConfigs($menuUrlId, true);
        $formFields = [];

        foreach ($fields as $field) {
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

            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_table) {
                $fieldData['fk_table'] = $field->wmfc_fk_table;
                $fieldData['fk_display_columns'] = $field->wmfc_fk_display_columns ?? [];
            }

            if ($field->wmfc_field_type === 'dropdown') {
                $fieldData['options'] = self::getEnumOptions($field->wmfc_column_name, $menuUrlId);
            }

            $formFields[] = $fieldData;
        }

        return $formFields;
    }

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
        
        if (preg_match('/^enum\((.*)\)$/', $type, $matches)) {
            $values = str_getcsv($matches[1], ',', "'");
            return array_map('trim', $values);
        }

        return [];
    }

    public static function insertData(string $tableName, array $data, int $menuUrlId): int
    {
        $processedData = self::applyFieldCriteria($data, $menuUrlId);
        
        $alias = self::getUserAlias();
        $processedData['created_by'] = $alias;
        $processedData['created_at'] = now();
        
        return DB::table($tableName)->insertGetId($processedData);
    }

    public static function updateData(
        string $tableName, 
        int $id, 
        array $data, 
        int $menuUrlId,
        string $pkColumn = 'id'
    ): bool {
        $processedData = self::applyFieldCriteria($data, $menuUrlId);
        
        $alias = self::getUserAlias();
        $processedData['updated_by'] = $alias;
        $processedData['updated_at'] = now();
        
        return DB::table($tableName)
            ->where($pkColumn, $id)
            ->update($processedData);
    }

    public static function deleteData(
        string $tableName, 
        int $id, 
        string $pkColumn = 'id'
    ): bool {
        $alias = self::getUserAlias();
        
        return DB::table($tableName)
            ->where($pkColumn, $id)
            ->update([
                'isDeleted' => 1,
                'deleted_by' => $alias,
                'deleted_at' => now(),
            ]);
    }

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

    protected static function getUserAlias(): string
    {
        $alias = null;
        
        if (session()->has('alias')) {
            $alias = session('alias');
        } else {
            $user = Auth::user();
            
            if (!$user) {
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                } catch (\Exception $e) {
                    $user = null;
                }
            }
            
            if ($user) {
                if (isset($user->nama_pengguna) && !empty($user->nama_pengguna)) {
                    $alias = \Modules\Sisfo\App\Models\UserModel::generateAlias($user->nama_pengguna);
                } elseif (isset($user->alias)) {
                    $alias = $user->alias;
                }
            }
        }
        
        return $alias ?? 'System';
    }
}
