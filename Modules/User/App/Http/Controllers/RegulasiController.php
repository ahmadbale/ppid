<?php

namespace Modules\User\App\Http\Controllers;

// use Modules\User\App\Http\Controllers;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\Request;

class RegulasiController extends Controller
{
    public function DHSOP(){
        $sopList = [
            [
                'judul' => 'SOP Layanan Permohonan Informasi Publik',
                'link' => '#'
            ],
            [
                'judul' => 'SOP Layanan Keberatan Atas Permohonan Informasi Publik',
                'link' => '#'
            ],
            [
                'judul' => 'SOP Penetapan dan Pemutakhiran Daftar Informasi Publik',
                'link' => '#'
            ],
            [
                'judul' => 'SOP Penanganan Sengketa Informasi',
                'link' => '#'
            ],
            [
                'judul' => 'SOP Pengujian tentang Konsekuensi',
                'link' => '#'
            ],
            [
                'judul' => 'SOP Pendokumentasian Informasi Publik',
                'link' => '#'
            ],
            [
                'judul' => 'SOP Pendokumentasian Informasi Publik yang Dikecualikan',
                'link' => '#'
            ],
        ];
        return view('user::informasi-publik.regulasi-DHSOP', compact('sopList'));
    }

    public function DHKIP(){
        $dhkip= [
            [
                'judul' => 'Undang-Undang Republik Indonesia Nomor 14 Tahun 2008',
                'link' => '#'
            ],
            [
                'judul' => 'Undang-Undang Republik Indonesia Nomor 25 Tahun 2009',
                'link' => '#'
            ],
            [
                'judul' => 'Undang-Undang Republik Indonesia Nomor 43 Tahun 2009',
                'link' => '#'
            ],
            [
                'judul' => 'Undang-Undang Republik Indonesia Nomor 12 Tahun 2012',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Pemerintah Republik Indonesia Nomor 61 Tahun 2010',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Pemerintah Republik Indonesia Nomor 96 Tahun 2012',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Pemerintah Republik Indonesia Nomor 4 Tahun 2014',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Menteri Dalam Negeri Republik Indonesia Nomor 3 Tahun 2017',
                'link' => '#'
            ],
            [
                'judul' => 'Keputusan Menteri Pendidikan dan Kebudayaan Nomor 244 Tahun 2015',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Menteri Pendidikan dan Kebudayaan Nomor 41 Tahun 2020',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Mahkamah Agung Nomor 2 Tahun 2011',
                'link' => '#'
            ],
            [
                'judul' => 'Keputusan Mahkamah Agung Nomor 85 Tahun 2011',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Nomor 1 Tahun 2010',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Nomor 1 Tahun 2013',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Nomor 5 Tahun 2016',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Nomor 1 Tahun 2017',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Nomor 1 Tahun 2021',
                'link' => '#'
            ],
        ];

        return view('user::informasi-publik.regulasi-DHKIP', compact('dhkip'));
    }

    public function DHLIP(){
        $dhlip = [
            [
                'judul' => 'Keputusan Direktur Politeknik Negeri Malang No.3047/PL34/OT/2018',
                'link' => '#'
            ],
            [
                'judul' => 'Keputusan Direktur Politeknik Negeri Malang No.36/PL34/OT.01.02/2022',
                'link' => '#'
            ],
            [
                'judul' => 'Keputusan Direktur Politeknik Negeri Malang Nomor 723 Tahun 2022',
                'link' => '#'
            ],
            [
                'judul' => 'Surat Edaran Nomor 189/DIR/SE/2022',
                'link' => '#'
            ],
            [
                'judul' => 'Peraturan Direktur Politeknik Negeri Malang Nomor 10 Tahun 2023',
                'link' => '#'
            ],
            [
                'judul' => 'SK Nomor 562 Tahun 2023',
                'link' => '#'
            ],
        ];

        return view('user::informasi-publik.regulasi-DHLIP', compact('dhlip'));
    }
}
