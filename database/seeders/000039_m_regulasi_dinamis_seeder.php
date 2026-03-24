<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000039MRegulasiDinamis extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'regulasi_dinamis_id' => 1,
                'rd_judul_reg_dinamis' => 'Regulasi',
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
            // SKIP ID 6: isDeleted = 1
        ];

        DB::table('m_regulasi_dinamis')->insertOrIgnore($data);
    }
}
