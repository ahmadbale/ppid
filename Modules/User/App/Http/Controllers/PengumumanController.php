<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PengumumanController extends Controller
{
    // public function index(Request $request){
    //     $title = "Pengumuman PPID Polinema";
    //     $pengumuman = [
    //         [
    //             'gambar' => 'img/gambarcoverpedoman.png',
    //             'tanggal' => '15 Maret 2024',
    //             'judul' => 'Nomor Verified WhatsApp Business Resmi Polinema',
    //             'link' => '#'
    //         ],
    //         [
    //             'gambar' => 'img/berita-1.png',
    //             'tanggal' => '12 Desember 2024',
    //             'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
    //             'link' => '#'
    //         ],
    //         [
    //             'gambar' => 'img/gambarcoverpedoman.png',
    //             'tanggal' => '12 Desember 2024',
    //             'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
    //             'link' => '#'
    //         ],
    //         [
    //             'gambar' => 'img/berita-1.png',
    //             'tanggal' => '12 Desember 2024',
    //             'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
    //             'link' => '#'
    //         ],
    //         [
    //             'gambar' => 'img/gambarcoverpedoman.png',
    //             'tanggal' => '12 Desember 2024',
    //             'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
    //             'link' => '#'
    //         ],
    //         [
    //             'gambar' => 'img/berita-1.png',
    //             'tanggal' => '12 Desember 2024',
    //             'judul' => 'Politeknik Negeri Malang (Polinema) melalui Pengabdian Kepada Masyarakat (PKM)',
    //             'link' => '#'
    //         ],
    //     ];

    //     return view('user::pengumuman', compact('title', 'pengumuman'));
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

            return view('user::landing_page', compact('pintasanMenus', 'aksesCepatMenus'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('user::landing_page', ['pintasanMenus' => [], 'aksesCepatMenus' => []]);
        }
    }
}
