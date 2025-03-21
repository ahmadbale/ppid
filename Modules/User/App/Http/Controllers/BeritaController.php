<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index()
    {
        $title = "Berita PPID Polinema";
        // $description = "E-Form ini digunakan untuk mengajukan permohonan akses informasi publik di Politeknik Negeri Malang<br>sesuai dengan ketentuan yang berlaku.";

        return view('user::berita', compact('title'));
    }

    public function detail()
    {
        $title = "Polinema Tingkatkan Daya Saing UMKM Desa Duwet dengan Inovasi Teknologi Produksi dan Branding";
        // $description = "E-Form ini digunakan untuk mengajukan permohonan akses informasi publik di Politeknik Negeri Malang<br>sesuai dengan ketentuan yang berlaku.";

        return view('user::berita-detail', compact('title'));
    }
}
