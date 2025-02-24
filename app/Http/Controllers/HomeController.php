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
        $heroSlides = [
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
        ];
        $quickAccessMenus = [
            [
                'name' => 'Informasi Setiap Saat',
                'route' => route('informasi-publik.setiap-saat'),
                'static_icon' => asset('img/24-hours.svg'),
                'animated_icon' => asset('img/24-hours.gif')
            ],
            [
                'name' => 'Informasi Berkala',
                'route' => route('informasi-publik.berkala'),
                'static_icon' => asset('img/callendar.svg'),
                'animated_icon' => asset('img/calendar-ez.gif')
            ],
            [
                'name' => 'Informasi Serta Merta',
                'route' => route('informasi-publik.serta-merta'),
                'static_icon' => asset('img/website.svg'),
                'animated_icon' => asset('img/website.gif')
            ],
            [
                'name' => 'Lacak Permohonan',
                'route' => route('permohonan.lacak'),
                'static_icon' => asset('img/email.svg'),
                'animated_icon' => asset('img/email.gif')
            ],
            [
                'name' => 'HELPDESK Akademik',
                'url' => 'https://helpakademik.polinema.ac.id/',
                'static_icon' => asset('img/mba-helpdesk.svg'),
                'animated_icon' => asset('img/helpdesk.gif')
            ],
            [
                'name' => 'Lapor PAN RB',
                'url' => 'https://www.lapor.go.id/',
                'static_icon' => asset('img/laporPANRB.svg'),
                'animated_icon' => asset('img/laporPANRB.svg')
            ],
            [
                'name' => 'Lapor KEMDIKBUD',
                'url' => 'https://kemdikbud.lapor.go.id/',
                'static_icon' => asset('img/laporKemdikbud.svg'),
                'animated_icon' => asset('img/laporKemdikbud.svg')
            ]
        ];

        return view('landing_page', compact('heroSlides', 'pengantar', 'quickAccessMenus'));
    }
}
