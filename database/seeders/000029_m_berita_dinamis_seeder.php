<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000029MBeritaDinamis extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'berita_dinamis_id' => 1,
                'bd_nama_submenu' => 'Berita PPID',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 6: isDeleted = 1
        ];

        DB::table('m_berita_dinamis')->insertOrIgnore($data);
    }
}
