<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000026MKategoriForm extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'kategori_form_id' => 1,
                'kf_nama' => 'Permohonan Informasi',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_form_id' => 2,
                'kf_nama' => 'Pernyataan Keberatan',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_form_id' => 3,
                'kf_nama' => 'Pengaduan Masyarakat',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_form_id' => 4,
                'kf_nama' => 'Whistle Blowing System',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'kategori_form_id' => 5,
                'kf_nama' => 'Permohonan Perawatan Sarana Prasarana',
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
        ];

        DB::table('m_kategori_form')->insertOrIgnore($data);
    }
}
