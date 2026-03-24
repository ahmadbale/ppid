<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000054TKategoriRegulasi extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'kategori_reg_id' => 1,
                'fk_regulasi_dinamis' => 1,
                'kr_kategori_reg_kode' => 'DH01',
                'kr_nama_kategori' => 'Dasar Hukum Layanan Informasi Publik di Lingkungan Politeknik Negeri Malang',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_reg_id' => 2,
                'fk_regulasi_dinamis' => 1,
                'kr_kategori_reg_kode' => 'DH02',
                'kr_nama_kategori' => 'Dasar Hukum Keterbukaan Informasi Publik PPID Politeknik Negeri Malang',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_reg_id' => 3,
                'fk_regulasi_dinamis' => 1,
                'kr_kategori_reg_kode' => 'DH03',
                'kr_nama_kategori' => 'Dasar Hukum Standard Operating Procedure (SOP) PPID Politeknik Negeri Malang',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 4: isDeleted = 1
            // SKIP ID 5: isDeleted = 1
        ];

        DB::table('t_kategori_regulasi')->insertOrIgnore($data);
    }
}
