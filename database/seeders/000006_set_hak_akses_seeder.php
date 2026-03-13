<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Seeder000006SetHakAkses extends Seeder
{
    public function run(): void
    {
        $data = [
            // A subset of seed data taken from the SQL dump. Add more rows as needed.
            ['set_hak_akses_id'=>1,'fk_web_menu'=>74,'ha_pengakses'=>1,'ha_menu'=>1,'ha_view'=>1,'ha_create'=>1,'ha_update'=>1,'ha_delete'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-02 03:05:25','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 04:43:13','deleted_by'=>null,'deleted_at'=>null],
            ['set_hak_akses_id'=>2,'fk_web_menu'=>74,'ha_pengakses'=>2,'ha_menu'=>1,'ha_view'=>1,'ha_create'=>1,'ha_update'=>1,'ha_delete'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-02 03:05:25','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 04:43:13','deleted_by'=>null,'deleted_at'=>null],
            ['set_hak_akses_id'=>3,'fk_web_menu'=>74,'ha_pengakses'=>6,'ha_menu'=>1,'ha_view'=>1,'ha_create'=>1,'ha_update'=>1,'ha_delete'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-02 03:05:25','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 04:43:13','deleted_by'=>null,'deleted_at'=>null],
            ['set_hak_akses_id'=>4,'fk_web_menu'=>74,'ha_pengakses'=>7,'ha_menu'=>1,'ha_view'=>1,'ha_create'=>1,'ha_update'=>1,'ha_delete'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-02 03:05:25','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 04:43:13','deleted_by'=>null,'deleted_at'=>null],
            ['set_hak_akses_id'=>5,'fk_web_menu'=>75,'ha_pengakses'=>1,'ha_menu'=>1,'ha_view'=>1,'ha_create'=>1,'ha_update'=>1,'ha_delete'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-02 03:05:25','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 04:43:13','deleted_by'=>null,'deleted_at'=>null],
            ['set_hak_akses_id'=>6,'fk_web_menu'=>75,'ha_pengakses'=>2,'ha_menu'=>1,'ha_view'=>1,'ha_create'=>1,'ha_update'=>1,'ha_delete'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-02 03:05:25','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 04:43:13','deleted_by'=>null,'deleted_at'=>null],
            // ... you can add the rest of the rows from the SQL dump if needed
        ];

        DB::table('set_hak_akses')->insertOrIgnore($data);
    }
}
