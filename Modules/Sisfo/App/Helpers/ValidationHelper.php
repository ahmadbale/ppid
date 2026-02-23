<?php

namespace Modules\Sisfo\App\Helpers;

/**
 * Validation Helper
 * 
 * Helper functions untuk dynamic validation
 * 
 * @package Modules\Sisfo\App\Helpers
 * @author Development Team
 * @version 1.0.0
 */
class ValidationHelper
{
    /**
     * Build Laravel validation rule string dari config
     * 
     * @param array $validation Validation config dari JSON
     * @param array $criteria Criteria config dari JSON
     * @param string $fieldType Field type
     * @param string|null $tableName Table name (untuk unique check)
     * @param string|null $columnName Column name (untuk unique check)
     * @param int|null $excludeId ID untuk exclude di unique check (update)
     * @param string|null $excludeColumn Column name untuk exclude (default: id)
     * @return string
     */
    public static function buildRuleString(
        array $validation,
        array $criteria,
        string $fieldType,
        ?string $tableName = null,
        ?string $columnName = null,
        ?int $excludeId = null,
        ?string $excludeColumn = 'id'
    ): string {
        $rules = [];

        // Required
        if (!empty($validation['required'])) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Type-specific validation
        switch ($fieldType) {
            case 'number':
                $rules[] = 'numeric';
                break;
                
            case 'date':
            case 'date2':
                $rules[] = 'date';
                break;
                
            case 'text':
            case 'textarea':
                $rules[] = 'string';
                break;
        }

        // Max length
        if (!empty($validation['max'])) {
            $rules[] = 'max:' . $validation['max'];
        }

        // Min length
        if (!empty($validation['min'])) {
            $rules[] = 'min:' . $validation['min'];
        }

        // Email
        if (!empty($validation['email'])) {
            $rules[] = 'email';
        }

        // Unique
        if (!empty($criteria['unique']) && $tableName && $columnName) {
            if ($excludeId && $excludeColumn) {
                $rules[] = "unique:{$tableName},{$columnName},{$excludeId},{$excludeColumn}";
            } else {
                $rules[] = "unique:{$tableName},{$columnName}";
            }
        }

        return implode('|', $rules);
    }

    /**
     * Build validation rules array untuk semua fields
     * 
     * @param array $fields Array of field configs
     * @param string $tableName Table name
     * @param int|null $excludeId ID untuk update
     * @param string $pkColumn PK column name
     * @return array
     */
    public static function buildRulesArray(
        array $fields,
        string $tableName,
        ?int $excludeId = null,
        string $pkColumn = 'id'
    ): array {
        $rules = [];

        foreach ($fields as $field) {
            // Skip auto increment & hidden fields
            if ($field->wmfc_is_auto_increment || !$field->wmfc_is_visible) {
                continue;
            }

            $columnName = $field->wmfc_column_name;
            $validation = $field->wmfc_validation ?? [];
            $criteria = $field->wmfc_criteria ?? [];

            $ruleString = self::buildRuleString(
                $validation,
                $criteria,
                $field->wmfc_field_type,
                $tableName,
                $columnName,
                $excludeId,
                $pkColumn
            );

            // FK validation
            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_table) {
                $ruleString .= "|exists:{$field->wmfc_fk_table},{$field->wmfc_fk_pk_column}";
            }

            $rules[$columnName] = $ruleString;
        }

        return $rules;
    }

    /**
     * Build custom validation messages (Bahasa Indonesia)
     * 
     * @param array $fields Array of field configs (bisa array of objects atau array of arrays)
     * @return array
     */
    public static function buildCustomMessages(array $fields): array
    {
        $messages = [];

        foreach ($fields as $field) {
            // ✅ Support both object dan array
            if (is_object($field)) {
                $columnName = $field->wmfc_column_name;
                $label = $field->wmfc_field_label;
            } else {
                $columnName = $field['wmfc_column_name'] ?? null;
                $label = $field['wmfc_field_label'] ?? null;
            }

            // Skip if column name or label not found
            if (!$columnName || !$label) {
                continue;
            }

            $messages["{$columnName}.required"] = "{$label} wajib diisi";
            $messages["{$columnName}.unique"] = "{$label} sudah digunakan";
            $messages["{$columnName}.max"] = "{$label} maksimal :max karakter";
            $messages["{$columnName}.min"] = "{$label} minimal :min karakter";
            $messages["{$columnName}.email"] = "{$label} harus berupa email yang valid";
            $messages["{$columnName}.numeric"] = "{$label} harus berupa angka";
            $messages["{$columnName}.date"] = "{$label} harus berupa tanggal yang valid";
            $messages["{$columnName}.exists"] = "{$label} tidak valid";
            $messages["{$columnName}.string"] = "{$label} harus berupa teks";
        }

        return $messages;
    }

    /**
     * Build custom attribute names
     * 
     * @param array $fields Array of field configs (bisa array of objects atau array of arrays)
     * @return array
     */
    public static function buildCustomAttributes(array $fields): array
    {
        $attributes = [];

        foreach ($fields as $field) {
            // ✅ Support both object dan array
            if (is_object($field)) {
                $columnName = $field->wmfc_column_name;
                $label = $field->wmfc_field_label;
            } else {
                $columnName = $field['wmfc_column_name'] ?? null;
                $label = $field['wmfc_field_label'] ?? null;
            }

            if ($columnName && $label) {
                $attributes[$columnName] = $label;
            }
        }

        return $attributes;
    }

    /**
     * Validate JSON structure untuk criteria
     * 
     * @param mixed $criteria Criteria value
     * @return bool
     */
    public static function isValidCriteriaJson($criteria): bool
    {
        if (is_null($criteria)) {
            return true;
        }

        if (!is_array($criteria)) {
            return false;
        }

        // Valid keys: unique, case
        $allowedKeys = ['unique', 'case'];
        $keys = array_keys($criteria);

        foreach ($keys as $key) {
            if (!in_array($key, $allowedKeys)) {
                return false;
            }
        }

        // Validate case value
        if (isset($criteria['case'])) {
            if (!in_array($criteria['case'], ['uppercase', 'lowercase'])) {
                return false;
            }
        }

        // Validate unique value
        if (isset($criteria['unique'])) {
            if (!is_bool($criteria['unique'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate JSON structure untuk validation
     * 
     * @param mixed $validation Validation value
     * @return bool
     */
    public static function isValidValidationJson($validation): bool
    {
        if (is_null($validation)) {
            return true;
        }

        if (!is_array($validation)) {
            return false;
        }

        // Valid keys: required, max, min, email
        $allowedKeys = ['required', 'max', 'min', 'email'];
        $keys = array_keys($validation);

        foreach ($keys as $key) {
            if (!in_array($key, $allowedKeys)) {
                return false;
            }
        }

        // Validate required (boolean)
        if (isset($validation['required'])) {
            if (!is_bool($validation['required'])) {
                return false;
            }
        }

        // Validate max (integer)
        if (isset($validation['max'])) {
            if (!is_int($validation['max']) || $validation['max'] < 1) {
                return false;
            }
        }

        // Validate min (integer)
        if (isset($validation['min'])) {
            if (!is_int($validation['min']) || $validation['min'] < 1) {
                return false;
            }
        }

        // Validate email (boolean)
        if (isset($validation['email'])) {
            if (!is_bool($validation['email'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sanitize validation input
     * 
     * @param array $validation Raw validation input
     * @return array
     */
    public static function sanitizeValidation(array $validation): array
    {
        $sanitized = [];

        if (isset($validation['required'])) {
            $sanitized['required'] = (bool) $validation['required'];
        }

        if (isset($validation['max'])) {
            $sanitized['max'] = (int) $validation['max'];
        }

        if (isset($validation['min'])) {
            $sanitized['min'] = (int) $validation['min'];
        }

        if (isset($validation['email'])) {
            $sanitized['email'] = (bool) $validation['email'];
        }

        return $sanitized;
    }

    /**
     * Sanitize criteria input
     * 
     * @param array $criteria Raw criteria input
     * @return array
     */
    public static function sanitizeCriteria(array $criteria): array
    {
        $sanitized = [];

        if (isset($criteria['unique'])) {
            $sanitized['unique'] = (bool) $criteria['unique'];
        }

        if (isset($criteria['case'])) {
            $case = strtolower($criteria['case']);
            if (in_array($case, ['uppercase', 'lowercase'])) {
                $sanitized['case'] = $case;
            }
        }

        return $sanitized;
    }

    /**
     * Merge validation config dengan default values
     * 
     * @param array $validation User validation config
     * @return array
     */
    public static function mergeWithDefaults(array $validation): array
    {
        $defaults = [
            'required' => false,
            'max' => null,
            'min' => null,
            'email' => false,
        ];

        return array_merge($defaults, $validation);
    }

    /**
     * Check apakah validation menandakan field wajib diisi
     * 
     * @param array $validation Validation config
     * @return bool
     */
    public static function isRequired(array $validation): bool
    {
        return !empty($validation['required']);
    }

    /**
     * Get max length dari validation
     * 
     * @param array $validation Validation config
     * @return int|null
     */
    public static function getMaxLength(array $validation): ?int
    {
        return $validation['max'] ?? null;
    }

    /**
     * Get min length dari validation
     * 
     * @param array $validation Validation config
     * @return int|null
     */
    public static function getMinLength(array $validation): ?int
    {
        return $validation['min'] ?? null;
    }
}
