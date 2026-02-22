<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompleteWebMenuDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Complete Web Menu Data Seeder...');
        
        $this->populateControllerNames();
        $this->populateWebMenuGlobalIcons();
        $this->updateParentIdsForSubMenus();
        $this->insertNotifikasiMenus();
        $this->updateBadgeMethod();
        
        $this->command->info('🎉 Complete Web Menu Data Seeder finished!');
    }
    
    /**
     * Populate controller_name untuk semua menu yang masih NULL
     */
    private function populateControllerNames(): void
    {
        $this->command->info('📝 Updating controller_name...');
        
        $mappings = [
            // ============================================
            // MANAGEMENT
            // ============================================
            'menu-management' => 'MenuManagement\MenuController',
            'management-level' => 'MenuManagement\LevelController',
            'management-menu-url' => 'MenuManagement\MenuUrlController',
            'management-menu-global' => 'MenuManagement\MenuGlobalController',
            'management-user' => 'MenuManagement\UserController',
            'suiii' => 'MenuManagement\SuiiiController',
            
            // ============================================
            // ADMIN WEB - FOOTER
            // ============================================
            'kategori-footer' => 'AdminWeb\Footer\KategoriFooterController',
            'menu-footer' => 'AdminWeb\Footer\FooterController',
            'detail-footer' => 'AdminWeb\Footer\DetailFooterController',
            
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
            // ADMIN WEB - BANNER & PENGUMUMAN
            // ============================================
            'banner' => 'AdminWeb\Banner\BannerController',
            'pengumuman' => 'AdminWeb\Pengumuman\PengumumanController',
            'kategori-pengumuman' => 'AdminWeb\Pengumuman\KategoriPengumumanController',
            'detail-pengumuman' => 'AdminWeb\Pengumuman\DetailPengumumanController',
            
            // ============================================
            // INFORMASI PUBLIK - GET
            // ============================================
            'get-informasi-publik-informasi-berkala' => 'SistemInformasi\InformasiPublik\GetInfoPublikController',
            'get-informasi-publik-informasi-setiap-saat' => 'SistemInformasi\InformasiPublik\GetInfoPublikController',
            'get-informasi-publik-informasi-serta-merta' => 'SistemInformasi\InformasiPublik\GetInfoPublikController',
            
            // ============================================
            // INFORMASI PUBLIK - KATEGORI DINAMIS
            // ============================================
            'kategori-informasi-publik-dinamis-tabel' => 'SistemInformasi\InformasiPublik\KategoriInfoPublikDinamisController',
            'set-informasi-publik-dinamis-tabel' => 'SistemInformasi\InformasiPublik\SetInfoPublikDinamisController',
            
            // ============================================
            // LHKPN & PEDOMAN
            // ============================================
            'lhkpn' => 'SistemInformasi\InformasiPublik\LHKPN\LHKPNController',
            'pedoman-umum-pengelolaan-layanan' => 'SistemInformasi\Pedoman\PedomanUmumController',
            'pedoman-layanan-kerjasama' => 'SistemInformasi\Pedoman\PedomanKerjasamaController',
            'prosedur-layanan-informasi' => 'SistemInformasi\Pedoman\ProsedurLayananController',
            
            // ============================================
            // E-FORM MANAGEMENT
            // ============================================
            'kategori-e-form' => 'SistemInformasi\Eform\KategoriEformController',
            'kategori-form' => 'SistemInformasi\Eform\KategoriFormController',
            'daftar-form' => 'SistemInformasi\Eform\DaftarFormController',
            'timeline' => 'SistemInformasi\Eform\TimelineController',
            'ketentuan-pelaporan' => 'SistemInformasi\Eform\KetentuanPelaporanController',
            
            // ============================================
            // REGULASI
            // ============================================
            'regulasi' => 'SistemInformasi\Regulasi\RegulasiController',
            'kategori-regulasi' => 'SistemInformasi\Regulasi\KategoriRegulasiController',
            'regulasi-dinamis' => 'SistemInformasi\Regulasi\RegulasiDinamisController',
            'detail-regulasi' => 'SistemInformasi\Regulasi\DetailRegulasiController',
            
            // ============================================
            // STRUKTUR & PROFILE
            // ============================================
            'beranda' => 'SistemInformasi\Beranda\BerandaController',
            'struktur-organisasi' => 'SistemInformasi\StrukturOrganisasi\StrukturOrganisasiController',
            'profile-ppid' => 'SistemInformasi\Profile\ProfilePPIDController',
            'profile-polinema' => 'SistemInformasi\Profile\ProfilePolinemaController',
            
            // ============================================
            // WHATSAPP & UTILITY
            // ============================================
            'whatsapp-management' => 'WhatsAppController',
            'set-ip-dinamis-tabel' => 'SetIpDinamisTabelController',
            
            // ============================================
            // DAFTAR PENGAJUAN (E-FORM ADMIN)
            // ============================================
            'daftar-pengajuan' => 'SistemInformasi\DaftarPengajuan\DaftarPengajuanController',
            'permohonan-informasi' => 'SistemInformasi\DaftarPengajuan\PermohonanInformasiController',
            'pernyataan-keberatan' => 'SistemInformasi\DaftarPengajuan\PernyataanKeberatanController',
            'pengaduan-masyarakat' => 'SistemInformasi\DaftarPengajuan\PengaduanMasyarakatController',
            'whistle-blowing-system' => 'SistemInformasi\DaftarPengajuan\WhistleBlowingSystemController',
            'permohonan-sarana-dan-prasarana' => 'SistemInformasi\DaftarPengajuan\PermohonanSarprasController',
            'permohonan-informasi-admin' => 'SistemInformasi\DaftarPengajuan\PermohonanInformasiAdminController',
            'pernyataan-keberatan-admin' => 'SistemInformasi\DaftarPengajuan\PernyataanKeberatanAdminController',
            'pengaduan-masyarakat-admin' => 'SistemInformasi\DaftarPengajuan\PengaduanMasyarakatAdminController',
            'whistle-blowing-system-admin' => 'SistemInformasi\DaftarPengajuan\WhistleBlowingSystemAdminController',
            'permohonan-sarana-dan-prasarana-admin' => 'SistemInformasi\DaftarPengajuan\PermohonanSarprasAdminController',
            
            // ============================================
            // INFORMASI PUBLIK - DAFTAR
            // ============================================
            'daftar-informasi-publik' => 'SistemInformasi\InformasiPublik\DaftarInformasiPublikController',
            'informasi-berkala' => 'SistemInformasi\InformasiPublik\InformasiBerkalaController',
            'informasi-setiap-saat' => 'SistemInformasi\InformasiPublik\InformasiSetiapSaatController',
            'informasi-serta-merta' => 'SistemInformasi\InformasiPublik\InformasiSertaMertaController',
            'informasi-dikecualikan' => 'SistemInformasi\InformasiPublik\InformasiDikecualikanController',
            
            // ============================================
            // VERIFIKASI PENGAJUAN (PARENT + SUB MENUS)
            // ============================================
            'daftar-verifikasi-pengajuan' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPengajuanController',
            'daftar-verifikasi-pengajuan-permohonan-informasi' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPIController',
            'daftar-verifikasi-pengajuan-pernyataan-keberatan' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPKController',
            'daftar-verifikasi-pengajuan-pengaduan-masyarakat' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPMController',
            'daftar-verifikasi-pengajuan-whistle-blowing-system' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifWBSController',
            'daftar-verifikasi-pengajuan-permohonan-perawatan' => 'SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPPController',
            
            // ============================================
            // REVIEW PENGAJUAN (PARENT + SUB MENUS)
            // ============================================
            'daftar-review-pengajuan' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPengajuanController',
            'daftar-review-pengajuan-permohonan-informasi' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPIController',
            'daftar-review-pengajuan-pernyataan-keberatan' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPKController',
            'daftar-review-pengajuan-pengaduan-masyarakat' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPMController',
            'daftar-review-pengajuan-whistle-blowing-system' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewWBSController',
            'daftar-review-pengajuan-permohonan-perawatan' => 'SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPPController',
            
            // ============================================
            // NOTIFIKASI
            // ============================================
            'notifikasi-masuk' => 'Notifikasi\NotifMasukController',
            'notifikasi-verifikasi' => 'Notifikasi\NotifVerifController',
        ];
        
        $updated = 0;
        foreach ($mappings as $url => $controller) {
            $result = DB::table('web_menu_url')
                ->where('wmu_nama', $url)
                ->where('module_type', 'sisfo')
                ->update(['controller_name' => $controller]);
            
            if ($result > 0) {
                $updated += $result;
            }
        }
        
        $this->command->info("   ✅ Updated controller_name for {$updated} URLs");
    }
    
    /**
     * Populate wmg_icon untuk web_menu_global yang masih NULL
     */
    private function populateWebMenuGlobalIcons(): void
    {
        $this->command->info('🎨 Updating wmg_icon...');
        
        // ============================================
        // PARENT MENU ICONS (Group Menu)
        // ============================================
        $parentMenuIcons = [
            'Management Pengguna' => 'fa-user-cog',
            'Management Pengumuman' => 'fa-bullhorn',
            'Management Berita' => 'fa-newspaper',
            'Management Footer' => 'fa-columns',
            'Management LHKPN' => 'fa-file-alt',
            'Management Akses & Pintasan Cepat' => 'fa-bolt',
            'Management Form' => 'fa-question-circle',
            'Management Regulasi' => 'fa-gavel',
            'Management Media' => 'fa-photo-video',
            'E-Form Admin' => 'fa-folder-open',
            'Daftar Pengajuan' => 'fa-list-alt',
            'Daftar Verifikasi Pengajuan' => 'fa-clipboard-check',
            'Daftar Review Pengajuan' => 'fa-tasks',
            'Profile' => 'fa-id-card',
            'E-Form' => 'fa-wpforms',
        ];
        
        $updated = 0;
        foreach ($parentMenuIcons as $menuName => $icon) {
            $result = DB::table('web_menu_global')
                ->where('wmg_nama_default', $menuName)
                ->where('wmg_kategori_menu', 'Group Menu')
                ->update(['wmg_icon' => $icon]);
            
            if ($result > 0) {
                $updated += $result;
            }
        }
        
        // ============================================
        // SINGLE MENU ICONS (Menu Biasa)
        // ============================================
        $singleMenuIcons = [
            'Menu Management' => 'fa-bars',
            'Management Level' => 'fa-layer-group',
            'Beranda' => 'fa-home',
            'Profil PPID' => 'fa-info-circle',
            'Profile Polinema' => 'fa-university',
            'Struktur Organisasi' => 'fa-sitemap',
            'Permohonan Informasi' => 'fa-file-alt',
            'Pernyataan Keberatan' => 'fa-exclamation-triangle',
            'Pengaduan Masyarakat' => 'fa-comment-alt',
            'Whistle Blowing System' => 'fa-bullhorn',
            'Permohonan Sarana Prasarana' => 'fa-tools',
            'Regulasi' => 'fa-gavel',
            'Daftar Informasi Publik' => 'fa-list-alt',
            'WhatsApp Management' => 'fa-whatsapp',
            'Set IP Dinamis Tabel' => 'fa-table',
            'Notifikasi Masuk' => 'fa-bell',
            'Notifikasi Verifikasi' => 'fa-clipboard-check',
        ];
        
        foreach ($singleMenuIcons as $menuName => $icon) {
            $result = DB::table('web_menu_global')
                ->where('wmg_nama_default', $menuName)
                ->where('wmg_kategori_menu', 'Menu Biasa')
                ->update(['wmg_icon' => $icon]);
            
            if ($result > 0) {
                $updated += $result;
            }
        }
        
        $this->command->info("   ✅ Updated wmg_icon for {$updated} menus");
    }
    
    /**
     * Update wmu_parent_id untuk sub-menu verifikasi dan review
     */
    private function updateParentIdsForSubMenus(): void
    {
        $this->command->info('🔗 Updating parent IDs for sub-menus...');
        
        // Get parent IDs
        $verifParentId = DB::table('web_menu_url')
            ->where('wmu_nama', 'daftar-verifikasi-pengajuan')
            ->where('module_type', 'sisfo')
            ->value('web_menu_url_id');
        
        $reviewParentId = DB::table('web_menu_url')
            ->where('wmu_nama', 'daftar-review-pengajuan')
            ->where('module_type', 'sisfo')
            ->value('web_menu_url_id');
        
        if (!$verifParentId || !$reviewParentId) {
            $this->command->warn('   ⚠️ Parent menus not found. Skipping parent ID update.');
            return;
        }
        
        // Update Verifikasi sub-menus
        $verifSubMenus = [
            'daftar-verifikasi-pengajuan-permohonan-informasi',
            'daftar-verifikasi-pengajuan-pernyataan-keberatan',
            'daftar-verifikasi-pengajuan-pengaduan-masyarakat',
            'daftar-verifikasi-pengajuan-whistle-blowing-system',
            'daftar-verifikasi-pengajuan-permohonan-perawatan',
        ];
        
        $verifUpdated = DB::table('web_menu_url')
            ->whereIn('wmu_nama', $verifSubMenus)
            ->update(['wmu_parent_id' => $verifParentId]);
        
        // Update Review sub-menus
        $reviewSubMenus = [
            'daftar-review-pengajuan-permohonan-informasi',
            'daftar-review-pengajuan-pernyataan-keberatan',
            'daftar-review-pengajuan-pengaduan-masyarakat',
            'daftar-review-pengajuan-whistle-blowing-system',
            'daftar-review-pengajuan-permohonan-perawatan',
        ];
        
        $reviewUpdated = DB::table('web_menu_url')
            ->whereIn('wmu_nama', $reviewSubMenus)
            ->update(['wmu_parent_id' => $reviewParentId]);
        
        $this->command->info("   ✅ Updated {$verifUpdated} verifikasi sub-menus");
        $this->command->info("   ✅ Updated {$reviewUpdated} review sub-menus");
    }
    
    /**
     * Insert menu Notifikasi jika belum ada
     */
    private function insertNotifikasiMenus(): void
    {
        $this->command->info('📬 Inserting Notifikasi menus...');
        
        // Get application ID
        $appId = DB::table('m_application')
            ->where('app_key', 'app ppid')
            ->value('application_id');
        
        if (!$appId) {
            $this->command->warn('   ⚠️ Application not found. Skipping notifikasi menus.');
            return;
        }
        
        $now = now();
        $inserted = 0;
        
        // Notifikasi Masuk
        $notifMasukExists = DB::table('web_menu_url')
            ->where('wmu_nama', 'notifikasi-masuk')
            ->exists();
        
        if (!$notifMasukExists) {
            $notifMasukId = DB::table('web_menu_url')->insertGetId([
                'fk_m_application' => $appId,
                'wmu_parent_id' => null,
                'wmu_nama' => 'notifikasi-masuk',
                'controller_name' => 'Notifikasi\NotifMasukController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Menu untuk menampilkan notifikasi pengajuan masuk',
                'wmu_kategori_menu' => 'custom',
                'wmu_akses_tabel' => null,
                'isDeleted' => 0,
                'created_by' => 'seeder',
                'created_at' => $now,
            ]);
            
            // Insert ke web_menu_global
            DB::table('web_menu_global')->insert([
                'fk_web_menu_url' => $notifMasukId,
                'wmg_parent_id' => null,
                'wmg_icon' => 'fa-bell',
                'wmg_type' => 'general',
                'wmg_kategori_menu' => 'Menu Biasa',
                'wmg_urutan_menu' => 100,
                'wmg_nama_default' => 'Notifikasi Masuk',
                'wmg_badge_method' => 'getBadgeCount',
                'wmg_status_menu' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'seeder',
                'created_at' => $now,
            ]);
            
            $inserted++;
        }
        
        // Notifikasi Verifikasi
        $notifVerifExists = DB::table('web_menu_url')
            ->where('wmu_nama', 'notifikasi-verifikasi')
            ->exists();
        
        if (!$notifVerifExists) {
            $notifVerifId = DB::table('web_menu_url')->insertGetId([
                'fk_m_application' => $appId,
                'wmu_parent_id' => null,
                'wmu_nama' => 'notifikasi-verifikasi',
                'controller_name' => 'Notifikasi\NotifVerifController',
                'module_type' => 'sisfo',
                'wmu_keterangan' => 'Menu untuk menampilkan notifikasi verifikasi pengajuan',
                'wmu_kategori_menu' => 'custom',
                'wmu_akses_tabel' => null,
                'isDeleted' => 0,
                'created_by' => 'seeder',
                'created_at' => $now,
            ]);
            
            // Insert ke web_menu_global
            DB::table('web_menu_global')->insert([
                'fk_web_menu_url' => $notifVerifId,
                'wmg_parent_id' => null,
                'wmg_icon' => 'fa-clipboard-check',
                'wmg_type' => 'general',
                'wmg_kategori_menu' => 'Menu Biasa',
                'wmg_urutan_menu' => 101,
                'wmg_nama_default' => 'Notifikasi Verifikasi',
                'wmg_badge_method' => 'getBadgeCount',
                'wmg_status_menu' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'seeder',
                'created_at' => $now,
            ]);
            
            $inserted++;
        }
        
        $this->command->info("   ✅ Inserted {$inserted} notifikasi menus");
    }
    
    /**
     * Update wmg_badge_method untuk menu yang membutuhkan badge count
     */
    private function updateBadgeMethod(): void
    {
        $this->command->info('🔔 Updating badge methods...');
        
        // Menu yang perlu badge method
        $badgeMenus = [
            'Notifikasi Masuk' => 'getBadgeCount',
            'Notifikasi Verifikasi' => 'getBadgeCount',
        ];
        
        $updated = 0;
        foreach ($badgeMenus as $menuName => $method) {
            $result = DB::table('web_menu_global')
                ->where('wmg_nama_default', $menuName)
                ->update(['wmg_badge_method' => $method]);
            
            if ($result > 0) {
                $updated += $result;
            }
        }
        
        $this->command->info("   ✅ Updated badge method for {$updated} menus");
    }
}
