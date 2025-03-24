<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    // public function index()
    // {
    //     $pengantar = [
    //         'content' => 'Politeknik Negeri Malang (Polinema) berkomitmen untuk mewujudkan transparansi dan akuntabilitas
    //                     publik sesuai dengan amanat Undang-Undang Nomor 14 Tahun 2008. Melalui Pejabat Pengelola
    //                     Informasi dan Dokumentasi (PPID), Polinema menyediakan akses mudah bagi masyarakat terhadap
    //                     berbagai informasi terkait kegiatan akademik, penelitian, keuangan, dan pengelolaan kampus.
    //                     Selain itu, PPID Polinema siap membantu Anda dalam mengajukan permohonan informasi, menyampaikan
    //                     pengaduan, atau sekadar mencari tahu lebih lanjut tentang Polinema.',
    //         'image' => asset('img/direktur-polinema-bendera.webp')
    //     ];
    //     $heroSlides = [
    //         [
    //             'image' => asset('img/grapol.webp'),
    //             'title' => 'Selamat Datang di Laman PPID<br>Politeknik Negeri Malang'
    //         ],
    //         [
    //             'image' => asset('img/maklumat-ppid.webp'),
    //             'title' => null
    //         ],
    //         [
    //             'image' => asset('img/jadwal-pelayanan-informasi-publik.webp'),
    //             'title' => null
    //         ]
    //     ];
    //     $quickAccessMenus = [
    //         [
    //             'name' => 'Informasi Setiap Saat',
    //             'route' => route('informasi-publik.setiap-saat'),
    //             'static_icon' => asset('img/24-hours.svg'),
    //             'animated_icon' => asset('img/24-hours.gif')
    //         ],
    //         [
    //             'name' => 'Informasi Berkala',
    //             'route' => route('informasi-publik.berkala'),
    //             'static_icon' => asset('img/callendar.svg'),
    //             'animated_icon' => asset('img/calendar-ez.gif')
    //         ],
    //         [
    //             'name' => 'Informasi Serta Merta',
    //             'route' => route('informasi-publik.serta-merta'),
    //             'static_icon' => asset('img/website.svg'),
    //             'animated_icon' => asset('img/website.gif')
    //         ],
    //         [
    //             'name' => 'Lacak Permohonan',
    //             'route' => route('permohonan.lacak'),
    //             'static_icon' => asset('img/email.svg'),
    //             'animated_icon' => asset('img/email.gif')
    //         ],
    //         [
    //             'name' => 'HELPDESK Akademik',
    //             'url' => 'https://helpakademik.polinema.ac.id/',
    //             'static_icon' => asset('img/mba-helpdesk.svg'),
    //             'animated_icon' => asset('img/helpdesk.gif')
    //         ],
    //         [
    //             'name' => 'Lapor PAN RB',
    //             'url' => 'https://www.lapor.go.id/',
    //             'static_icon' => asset('img/laporPANRB.svg'),
    //             'animated_icon' => asset('img/laporPANRB.svg')
    //         ],
    //         [
    //             'name' => 'Lapor KEMDIKBUD',
    //             'url' => 'https://kemdikbud.lapor.go.id/',
    //             'static_icon' => asset('img/laporKemdikbud.svg'),
    //             'animated_icon' => asset('img/laporKemdikbud.svg')
    //         ]
    //     ];

    
    //     $pintasanMenus = [
    //         [
    //             'title' => 'Sistem Informasi',
    //             'menu' => [
    //                 ['name' => 'POLINEMA', 'route' => '#'],
    //                 ['name' => 'PORTAL', 'route' => '#'],
    //                 ['name' => 'SIAKAD', 'route' => '#'],
    //                 ['name' => 'SPMB', 'route' => '#'],
    //                 ['name' => 'P2M', 'route' => '#'],
    //                 ['name' => 'Jaminan Mutu', 'route' => '#'],
    //                 ['name' => 'Alumni', 'route' => '#'],
    //             ]
    //         ],
    //         [
    //             'title' => 'Kembdikbud',
    //             'menu' => [
    //                 ['name' => 'LPSE KEMDIKBUD', 'route' => '#'],
    //             ]
    //         ]
    //     ];
    //     $berita = [
    //         [
    //             'title' => 'Sosialisasi Layanan Informasi Publik Polinema, Meningkatkan Keterbukaan dan Aksesibilitas Informasi untuk Semua',
    //             'deskripsi' => 'Malang, 17 Oktober 2024 - Politeknik Negeri Malang (Polinema) hari ini menggelar kegiatan
    //             “Sosialisasi Layanan Informasi Publik Polinema Pejabat Pengelola Informasi dan Dokumentasi”.
    //             Acara yang berlangsung di Ruang Rapim Gedung AA lantai 2 ini diadakan secara hybrid ..',
    //             'route' => '#'
    //         ],
    //         [
    //             'title' => 'Kegiatan Sosialisasi Pejabat Pengelola Informasi Dan Dokumentasi Oleh Pimpinan Dan Jajarannya',
    //             'deskripsi' => 'Keterbukaan Informasi sangat diperlukan pada era digital saat ini. Oleh karena itu, melalui
    //             Pejabat Pengelola Informasi dan Dokumentasi (PPID) Politeknik Negeri Malang memberikan pelayanan
    //             informasi publik yang bersifat terbuka dan dapat diakses oleh masyarakat..',
    //             'route' => '#'
    //         ]
    //     ];
    //     $dokumentasi = [
    //         [
    //             'dokumentasi' => asset('img/dokumentasi-1.webp')
    //         ],
    //         [
    //             'dokumentasi' => asset('img/dokumentasi-2.webp')
    //         ],
    //         [
    //             'dokumentasi' => asset('img/dokumentasi-3.webp')
    //         ],
    //         [
    //             'dokumentasi' => asset('img/dokumentasi-1.webp')
    //         ]
    //     ];
    //     $pengumuman = [
    //         [
    //             'tanggal' => '1 Februari 2025',
    //             'deskripsi' => 'Ini adalah Pengumuman 1',
    //             'route' => '#'
    //         ],
    //         [
    //             'tanggal' => '30 Januari 2025',
    //             'deskripsi' => 'Ini adalah Pengumuman 2',
    //             'route' => '#'
    //         ],
    //         [
    //             'tanggal' => '25 Januari 2025',
    //             'deskripsi' => 'Ini adalah Pengumuman 3',
    //             'route' => '#'
    //         ]
    //     ];
    //     $media = [
    //         [
    //             'title' => 'Keterbukaan Informasi Publik',
    //             'link' => 'https://www.youtube.com/embed/9vlRk9C37JE'
    //         ]
    //     ];

    //     return view('user::landing_page', compact('heroSlides', 'pengantar', 'quickAccessMenus', 'pintasanMenus', 'berita', 'dokumentasi', 'pengumuman', 'media'));
    // }
    public function index()
    {
        try {
            Log::info('Mengambil data dari API');
    
            // Ambil data pintasan
            $pintasanResponse = Http::get('http://ppid-polinema.test/api/public/getDataPintasanLainnya');
            $pintasanMenus = $this->fetchPintasanData($pintasanResponse);
    
            // Ambil data akses cepat
            $aksesCepatResponse = Http::get('http://ppid-polinema.test/api/public/getDataAksesCepat');
            $aksesCepatMenus = $this->fetchAksesCepatData($aksesCepatResponse);

            $pengumumanResponse = Http::get('http://ppid-polinema.test/api/public/getDataPengumumanLandingPage');
            $pengumumanMenus = $this->fetchPengumumanData($pengumumanResponse);

            $beritaResponse = Http::get('http://ppid-polinema.test/api/public/getDataBeritaLandingPage');
            $beritaMenus = $this->fetchBeritaData($beritaResponse);
    
            return view('user::landing_page', compact('pintasanMenus', 'aksesCepatMenus',
                        'pengumumanMenus',
                        'beritaMenus'
                        ));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('user::landing_page', ['pintasanMenus' => [], 'aksesCepatMenus' => [], 'pengumumanMenus' => [], 'beritaMenus' => []]);
        }
    }
    
    private function fetchPintasanData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pintasan gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }
        
        return $this->processPintasanData($response->json('data'));
    }
    
    private function processPintasanData($data)
    {
        $result = [];
        foreach ($data as $item) {
            foreach ($item['pintasan'] as $pintasan) {
                $result[] = [
                    'title' => $pintasan['nama_kategori'],
                    'menu' => array_map(function ($detail) {
                        return [
                            'name' => $detail['judul'],
                            'route' => $detail['url']
                        ];
                    }, $pintasan['detail'])
                ];
            }
        }
        return $result;
    }
    
    private function fetchAksesCepatData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Akses Cepat gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }
        
        return $this->processAksesCepatData($response->json('data'));
    }
    
    private function processAksesCepatData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'title' => $item['kategori_judul'],
                'menu' => array_map(function ($akses) {
                    return [
                        'name' => $akses['judul'],
                        'static_icon' => $akses['static_icon'],
                        'animation_icon' => $akses['animation_icon'],
                        'route' => $akses['url']
                    ];
                }, $item['akses_cepat'])
            ];
        }
        return $result;
    }
    
 private function fetchPengumumanData($response)
 {
     if ($response->failed() || !$response->json('success')) {
         Log::warning('API Pengumuman gagal atau data tidak lengkap', [
             'response' => $response->json() ?? 'Tidak ada response'
         ]);
         return [];
     }
     
     return $this->processPengumumanData($response->json('data'));
 }
 private function processPengumumanData($data)
 {
     return collect($data)->map(function ($item) {
         return [
             'id' => $item['id'] ?? null,
             'judul' => $item['judul'] ?? 'Tanpa Judul',
             'slug' => $item['slug'] ?? null,
             'kategoriSubmenu' => $item['kategoriSubmenu'] ?? null,
             'thumbnail' => $item['thumbnail'] ?? null,
             'tipe' => $item['tipe'] ?? null,
             'value' => $item['value'] ?? null,
             'deskripsi' => $item['deskripsi'] ?? null,
             'url_selengkapnya' => $item['url_selengkapnya'] ?? null,
             'created_at' => $item['created_at'] ?? null,
         ];
     })->toArray();
 }
 

    private function fetchBeritaData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }
        
        return $this->processBeritaData($response->json('data'));
    }

    private function processBeritaData($data)
    {
        return collect($data)->map(function ($item) {
            return [
            'kategori' => $item['kategori'] ?? 'Berita',
            'judul' => $item['judul'] ?? 'Tanpa Judul',
            'slug' => $item['slug'] ?? null,
            'deskripsi' => $item['deskripsi'] ?? null,
            'url_selengkapnya' => $item['url_selengkapnya'] ?? null,
            ];
        })->toArray();
    }

}
