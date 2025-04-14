<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PengumumanController extends Controller
{
    // public function index()
    // {
    //     try {
    //         Log::info('Mengambil data dari API');

    //         // Ambil data pintasan
    //         $pengumumanResponse = Http::get('http://ppid-polinema.test/api/public/getDataPengumumanLandingPage');
    //         $pengumumanMenus = $this->fetchPengumumanData($pengumumanResponse);

    //         return view('user::pengumuman', compact('pengumumanMenus'));
    //     } catch (\Exception $e) {
    //         Log::error('Error saat mengambil data dari API', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return view('user::pengumuman', ['pengumumanMenus' => [],
    //     ]);
    //   }
    // }

    // private function fetchPengumumanData($response)
    // {
    //     if ($response->failed() || !$response->json('success')) {
    //         Log::warning('API Pintasan gagal atau data tidak lengkap', [
    //             'response' => $response->json() ?? 'Tidak ada response'
    //         ]);
    //         return [];
    //     }

    //     return $this->processPintasanData($response->json('data'));
    // }

    //  private function processPengumumanData($data)
    // {
    //     $result = [];
    //     foreach ($data as $item) {
    //         $result[] = [
    //             'id' => $item['id'] ?? null,
    //             'judul' => $item['judul'] ?? 'Tanpa Judul',
    //             'slug' => $item['slug'] ?? null,
    //             'kategoriSubmenu' => $item['kategoriSubmenu'] ?? null,
    //             'thumbnail' => $item['thumbnail'] ?? null,
    //             'tipe' => $item['tipe'] ?? null,
    //             'value' => $item['value'] ?? null,
    //             'deskripsi' => $item['deskripsi'] ?? null,
    //             'url_selengkapnya' => $item['url_selengkapnya'] ?? null,
    //             'created_at' => $item['created_at'] ?? null,
    //         ];
    //     }
    //     return $result;
    // }


    
public function index(Request $request){
        $title = "Pengumuman PPID Polinema";
        $pengumuman = [
            [
                'gambar' => 'img/gambarcoverpedoman.png',
                'tanggal' => '15 Maret 2024',
                'judul' => 'Nomor Verified WhatsApp Business Resmi Polinema',
                'link' => '#'
            ],
            [
                'gambar' => 'img/berita-1.png',
                'tanggal' => '12 Desember 2024',
                'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
                'link' => '#'
            ],
            [
                'gambar' => 'img/gambarcoverpedoman.png',
                'tanggal' => '12 Desember 2024',
                'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
                'link' => '#'
            ],
            [
                'gambar' => 'img/berita-1.png',
                'tanggal' => '12 Desember 2024',
                'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
                'link' => '#'
            ],
            [
                'gambar' => 'img/gambarcoverpedoman.png',
                'tanggal' => '12 Desember 2024',
                'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
                'link' => '#'
            ],
            [
                'gambar' => 'img/berita-1.png',
                'tanggal' => '12 Desember 2024',
                'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
                'link' => '#'
            ],
        ];

        return view('user::pengumuman', compact('title', 'pengumuman'));
    }


}

