<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000036MMediaDinamis extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'media_dinamis_id' => 1,
                'md_kategori_media' => 'Hero Section',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'media_dinamis_id' => 2,
                'md_kategori_media' => 'Dokumentasi PPID',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'media_dinamis_id' => 3,
                'md_kategori_media' => 'Media Informasi Publik',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 5: isDeleted = 1
            // SKIP ID 6: isDeleted = 1
            // SKIP ID 7: isDeleted = 1
        ];

        DB::table('m_media_dinamis')->insertOrIgnore($data);
    }
}
