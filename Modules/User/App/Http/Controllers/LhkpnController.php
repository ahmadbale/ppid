<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LHKPNController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)


    public function getDataLhkpn(Request $request)

    {
        // Data dasar hukum
        $dasarHukum = [
            'Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 Pasal 15',
            'Keputusan Direktur No. 1228 Tahun 2022 Butir 1'
        ];

        // Data tahun tersedia
        $tahunList = [2022, 2023];

        // Tahun default
        $tahunDipilih = $request->get('tahun', 2022);

        // Data LHKPN berdasarkan tahun
//         $lhkpnData = [
//             2022 => [
//                 ['nama' => 'Aang Afandi', 'link' => '#'],
//                 ['nama' => 'Abdul Rasyid', 'link' => '#'],
//                 ['nama' => 'Abdullah Helmy', 'link' => '#'],
//                 ['nama' => 'Agus Suhardono', 'link' => '#'],
//                 ['nama' => 'Ahmad Hermawan', 'link' => '#'],
//             ],
//             2023 => [
//                 ['nama' => 'Bayu Saputra', 'link' => '#'],
//                 ['nama' => 'Citra Dewi', 'link' => '#'],
//                 ['nama' => 'Dian Pratama', 'link' => '#'],
//                 ['nama' => 'Eko Suhendar', 'link' => '#'],
//                 ['nama' => 'Fajar Santoso', 'link' => '#'],
//             ],
//         ];

        // Kirim data ke view
        return view('user::LHKPN', [
            'dasarHukum' => $dasarHukum,
            'tahunList' => $tahunList,
            'tahunDipilih' => $tahunDipilih,
            'lhkpnList' => $lhkpnData[$tahunDipilih] ?? []
        ]);

            $currentPage = $request->query('page', 1);
            $perPage = 10;

            $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn', [
                'per_page' => 10,
                'page' => 1
            ]);

            if ($response->failed() || !$response->json('success')) {
                Log::warning('LHKPN API gagal diambil atau data tidak lengkap');
                return view('user::lhkpn', [
                    'tahunList' => [],
                    'tahunDipilih' => null,
                    'lhkpnList' => [],
                    'updated_at' => null,
                    'pagination' => null,
                    'error' => 'Gagal mengambil data LHKPN dari API'
                ]);
            }

            $allData = $response->json('data');
            $tahunList = collect($allData['data'] ?? [])->pluck('tahun')->unique()->sortDesc()->toArray();

            $tahunDipilih = $request->query('tahun', null);

            if ($tahunDipilih) {
                $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn', [
                    'tahun' => $tahunDipilih,
                    'per_page' => $perPage,
                    'page' => $currentPage
                ]);
            }

                if ($response->failed() || !$response->json('success')) {
                    Log::warning('LHKPN API gagal diambil atau data tidak lengkap untuk tahun spesifik');
                    return view('user::lhkpn', [
                        'tahunList' => $tahunList,
                        'tahunDipilih' => $tahunDipilih,
                        'lhkpnList' => [],
                        'updated_at' => null,
                        'pagination' => null,
                        'error' => 'Gagal mengambil data LHKPN untuk tahun '.$tahunDipilih
                    ]);
                }

                $dataLhkpn = $response->json('data');
                $paginationData = [
                    'current_page' => $dataLhkpn['current_page'] ?? 1,
                    'last_page' => $dataLhkpn['last_page'] ?? 1,
                    'per_page' => $dataLhkpn['per_page'] ?? $perPage,
                    'total' => $dataLhkpn['total'] ?? 0
                ];
            $filteredData = [];
            if ($tahunDipilih && isset($dataLhkpn['data'])) {
                $filteredData = collect($dataLhkpn['data'] ?? [])->where('tahun', $tahunDipilih)->values()->all();
            }

            $processedData = $this->processLHKPNData($filteredData);

            return view('user::lhkpn', [
                'tahunList' => $tahunList,
                'tahunDipilih' => $tahunDipilih,
                'lhkpnList' => $processedData['data'],
                'updated_at' => $processedData['updated_at'],
                'pagination' => $paginationData,
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


        // try {
        //     Log::info('Memulai proses pengambilan data LHKPN');

        //     $currentPage = $request->query('page', 1);
        //     $perPage = 10;
        //     $tahunDipilih = $request->query('tahun', null);

        //     $params = ['per_page' => $perPage, 'page' => $currentPage];
        //     if ($tahunDipilih) $params['tahun'] = $tahunDipilih;

        //     $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn', $params);

        //     if ($response->failed() || !$response->json('success')) {
        //         Log::warning('Gagal mengambil data LHKPN dari API');
        //         return view('user::lhkpn', [
        //             'tahunList' => [],
        //             'tahunDipilih' => $tahunDipilih,
        //             'lhkpnList' => [],
        //             'updated_at' => null,
        //             'pagination' => null,
        //             'error' => 'Gagal mengambil data LHKPN'
        //         ]);
        //     }

        //     $dataLhkpn = $response->json('data');
        //     $tahunList = collect($dataLhkpn['data'] ?? [])->pluck('tahun')->unique()->sortDesc()->toArray();
        //     $filteredData = $tahunDipilih ? collect($dataLhkpn['data'])->where('tahun', $tahunDipilih)->values()->all() : [];

        //     $processedData = $this->processLHKPNData($filteredData);
        //     $paginationData = collect($dataLhkpn)->only(['current_page', 'last_page', 'per_page', 'total'])->toArray();
        //     return view('user::lhkpn', compact('tahunList', 'tahunDipilih', 'processedData', 'paginationData', 'updated_at'));
        // } catch (\Exception $e) {
        //     Log::error('LHKPN Data Fetch Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        //     return view('user::lhkpn', ['tahunList' => [], 'tahunDipilih' => null, 'lhkpnList' => [], 'updated_at' => null, 'pagination' => null, 'error' => 'Terjadi kesalahan saat mengambil data']);
        // }




//         try {
//             Log::info('Mengambil data dari API');

//             // Ambil data pintasan
//             $pintasanResponse = Http::get('http://ppid-polinema.test/api/public/getDataPintasanLainnya');
//             $pintasanMenus = $this->fetchPintasanData($pintasanResponse);

//             // Ambil data akses cepat
//             $aksesCepatResponse = Http::get('http://ppid-polinema.test/api/public/getDataAksesCepat');
//             $aksesCepatMenus = $this->fetchAksesCepatData($aksesCepatResponse);

//             return view('user::landing_page', compact('pintasanMenus', 'aksesCepatMenus'));
//         } catch (\Exception $e) {
//             Log::error('Error saat mengambil data dari API', [
//                 'message' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ]);
//             return view('user::landing_page', ['pintasanMenus' => [], 'aksesCepatMenus' => []]);
//         }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()

        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request): RedirectResponse
    // {

    // }

    /**
     * Show the specified resource.
     */
    // public function show($id)
    // {
    //     return view('user::show');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     return view('user::edit');
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id): RedirectResponse
    // {

    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     //
    // }

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
