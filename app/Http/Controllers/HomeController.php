<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pengantar = [
            'content' => 'Politeknik Negeri Malang (Polinema) berkomitmen untuk mewujudkan transparansi dan akuntabilitas
                        publik sesuai dengan amanat Undang-Undang Nomor 14 Tahun 2008. Melalui Pejabat Pengelola
                        Informasi dan Dokumentasi (PPID), Polinema menyediakan akses mudah bagi masyarakat terhadap
                        berbagai informasi terkait kegiatan akademik, penelitian, keuangan, dan pengelolaan kampus.
                        Selain itu, PPID Polinema siap membantu Anda dalam mengajukan permohonan informasi, menyampaikan
                        pengaduan, atau sekadar mencari tahu lebih lanjut tentang Polinema.',
            'image' => asset('img/direktur-polinema-bendera.webp')
        ];

        return view('landing_page', [
            'heroSlides' => [
                [
                    'image' => asset('img/grapol.webp'),
                    'title' => 'Selamat Datang di Laman PPID<br>Politeknik Negeri Malang'
                ],
                [
                    'image' => asset('img/maklumat-ppid.webp'),
                    'title' => null
                ],
                [
                    'image' => asset('img/jadwal-pelayanan-informasi-publik.webp'),
                    'title' => null
                ]
            ],
            'pengantar' => $pengantar,
        ]);
    }
}
