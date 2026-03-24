<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000043TDetailMediaDinamis extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'detail_media_dinamis_id' => 1,
                'fk_m_media_dinamis' => 1,
                'dm_type_media' => 'file',
                'dm_judul_media' => 'Media Hero Section',
                'dm_media_upload' => 'media_dinamis/3ghFj0L8e5DbaQR4Zbh1.svg',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_media_dinamis_id' => 2,
                'fk_m_media_dinamis' => 1,
                'dm_type_media' => 'file',
                'dm_judul_media' => 'Media Hero Section',
                'dm_media_upload' => 'media_dinamis/loGIYnRmrNlKV9fOMEbC.webp',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_media_dinamis_id' => 3,
                'fk_m_media_dinamis' => 1,
                'dm_type_media' => 'file',
                'dm_judul_media' => 'Media Hero Section',
                'dm_media_upload' => 'media_dinamis/j3DCePaFf69XgEpOqyLX.svg',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_media_dinamis_id' => 4,
                'fk_m_media_dinamis' => 2,
                'dm_type_media' => 'file',
                'dm_judul_media' => 'Dokumentasi  Gedung Polinema',
                'dm_media_upload' => 'media_dinamis/VYyRbksMjkHeobLn6rKc.webp',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_media_dinamis_id' => 5,
                'fk_m_media_dinamis' => 2,
                'dm_type_media' => 'file',
                'dm_judul_media' => 'Dokumentasi  PKKMB',
                'dm_media_upload' => 'media_dinamis/F9cUVeTYRELAD9m3TPwB.webp',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_media_dinamis_id' => 6,
                'fk_m_media_dinamis' => 2,
                'dm_type_media' => 'file',
                'dm_judul_media' => 'Dokumentasi   Sosialisasi',
                'dm_media_upload' => 'media_dinamis/MbsDGuXINVjssZorkCU8.webp',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'detail_media_dinamis_id' => 7,
                'fk_m_media_dinamis' => 3,
                'dm_type_media' => 'link',
                'dm_judul_media' => 'Keterbukaan Informasi Publik',
                'dm_media_upload' => 'https://www.youtube.com/embed/9vlRk9C37JE',
                'status_media' => 'aktif',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('t_detail_media_dinamis')->insertOrIgnore($data);
    }
}
