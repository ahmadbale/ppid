<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000007SetUserHakAkses extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            // Gelby Firmansyah (user_id:1) - Super Administrator (hak_akses_id:1)
            [
                'set_user_hak_akses_id' => 1,
                'fk_m_hak_akses' => 1,
                'fk_m_user' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Gelby Firmansyah (user_id:1) - Administrator (hak_akses_id:2)
            [
                'set_user_hak_akses_id' => 2,
                'fk_m_hak_akses' => 2,
                'fk_m_user' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Lionel Messi (user_id:2) - Administrator (hak_akses_id:2)
            [
                'set_user_hak_akses_id' => 3,
                'fk_m_hak_akses' => 2,
                'fk_m_user' => 2,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Zaenal Arifin (user_id:3) - Manajemen dan Pimpinan Unit (hak_akses_id:3)
            [
                'set_user_hak_akses_id' => 4,
                'fk_m_hak_akses' => 3,
                'fk_m_user' => 3,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Agus Subianto (user_id:4) - Verifikator (hak_akses_id:4)
            [
                'set_user_hak_akses_id' => 5,
                'fk_m_hak_akses' => 4,
                'fk_m_user' => 4,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Ahmad Isroqi Firdaus (user_id:5) - Responden (hak_akses_id:5)
            [
                'set_user_hak_akses_id' => 6,
                'fk_m_hak_akses' => 5,
                'fk_m_user' => 5,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Cristiano Ronaldo (user_id:6) - Administrator (hak_akses_id:2)
            [
                'set_user_hak_akses_id' => 7,
                'fk_m_hak_akses' => 2,
                'fk_m_user' => 6,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Balee (user_id:7) - Administrator (hak_akses_id:2)
            [
                'set_user_hak_akses_id' => 8,
                'fk_m_hak_akses' => 2,
                'fk_m_user' => 7,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Abdus Sallam (user_id:8) - Acessor (hak_akses_id:7)
            [
                'set_user_hak_akses_id' => 9,
                'fk_m_hak_akses' => 7,
                'fk_m_user' => 8,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SOFT DELETED - Gelby Firmansyah (user_id:1) - Verifikator (hak_akses_id:4) - deleted without marked deletion
            [
                'set_user_hak_akses_id' => 10,
                'fk_m_hak_akses' => 4,
                'fk_m_user' => 1,
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => 'System',
                'updated_at' => $now,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SOFT DELETED - Gelby Firmansyah (user_id:1) - Manajemen dan Pimpinan Unit (hak_akses_id:3)
            [
                'set_user_hak_akses_id' => 11,
                'fk_m_hak_akses' => 3,
                'fk_m_user' => 1,
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => 'System',
                'updated_at' => $now,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SOFT DELETED - Gelby Firmansyah (user_id:1) - Verifikator (hak_akses_id:4) - deleted without marked deletion
            [
                'set_user_hak_akses_id' => 12,
                'fk_m_hak_akses' => 4,
                'fk_m_user' => 1,
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => 'System',
                'updated_at' => $now,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SOFT DELETED - Gelby Firmansyah (user_id:1) - Verifikator (hak_akses_id:4) - with marked deletion
            [
                'set_user_hak_akses_id' => 13,
                'fk_m_hak_akses' => 4,
                'fk_m_user' => 1,
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => 'System',
                'updated_at' => $now,
                'deleted_by' => 'Gelby F.',
                'deleted_at' => $now,
            ],
            // Gelby Firmansyah (user_id:1) - Verifikator (hak_akses_id:4) - restored after deletion
            [
                'set_user_hak_akses_id' => 14,
                'fk_m_hak_akses' => 4,
                'fk_m_user' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SOFT DELETED - Lionel Messi (user_id:2) - Super Administrator (hak_akses_id:1) - with marked deletion
            [
                'set_user_hak_akses_id' => 15,
                'fk_m_hak_akses' => 1,
                'fk_m_user' => 2,
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => 'System',
                'updated_at' => $now,
                'deleted_by' => 'Gelby F.',
                'deleted_at' => $now,
            ],
            // SOFT DELETED - Agus Subianto (user_id:4) - Administrator (hak_akses_id:2) - with marked deletion
            [
                'set_user_hak_akses_id' => 16,
                'fk_m_hak_akses' => 2,
                'fk_m_user' => 4,
                'isDeleted' => 1,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => 'System',
                'updated_at' => $now,
                'deleted_by' => 'Gelby F.',
                'deleted_at' => $now,
            ],
            // Abdus Sallam (user_id:8) - Administrator (hak_akses_id:2)
            [
                'set_user_hak_akses_id' => 17,
                'fk_m_hak_akses' => 2,
                'fk_m_user' => 8,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Lionel Messi (user_id:2) - Verifikator (hak_akses_id:4)
            [
                'set_user_hak_akses_id' => 18,
                'fk_m_hak_akses' => 4,
                'fk_m_user' => 2,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Lionel Messi (user_id:2) - Acessor (hak_akses_id:7)
            [
                'set_user_hak_akses_id' => 19,
                'fk_m_hak_akses' => 7,
                'fk_m_user' => 2,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Gelby Firmansyah (user_id:1) - Manajemen dan Pimpinan Unit (hak_akses_id:3)
            [
                'set_user_hak_akses_id' => 20,
                'fk_m_hak_akses' => 3,
                'fk_m_user' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('set_user_hak_akses')->insertOrIgnore($data);
    }
}
