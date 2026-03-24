<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000046TFooter extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'footer_id' => 1,
                'fk_m_kategori_footer' => 2,
                'f_judul_footer' => 'Jaminan Mutu',
                'f_icon_footer' => null,
                'f_url_footer' => 'https://jaminanmutu.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'footer_id' => 2,
                'fk_m_kategori_footer' => 2,
                'f_judul_footer' => 'Perpustakaan',
                'f_icon_footer' => null,
                'f_url_footer' => 'https://library.polinema.ac.id/',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 3: isDeleted = 1
            // SKIP ID 4: isDeleted = 1
            // SKIP ID 5: isDeleted = 1
            // SKIP ID 7: isDeleted = 1
            [
                'footer_id' => 6,
                'fk_m_kategori_footer' => 5,
                'f_judul_footer' => 'Logo yuotube',
                'f_icon_footer' => 'footer_icons/3m8DfO9aAowoNqwn4IKDB76qUpl5myjRWnuaOJm4.svg',
                'f_url_footer' => null,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'footer_id' => 8,
                'fk_m_kategori_footer' => 5,
                'f_judul_footer' => 'logo instagram',
                'f_icon_footer' => 'footer_icons/d17WF7XaejXQB40EOGR7k6gEn0h5wruvo2rtKs1R.svg',
                'f_url_footer' => null,
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('t_footer')->insertOrIgnore($data);
    }
}
