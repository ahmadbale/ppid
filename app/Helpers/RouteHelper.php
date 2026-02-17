<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RouteHelper
{
    private const CACHE_KEY_USER_URLS = 'route_helper_user_module_urls';
    private const CACHE_TTL = 3600; // 1 hour
    
    // Daftar URL Sisfo yang punya route khusus (tidak pakai dynamic routing)
    private static array $nonStandardSisfoUrls = [
        // 'get-informasi-publik-informasi-berkala',
        // 'get-informasi-publik-informasi-serta-merta',
        // 'get-informasi-publik-informasi-setiap-saat',
        // 'daftar-verifikasi-pengajuan',  // ❌ REMOVED - Parent only route, sub-menu pakai dynamic routing
        // 'daftar-review-pengajuan',      // ❌ REMOVED - Parent only route, sub-menu pakai dynamic routing
        'whatsapp-management',
    ];
    
    // Ambil daftar URL User Module dari database (dengan cache 1 jam)
    public static function getUserModuleUrls(): array
    {
        return Cache::remember(self::CACHE_KEY_USER_URLS, self::CACHE_TTL, function () {
            try {
                $urls = DB::table('web_menu_url')
                    ->where('module_type', 'user')
                    ->pluck('wmu_nama')
                    ->toArray();
                
                if (config('app.debug')) {
                    Log::debug("RouteHelper: Loaded User Module URLs", [
                        'count' => count($urls),
                        'urls' => $urls
                    ]);
                }
                
                return $urls;
            } catch (\Exception $e) {
                Log::error("RouteHelper: Failed to load User URLs", [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }
    
    // Ambil daftar URL Sisfo yang punya route khusus
    public static function getNonStandardSisfoUrls(): array
    {
        return self::$nonStandardSisfoUrls;
    }
    
    // Check apakah URL ini milik User Module (static route)
    public static function isUserModuleUrl(string $url): bool
    {
        $userUrls = self::getUserModuleUrls();
        
        if (in_array($url, $userUrls)) {
            return true;
        }
        
        foreach ($userUrls as $pattern) {
            if (str_starts_with($url, $pattern . '/')) {
                return true;
            }
        }
        
        return false;
    }
    
    // Check apakah URL ini Sisfo non-standard (punya route khusus)
    public static function isNonStandardSisfoUrl(string $url): bool
    {
        return in_array($url, self::$nonStandardSisfoUrls);
    }
    
    // Check apakah URL ini boleh masuk dynamic routing
    public static function isDynamicRoutingUrl(string $url): bool
    {
        if (self::isUserModuleUrl($url)) {
            return false;
        }
        
        if (self::isNonStandardSisfoUrl($url)) {
            return false;
        }
        
        return true;
    }
    
    // Generate regex pattern untuk route where() - exclude User Module & Non-Standard URLs
    public static function getDynamicRoutePattern(): string
    {
        $excludedUrls = array_merge(
            self::getUserModuleUrls(),
            self::$nonStandardSisfoUrls
        );
        
        $escapedUrls = array_map(function($url) {
            return preg_quote($url, '/');
        }, $excludedUrls);
        
        // ✅ FIX: Tambahkan word boundary agar hanya exact match yang di-exclude
        // Sebelum: (?!permohonan\-informasi) → MENOLAK "permohonan-informasi-admin" ❌
        // Sesudah: (?!permohonan\-informasi(?![a-zA-Z0-9\-])) → HANYA MENOLAK exact "permohonan-informasi" ✅
        $patterns = array_map(function($url) {
            return $url . '(?![a-zA-Z0-9\-])';
        }, $escapedUrls);
        
        return '(?!' . implode('|', $patterns) . ')[a-zA-Z0-9\-]+';
    }
    
    // Ambil semua URL yang di-exclude dari dynamic routing (untuk debugging)
    public static function getAllExcludedUrls(): array
    {
        return array_merge(
            self::getUserModuleUrls(),
            self::$nonStandardSisfoUrls
        );
    }
    
    // Hapus cache User Module URLs (gunakan setelah update database)
    public static function clearUserUrlsCache(): bool
    {
        return Cache::forget(self::CACHE_KEY_USER_URLS);
    }
    
    // Refresh cache User Module URLs dari database
    public static function refreshUserUrlsCache(): array
    {
        self::clearUserUrlsCache();
        return self::getUserModuleUrls();
    }
}
