<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebMenuUrlMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini akan:
     * 1. Set wmu_kategori_menu = 'custom' untuk semua menu yang sudah ada
     * 2. Set wmu_akses_tabel = NULL untuk menu custom (default behavior)
     * 
     * Karena sistem Template Master adalah fitur baru, semua menu yang 
     * sudah ada saat ini menggunakan manual coding (kategori = custom)
     */
    public function run(): void
    {
        $this->command->info('🔄 Updating existing web_menu_url records...');
        
        try {
            // Update semua record yang belum memiliki kategori
            $updated = DB::table('web_menu_url')
                ->whereNull('wmu_kategori_menu')
                ->orWhere('wmu_kategori_menu', '')
                ->update([
                    'wmu_kategori_menu' => 'custom',
                    'wmu_akses_tabel' => null,
                    'updated_at' => now(),
                ]);
            
            $this->command->info("✅ Updated {$updated} records to kategori 'custom'");
            
            // Tampilkan summary
            $summary = DB::table('web_menu_url')
                ->select('wmu_kategori_menu', DB::raw('COUNT(*) as total'))
                ->where('isDeleted', 0)
                ->groupBy('wmu_kategori_menu')
                ->get();
            
            $this->command->info("\n📊 Summary web_menu_url by kategori:");
            $this->command->table(
                ['Kategori', 'Total'],
                $summary->map(function ($item) {
                    return [$item->wmu_kategori_menu, $item->total];
                })
            );
            
            // Verifikasi data
            $customCount = DB::table('web_menu_url')
                ->where('wmu_kategori_menu', 'custom')
                ->where('isDeleted', 0)
                ->count();
            
            $masterCount = DB::table('web_menu_url')
                ->where('wmu_kategori_menu', 'master')
                ->where('isDeleted', 0)
                ->count();
            
            $pengajuanCount = DB::table('web_menu_url')
                ->where('wmu_kategori_menu', 'pengajuan')
                ->where('isDeleted', 0)
                ->count();
            
            $this->command->info("\n✅ Verification:");
            $this->command->info("   • Custom Menu: {$customCount}");
            $this->command->info("   • Master Menu: {$masterCount} (Template Master - Auto CRUD)");
            $this->command->info("   • Pengajuan Menu: {$pengajuanCount} (Template Pengajuan - TBD)");
            
            $this->command->info("\n🎉 Seeder completed successfully!");
            
        } catch (\Exception $e) {
            $this->command->error("❌ Error updating records: " . $e->getMessage());
            throw $e;
        }
    }
}
