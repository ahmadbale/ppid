<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\User\App\Http\Controllers\Controller;

class LHKPNController extends Controller
{
    /**
     * Menampilkan halaman daftar LHKPN.
     */
    public function index(Request $request)
    {
        try {
            $currentPage = $request->query('page', 1);
            $perPage = 10;
            $tahunDipilih = $request->query('tahun', 2022);

            $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn', [
                'per_page' => $perPage,
                'page' => $currentPage
            ]);

            if ($response->failed() || !$response->json('success')) {
                Log::warning('Gagal mengambil data LHKPN dari API');
                return view('user::lhkpn', [
                    'tahunList' => [],
                    'tahunDipilih' => null,
                    'lhkpnList' => [],
                    'updated_at' => null,
                    'pagination' => null,
                    'error' => 'Gagal mengambil data LHKPN dari API'
                ]);
            }

            $dataLhkpn = $response->json('data');
            $tahunList = collect($dataLhkpn['data'] ?? [])->pluck('tahun')->unique()->sortDesc()->toArray();
            $filteredData = collect($dataLhkpn['data'] ?? [])->where('tahun', $tahunDipilih)->values()->all();
            $processedData = $this->processLHKPNData($filteredData);

            return view('user::lhkpn', [
                'tahunList' => $tahunList,
                'tahunDipilih' => $tahunDipilih,
                'lhkpnList' => $processedData['data'],
                'updated_at' => $processedData['updated_at'],
                'pagination' => [
                    'current_page' => $dataLhkpn['current_page'] ?? 1,
                    'last_page' => $dataLhkpn['last_page'] ?? 1,
                    'per_page' => $dataLhkpn['per_page'] ?? $perPage,
                    'total' => $dataLhkpn['total'] ?? 0
                ],
                'error' => null
            ]);
        } catch (\Exception $e) {
            Log::error('LHKPN Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('user::lhkpn', [
                'tahunList' => [],
                'tahunDipilih' => null,
                'lhkpnList' => [],
                'updated_at' => null,
                'pagination' => null,
                'error' => 'Terjadi kesalahan saat mengambil data'
            ]);
        }
    }

    /**
     * Menampilkan form untuk membuat entri baru.
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Memproses data LHKPN untuk ditampilkan.
     */
    private function processLHKPNData(array $data)
    {
        $latestUpdatedAt = collect($data)->pluck('updated_at')->filter()->max();
        return [
            'data' => array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'tahun' => $item['tahun'],
                    'judul' => $item['judul'],
                    'deskripsi' => $item['deskripsi'],
                    'details' => isset($item['details']) ? array_map(function ($detail) {
                        return [
                            'id' => $detail['id'] ?? null,
                            'nama_karyawan' => $detail['nama_karyawan'] ?? 'Tidak diketahui',
                            'file' => $detail['file'] ?? '#'
                        ];
                    }, $item['details']) : [],
                    'total_karyawan' => $item['total_karyawan'] ?? 0,
                    'has_more' => $item['has_more'] ?? false
                ];
            }, $data),
            'updated_at' => $latestUpdatedAt ? date('d M Y, H:i', strtotime($latestUpdatedAt)) : null
        ];
    }
}