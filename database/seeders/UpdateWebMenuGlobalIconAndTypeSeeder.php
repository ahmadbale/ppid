<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateWebMenuGlobalIconAndTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================
        // STEP 1: Set semua menu existing ke type 'general' (default)
        // ============================================
        DB::table('web_menu_global')
            ->where('wmg_type', null)
            ->orWhereNull('wmg_type')
            ->update(['wmg_type' => 'general']);
        
        $this->command->info('✅ All existing menus set to type "general"');

        // ============================================
        // STEP 2: Update icon untuk parent menu (Group Menu)
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
        ];

        foreach ($parentMenuIcons as $menuName => $icon) {
            DB::table('web_menu_global')
                ->where('wmg_nama_default', $menuName)
                ->where('wmg_kategori_menu', 'Group Menu')
                ->update(['wmg_icon' => $icon]);
        }

        $this->command->info('✅ Parent menu icons updated');

        // ============================================
        // STEP 3: Update icon untuk menu biasa (tanpa submenu)
        // ============================================
        $singleMenuIcons = [
            'Regulasi' => 'fa-gavel',
            'Daftar Informasi Publik' => 'fa-list-alt',
            'WhatsApp Management' => 'fa-whatsapp',
            'Set IP Dinamis Tabel' => 'fa-table',
        ];

        foreach ($singleMenuIcons as $menuName => $icon) {
            DB::table('web_menu_global')
                ->where('wmg_nama_default', $menuName)
                ->where('wmg_kategori_menu', 'Menu Biasa')
                ->update(['wmg_icon' => $icon]);
        }

        $this->command->info('✅ Single menu icons updated');

        // ============================================
        // STEP 4: Sub menu tetap NULL (akan default ke fa-circle)
        // ============================================
        $this->command->info('✅ Sub menu icons kept as NULL (will use default fa-circle)');

        // ============================================
        // SUMMARY
        // ============================================
        $totalGeneral = DB::table('web_menu_global')->where('wmg_type', 'general')->count();
        $totalSpecial = DB::table('web_menu_global')->where('wmg_type', 'special')->count();
        $totalWithIcon = DB::table('web_menu_global')->whereNotNull('wmg_icon')->count();

        $this->command->info('');
        $this->command->info('📊 SUMMARY:');
        $this->command->info("   - General menus: {$totalGeneral}");
        $this->command->info("   - Special menus: {$totalSpecial}");
        $this->command->info("   - Menus with custom icon: {$totalWithIcon}");
        $this->command->info('');
        $this->command->info('✅ WebMenuGlobal icon and type update completed!');
    }
}
