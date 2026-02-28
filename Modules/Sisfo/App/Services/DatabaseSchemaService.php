<?php

namespace Modules\Sisfo\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Database Schema Service
 * 
 * Service untuk inspeksi struktur database (table, columns, foreign keys, indexes)
 * Digunakan untuk auto-generate field config saat membuat menu master
 * 
 * @package Modules\Sisfo\App\Services
 * @author Development Team
 * @version 1.0.0
 */
class DatabaseSchemaService
{
    /**
     * Check apakah tabel exists di database
     * 
     * @param string $tableName Nama tabel
     * @return bool
     */
    public static function tableExists(string $tableName): bool
    {
        return Schema::hasTable($tableName);
    }

    /**
     * Get struktur lengkap tabel (columns info)
     * 
     * @param string $tableName Nama tabel
     * @return array
     * @throws \Exception
     */
    public static function getTableStructure(string $tableName): array
    {
        if (!self::tableExists($tableName)) {
            throw new \Exception("Tabel '{$tableName}' tidak ditemukan di database");
        }

        $columns = DB::select("DESCRIBE {$tableName}");
        $structure = [];

        foreach ($columns as $column) {
            $structure[] = [
                'column_name' => $column->Field,
                'data_type' => self::parseDataType($column->Type),
                'data_type_full' => $column->Type,
                'is_nullable' => $column->Null === 'YES',
                'column_key' => $column->Key,
                'is_primary_key' => $column->Key === 'PRI',
                'is_unique' => $column->Key === 'UNI',
                'is_foreign_key' => $column->Key === 'MUL',
                'default_value' => $column->Default,
                'extra' => $column->Extra,
                'is_auto_increment' => stripos($column->Extra, 'auto_increment') !== false,
            ];
        }

        return $structure;
    }

    /**
     * Get primary key column dari tabel
     * 
     * @param string $tableName Nama tabel
     * @return string|null
     */
    public static function getPrimaryKey(string $tableName): ?string
    {
        $structure = self::getTableStructure($tableName);
        
        foreach ($structure as $column) {
            if ($column['is_primary_key']) {
                return $column['column_name'];
            }
        }
        
        return null;
    }

    /**
     * Get foreign keys dari tabel
     * 
     * @param string $tableName Nama tabel
     * @return array
     */
    public static function getForeignKeys(string $tableName): array
    {
        $query = "
            SELECT 
                COLUMN_NAME as column_name,
                REFERENCED_TABLE_NAME as referenced_table,
                REFERENCED_COLUMN_NAME as referenced_column
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        $foreignKeys = DB::select($query, [$tableName]);
        $result = [];

        foreach ($foreignKeys as $fk) {
            $result[] = [
                'column_name' => $fk->column_name,
                'referenced_table' => $fk->referenced_table,
                'referenced_column' => $fk->referenced_column,
            ];
        }

        return $result;
    }

    /**
     * Get kolom yang ditampilkan dari tabel FK (exclude PK, timestamps, soft delete)
     * 
     * @param string $tableName Nama tabel FK
     * @return array
     */
    public static function getFKDisplayableColumns(string $tableName): array
    {
        $structure = self::getTableStructure($tableName);
        $displayable = [];

        // Kolom yang di-skip
        $skipColumns = [
            'isDeleted', 'created_by', 'created_at', 
            'updated_by', 'updated_at', 'deleted_by', 'deleted_at'
        ];

        foreach ($structure as $column) {
            $columnName = $column['column_name'];
            
            // Skip PK
            if ($column['is_primary_key']) {
                continue;
            }
            
            // Skip common fields
            if (in_array($columnName, $skipColumns)) {
                continue;
            }
            
            // Skip FK columns
            if ($column['is_foreign_key']) {
                continue;
            }
            
            $displayable[] = [
                'column_name' => $columnName,
                'data_type' => $column['data_type'],
                'suggested_label' => self::generateFieldLabel($columnName),
            ];
        }

        return $displayable;
    }

    /**
     * Suggest field type berdasarkan data type MySQL
     * 
     * @param string $dataType MySQL data type
     * @param bool $isForeignKey Apakah kolom FK
     * @param string|null $columnName Nama kolom (untuk detect pattern)
     * @return string
     */
    public static function suggestFieldType(
        string $dataType, 
        bool $isForeignKey = false,
        ?string $columnName = null
    ): string {
        // FK selalu search modal
        if ($isForeignKey) {
            return 'search';
        }

        // Deteksi berdasarkan data type
        $baseType = strtolower(preg_replace('/\(.*\)/', '', $dataType));

        switch ($baseType) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'decimal':
            case 'float':
            case 'double':
                return 'number';

            case 'date':
            case 'datetime':
            case 'timestamp':
                return 'date';

            case 'enum':
                return 'dropdown';

            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'textarea';

            case 'varchar':
            case 'char':
                // Pattern detection
                if ($columnName) {
                    $columnLower = strtolower($columnName);
                    
                    // Detect keterangan/deskripsi
                    if (stripos($columnLower, 'keterangan') !== false ||
                        stripos($columnLower, 'deskripsi') !== false ||
                        stripos($columnLower, 'description') !== false ||
                        stripos($columnLower, 'alamat') !== false) {
                        return 'textarea';
                    }
                }
                return 'text';

            default:
                return 'text';
        }
    }

    /**
     * Generate field label dari column name
     * 
     * @param string $columnName Nama kolom
     * @return string
     */
    public static function generateFieldLabel(string $columnName): string
    {
        // Remove prefix fk_
        $label = preg_replace('/^fk_/', '', $columnName);
        
        // Remove table prefix (m_, t_, etc)
        $label = preg_replace('/^[a-z]_/', '', $label);
        
        // Replace underscore dengan space
        $label = str_replace('_', ' ', $label);
        
        // Uppercase setiap kata
        $label = ucwords($label);
        
        return $label;
    }

    /**
     * Detect kriteria default berdasarkan column info
     * 
     * @param array $columnInfo Column info dari getTableStructure
     * @return array
     */
    public static function detectDefaultCriteria(array $columnInfo): array
    {
        $criteria = [];

        // Unique constraint
        if ($columnInfo['is_unique']) {
            $criteria['unique'] = true;
        }

        // Detect uppercase pattern dari nama kolom
        $columnName = strtolower($columnInfo['column_name']);
        if (stripos($columnName, 'kode') !== false || 
            stripos($columnName, 'code') !== false) {
            $criteria['case'] = 'uppercase';
        }

        return $criteria;
    }

    /**
     * Detect validasi default berdasarkan column info
     * 
     * @param array $columnInfo Column info dari getTableStructure
     * @return array
     */
    public static function detectDefaultValidation(array $columnInfo): array
    {
        $validation = [];

        // Required jika NOT NULL
        $validation['required'] = !$columnInfo['is_nullable'];

        // Max length untuk VARCHAR/CHAR
        if (preg_match('/varchar\((\d+)\)/i', $columnInfo['data_type_full'], $matches)) {
            $validation['max'] = (int) $matches[1];
        } elseif (preg_match('/char\((\d+)\)/i', $columnInfo['data_type_full'], $matches)) {
            $validation['max'] = (int) $matches[1];
        }

        // Email detection dari nama kolom
        $columnName = strtolower($columnInfo['column_name']);
        if (stripos($columnName, 'email') !== false) {
            $validation['email'] = true;
        }

        return $validation;
    }

    /**
     * Get summary tabel (untuk display di form)
     * 
     * @param string $tableName Nama tabel
     * @return array
     */
    public static function getTableSummary(string $tableName): array
    {
        $structure = self::getTableStructure($tableName);
        $foreignKeys = self::getForeignKeys($tableName);

        return [
            'table_name' => $tableName,
            'total_columns' => count($structure),
            'primary_key' => self::getPrimaryKey($tableName),
            'foreign_keys_count' => count($foreignKeys),
            'foreign_keys' => $foreignKeys,
            'has_auto_increment' => collect($structure)->contains('is_auto_increment', true),
        ];
    }

    /**
     * Auto-generate field configs untuk tabel
     * (Untuk AJAX endpoint: check tabel & generate preview)
     * 
     * @param string $tableName Nama tabel
     * @return array
     */
    public static function autoGenerateFieldConfigs(string $tableName): array
    {
        $structure = self::getTableStructure($tableName);
        $foreignKeys = self::getForeignKeys($tableName);
        $configs = [];
        $order = 1;

        // Map FK untuk lookup cepat
        $fkMap = [];
        foreach ($foreignKeys as $fk) {
            $fkMap[$fk['column_name']] = $fk;
        }

        foreach ($structure as $column) {
            $columnName = $column['column_name'];
            $isForeignKey = isset($fkMap[$columnName]);

            // Config dasar
            $config = [
                'wmfc_column_name' => $columnName,
                'wmfc_field_label' => self::generateFieldLabel($columnName),
                'wmfc_field_type' => self::suggestFieldType(
                    $column['data_type_full'],
                    $isForeignKey,
                    $columnName
                ),
                'wmfc_criteria' => self::detectDefaultCriteria($column),
                'wmfc_validation' => self::detectDefaultValidation($column),
                'wmfc_order' => $order++,
                'wmfc_is_primary_key' => $column['is_primary_key'],
                'wmfc_is_auto_increment' => $column['is_auto_increment'],
                'wmfc_is_visible' => !$column['is_auto_increment'], // Hidden jika auto increment
            ];

            // FK config
            if ($isForeignKey) {
                $fk = $fkMap[$columnName];
                $displayColumns = self::getFKDisplayableColumns($fk['referenced_table']);
                $displayColNames = array_column($displayColumns, 'column_name');
                
                $config['wmfc_fk_table'] = $fk['referenced_table'];
                $config['wmfc_fk_pk_column'] = $fk['referenced_column'];
                $config['wmfc_fk_display_columns'] = $displayColNames;
                $config['wmfc_fk_available_cols'] = $displayColNames; // Semua kolom yang bisa dipilih (UI)
                $config['wmfc_fk_label_columns'] = null; // Default: pakai nama kolom asli
            } else {
                $config['wmfc_fk_table'] = null;
                $config['wmfc_fk_pk_column'] = null;
                $config['wmfc_fk_display_columns'] = null;
                $config['wmfc_fk_available_cols'] = null;
                $config['wmfc_fk_label_columns'] = null;
            }

            $configs[] = $config;
        }

        return $configs;
    }

    /**
     * Parse data type dari DESCRIBE result
     * 
     * @param string $fullType Full type dari MySQL (e.g., varchar(100), int(11))
     * @return string
     */
    private static function parseDataType(string $fullType): string
    {
        // Remove length/precision
        return preg_replace('/\(.*\)/', '', strtolower($fullType));
    }
}
