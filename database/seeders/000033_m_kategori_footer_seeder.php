<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000033MKategoriFooter extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'kategori_footer_id' => 1,
                'kt_footer_kode' => 'KPP',
                'kt_footer_nama' => 'Kantor PPID Politeknik Negeri Malang',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_footer_id' => 2,
                'kt_footer_kode' => 'PUL',
                'kt_footer_nama' => 'Pusat Unit layanan',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_footer_id' => 3,
                'kt_footer_kode' => 'LIO',
                'kt_footer_nama' => 'Layanan Informasi Offline',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_footer_id' => 4,
                'kt_footer_kode' => 'HKI',
                'kt_footer_nama' => 'Hubungi Kami',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_footer_id' => 5,
                'kt_footer_kode' => 'KIG',
                'kt_footer_nama' => 'Khusus Icon atau Gambar',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 7: isDeleted = 1
            // SKIP ID 8: isDeleted = 1
            // SKIP ID 9: isDeleted = 1
            [
                'kategori_footer_id' => 12,
                'kt_footer_kode' => 'TES',
                'kt_footer_nama' => 'Logo Ayam',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 11: isDeleted = 1
            // SKIP ID 13: isDeleted = 1
            // SKIP ID 14: isDeleted = 1
        ];

        DB::table('m_kategori_footer')->insertOrIgnore($data);
    }
}
