<?php

namespace Modules\Sisfo\App\Helpers;

/**
 * Database Schema Helper
 * 
 * Helper functions untuk operasi database schema
 * 
 * @package Modules\Sisfo\App\Helpers
 * @author Development Team
 * @version 1.0.0
 */
class DatabaseSchemaHelper
{
    /**
     * Field types yang didukung oleh Template Master
     */
    const SUPPORTED_FIELD_TYPES = [
        'text', 'textarea', 'number', 'date', 'date2', 
        'dropdown', 'radio', 'search', 'file', 'image'
    ];

    /**
     * Common fields yang di-skip saat generate config
     */
    const COMMON_FIELDS = [
        'isDeleted', 'created_by', 'created_at', 
        'updated_by', 'updated_at', 'deleted_by', 'deleted_at'
    ];

    /**
     * Mapping MySQL data type ke field type
     */
    const DATA_TYPE_MAPPING = [
        'int' => 'number',
        'tinyint' => 'number',
        'smallint' => 'number',
        'mediumint' => 'number',
        'bigint' => 'number',
        'decimal' => 'number',
        'float' => 'number',
        'double' => 'number',
        'date' => 'date',
        'datetime' => 'date',
        'timestamp' => 'date',
        'enum' => 'dropdown',
        'text' => 'textarea',
        'mediumtext' => 'textarea',
        'longtext' => 'textarea',
        'varchar' => 'text',
        'char' => 'text',
    ];

    /**
     * Check apakah field type valid
     * 
     * @param string $fieldType Field type
     * @return bool
     */
    public static function isValidFieldType(string $fieldType): bool
    {
        return in_array($fieldType, self::SUPPORTED_FIELD_TYPES);
    }

    /**
     * Check apakah column adalah common field
     * 
     * @param string $columnName Nama kolom
     * @return bool
     */
    public static function isCommonField(string $columnName): bool
    {
        return in_array($columnName, self::COMMON_FIELDS);
    }

    /**
     * Normalize table name (add prefix if needed)
     * 
     * @param string $tableName Nama tabel
     * @return string
     */
    public static function normalizeTableName(string $tableName): string
    {
        // Jika sudah ada prefix m_, t_, dll, return as is
        if (preg_match('/^[a-z]_/', $tableName)) {
            return $tableName;
        }

        // Default prefix 'm_' untuk master table
        return 'm_' . $tableName;
    }

    /**
     * Extract column length dari data type
     * 
     * @param string $dataType MySQL data type (e.g., varchar(100))
     * @return int|null
     */
    public static function extractColumnLength(string $dataType): ?int
    {
        if (preg_match('/\((\d+)\)/', $dataType, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Generate default validation berdasarkan data type
     * 
     * @param string $dataType MySQL data type
     * @param bool $isNullable Apakah nullable
     * @return array
     */
    public static function generateDefaultValidation(string $dataType, bool $isNullable): array
    {
        $validation = [
            'required' => !$isNullable,
        ];

        // Max length untuk string types
        $length = self::extractColumnLength($dataType);
        if ($length) {
            $validation['max'] = $length;
        }

        return $validation;
    }

    /**
     * Sanitize table name (untuk keamanan)
     * 
     * @param string $tableName Nama tabel
     * @return string
     */
    public static function sanitizeTableName(string $tableName): string
    {
        // Hanya allow alphanumeric dan underscore
        return preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    }

    /**
     * Validate table name format
     * 
     * @param string $tableName Nama tabel
     * @return bool
     */
    public static function isValidTableName(string $tableName): bool
    {
        // Table name harus alphanumeric + underscore, tidak diawali angka
        return (bool) preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $tableName);
    }

    /**
     * Get HTML input type dari field type
     * 
     * @param string $fieldType Field type
     * @return string
     */
    public static function getHtmlInputType(string $fieldType): string
    {
        $mapping = [
            'text' => 'text',
            'textarea' => 'textarea',
            'number' => 'number',
            'date' => 'date',
            'date2' => 'date',
            'dropdown' => 'select',
            'radio' => 'radio',
            'search' => 'search-modal',
        ];

        return $mapping[$fieldType] ?? 'text';
    }

    /**
     * Format field label untuk display
     * 
     * @param string $label Raw label
     * @return string
     */
    public static function formatFieldLabel(string $label): string
    {
        // Trim whitespace
        $label = trim($label);
        
        // Capitalize first letter
        return ucfirst($label);
    }

    /**
     * Parse FK column name pattern
     * (Detect fk_table_name pattern)
     * 
     * @param string $columnName Nama kolom
     * @return array|null ['table' => 'table_name', 'column' => 'fk_table_name']
     */
    public static function parseFKColumnName(string $columnName): ?array
    {
        if (preg_match('/^fk_(.+)$/', $columnName, $matches)) {
            $tableName = $matches[1];
            
            // Check if valid table name pattern
            if (self::isValidTableName($tableName)) {
                return [
                    'table' => $tableName,
                    'column' => $columnName,
                ];
            }
        }
        
        return null;
    }

    /**
     * Get validation error messages (Bahasa Indonesia)
     * 
     * @return array
     */
    public static function getValidationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi',
            'unique' => ':attribute sudah digunakan',
            'max' => ':attribute maksimal :max karakter',
            'min' => ':attribute minimal :min karakter',
            'email' => ':attribute harus berupa email yang valid',
            'numeric' => ':attribute harus berupa angka',
            'date' => ':attribute harus berupa tanggal yang valid',
            'exists' => ':attribute tidak valid',
        ];
    }

    /**
     * Generate route name dari table name
     * 
     * @param string $tableName Nama tabel
     * @return string
     */
    public static function generateRouteName(string $tableName): string
    {
        // Remove prefix
        $name = preg_replace('/^[a-z]_/', '', $tableName);
        
        // Convert to kebab-case
        return str_replace('_', '-', strtolower($name));
    }

    /**
     * Generate controller method name dari action
     * 
     * @param string $action Action (index, getData, create, etc)
     * @return string
     */
    public static function generateMethodName(string $action): string
    {
        $standardMethods = [
            'index', 'getData', 'addData', 'createData',
            'editData', 'updateData', 'detailData', 'deleteData'
        ];

        if (in_array($action, $standardMethods)) {
            return $action;
        }

        // Convert kebab-case to camelCase
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $action))));
    }
}
