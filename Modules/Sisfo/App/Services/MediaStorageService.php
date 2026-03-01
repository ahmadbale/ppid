<?php

namespace Modules\Sisfo\App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorageService
{
    /**
     * Upload file dengan hash dan folder per tabel
     * 
     * @param UploadedFile $file
     * @param string $tableName
     * @return string Hash filename yang disimpan ke database
     */
    public static function uploadFile(UploadedFile $file, string $tableName): string
    {
        // Generate hash filename
        $extension = $file->getClientOriginalExtension();
        $hashedName = Str::random(40) . '.' . $extension;
        
        // Path: menu_master/{table_name}
        $directory = "menu_master/{$tableName}";
        
        // Full path di storage/app/public (menggunakan symlink public/storage)
        $storagePath = storage_path("app/public/{$directory}");
        
        // Buat folder jika belum ada
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        
        // Move file ke storage/app/public/menu_master/{table_name}
        $file->move($storagePath, $hashedName);
        
        // Return path yang disimpan ke database (relatif dari public/storage)
        return "{$directory}/{$hashedName}";
    }
    
    /**
     * Hapus file berdasarkan hash path
     * 
     * @param string|null $hashPath
     * @return bool
     */
    public static function deleteFile(?string $hashPath): bool
    {
        if (empty($hashPath)) {
            return false;
        }
        
        // Path lengkap di storage/app/public
        $fullPath = storage_path("app/public/{$hashPath}");
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Get full URL untuk akses file
     * 
     * @param string|null $hashPath
     * @return string|null
     */
    public static function getFileUrl(?string $hashPath): ?string
    {
        if (empty($hashPath)) {
            return null;
        }
        
        return asset("storage/{$hashPath}");
    }
    
    /**
     * Check apakah file exists
     * 
     * @param string|null $hashPath
     * @return bool
     */
    public static function fileExists(?string $hashPath): bool
    {
        if (empty($hashPath)) {
            return false;
        }
        
        // Cek di storage/app/public
        return file_exists(storage_path("app/public/{$hashPath}"));
    }
}
