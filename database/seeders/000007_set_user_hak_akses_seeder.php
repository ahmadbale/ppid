<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Seeder000007SetUserHakAkses extends Seeder
{
    public function run(): void
    {
        $data = [
            ['set_user_hak_akses_id'=>1,'fk_m_hak_akses'=>1,'fk_m_user'=>1,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 12:27:35','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>2,'fk_m_hak_akses'=>2,'fk_m_user'=>1,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 12:27:35','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>3,'fk_m_hak_akses'=>2,'fk_m_user'=>2,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 16:39:03','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>4,'fk_m_hak_akses'=>3,'fk_m_user'=>3,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 16:39:48','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>5,'fk_m_hak_akses'=>4,'fk_m_user'=>4,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 16:39:48','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>6,'fk_m_hak_akses'=>5,'fk_m_user'=>5,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 16:41:13','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>7,'fk_m_hak_akses'=>2,'fk_m_user'=>6,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 16:41:13','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>8,'fk_m_hak_akses'=>2,'fk_m_user'=>7,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-04-27 16:41:13','updated_by'=>null,'updated_at'=>null,'deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>9,'fk_m_hak_akses'=>4,'fk_m_user'=>1,'isDeleted'=>1,'created_by'=>'Gelby F.','created_at'=>'2025-04-28 22:53:42','updated_by'=>'Gelby F.','updated_at'=>'2025-05-06 23:21:16','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>10,'fk_m_hak_akses'=>3,'fk_m_user'=>1,'isDeleted'=>1,'created_by'=>'Gelby F.','created_at'=>'2025-05-06 23:22:28','updated_by'=>'Gelby F.','updated_at'=>'2025-05-06 23:22:50','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>11,'fk_m_hak_akses'=>4,'fk_m_user'=>1,'isDeleted'=>1,'created_by'=>'Gelby F.','created_at'=>'2025-05-06 23:39:18','updated_by'=>'Gelby F.','updated_at'=>'2025-05-06 23:39:30','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>12,'fk_m_hak_akses'=>4,'fk_m_user'=>1,'isDeleted'=>1,'created_by'=>'Gelby F.','created_at'=>'2025-05-06 23:46:39','updated_by'=>'Gelby F.','updated_at'=>'2025-05-06 23:46:44','deleted_by'=>'Gelby F.','deleted_at'=>'2025-05-06 23:46:44'],
            ['set_user_hak_akses_id'=>13,'fk_m_hak_akses'=>4,'fk_m_user'=>1,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-05-07 07:54:26','updated_by'=>null,'updated_at'=>'2025-05-07 07:54:26','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>14,'fk_m_hak_akses'=>1,'fk_m_user'=>2,'isDeleted'=>1,'created_by'=>'Gelby F.','created_at'=>'2025-05-23 08:08:38','updated_by'=>'Gelby F.','updated_at'=>'2025-06-29 05:39:08','deleted_by'=>'Gelby F.','deleted_at'=>'2025-06-29 05:39:08'],
            ['set_user_hak_akses_id'=>15,'fk_m_hak_akses'=>7,'fk_m_user'=>8,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-23 10:29:43','updated_by'=>null,'updated_at'=>'2025-06-23 10:29:43','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>16,'fk_m_hak_akses'=>2,'fk_m_user'=>4,'isDeleted'=>1,'created_by'=>'Gelby F.','created_at'=>'2025-06-23 10:55:38','updated_by'=>'Gelby F.','updated_at'=>'2025-06-23 10:55:44','deleted_by'=>'Gelby F.','deleted_at'=>'2025-06-23 10:55:44'],
            ['set_user_hak_akses_id'=>17,'fk_m_hak_akses'=>2,'fk_m_user'=>8,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-23 10:56:04','updated_by'=>null,'updated_at'=>'2025-06-23 10:56:04','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>18,'fk_m_hak_akses'=>4,'fk_m_user'=>2,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-29 05:39:25','updated_by'=>null,'updated_at'=>'2025-06-29 05:39:25','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>19,'fk_m_hak_akses'=>7,'fk_m_user'=>2,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-29 05:39:40','updated_by'=>null,'updated_at'=>'2025-06-29 05:39:40','deleted_by'=>null,'deleted_at'=>null],
            ['set_user_hak_akses_id'=>20,'fk_m_hak_akses'=>3,'fk_m_user'=>1,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-29 12:14:29','updated_by'=>null,'updated_at'=>'2025-06-29 12:14:29','deleted_by'=>null,'deleted_at'=>null],
        ];

        DB::table('set_user_hak_akses')->insertOrIgnore($data);
    }
}
