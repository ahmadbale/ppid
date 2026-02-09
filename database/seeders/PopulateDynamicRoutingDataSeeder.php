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
            // Admin Web - Footer
            'kategori-footer' => 'AdminWeb\Footer\KategoriFooterController',
            'detail-footer' => 'AdminWeb\Footer\FooterController',
            
            // Admin Web - Akses Cepat
            'kategori-akses-cepat' => 'AdminWeb\KategoriAkses\KategoriAksesController',
            'detail-akses-cepat' => 'AdminWeb\KategoriAkses\AksesCepatController',
            
            // Admin Web - Berita
            'kategori-berita' => 'AdminWeb\Berita\BeritaDinamisController',
            'detail-berita' => 'AdminWeb\Berita\BeritaController',
            
            // Admin Web - Media
            'kategori-media' => 'AdminWeb\MediaDinamis\MediaDinamisController',
            'detail-media' => 'AdminWeb\MediaDinamis\DetailMediaDinamisController',
            
            // Admin Web - LHKPN
            'kategori-tahun-lhkpn' => 'AdminWeb\InformasiPublik\LHKPN\LhkpnController',
            'detail-lhkpn' => 'AdminWeb\InformasiPublik\LHKPN\DetailLhkpnController',
            
            // Admin Web - Pintasan Lainnya
            'kategori-pintasan-lainnya' => 'AdminWeb\KategoriAkses\PintasanLainnyaController',
            'detail-pintasan-lainnya' => 'AdminWeb\KategoriAkses\DetailPintasanLainnyaController',
            
            // Admin Web - Regulasi
            'regulasi-dinamis' => 'AdminWeb\InformasiPublik\Regulasi\RegulasiDinamisController',
            'detail-regulasi' => 'AdminWeb\InformasiPublik\Regulasi\RegulasiController',
            'kategori-regulasi' => 'AdminWeb\InformasiPublik\Regulasi\KategoriRegulasiController',
            
            // Sistem Informasi - E-Form
            'permohonan-informasi-admin' => 'SistemInformasi\EForm\PermohonanInformasiController',
            'pernyataan-keberatan-admin' => 'SistemInformasi\EForm\PernyataanKeberatanController',
            'pengaduan-masyarakat-admin' => 'SistemInformasi\EForm\PengaduanMasyarakatController',
            'whistle-blowing-system-admin' => 'SistemInformasi\EForm\WBSController',
            'permohonan-sarana-dan-prasarana-admin' => 'SistemInformasi\EForm\PermohonanPerawatanController',
            
            // Sistem Informasi - Timeline & Ketentuan
            'timeline' => 'SistemInformasi\Timeline\TimelineController',
            'ketentuan-pelaporan' => 'SistemInformasi\KetentuanPelaporan\KetentuanPelaporanController',
            'kategori-form' => 'SistemInformasi\KategoriForm\KategoriFormController',
            
            // Admin Web - Pengumuman
            'kategori-pengumuman' => 'AdminWeb\Pengumuman\PengumumanDinamisController',
            'detail-pengumuman' => 'AdminWeb\Pengumuman\PengumumanController',
            
            // Management - User & Hak Akses
            'management-level' => 'ManagePengguna\HakAksesController',
            'management-user' => 'ManagePengguna\UserController',
            
            // Admin Web - Menu Management
            'management-menu-url' => 'AdminWeb\MenuManagement\WebMenuUrlController',
            'management-menu-global' => 'AdminWeb\MenuManagement\WebMenuGlobalController',
            
            // Informasi Publik - Tabel Dinamis
            'kategori-informasi-publik-dinamis-tabel' => 'AdminWeb\InformasiPublik\TabelDinamis\IpDinamisTabelController',
            
            // Informasi Publik - Konten Dinamis
            'dinamis-konten' => 'AdminWeb\InformasiPublik\KontenDinamis\IpDinamisKontenController',
            'upload-detail-konten' => 'AdminWeb\InformasiPublik\KontenDinamis\IpUploadKontenController',
            
            // Admin Web - Layanan Informasi
            'layanan-informasi-Dinamis' => 'AdminWeb\LayananInformasi\LIDinamisController',
            'layanan-informasi-upload' => 'AdminWeb\LayananInformasi\LIDUploadController',
            
            // Admin Web - Penyelesaian Sengketa
            'penyelesaian-sengketa' => 'AdminWeb\InformasiPublik\PenyelesaianSengketa\PenyelesaianSengketaController',
            'upload-penyelesaian-sengketa' => 'AdminWeb\InformasiPublik\PenyelesaianSengketa\UploadPSController',
            
            // WhatsApp Management
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
    }
    
    // Set module_type untuk semua URL (sisfo/user)
    private function populateModuleTypes(): void
    {
        $userUrls = [
            'beranda', 'login-ppid', 'register', 'profile-ppid', 'profile-polinema',
            'struktur-organisasi', 'berita', 'pengumuman', 'lhkpn', 'daftar-informasi-publik',
            'informasi-dikecualikan', 'informasi-setiap-saat', 'informasi-berkala',
            'informasi-serta-merta', 'regulasi', 'pedoman-umum-pengelolaan-layanan',
            'pedoman-layanan-kerjasama', 'prosedur-layanan-informasi',
            'form-permohonan-informasi', 'form-pernyataan-keberatan', 'form-whistle-blowing',
            'form-pengaduan-masyarakat', 'form-sarana-prasarana', 'permohonan-penyelesaian-sengketa',
            'content-dinamis',
        ];
        
        DB::table('web_menu_url')
            ->whereIn('wmu_nama', $userUrls)
            ->update(['module_type' => 'user']);
        
        DB::table('web_menu_url')
            ->whereNotNull('controller_name')
            ->where('controller_name', '!=', '')
            ->update(['module_type' => 'sisfo']);
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
