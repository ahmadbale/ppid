<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class LhkpnController extends Controller
{
    public function getLHKPNData()
    {
        try {
            Log::info('Memulai proses pengambilan data LHKPN');

            $response = Http::get('http://ppid-polinema.test/api/public/getDataLhkpn');
       
            if ($response->failed() || !$response->json('success')) {
                Log::warning('LHKPN API gagal diambil atau data tidak lengkap', [
                    'response' => $response->json() ?? 'Tidak ada response'
                ]);
                return view('user::lhkpn', ['data' => [], 'error' => 'Gagal mengambil data LHKPN']);
            }

            Log::info('LHKPNController: Response API', ['response' => $response->json()]);
      
            $processedData = $this->processLHKPNData($response->json('data'));
            return view('user::lhkpn', ['data' => $processedData, 'error' => null]);
        } catch (\Exception $e) {
            Log::error('LHKPN Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('user::lhkpn', ['data' => [], 'error' => 'Terjadi kesalahan saat mengambil data']);
        }
    }

    private function processLHKPNData($data)
    {
        $result = [];

        foreach ($data['data'] as $item) {
            $result[] = [
                'id' => $item['id'],
                'tahun' => $item['tahun'],
                'judul' => $item['judul'],
                'deskripsi' => $item['deskripsi'],
                'details' => array_map(function ($detail) {
                    return [
                        'id' => $detail['id'],
                        'nama_karyawan' => $detail['nama_karyawan'],
                        'file' => $detail['file']
                    ];
                }, $item['details']),
                'total_karyawan' => $item['total_karyawan'],
                'has_more' => $item['has_more']
            ];
        }

        return [
            'data' => $result,
            'pagination' => $data['pagination']
        ];
    }
}
