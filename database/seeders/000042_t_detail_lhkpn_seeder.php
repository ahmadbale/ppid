<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000042TDetailLhkpn extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'detail_lhkpn_id' => 1,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Wahiddin',
                'dl_file_lhkpn' => 'lhkpn/gf7iYRMqj6mxtbVq17nn.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 2,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Andi Kusuma Irawan',
                'dl_file_lhkpn' => 'lhkpn/qgKgcRYLeoE1aeP4rglB.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 3,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Apit Miharso',
                'dl_file_lhkpn' => 'lhkpn/vZAYh46SB12sesGMBcWg.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 4,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Dandung Novianto',
                'dl_file_lhkpn' => 'lhkpn/RWpJKZGeUTqBPQ9n8X71.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 5,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Elvyra Handayani Soedarso',
                'dl_file_lhkpn' => 'lhkpn/diJ2VS8zyAGwGOOFVn3D.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 6,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Nanak Zakaria',
                'dl_file_lhkpn' => 'lhkpn/c6T21Km1uBrIIOHzOxPF.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 7,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Rosa Andrie Asmara',
                'dl_file_lhkpn' => 'lhkpn/z1FfjUWcl0tQX2nC7yLQ.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 8,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Jaswadi',
                'dl_file_lhkpn' => 'lhkpn/mEEdfOPRhKE9n6iQUDrd.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_lhkpn_id' => 9,
                'fk_m_lhkpn' => 1,
                'dl_nama_karyawan' => 'Moch Sholeh',
                'dl_file_lhkpn' => 'lhkpn/8TCLzO3dJ5nGQ0HBJx01.pdf',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('t_detail_lhkpn')->insertOrIgnore($data);
    }
}
