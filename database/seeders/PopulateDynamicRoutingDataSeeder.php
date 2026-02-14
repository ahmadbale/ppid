<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PopulateDynamicRoutingDataSeeder extends Seeder
{
    // Isi data controller_name dan module_type untuk dynamic routing system
    public function run(): void
    {
        $this->populateControllerNames();
        $this->populateModuleTypes();
        $this->insertSubMenus(); // Tambah sub-menu untuk verifikasi dan review
    }
    
    // Set controller_name untuk semua URL Sisfo
    private function populateControllerNames(): void
    {
        $mappings = [
            // ============================================
            // ADMIN WEB - FOOTER
            // ============================================
            'kategori-footer' => 'AdminWeb\Footer\KategoriFooterController',
            'detail-footer' => 'AdminWeb\Footer\FooterController',
            
            // ============================================
            // ADMIN WEB - AKSES CEPAT
            // ============================================
            'kategori-akses-cepat' => 'AdminWeb\KategoriAkses\KategoriAksesController',
            'detail-akses-cepat' => 'AdminWeb\KategoriAkses\AksesCepatController',
            'kategori-pintasan-lainnya' => 'AdminWeb\KategoriAkses\PintasanLainnyaController',
            'detail-pintasan-lainnya' => 'AdminWeb\KategoriAkses\DetailPintasanLainnyaController',
            
            // ============================================
            // ADMIN WEB - BERITA
            // ============================================
            'kategori-berita' => 'AdminWeb\Berita\BeritaDinamisController',
            'detail-berita' => 'AdminWeb\Berita\BeritaController',
            
            // ============================================
            // ADMIN WEB - MEDIA
            // ============================================
            'kategori-media' => 'AdminWeb\MediaDinamis\MediaDinamisController',
            'detail-media' => 'AdminWeb\MediaDinamis\DetailMediaDinamisController',
            
            // ============================================
            // ADMIN WEB - LHKPN
            // ============================================
            'kategori-tahun-lhkpn' => 'AdminWeb\InformasiPublik\LHKPN\LhkpnController',
            'detail-lhkpn' => 'AdminWeb\InformasiPublik\LHKPN\DetailLhkpnController',
            
            // ============================================
            // ADMIN WEB - REGULASI
            // ============================================
            'regulasi-dinamis' => 'AdminWeb\InformasiPublik\Regulasi\RegulasiDinamisController',
            'detail-regulasi' => 'AdminWeb\InformasiPublik\Regulasi\RegulasiController',
            'kategori-regulasi' => 'AdminWeb\InformasiPublik\Regulasi\KategoriRegulasiController',
            
            // ============================================
            // ADMIN WEB - PENGUMUMAN
            // ============================================
            'kategori-pengumuman' => 'AdminWeb\Pengumuman\PengumumanDinamisController',
            'detail-pengumuman' => 'AdminWeb\Pengumuman\PengumumanController',
            
            // ============================================
            // SISTEM INFORMASI - E-FORM ADMIN (CRUD untuk Admin)
            // ============================================
            'permohonan-informasi-admin' => 'SistemInformasi\EForm\PermohonanInformasiController',
            'pernyataan-keberatan-admin' => 'SistemInformasi\EForm\PernyataanKeberatanController',
            'pengaduan-masyarakat-admin' => 'SistemInformasi\EForm\PengaduanMasyarakatController',
            'whistle-blowing-system-admin' => 'SistemInformasi\EForm\WBSController',
            'permohonan-sarana-dan-prasarana-admin' => 'SistemInformasi\EForm\PermohonanPerawatanController',
            
            // ============================================
            // SISTEM INFORMASI - TIMELINE & KETENTUAN
            // ============================================
            'timeline' => 'SistemInformasi\Timeline\TimelineController',
            'ketentuan-pelaporan' => 'SistemInformasi\KetentuanPelaporan\KetentuanPelaporanController',
            'kategori-form' => 'SistemInformasi\KategoriForm\KategoriFormController',
            
            // ============================================
            // MANAGEMENT - USER & HAK AKSES
            // ============================================
            'management-level' => 'ManagePengguna\HakAksesController',
            'management-user' => 'ManagePengguna\UserController',
            
            // ============================================
            // ADMIN WEB - MENU MANAGEMENT
            // ============================================
            'menu-management' => 'AdminWeb\MenuManagement\MenuManagementController',
            'management-menu-url' => 'AdminWeb\MenuManagement\WebMenuUrlController',
            'management-menu-global' => 'AdminWeb\MenuManagement\WebMenuGlobalController',
            
            // ============================================
            // INFORMASI PUBLIK - TABEL DINAMIS
            // ============================================
            'kategori-informasi-publik-dinamis-tabel' => 'AdminWeb\InformasiPublik\TabelDinamis\IpDinamisTabelController',
            'set-informasi-publik-dinamis-tabel' => 'AdminWeb\InformasiPublik\TabelDinamis\SetIpDinamisTabelController',
            'get-informasi-publik-informasi-berkala' => 'AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiBerkalaController',
            'get-informasi-publik-informasi-serta-merta' => 'AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiSertaMertaController',
            'get-informasi-publik-informasi-setiap-saat' => 'AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiSetiapSaatController',
            
            // ============================================
            // INFORMASI PUBLIK - KONTEN DINAMIS
            // ============================================
            'dinamis-konten' => 'AdminWeb\InformasiPublik\KontenDinamis\IpDinamisKontenController',
            'upload-detail-konten' => 'AdminWeb\InformasiPublik\KontenDinamis\IpUploadKontenController',
            
            // ============================================
            // ADMIN WEB - LAYANAN INFORMASI
            // ============================================
            'layanan-informasi-Dinamis' => 'AdminWeb\LayananInformasi\LIDinamisController',
            'layanan-informasi-upload' => 'AdminWeb\LayananInformasi\LIDUploadController',
            
            // ============================================
            // ADMIN WEB - PENYELESAIAN SENGKETA
            // ============================================
            'penyelesaian-sengketa' => 'AdminWeb\InformasiPublik\PenyelesaianSengketa\PenyelesaianSengketaController',
            'upload-penyelesaian-sengketa' => 'AdminWeb\InformasiPublik\PenyelesaianSengketa\UploadPSController',
            
            // ============================================
            // SISTEM INFORMASI - VERIFIKASI PENGAJUAN (Parent + Sub)
            // ============================================
            'daftar-verifikasi-pengajuan' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPengajuanController',
            'daftar-verifikasi-pengajuan-permohonan-informasi' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPIController',
            'daftar-verifikasi-pengajuan-pernyataan-keberatan' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPKController',
            'daftar-verifikasi-pengajuan-pengaduan-masyarakat' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPMController',
            'daftar-verifikasi-pengajuan-whistle-blowing-system' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifWBSController',
            'daftar-verifikasi-pengajuan-permohonan-perawatan' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPPController',
            
            // ============================================
            // SISTEM INFORMASI - REVIEW PENGAJUAN (Parent + Sub)
            // ============================================
            'daftar-review-pengajuan' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPengajuanController',
            'daftar-review-pengajuan-permohonan-informasi' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPIController',
            'daftar-review-pengajuan-pernyataan-keberatan' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPKController',
            'daftar-review-pengajuan-pengaduan-masyarakat' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPMController',
            'daftar-review-pengajuan-whistle-blowing-system' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewWBSController',
            'daftar-review-pengajuan-permohonan-perawatan' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPPController',
            
            // ============================================
            // WHATSAPP MANAGEMENT (NON-STANDARD)
            // ============================================
            'whatsapp-management' => 'WhatsAppController',
        ];
        
        $updated = 0;
        foreach ($mappings as $url => $controller) {
            $affected = DB::table('web_menu_url')
                ->where('wmu_nama', $url)
                ->where('isDeleted', 0)
                ->update(['controller_name' => $controller, 'updated_at' => now()]);
            
            if ($affected > 0) $updated++;
        }
        
        echo "\n✅ Updated controller_name for {$updated} URLs\n";
    }
    
    // Set module_type untuk semua URL (sisfo/user)
    private function populateModuleTypes(): void
    {
        // ============================================
        // USER MODULE URLs - Website & Form Pengajuan untuk Responden
        // ============================================
        $userUrls = [
            // Website Public URLs
            'beranda', 'login-ppid', 'register', 'profile-ppid', 'profile-polinema',
            'struktur-organisasi', 'berita', 'pengumuman', 'lhkpn', 'daftar-informasi-publik',
            'informasi-dikecualikan', 'informasi-setiap-saat', 'informasi-berkala',
            'informasi-serta-merta', 'regulasi', 'pedoman-umum-pengelolaan-layanan',
            'pedoman-layanan-kerjasama', 'prosedur-layanan-informasi',
            'form-permohonan-informasi', 'form-pernyataan-keberatan', 'form-whistle-blowing',
            'form-pengaduan-masyarakat', 'form-sarana-prasarana', 'permohonan-penyelesaian-sengketa',
            'content-dinamis',
            
            // Form Pengajuan untuk Responden (PENTING!)
            'permohonan-informasi',              // Form permohonan informasi (user)
            'pernyataan-keberatan',              // Form pernyataan keberatan (user)
            'pengaduan-masyarakat',              // Form pengaduan masyarakat (user)
            'whistle-blowing-system',            // Form WBS (user)
            'permohonan-sarana-dan-prasarana',   // Form permohonan perawatan (user)
        ];
        
        // Set module_type = 'user'
        $updatedUser = DB::table('web_menu_url')
            ->whereIn('wmu_nama', $userUrls)
            ->update(['module_type' => 'user', 'updated_at' => now()]);
        
        echo "✅ Set module_type='user' for {$updatedUser} URLs\n";
        
        // Set module_type = 'sisfo' untuk sisanya
        $updatedSisfo = DB::table('web_menu_url')
            ->whereNotIn('wmu_nama', $userUrls)
            ->update(['module_type' => 'sisfo', 'updated_at' => now()]);
        
        echo "✅ Set module_type='sisfo' for {$updatedSisfo} URLs\n";
    }
    
    // Insert sub-menu untuk Verifikasi dan Review Pengajuan
    private function insertSubMenus(): void
    {
        // Get parent IDs
        $verifParentId = DB::table('web_menu_url')
            ->where('wmu_nama', 'daftar-verifikasi-pengajuan')
            ->value('web_menu_url_id');
        
        $reviewParentId = DB::table('web_menu_url')
            ->where('wmu_nama', 'daftar-review-pengajuan')
            ->value('web_menu_url_id');
        
        // Jika parent belum ada, skip
        if (!$verifParentId || !$reviewParentId) {
            echo "⚠️ Parent menu 'daftar-verifikasi-pengajuan' atau 'daftar-review-pengajuan' tidak ditemukan. Skip insert sub-menu.\n";
            return;
        }
        
        $now = now();
        $insertedCount = 0;
        
        // 5 Sub-menu Verifikasi Pengajuan
        $verifSubMenus = [
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $verifParentId,
                'wmu_nama' => 'daftar-verifikasi-pengajuan-permohonan-informasi',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPIController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Verifikasi Pengajuan Permohonan Informasi',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $verifParentId,
                'wmu_nama' => 'daftar-verifikasi-pengajuan-pernyataan-keberatan',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPKController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Verifikasi Pengajuan Pernyataan Keberatan',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $verifParentId,
                'wmu_nama' => 'daftar-verifikasi-pengajuan-pengaduan-masyarakat',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPMController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Verifikasi Pengajuan Pengaduan Masyarakat',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $verifParentId,
                'wmu_nama' => 'daftar-verifikasi-pengajuan-whistle-blowing-system',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifWBSController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Verifikasi Pengajuan Whistle Blowing System',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $verifParentId,
                'wmu_nama' => 'daftar-verifikasi-pengajuan-permohonan-perawatan',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPPController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Verifikasi Pengajuan Permohonan Perawatan Sarana Prasarana',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
        ];
        
        // 5 Sub-menu Review Pengajuan
        $reviewSubMenus = [
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $reviewParentId,
                'wmu_nama' => 'daftar-review-pengajuan-permohonan-informasi',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPIController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Review Pengajuan Permohonan Informasi',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $reviewParentId,
                'wmu_nama' => 'daftar-review-pengajuan-pernyataan-keberatan',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPKController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Review Pengajuan Pernyataan Keberatan',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $reviewParentId,
                'wmu_nama' => 'daftar-review-pengajuan-pengaduan-masyarakat',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPMController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Review Pengajuan Pengaduan Masyarakat',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $reviewParentId,
                'wmu_nama' => 'daftar-review-pengajuan-whistle-blowing-system',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewWBSController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Review Pengajuan Whistle Blowing System',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
            [
                'fk_m_application' => 1,
                'wmu_parent_id' => $reviewParentId,
                'wmu_nama' => 'daftar-review-pengajuan-permohonan-perawatan',
                'controller_name' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPPController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Daftar Review Pengajuan Permohonan Perawatan Sarana Prasarana',
                'isDeleted' => 0,
                'created_by' => 'system',
                'created_at' => $now,
            ],
        ];
        
        // Insert Verifikasi sub-menus
        foreach ($verifSubMenus as $menu) {
            $exists = DB::table('web_menu_url')
                ->where('wmu_nama', $menu['wmu_nama'])
                ->exists();
            
            if (!$exists) {
                DB::table('web_menu_url')->insert($menu);
                $insertedCount++;
                echo "✅ Inserted: {$menu['wmu_nama']}\n";
            } else {
                echo "⏭️ Skip (exists): {$menu['wmu_nama']}\n";
            }
        }
        
        // Insert Review sub-menus
        foreach ($reviewSubMenus as $menu) {
            $exists = DB::table('web_menu_url')
                ->where('wmu_nama', $menu['wmu_nama'])
                ->exists();
            
            if (!$exists) {
                DB::table('web_menu_url')->insert($menu);
                $insertedCount++;
                echo "✅ Inserted: {$menu['wmu_nama']}\n";
            } else {
                echo "⏭️ Skip (exists): {$menu['wmu_nama']}\n";
            }
        }
        
        echo "\n🎯 Total sub-menu inserted: {$insertedCount}/10\n";
    }
}
