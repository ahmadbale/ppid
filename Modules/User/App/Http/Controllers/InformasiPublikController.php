<?php

namespace Modules\User\App\Http\Controllers;

// use Modules\User\App\Http\Controllers;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\Request;

class InformasiPublikController extends Controller
{
    public function index(){
        $pdfFile = 'storage/test-pdfview1.pdf';
        $pdfName = 'Daftar Informasi Publik Polinema.pdf';
        $sharedBy = 'superadmin';

        return view('user::informasi-publik.daftar',
        compact('pdfFile', 'pdfName', 'sharedBy')
        );
    }

    public function setiapSaat()
    {
        $informasiSetiapSaat = [
            [
                'nama' => 'Seluruh informasi lengkap yang wajib disediakan dan diumumkan secara berkala',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Daftar Informasi Publik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Risalah Rapat Pembentukan Peraturan Perundang-Undangan (Alih Jabatan Dosen)',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Pemberian Pertimbangan Draf Peraturan Direktur Tentang Pedoman Akademik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Rancangan Peraturan atau Kebijakan yang Dibentuk',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Surat Keputusan 27 - 2023 Tentang Tarif Layanan non Akademik Aset Barang Milik Negara',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Informasi tentang peraturan, keputusan dan/atau kebijakan unit organisasi',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
        ];

        $updated_item = [
            ['updated_at' => '14 Juni 2025'],
        ];
        return view('user::informasi-publik.setiap-saat', compact('informasiSetiapSaat', 'updated_item'));
    }

    public function berkala(){

        $informasiBerkala = [
            [
                'nama' => 'Seluruh informasi lengkap yang wajib disediakan dan diumumkan secara berkala',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Daftar Informasi Publik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Risalah Rapat Pembentukan Peraturan Perundang-Undangan (Alih Jabatan Dosen)',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Pemberian Pertimbangan Draf Peraturan Direktur Tentang Pedoman Akademik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Rancangan Peraturan atau Kebijakan yang Dibentuk',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Surat Keputusan 27 - 2023 Tentang Tarif Layanan non Akademik Aset Barang Milik Negara',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Informasi tentang peraturan, keputusan dan/atau kebijakan unit organisasi',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
        ];

        $updated_item = [
            ['updated_at' => '14 Juni 2025'],
        ];
        return view('user::informasi-publik.berkala', compact('informasiBerkala', 'updated_item'));
    }

    public function sertaMerta(){

        $sertaMerta = [
            [
                'nama' => 'Seluruh informasi lengkap yang wajib disediakan dan diumumkan secara berkala',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Daftar Informasi Publik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Risalah Rapat Pembentukan Peraturan Perundang-Undangan (Alih Jabatan Dosen)',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Pemberian Pertimbangan Draf Peraturan Direktur Tentang Pedoman Akademik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Rancangan Peraturan atau Kebijakan yang Dibentuk',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Surat Keputusan 27 - 2023 Tentang Tarif Layanan non Akademik Aset Barang Milik Negara',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Informasi tentang peraturan, keputusan dan/atau kebijakan unit organisasi',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
        ];

        $updated_item = [
            ['updated_at' => '14 Juni 2025'],
        ];
        return view('user::informasi-publik.serta-merta', compact('sertaMerta', 'updated_item'));
    }
}
