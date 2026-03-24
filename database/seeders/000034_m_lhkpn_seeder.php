<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000034MLhkpn extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'lhkpn_id' => 1,
                'lhkpn_tahun' => 2023,
                'lhkpn_judul_informasi' => 'Laporan Harta Kekayaan Penyelenggara Negara',
                'lhkpn_deskripsi_informasi' => '<p><span style="font-family: "schibsted grotesk", sans-serif;">Dasar Hukum dalam menampilkan Laporan Harta Kekayaan Penyelenggara Negara yang telah diumumkan oleh Komisi Pemberantasan Korupsi: </span></p><ol><li><span style="font-family: "schibsted grotesk", sans-serif;">Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 Pasal 15</span></li><li><span style="font-family: "schibsted grotesk", sans-serif; font-size: 1rem;">Keputusan Direktur No. 1228 Tahun 2022 Butir 1</span></li></ol>',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'lhkpn_id' => 2,
                'lhkpn_tahun' => 2024,
                'lhkpn_judul_informasi' => 'Laporan Harta Kekayaan Penyelenggara Negara',
                'lhkpn_deskripsi_informasi' => '<p><span style="font-family: &quot;Schibsted Grotesk&quot;, sans-serif;">Dasar Hukum dalam menampilkan Laporan Harta Kekayaan Penyelenggara Negara yang telah diumumkan oleh Komisi Pemberantasan Korupsi:</span></p><ol><li><span style="font-family: &quot;Schibsted Grotesk&quot;, sans-serif;">Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 Pasal 15</span></li><li><span style="font-family: &quot;Schibsted Grotesk&quot;, sans-serif; font-size: 1rem;">Keputusan Direktur No. 1228 Tahun 2022 Butir 1</span></li></ol>',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('m_lhkpn')->insertOrIgnore($data);
    }
}
