<?php

namespace Modules\Sisfo\App\Helpers;

class EFormActiveMenuHelper
{
    /**
     * Daftar mapping URL e-form ke active menu
     * 
     * @return array
     */
    public static function getMapping()
    {
        return [
            'permohonan-informasi' => 'permohonaninformasi',
            'permohonan-informasi-admin' => 'permohonaninformasiadmin',
            'pernyataan-keberatan' => 'pernyataankeberatan',
            'pernyataan-keberatan-admin' => 'pernyataankeberatanadmin',
            'pengaduan-masyarakat' => 'pengaduanmasyarakat',
            'pengaduan-masyarakat-admin' => 'pengaduanmasyarakatadmin',
            'whistle-blowing-system' => 'whistleblowingsystem(wbs)',
            'whistle-blowing-system-admin' => 'whistleblowingsystem(wbs)admin',
            'permohonan-sarana-dan-prasarana' => 'permohonansaranadanprasarana',
            'permohonan-sarana-dan-prasarana-admin' => 'permohonansaranadanprasaranaadmin',
        ];
    }
    
    /**
     * Check apakah URL adalah e-form dan dapatkan active menu-nya
     * 
     * @param string $url URL yang akan dicek
     * @return string|null Active menu jika URL adalah e-form, null jika bukan
     */
    public static function getActiveMenu($url)
    {
        $mapping = self::getMapping();
        
        // Cek exact match
        if (isset($mapping[$url])) {
            return $mapping[$url];
        }
        
        // Cek dengan regex untuk pattern yang lebih kompleks
        foreach (self::getMapping() as $pattern => $menu) {
            // Ubah - menjadi \- untuk regex, dan tambahkan ^ dan $ untuk match exact
            $regexPattern = '/^' . str_replace('-', '\-', $pattern) . '$/';
            
            if (preg_match($regexPattern, $url)) {
                return $menu;
            }
        }
        
        // Cek untuk URL dengan path tambahan (untuk form detail, edit, dll)
        $basePath = strtok($url, '/');
        if (isset($mapping[$basePath])) {
            return $mapping[$basePath];
        }
        
        // Cek untuk URL dengan akhiran "-admin" yang tidak ada di mapping
        if (str_ends_with($url, '-admin')) {
            $nonAdminUrl = str_replace('-admin', '', $url);
            if (isset($mapping[$nonAdminUrl])) {
                return $mapping[$nonAdminUrl];
            }
        }
        
        return null; // Bukan URL e-form
    }
}