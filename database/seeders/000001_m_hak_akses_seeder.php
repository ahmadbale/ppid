<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Seeder000001MHakAkses extends Seeder
{
    public function run(): void
    {
        $data = [
            // Super Administrator - Full Access
            [
                'hak_akses_id' => 1,
                'hak_akses_kode' => 'SAR',
                'hak_akses_nama' => 'Super Administrator',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => '2025-03-02 13:06:18',
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
                'created_at' => '2025-03-02 13:06:18',
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
                'created_at' => '2025-03-02 13:08:30',
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
                'created_at' => '2025-03-02 13:08:30',
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
                'created_at' => '2025-03-02 13:08:30',
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Senior Operator - DELETED (soft delete: isDeleted = 1)
            [
                'hak_akses_id' => 6,
                'hak_akses_kode' => 'OPT',
                'hak_akses_nama' => 'Operator Senior',
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => '2025-04-28 21:35:04',
                'updated_by' => 'Gelby F.',
                'updated_at' => '2025-04-28 21:35:54',
                'deleted_by' => 'Gelby F.',
                'deleted_at' => '2025-04-28 21:35:54',
            ],
            // Assessor Access
            [
                'hak_akses_id' => 7,
                'hak_akses_kode' => 'ACS',
                'hak_akses_nama' => 'Acessor',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => '2025-05-14 06:49:36',
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('m_hak_akses')->insertOrIgnore($data);
    }
}
