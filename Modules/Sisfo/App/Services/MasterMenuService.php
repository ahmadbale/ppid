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

            // ✅ FIX: Ensure validation and criteria are arrays
            $validation = $field->wmfc_validation ?? [];
            if (is_string($validation)) {
                $validation = json_decode($validation, true) ?? [];
            }
            
            $criteria = $field->wmfc_criteria ?? [];
            if (is_string($criteria)) {
                $criteria = json_decode($criteria, true) ?? [];
            }

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
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'time':
                    // HH:MM atau HH:MM:SS
                    $fieldRules[] = 'regex:/^\d{1,2}:\d{2}(:\d{2})?$/';
                    break;
                case 'year':
                    $fieldRules[] = 'integer';
                    $fieldRules[] = 'min:1901';
                    $fieldRules[] = 'max:2155';
                    break;
                case 'date2':
                case 'datetime2':
                case 'time2':
                case 'year2':
                    // Validasi pada _start dan _end secara terpisah (form mengirim 2 field)
                    // Encode JSON dilakukan di controller SETELAH validasi, jadi validasi harus
                    // dilakukan pada {col}_start dan {col}_end — bukan kolom aslinya
                    $isRequired = !empty($validation['required']);
                    $baseRule   = $isRequired ? ['required'] : ['nullable'];

                    if (in_array($field->wmfc_field_type, ['year2'])) {
                        $rules[$columnName . '_start'] = array_merge($baseRule, ['integer', 'min:1901', 'max:2155']);
                        $rules[$columnName . '_end']   = array_merge($baseRule, ['integer', 'min:1901', 'max:2155', "gte:{$columnName}_start"]);
                    } elseif (in_array($field->wmfc_field_type, ['date2'])) {
                        $rules[$columnName . '_start'] = array_merge($baseRule, ['date']);
                        $rules[$columnName . '_end']   = array_merge($baseRule, ['date', "after_or_equal:{$columnName}_start"]);
                    } elseif (in_array($field->wmfc_field_type, ['datetime2'])) {
                        $rules[$columnName . '_start'] = array_merge($baseRule, ['date']);
                        $rules[$columnName . '_end']   = array_merge($baseRule, ['date', "after_or_equal:{$columnName}_start"]);
                    } else {
                        // time2
                        $rules[$columnName . '_start'] = array_merge($baseRule, ['regex:/^\d{1,2}:\d{2}(:\d{2})?$/']);
                        $rules[$columnName . '_end']   = array_merge($baseRule, ['regex:/^\d{1,2}:\d{2}(:\d{2})?$/', "after_or_equal:{$columnName}_start"]);
                    }
                    // Skip menambahkan rule ke $rules[$columnName] (tidak ada di request)
                    continue 2;
                case 'media':
                    $fieldRules[] = 'file';
                    // Tambahkan validasi mimes jika ada format yang dikonfigurasi
                    if (!empty($validation['mimes'])) {
                        $mimes = $validation['mimes'];
                        
                        // Map extensions ke MIME types untuk better compatibility
                        $mimeMap = [
                            'pdf' => 'application/pdf,application/x-pdf,application/octet-stream',
                            'doc' => 'application/msword,application/octet-stream',
                            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/octet-stream',
                            'xls' => 'application/vnd.ms-excel,application/octet-stream',
                            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/octet-stream',
                            'ppt' => 'application/vnd.ms-powerpoint,application/octet-stream',
                            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation,application/octet-stream',
                            'jpg' => 'image/jpeg',
                            'jpeg' => 'image/jpeg',
                            'png' => 'image/png',
                            'gif' => 'image/gif',
                            'webp' => 'image/webp',
                            'svg' => 'image/svg+xml',
                        ];
                        
                        // Parse extensions
                        $exts = array_map('trim', explode(',', $mimes));
                        $mimeTypes = [];
                        
                        foreach ($exts as $ext) {
                            if (isset($mimeMap[$ext])) {
                                $mimeTypes[] = $mimeMap[$ext];
                            }
                        }
                        
                        // HANYA gunakan mimetypes untuk validasi (lebih reliable di Windows)
                        // Rule 'mimes' di Laravel tetap cek MIME type, bukan cuma extension
                        if (!empty($mimeTypes)) {
                            $fieldRules[] = 'mimetypes:' . implode(',', $mimeTypes);
                        }
                    }
                    // Tambahkan validasi ukuran max jika dikonfigurasi (dalam KB, ukuran_max dalam MB)
                    if (!empty($field->wmfc_ukuran_max)) {
                        $fieldRules[] = 'max:' . ($field->wmfc_ukuran_max * 1024);
                    }
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

            // ✅ FIX: Ensure criteria is array
            $criteria = $field->wmfc_criteria ?? [];
            if (is_string($criteria)) {
                $criteria = json_decode($criteria, true) ?? [];
            }

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
                
                // ✅ FIX: Ensure $displayColumns is array (handle string JSON from DB)
                $displayColumns = $field->wmfc_fk_display_columns ?? [];
                
                // If it's still a string (JSON), decode it
                if (is_string($displayColumns)) {
                    $displayColumns = json_decode($displayColumns, true) ?? [];
                }
                
                if (empty($displayColumns)) {
                    continue;
                }

                // Gunakan wmfc_fk_pk_column yang sudah tersimpan di field config
                $fkPkColumn = $field->wmfc_fk_pk_column ?? null;

                // Fallback: cari PK dari DatabaseSchemaService jika wmfc_fk_pk_column kosong
                if (!$fkPkColumn) {
                    $fkPkColumn = \Modules\Sisfo\App\Services\DatabaseSchemaService::getPrimaryKey($fkTable);
                }

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
                
                // Jika priority display column tidak ada di displayColumns, tambahkan ke SELECT
                $priorityCol = $field->wmfc_fk_priority_display ?? null;
                if ($priorityCol && !in_array($priorityCol, $displayColumns)) {
                    $selectColumns[] = "{$fkTable}.{$priorityCol} as {$columnName}_{$priorityCol}";
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

            // ✅ FIX: Ensure validation and criteria are arrays
            $validation = $field->wmfc_validation ?? [];
            if (is_string($validation)) {
                $validation = json_decode($validation, true) ?? [];
            }
            
            $criteria = $field->wmfc_criteria ?? [];
            if (is_string($criteria)) {
                $criteria = json_decode($criteria, true) ?? [];
            }

            $fieldData = [
                'column' => $field->wmfc_column_name,
                'label' => $field->wmfc_field_label,
                // Normalisasi tipe lama 'file'/'gambar' → 'media' (backward compat DB lama)
                'type' => in_array($field->wmfc_field_type, ['file', 'gambar']) ? 'media' : $field->wmfc_field_type,
                'value' => $existingData->{$field->wmfc_column_name} ?? null,
                'validation' => $validation,
                'criteria' => $criteria,
                'is_pk' => $field->wmfc_is_primary_key,
                'is_auto_increment' => $field->wmfc_is_auto_increment,
                'is_required' => $validation['required'] ?? false,
                'label_keterangan' => $field->wmfc_label_keterangan ?? null,
                'ukuran_max' => $field->wmfc_ukuran_max ?? null,
                'mimes' => $validation['mimes'] ?? null,
            ];

            // Decode nilai JSON untuk type rentang (*2) saat mode update
            $rangeTypes = ['date2', 'datetime2', 'time2', 'year2'];
            if (in_array($field->wmfc_field_type, $rangeTypes) && $existingData) {
                $rawValue = $existingData->{$field->wmfc_column_name} ?? null;
                if ($rawValue) {
                    $decoded = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;
                    if (is_array($decoded) && isset($decoded['start'], $decoded['end'])) {
                        $fieldData['value_start'] = $decoded['start'];
                        $fieldData['value_end']   = $decoded['end'];
                    }
                }
            }
            
            // Handle media type - tambahkan file info untuk existing data
            if ($field->wmfc_field_type === 'media' && $existingData) {
                $filePath = $existingData->{$field->wmfc_column_name} ?? null;
                if (!empty($filePath)) {
                    $fieldData['existing_file'] = [
                        'path' => $filePath,
                        'url' => asset('storage/' . $filePath),
                        'name' => basename($filePath),
                    ];
                }
            }

            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_table) {
                // ✅ FIX: Ensure fk_display_columns is array
                $fkDisplayColumns = $field->wmfc_fk_display_columns ?? [];
                if (is_string($fkDisplayColumns)) {
                    $fkDisplayColumns = json_decode($fkDisplayColumns, true) ?? [];
                }
                
                // Ensure fk_label_columns is array (alias label untuk header modal)
                $fkLabelColumns = $field->wmfc_fk_label_columns ?? [];
                if (is_string($fkLabelColumns)) {
                    $fkLabelColumns = json_decode($fkLabelColumns, true) ?? [];
                }
                
                // ✅ FIX: Get FK table's primary key from stored wmfc_fk_pk_column
                $fkPkColumn = $field->wmfc_fk_pk_column ?? null;
                
                // Fallback: cari PK dari DatabaseSchemaService jika wmfc_fk_pk_column kosong
                if (!$fkPkColumn) {
                    $fkPkColumn = \Modules\Sisfo\App\Services\DatabaseSchemaService::getPrimaryKey($field->wmfc_fk_table);
                }
                
                $fieldData['fk_table'] = $field->wmfc_fk_table;
                $fieldData['fk_pk'] = $fkPkColumn ?? 'id'; // Default to 'id' if not found
                $fieldData['fk_display_columns'] = $fkDisplayColumns;
                $fieldData['fk_label_columns'] = $fkLabelColumns; // Alias label untuk header modal
                $fieldData['fk_priority_display'] = $field->wmfc_fk_priority_display ?? ($fkDisplayColumns[0] ?? null);
                
                // Build display_value untuk edit form (tampilkan nilai kolom priority, bukan ID)
                $priorityCol = $field->wmfc_fk_priority_display ?? ($fkDisplayColumns[0] ?? null);
                if ($existingData && $priorityCol) {
                    $fkTable = $field->wmfc_fk_table;
                    $fkId = $existingData->{$field->wmfc_column_name} ?? null;
                    
                    if ($fkId !== null && $fkId !== '' && $fkTable) {
                        // Cek apakah data sudah di-join (key: columnName_priorityCol)
                        $joinedKey = $field->wmfc_column_name . '_' . $priorityCol;
                        if (isset($existingData->$joinedKey) && $existingData->$joinedKey !== null) {
                            $fieldData['display_value'] = $existingData->$joinedKey;
                        } else {
                            // Fallback: query langsung
                            $fkPkCol = $fkPkColumn ?? 'id';
                            $fkRow = DB::table($fkTable)->where($fkPkCol, $fkId)->first();
                            $fieldData['display_value'] = $fkRow ? ($fkRow->$priorityCol ?? $fkId) : $fkId;
                        }
                    }
                }
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
            $values = array_map('trim', $values);
            // Return associative array: value => label (agar form mengirim string ENUM, bukan index)
            return array_combine($values, $values);
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
