<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\DashboardStatistics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DashboardStatisticsModel extends Model
{
    protected $table = null;
    // public static function getDashboardStatistics()
    // {

    //     $tables = [
    //         ['key' => 'permohonan_informasi', 'table' => 't_permohonan_informasi', 'status' => 'pi_status'],
    //         ['key' => 'whistle_blowing_system', 'table' => 't_wbs', 'status' => 'wbs_status'],
    //         ['key' => 'pernyataan_keberatan', 'table' => 't_pernyataan_keberatan', 'status' => 'pk_status'],
    //         ['key' => 'aduan_masyarakat', 'table' => 't_pengaduan_masyarakat', 'status' => 'pm_status'],
    //         ['key' => 'permohonan_pemeliharaan', 'table' => 't_permohonan_perawatan', 'status' => 'pp_status']
    //     ];

    //     // Inisialisasi array dan total
    //     $results = [];
    //     $totalPermohonan = 0;
    //     $totalDiterima = 0;
    //     $totalDitolak = 0;

    //     // Looping untuk setiap tabel
    //     foreach ($tables as $table) {
    //         $data = DB::table($table['table'])
    //             ->selectRaw("
    //             COUNT(*) as total, 
    //             SUM(CASE WHEN {$table['status']} = 'Disetujui' THEN 1 ELSE 0 END) as diterima, 
    //             SUM(CASE WHEN {$table['status']} = 'Ditolak' THEN 1 ELSE 0 END) as ditolak
    //         ")
    //             ->where('isDeleted', 0)
    //             ->first();

    //         // Simpan hasil
    //         $results[$table['key']] = $data->total;

    //         // Update total
    //         $totalPermohonan += $data->total;
    //         $totalDiterima += $data->diterima;
    //         $totalDitolak += $data->ditolak;
    //     }

    //     // hasil akhir
    //     $arr_data = [
    //         'periode' => [
    //             'tahun' => Carbon::now()->subYear()->format('Y') . '/' . Carbon::now()->format('Y'),
    //             'pengajuan_total' => $totalPermohonan,
    //             'pengajuan_diterima' => $totalDiterima,
    //             'pengajuan_ditolak' => $totalDitolak
    //         ],
    //         'jenis_kasus' => $results
    //     ];

    //     return $arr_data;
    // }
    public static function getDashboardStatistics()
{
    $tables = [
        ['key' => 'permohonan_informasi', 'table' => 't_permohonan_informasi', 'status' => 'pi_status'],
        ['key' => 'whistle_blowing_system', 'table' => 't_wbs', 'status' => 'wbs_status'],
        ['key' => 'pernyataan_keberatan', 'table' => 't_pernyataan_keberatan', 'status' => 'pk_status'],
        ['key' => 'aduan_masyarakat', 'table' => 't_pengaduan_masyarakat', 'status' => 'pm_status'],
        ['key' => 'permohonan_pemeliharaan', 'table' => 't_permohonan_perawatan', 'status' => 'pp_status']
    ];

    // Inisialisasi array dan total
    $results = [];
    $totalPermohonan = 0;
    $totalDiterima = 0;
    $totalDitolak = 0;
    
    // Variabel untuk melacak tahun terbaru
    $latestYear = null;

    // Looping untuk setiap tabel
    foreach ($tables as $table) {
        // Ambil data statistik
        $data = DB::table($table['table'])
            ->selectRaw("
                COUNT(*) as total, 
                SUM(CASE WHEN {$table['status']} = 'Disetujui' THEN 1 ELSE 0 END) as diterima, 
                SUM(CASE WHEN {$table['status']} = 'Ditolak' THEN 1 ELSE 0 END) as ditolak
            ")
            ->where('isDeleted', 0)
            ->first();
            
        // Ambil tahun terbaru untuk perhitungan periode
        $yearData = DB::table($table['table'])
            ->selectRaw("MAX(YEAR(created_at)) as latest_year")
            ->where('isDeleted', 0)
            ->first();
            
        // Update tahun terbaru 
        if ($yearData->latest_year && ($latestYear === null || $yearData->latest_year > $latestYear)) {
            $latestYear = $yearData->latest_year;
        }

        // Simpan hasil
        $results[$table['key']] = $data->total;

        // Update total
        $totalPermohonan += $data->total;
        $totalDiterima += $data->diterima;
        $totalDitolak += $data->ditolak;
    }
    
    // Jika tidak ada data, gunakan tahun sekarang
    if ($latestYear === null) {
        $latestYear = Carbon::now()->format('Y');
    }
    
    // Format string periode dengan tahun sebelumnya/tahun terbaru
    $periodString = ($latestYear - 1) . '/' . $latestYear;

    // hasil akhir
    $arr_data = [
        'periode' => [
            'tahun' => $periodString,
            'pengajuan_total' => $totalPermohonan,
            'pengajuan_diterima' => $totalDiterima,
            'pengajuan_ditolak' => $totalDitolak
        ],
        'jenis_kasus' => $results
    ];

    return $arr_data;
}
}