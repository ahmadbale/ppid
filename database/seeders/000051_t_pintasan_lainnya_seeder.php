<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000051TPintasanLainnya extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'detail_pintasan_lainnya_id' => 1,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'POLINEMA',
                'dpl_url' => 'https://www.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 2,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'PORTAL',
                'dpl_url' => 'http://portal.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 3,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'SIAKAD',
                'dpl_url' => ' https://siakad.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 4,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'SPMB',
                'dpl_url' => 'https://spmb.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 5,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'P2M',
                'dpl_url' => 'https://ppm.polinema.ac.id/main/home',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 6,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'Jaminan Mutu',
                'dpl_url' => 'https://jaminanmutu.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 7,
                'fk_pintasan_lainnya' => 1,
                'dpl_judul' => 'Alumni',
                'dpl_url' => ' https://alumni.polinema.ac.id/front/home',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_pintasan_lainnya_id' => 8,
                'fk_pintasan_lainnya' => 2,
                'dpl_judul' => 'LPSE KEMDIKBUD',
                'dpl_url' => ' https://lpse.dikdasmen.go.id/eproc4',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 9: isDeleted = 1
        ];

        DB::table('t_detail_pintasan_lainnya')->insertOrIgnore($data);
    }
}
