<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

/**
 * Master Migration Runner
 * 
 * File ini memanggil 64 file migration secara berurutan.
 * Jalankan dengan: php artisan migrate --path=database/migrations/2026_03_12_000000_run_all_migrations.php
 */
return new class extends Migration
{
    /**
     * Daftar file migration yang akan dijalankan secara berurutan.
     */
    private array $migrationFiles = [
        // Session 2 - 2026_03_07 (000001–000008)
        '2026_03_07_000001_create_table_m_hak_akses',
        '2026_03_07_000002_create_table_m_user',
        '2026_03_07_000003_create_table_web_menu_url',
        '2026_03_07_000004_create_table_web_menu_global',
        '2026_03_07_000005_create_table_web_menu',
        '2026_03_07_000006_create_table_set_hak_akses',
        '2026_03_07_000007_create_table_set_user_hak_akses',
        '2026_03_07_000008_create_table_web_menu_field_config',

        // Session 3 - 2026_03_09 (000009–000034)
        '2026_03_09_000009_create_table_m_application',
        '2026_03_09_000010_create_table_t_permohonan_informasi',
        '2026_03_09_000011_create_table_t_form_pi_diri_sendiri',
        '2026_03_09_000012_create_table_t_form_pi_orang_lain',
        '2026_03_09_000013_create_table_t_form_pi_organisasi',
        '2026_03_09_000014_create_table_t_form_pk_diri_sendiri',
        '2026_03_09_000015_create_table_t_form_pk_orang_lain',
        '2026_03_09_000016_create_table_t_pernyataan_keberatan',
        '2026_03_09_000017_create_table_t_pengaduan_masyarakat',
        '2026_03_09_000018_create_table_t_wbs',
        '2026_03_09_000019_create_table_t_permohonan_perawatan',
        '2026_03_09_000020_create_table_log_transaction',
        '2026_03_09_000021_create_table_log_notif_masuk',
        '2026_03_09_000022_create_table_log_notif_verif',
        '2026_03_09_000023_create_table_log_email',
        '2026_03_09_000024_create_table_log_qrcode_wa',
        '2026_03_09_000025_create_table_log_whatsapp',
        '2026_03_09_000026_create_table_m_kategori_form',
        '2026_03_09_000027_create_table_m_ketentuan_pelaporan',
        '2026_03_09_000028_create_table_t_timeline',
        '2026_03_09_000029_create_table_m_berita_dinamis',
        '2026_03_09_000030_create_table_m_ip_dinamis_konten',
        '2026_03_09_000031_create_table_m_ip_dinamis_tabel',
        '2026_03_09_000032_create_table_m_kategori_akses',
        '2026_03_09_000033_create_table_m_kategori_footer',
        '2026_03_09_000034_create_table_m_lhkpn',

        // Session 4 - 2026_03_10 (000035–000050)
        '2026_03_10_000035_create_table_m_li_dinamis',
        '2026_03_10_000036_create_table_m_media_dinamis',
        '2026_03_10_000037_create_table_m_pengumuman_dinamis',
        '2026_03_10_000038_create_table_m_penyelesaian_sengketa',
        '2026_03_10_000039_create_table_m_regulasi_dinamis',
        '2026_03_10_000040_create_table_t_akses_cepat',
        '2026_03_10_000041_create_table_t_berita',
        '2026_03_10_000042_create_table_t_detail_lhkpn',
        '2026_03_10_000043_create_table_t_detail_media_dinamis',
        '2026_03_10_000044_create_table_t_detail_pintasan_lainnya',
        '2026_03_10_000045_create_table_t_dropdown_dinamis',
        '2026_03_10_000046_create_table_t_footer',
        '2026_03_10_000047_create_table_t_form_dinamis',
        '2026_03_10_000048_create_table_t_ip_menu_utama',
        '2026_03_10_000049_create_table_t_ip_sub_menu_utama',
        '2026_03_10_000050_create_table_t_ip_sub_menu',

        // Session 5 - 2026_03_11 (000051–000064)
        '2026_03_11_000051_create_table_t_pintasan_lainnya',
        '2026_03_11_000052_create_table_t_pengumuman',
        '2026_03_11_000053_create_table_t_pertanyaan_dinamis',
        '2026_03_11_000054_create_table_t_kategori_regulasi',
        '2026_03_11_000055_create_table_t_langkah_timeline',
        '2026_03_11_000056_create_table_t_lid_upload',
        '2026_03_11_000057_create_table_t_jawaban_dinamis',
        '2026_03_11_000058_create_table_t_ip_upload_konten',
        '2026_03_11_000059_create_table_t_regulasi',
        '2026_03_11_000060_create_table_t_upload_berita',
        '2026_03_11_000061_create_table_t_upload_pengumuman',
        '2026_03_11_000062_create_table_t_upload_ps',
        '2026_03_11_000063_create_table_web_konten',
        '2026_03_11_000064_create_table_web_konten_images',
    ];

    public function up(): void
    {
        $basePath = database_path('migrations');

        foreach ($this->migrationFiles as $migrationName) {
            $filePath = $basePath . DIRECTORY_SEPARATOR . $migrationName . '.php';

            if (!file_exists($filePath)) {
                echo "  [SKIP] File tidak ditemukan: {$migrationName}.php\n";
                continue;
            }

            $migration = require $filePath;

            if (method_exists($migration, 'up')) {
                echo "  [RUN]  {$migrationName}\n";
                $migration->up();
            }
        }
    }

    public function down(): void
    {
        $basePath = database_path('migrations');

        // Jalankan rollback dalam urutan terbalik
        foreach (array_reverse($this->migrationFiles) as $migrationName) {
            $filePath = $basePath . DIRECTORY_SEPARATOR . $migrationName . '.php';

            if (!file_exists($filePath)) {
                echo "  [SKIP] File tidak ditemukan: {$migrationName}.php\n";
                continue;
            }

            $migration = require $filePath;

            if (method_exists($migration, 'down')) {
                echo "  [ROLLBACK]  {$migrationName}\n";
                $migration->down();
            }
        }
    }
};
