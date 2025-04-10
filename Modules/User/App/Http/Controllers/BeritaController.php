<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info('Mengambil data dari API');

            // Ambil halaman saat ini dari request, default ke 1
            $page = $request->get('page', 1);

            // Ambil data dari API dengan parameter halaman
            $beritaResponse = Http::get("http://ppid-polinema.test/api/public/getDataBerita", [
                'page' => $page
            ]);

            $beritaData = $this->fetchBeritaData($beritaResponse);

            // Jika ini adalah request AJAX, kembalikan hanya data JSON
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('user::partials.berita-list', [
                        'beritaMenus' => $beritaData['items'],
                    ])->render(),
                    'pagination' => $beritaData['pagination'],
                ]);
            }

            return view('user::berita', [
                'beritaMenus' => $beritaData['items'],
                'pagination' => $beritaData['pagination'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'html' => '<p class="text-center">Terjadi kesalahan saat mengambil data.</p>',
                    'pagination' => null
                ]);
            }

            return view('user::berita', [
                'beritaMenus' => [],
                'pagination' => null,
            ]);
        }
    }

    private function fetchBeritaData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [
                'items' => [],
                'pagination' => null,
            ];
        }

        return $this->processBeritaData($response->json('data'));
    }

    private function processBeritaData($data)
    {
        $result = [];

        $beritaList = $data['data'] ?? [];

        foreach ($beritaList as $item) {
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

        return [
            'items' => $result,
            'pagination' => [
                'current_page' => $data['current_page'] ?? 1,
                'total_pages' => $data['total_pages'] ?? 1,
                'next_page_url' => $data['next_page_url'] ?? null,
                'prev_page_url' => $data['prev_page_url'] ?? null,
            ]
        ];
    }
}
