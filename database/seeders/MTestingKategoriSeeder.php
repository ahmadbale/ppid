<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MTestingKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'm_testing_kategori_id' => 1,
                'tk_kode' => 'MBL',
                'tk_nama' => 'mobile',
                'tk_keterangan' => 'Testing Mobile Application',
                'isDeleted' => 0,
                'created_at' => now(),
                'created_by' => 'seeder',
            ],
            [
                'm_testing_kategori_id' => 2,
                'tk_kode' => 'WEB',
                'tk_nama' => 'website',
                'tk_keterangan' => 'Testing Website Application',
                'isDeleted' => 0,
                'created_at' => now(),
                'created_by' => 'seeder',
            ],
        ];

        DB::table('m_testing_kategori')->insert($data);
    }
}
