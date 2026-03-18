<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000006SetHakAkses extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            // Super Admin - Management Menu Global (id:74)
            [
                'set_hak_akses_id' => 1,
                'fk_web_menu' => 74,
                'ha_pengakses' => 1,
                'ha_menu' => 1,
                'ha_view' => 1,
                'ha_create' => 1,
                'ha_update' => 1,
                'ha_delete' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Admin - Management Menu Global (id:74)
            [
                'set_hak_akses_id' => 2,
                'fk_web_menu' => 74,
                'ha_pengakses' => 2,
                'ha_menu' => 1,
                'ha_view' => 1,
                'ha_create' => 1,
                'ha_update' => 1,
                'ha_delete' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Acessor - Management Menu Global (id:74)
            [
                'set_hak_akses_id' => 3,
                'fk_web_menu' => 74,
                'ha_pengakses' => 7,
                'ha_menu' => 1,
                'ha_view' => 1,
                'ha_create' => 1,
                'ha_update' => 1,
                'ha_delete' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Super Admin - Management Menu Biasa (id:75)
            [
                'set_hak_akses_id' => 4,
                'fk_web_menu' => 75,
                'ha_pengakses' => 1,
                'ha_menu' => 1,
                'ha_view' => 1,
                'ha_create' => 1,
                'ha_update' => 1,
                'ha_delete' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Admin - Management Menu Biasa (id:75)
            [
                'set_hak_akses_id' => 5,
                'fk_web_menu' => 75,
                'ha_pengakses' => 2,
                'ha_menu' => 1,
                'ha_view' => 1,
                'ha_create' => 1,
                'ha_update' => 1,
                'ha_delete' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // Acessor - Management Menu Biasa (id:75)
            [
                'set_hak_akses_id' => 6,
                'fk_web_menu' => 75,
                'ha_pengakses' => 7,
                'ha_menu' => 1,
                'ha_view' => 1,
                'ha_create' => 1,
                'ha_update' => 1,
                'ha_delete' => 1,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('set_hak_akses')->insertOrIgnore($data);
    }
}

