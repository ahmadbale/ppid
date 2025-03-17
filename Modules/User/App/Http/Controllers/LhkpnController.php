<?php

namespace Modules\User\App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;

namespace Modules\User\App\Http\Controllers;

 use App\Http\Controllers\Controller;
 use Illuminate\Http\RedirectResponse;
 use Illuminate\Http\Request;
 use Illuminate\Http\Response;

class LhkpnController extends Controller
{
    // public function getLHKPNData()
    // {
    //     try {
    //         Log::info('Memulai proses pengambilan data LHKPN');

    //         $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn');

    //         if ($response->failed() || !$response->json('success')) {
    //             Log::warning('LHKPN API gagal diambil atau data tidak lengkap', [
    //                 'response' => $response->json() ?? 'Tidak ada response'
    //             ]);
    //             return view('user::lhkpn', ['data' => [], 'error' => 'Gagal mengambil data LHKPN']);
    //         }

    //         Log::info('LHKPNController: Response API', ['response' => $response->json()]);

    //         $processedData = $this->processLHKPNData($response->json('data'));
    //         return view('user::lhkpn', ['data' => $processedData, 'error' => null]);
    //     } catch (\Exception $e) {
    //         Log::error('LHKPN Data Fetch Error', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return view('user::lhkpn', ['data' => [], 'error' => 'Terjadi kesalahan saat mengambil data']);
    //     }
    // }

    // private function processLHKPNData($data)
    // {
    //     $result = [];

    //     foreach ($data['data'] as $item) {
    //         $result[] = [
    //             'id' => $item['id'],
    //             'tahun' => $item['tahun'],
    //             'judul' => $item['judul'],
    //             'deskripsi' => $item['deskripsi'],
    //             'details' => array_map(function ($detail) {
    //                 return [
    //                     'id' => $detail['id'],
    //                     'nama_karyawan' => $detail['nama_karyawan'],
    //                     'file' => $detail['file']
    //                 ];
    //             }, $item['details']),
    //             'total_karyawan' => $item['total_karyawan'],
    //             'has_more' => $item['has_more']
    //         ];
    //     }

    //     return [
    //         'data' => $result,
    //         'pagination' => $data['pagination']
    //     ];
    // }
    public function index(Request $request)
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
         $lhkpnData = [
             2022 => [
                 ['nama' => 'Aang Afandi', 'link' => '#'],
                 ['nama' => 'Abdul Rasyid', 'link' => '#'],
                 ['nama' => 'Abdullah Helmy', 'link' => '#'],
                 ['nama' => 'Agus Suhardono', 'link' => '#'],
                 ['nama' => 'Ahmad Hermawan', 'link' => '#'],
             ],
             2023 => [
                 ['nama' => 'Bayu Saputra', 'link' => '#'],
                 ['nama' => 'Citra Dewi', 'link' => '#'],
                 ['nama' => 'Dian Pratama', 'link' => '#'],
                 ['nama' => 'Eko Suhendar', 'link' => '#'],
                 ['nama' => 'Fajar Santoso', 'link' => '#'],
             ],
         ];

         // Kirim data ke view
         return view('user::LHKPN', [
             'dasarHukum' => $dasarHukum,
             'tahunList' => $tahunList,
             'tahunDipilih' => $tahunDipilih,
             'lhkpnList' => $lhkpnData[$tahunDipilih] ?? []
         ]);
     }
}

// ------------------------------------------------------------
// namespace Modules\User\App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;

// class LhkpnController extends Controller
// {
//     public function index(Request $request)
//     {
//         try {
//             Log::info('Memulai proses pengambilan data LHKPN');

//             $tahun = $request->query('tahun', now()->year);

//             $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn', [
//                 'tahun' => $tahun,
//                 'page' => 1,
//                 'per_page' => 10,
//                 'limit_karyawan' => 10
//             ]);

//             if ($response->failed() || !$response->json('success')) {
//                 Log::warning('LHKPN API gagal diambil', ['response' => $response->json()]);
//                 return view('user::lhkpn', [
//                     'tahunList' => [],
//                     'tahunDipilih' => null,
//                     'lhkpnList' => [],
//                     'update_at' => null,
//                     'error' => 'Gagal mengambil data LHKPN'
//                 ]);
//             }

//             Log::info('LHKPN API Response', ['response' => $response->json()]);

//             $processedData = $this->processLHKPNData($response->json('data'), $tahun);

//             return view('user::lhkpn', [
//                 'tahunList' => $processedData['tahun_list'],
//                 'tahunDipilih' => $tahun,
//                 'lhkpnList' => $processedData['data'],
//                 'update_at' => $processedData['update_at'] ?? null,
//                 'error' => null
//             ]);
//         } catch (\Exception $e) {
//             Log::error('LHKPN Data Fetch Error', ['message' => $e->getMessage()]);
//             return view('user::lhkpn', [
//                 'tahunList' => [],
//                 'tahunDipilih' => null,
//                 'lhkpnList' => [],
//                 'update_at' => null,
//                 'error' => 'Terjadi kesalahan saat mengambil data'
//             ]);
//         }
//     }

//     private function processLHKPNData($data, $tahun)
//     {
//         $tahunList = collect($data)->pluck('tahun')->unique()->sort()->values()->all();

//         $filteredData = collect($data)->filter(function ($item) use ($tahun) {
//             return $item['tahun'] == $tahun;
//         })->map(function ($item) {
//             return [
//                 'nama' => $item['judul'],
//                 'link' => url('lhkpn/' . $item['id']),
//                 'updated_at' => $item['updated_at'] ?? null
//             ];
//         })->values()->all();

//         $latestUpdate = collect($filteredData)->max('updated_at');

//         return [
//             'tahun_list' => $tahunList,
//             'data' => $filteredData,
//             'update_at' => $latestUpdate
//         ];
//     }
// }

