<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PengumumanController extends Controller
{
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