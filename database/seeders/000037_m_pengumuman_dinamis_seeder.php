<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000037MPengumumanDinamis extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'pengumuman_dinamis_id' => 2,
                'pd_nama_submenu' => 'Pengumuman',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'pengumuman_dinamis_id' => 5,
                'pd_nama_submenu' => 'Pengumuman PPID',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 6: isDeleted = 1
            // SKIP ID 7: isDeleted = 1
            // SKIP ID 8: isDeleted = 1
            // SKIP ID 9: isDeleted = 1
        ];

        DB::table('m_pengumuman_dinamis')->insertOrIgnore($data);
    }
}
