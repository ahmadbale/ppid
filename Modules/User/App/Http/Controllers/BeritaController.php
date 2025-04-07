<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeritaController extends Controller
{
    public function index()
    {
        try {
            Log::info('Mengambil data dari API');

            // Ambil data pintasan
            $beritaResponse = Http::get('http://ppid-polinema.test/api/public/getDataBerita');
            $beritaMenus = $this->fetchBeritaData($beritaResponse);


            return view('user::berita', compact(
                'beritaMenus',
            ));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('user::berita', [
                'beritaMenus' => [],
            ]);
        }
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

    private function processBeritaData($jsonData)
    {
        $result = [];
        $data = json_decode($jsonData, true);
        
        if (isset($data['data']['data']) && is_array($data['data']['data'])) {
            foreach ($data['data']['data'] as $item) {
                $result[] = [
                    'kategori' => $item['kategori'] ?? 'Berita',
                    'judul' => $item['judul'] ?? 'Tanpa Judul',
                    'slug' => $item['slug'] ?? null,
                    'thumbnail' => $item['thumbnail'] ?? null,
                    'deskripsiThumbnail' => $item['deskripsiThumbnail'] ?? null,
                    'tanggal' => $item['tanggal'] ?? null,
                    'url_selengkapnya' => $item['url_selengkapnya'] ?? null,
                ];
            }
        }
        
        return $result;
    }
    

    public function detail()
    {
        $title = "Polinema Tingkatkan Daya Saing UMKM Desa Duwet dengan Inovasi Teknologi Produksi dan Branding";
        // $description = "E-Form ini digunakan untuk mengajukan permohonan akses informasi publik di Politeknik Negeri Malang<br>sesuai dengan ketentuan yang berlaku.";

        return view('user::berita-detail', compact('title'));
    }
}
