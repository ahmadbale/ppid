<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000040TAksesCepat extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'akses_cepat_id' => 1,
                'fk_m_kategori_akses' => 1,
                'ac_judul' => 'Informasi Setiap Saat',
                'ac_static_icon' => 'DABt1qd562hCjd9WXFbwgPlofLvVNKkPkI8JrII8.svg',
                'ac_animation_icon' => 'GE2IIEYCvKhrCyWwamqoxNqNtlEjIQWQlVHFLdl5.gif',
                'ac_url' => 'https://contoh.com',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'akses_cepat_id' => 2,
                'fk_m_kategori_akses' => 1,
                'ac_judul' => 'Informasi Berkala',
                'ac_static_icon' => 'TnsXPMKqP3VwcYW3PWwxO6CReRDR82htsCtSfBEb.svg',
                'ac_animation_icon' => 'qMVhhyX7upPWNYuQn2FKBYmLRW1brfTA7DpoZ0aF.gif',
                'ac_url' => 'https://contoh.com',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'akses_cepat_id' => 3,
                'fk_m_kategori_akses' => 1,
                'ac_judul' => 'Informasi Serta Merta',
                'ac_static_icon' => 'vGDhlJeCLE3VdOSeBM6yaYYmzi9zNafVOet6dL1k.svg',
                'ac_animation_icon' => 'VHTeDFhWnseHACApOzTYqrVnjT74GMacJCdLi8b0.gif',
                'ac_url' => 'https://contoh.com',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'akses_cepat_id' => 4,
                'fk_m_kategori_akses' => 1,
                'ac_judul' => 'Lacak Permohonan',
                'ac_static_icon' => 'GFUmMWDXgWl0nmLiyTXTQqrIgWBOmM7JpICcS9h9.svg',
                'ac_animation_icon' => 'Ym6iviiuJsPNM2L5pkxVfx5DscEFnsGMK4r7u4Pg.gif',
                'ac_url' => 'https://contoh.com',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'akses_cepat_id' => 5,
                'fk_m_kategori_akses' => 1,
                'ac_judul' => 'HELPDESK Akademik',
                'ac_static_icon' => 'BtY1yqaKwgAHuMKNEAgDI8DKuWWCLmWgrLj6wCVa.svg',
                'ac_animation_icon' => 'cjAhOwGVvkrTiTYaOkYTnpsbDWHBgZN95uNCVbda.gif',
                'ac_url' => 'https://helpakademik.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('t_akses_cepat')->insertOrIgnore($data);
    }
}
