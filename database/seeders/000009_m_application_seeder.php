<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000009MApplication extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'application_id' => 1,
                'app_key' => 'app ppid',
                'app_nama' => 'Pejabat Pengelola Informasi dan Dokumentasi',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'application_id' => 2,
                'app_key' => 'app siakad',
                'app_nama' => 'Sistem Informasi Akademik',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('table_m_application')->insertOrIgnore($data);
    }
}
