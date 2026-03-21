<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000032MKategoriAkses extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'kategori_akses_id' => 1,
                'mka_judul_kategori' => 'Akses Menu Cepat',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_akses_id' => 2,
                'mka_judul_kategori' => 'Pintasan Lainnya',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 3: isDeleted = 1
            // SKIP ID 4: isDeleted = 1
        ];

        DB::table('m_kategori_akses')->insertOrIgnore($data);
    }
}
