<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LHKPNController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            Log::info('Mengambil data LHKPN dari API');

            $page = $request->get('page', 1);
            $tahunDipilih = $request->get('tahun');

            $params = ['page' => $page];
            if ($tahunDipilih) {
                $params['tahun'] = $tahunDipilih;
            }

            $response = Http::get("http://ppid-polinema.test/api/public/getDataLhkpn", $params);

            if (!$response->successful()) {
                throw new \Exception("Gagal mendapatkan data dari API LHKPN");
            }

            $lhkpnData = $this->fetchLHKPNData($response);

            // Ambil tahun dari semua item LHKPN secara dinamis
            $allData = $response->json('data.data.data') ?? [];
            $tahunList = collect($allData)
                            ->pluck('tahun')
                            ->unique()
                            ->sortDesc()
                            ->values()
                            ->all();

            // Filter item berdasarkan tahun yang dipilih
            $filteredItems = $lhkpnData['items'];
            if ($tahunDipilih) {
                $filteredItems = array_filter($lhkpnData['items'], function($item) use ($tahunDipilih) {
                    return $item['tahun'] === $tahunDipilih;
                });
                
                // Reindex array after filtering
                $filteredItems = array_values($filteredItems);
            }

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('user::partials.lhkpn-list', [
                        'lhkpnItems' => $filteredItems,
                        'tahunDipilih' => $tahunDipilih,
                        'pagination' => $lhkpnData['pagination'],
                    ])->render(),
                    'pagination' => $lhkpnData['pagination'],
                ]);
            }

            return view('user::LHKPN', [
                'lhkpnItems' => $filteredItems,
                'pagination' => $lhkpnData['pagination'],
                'tahunDipilih' => $tahunDipilih,
                'tahunList' => $tahunList,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data LHKPN dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $request->ajax()
                ? response()->json([
                    'html' => '<p class="text-center text-danger">Terjadi kesalahan saat mengambil data LHKPN.</p>',
                    'pagination' => null,
                ])
                : view('user::LHKPN', [
                    'lhkpnItems' => [],
                    'pagination' => null,
                    'tahunDipilih' => $request->get('tahun'),
                    'tahunList' => [],
                ]);
        }
    }

    private function fetchLHKPNData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API LHKPN gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [
                'items' => [],
                'pagination' => null,
            ];
        }

        return $this->processLHKPNData($response->json());
    }

    private function processLHKPNData($data)
    {
        $result = [];
        
        // Sesuaikan dengan struktur JSON yang diberikan
        $lhkpnList = $data['data']['data']['data'] ?? [];
        
        foreach ($lhkpnList as $item) {
            $details = [];
            
            // Jika ada detail karyawan, proses data detail
            if (!empty($item['details'])) {
                foreach ($item['details'] as $detail) {
                    $details[] = [
                        'id' => $detail['id'] ?? null,
                        'nama_karyawan' => $detail['nama_karyawan'] ?? 'Tidak Diketahui',
                        'file' => $detail['file'] ?? null,
                    ];
                }
            }
            
            // Cek apakah ada lebih banyak data yang tersedia
            $has_more = false;
            $total_karyawan = $item['total_karyawan'] ?? 0;
            $total_details = count($details);
            
            if ($total_karyawan > $total_details) {
                $has_more = true;
            }
            
            // Susun data
            $result[] = [
                'id' => $item['id'] ?? null,
                'tahun' => $item['tahun'] ?? 'N/A',
                'judul' => $item['judul'] ?? 'Tanpa Judul',
                'deskripsi' => $item['deskripsi'] ?? '',
                'updated_at' => $item['updated_at'] ?? null,
                'total_karyawan' => $total_karyawan,
                'has_more' => $has_more,
                'details' => $details,
                'next_page' => $item['next_page'] ?? null,
                'prev_page' => $item['prev_page'] ?? null,
                'current_page' => $item['current_page'] ?? 1,
                'total_pages' => $item['total_pages'] ?? 1,
            ];
        }
        
        // Ambil informasi paginasi dari response
        $paginationData = $data['data']['data'] ?? [];
        
        return [
            'items' => $result,
            'pagination' => [
                'current_page' => $paginationData['current_page'] ?? 1,
                'last_page' => $paginationData['total_pages'] ?? 1,
                'next_page_url' => $paginationData['current_page'] < $paginationData['total_pages'] 
                    ? "?page=" . ($paginationData['current_page'] + 1) 
                    : null,
                'prev_page_url' => $paginationData['current_page'] > 1 
                    ? "?page=" . ($paginationData['current_page'] - 1) 
                    : null,
                'total' => $paginationData['total_items'] ?? 0,
                'per_page' => $paginationData['per_page'] ?? 0,
            ]
        ];
    }
}