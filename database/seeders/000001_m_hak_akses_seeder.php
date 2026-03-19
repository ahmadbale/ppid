<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000001MHakAkses extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            // Super Administrator - Full Access
            [
                'hak_akses_id' => 1,
                'hak_akses_kode' => 'SAR',
                'hak_akses_nama' => 'Super Administrator',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Administrator - System Admin Access
            [
                'hak_akses_id' => 2,
                'hak_akses_kode' => 'ADM',
                'hak_akses_nama' => 'Administrator',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Management and Unit Leadership
            [
                'hak_akses_id' => 3,
                'hak_akses_kode' => 'MPU',
                'hak_akses_nama' => 'Manajemen dan Pimpinan Unit',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Verifier Access
            [
                'hak_akses_id' => 4,
                'hak_akses_kode' => 'VFR',
                'hak_akses_nama' => 'Verifikator',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Respondent Access
            [
                'hak_akses_id' => 5,
                'hak_akses_kode' => 'RPN',
                'hak_akses_nama' => 'Responden',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Assessor Access
            [
                'hak_akses_id' => 7,
                'hak_akses_kode' => 'ACS',
                'hak_akses_nama' => 'Acessor',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('m_hak_akses')->insertOrIgnore($data);
    }
}
