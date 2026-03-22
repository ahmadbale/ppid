<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000031MIpDinamisTabel extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'ip_dinamis_tabel_id' => 1,
                'ip_nama_submenu' => 'Informasi Berkala',
                'ip_judul' => 'Informasi Publik yang Wajib Disediakan dan Diumumkan Secara Berkala',
                'ip_deskripsi' => 'Informasi berkala adalah informasi yang wajib diumumkan secara rutin oleh badan publik dalam jangka waktu tertentu, tanpa harus diminta oleh masyarakat. Jenis informasi ini meliputi laporan keuangan, laporan kinerja, program dan kegiatan, serta informasi lain yang berdampak luas pada masyarakat. Tujuannya adalah untuk memberikan kepastian akses informasi dan mendukung partisipasi aktif masyarakat, sebagaimana diatur dalam Undang-Undang No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik, Pasal 9.',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'ip_dinamis_tabel_id' => 2,
                'ip_nama_submenu' => 'informasi Setiap Saat',
                'ip_judul' => 'Informasi Publik yang Wajib Tersedia Setiap Saat',
                'ip_deskripsi' => 'Informasi setiap saat adalah informasi publik yang wajib disediakan oleh badan publik dan dapat diakses oleh masyarakat kapan saja tanpa perlu menunggu permintaan khusus. Jenis informasi ini mencakup profil badan publik, struktur organisasi, tugas dan fungsi, daftar aset, peraturan, keputusan, dan kebijakan yang berdampak langsung pada masyarakat. Informasi ini disediakan untuk menjamin transparansi dan akuntabilitas, sebagaimana diatur dalam Undang-Undang No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik, Pasal 11.',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            [
                'ip_dinamis_tabel_id' => 3,
                'ip_nama_submenu' => 'Informasi Serta Merta',
                'ip_judul' => 'Informasi Publik yang Wajib Diumumkan Secara Serta Merta',
                'ip_deskripsi' => 'Informasi serta merta adalah informasi yang wajib diumumkan secara langsung oleh badan publik tanpa perlu permohonan karena bersifat mendesak dan dapat mengancam hajat hidup orang banyak atau ketertiban umum, seperti bencana alam, wabah penyakit, atau gangguan layanan publik; kewajiban ini diatur dalam Undang-Undang No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik, Pasal 10 Ayat 1.',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => $now,
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // SKIP ID 4: isDeleted = 1
        ];

        DB::table('m_ip_dinamis_tabel')->insertOrIgnore($data);
    }
}
