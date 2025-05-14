<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;

class LHKPNController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

    private function makeAuthenticatedRequest($endpoint)
    {
        try {
            // Get active token
            $tokenData = $this->jwtTokenService->getActiveToken();
            
            // Make request with token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['token']
            ])->get($this->baseUrl . '/api/' . $endpoint);

            // Check if token expired
            if ($response->status() === 401) {
                // Generate new token and retry
                $tokenData = $this->jwtTokenService->generateSystemToken();
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tokenData['token']
                ])->get($this->baseUrl . '/api/' . $endpoint);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function index(Request $request)
    {
        try {
            Log::info('Mengambil data LHKPN dari API');

            $page = $request->get('page', 1);
            $tahunDipilih = $request->get('tahun');
            $detailPage = $request->get('detail_page', []);

            $params = ['page' => $page];
            if ($tahunDipilih) {
                $params['tahun'] = $tahunDipilih;
            }
            
            // Tambahkan parameter detail_page jika ada
            if (!empty($detailPage)) {
                $params['detail_page'] = $detailPage;
            }

            $response = $this->makeAuthenticatedRequest('public/getDataLhkpn?' . http_build_query($params));

            if (!$response->successful()) {
                throw new \Exception("Gagal mendapatkan data dari API LHKPN");
            }

            $lhkpnData = $this->fetchLHKPNData($response);

            // Ambil tahun dari semua item LHKPN secara dinamis
            $allData = $response->json('data.data') ?? [];
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
        $lhkpnList = $data['data']['data'] ?? [];
        
        foreach ($lhkpnList as $item) {
            $details = $item['details'] ?? [];
            
            // Ambil informasi pagination detail dari response
            $detailPagination = $item['detail_pagination'] ?? null;
            
            // Cek apakah ada lebih banyak data yang tersedia
            $has_more = false;
            $total_karyawan = $detailPagination['total_items'] ?? 0;
            $total_details = count($details);
            
            if ($detailPagination && $detailPagination['total_pages'] > $detailPagination['current_page']) {
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
                'detail_pagination' => $detailPagination,
            ];
        }
        
        // Ambil informasi paginasi dari response
        $paginationData = $data['data'] ?? [];
        
        return [
            'items' => $result,
            'pagination' => [
                'current_page' => $paginationData['current_page'] ?? 1,
                'last_page' => $paginationData['total_pages'] ?? 1,
                'next_page_url' => $paginationData['next_page_url'] ?? null,
                'prev_page_url' => $paginationData['prev_page_url'] ?? null,
                'total' => $paginationData['total_items'] ?? 0,
                'per_page' => $paginationData['per_page'] ?? 0,
            ]
        ];
    }
}