<?php
namespace Modules\Sisfo\App\Models\SistemInformasi\DashboardStatistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardStatisticsModel extends Model
{
    protected $table = null;
    
    public static function getDashboardStatistics()
    {
        // Retrieve counts for t_permohonan_informasi
        $permohonanInformasi = DB::table('t_permohonan_informasi')
            ->select(DB::raw('COUNT(*) as total, 
                SUM(CASE WHEN pi_status = "Disetujui" THEN 1 ELSE 0 END) as diterima, 
                SUM(CASE WHEN pi_status = "Ditolak" THEN 1 ELSE 0 END) as ditolak'))
            ->where('isDeleted', 0)
            ->first();
        
        // Retrieve counts for t_wbs
        $wbs = DB::table('t_wbs')
            ->select(DB::raw('COUNT(*) as total, 
                SUM(CASE WHEN wbs_status = "Disetujui" THEN 1 ELSE 0 END) as diterima, 
                SUM(CASE WHEN wbs_status = "Ditolak" THEN 1 ELSE 0 END) as ditolak'))
            ->where('isDeleted', 0)
            ->first();
        
        // Retrieve counts for t_pernyataan_keberatan
        $pernyataanKeberatan = DB::table('t_pernyataan_keberatan')
            ->select(DB::raw('COUNT(*) as total, 
                SUM(CASE WHEN pk_status = "Disetujui" THEN 1 ELSE 0 END) as diterima, 
                SUM(CASE WHEN pk_status = "Ditolak" THEN 1 ELSE 0 END) as ditolak'))
            ->where('isDeleted', 0)
            ->first();
        
        // Retrieve counts for t_pengaduan_masyarakat
        $pengaduanMasyarakat = DB::table('t_pengaduan_masyarakat')
            ->select(DB::raw('COUNT(*) as total, 
                SUM(CASE WHEN pm_status = "Disetujui" THEN 1 ELSE 0 END) as diterima, 
                SUM(CASE WHEN pm_status = "Ditolak" THEN 1 ELSE 0 END) as ditolak'))
            ->where('isDeleted', 0)
            ->first();
        
        // Retrieve counts for t_permohonan_perawatan
        $permohonanPerawatan = DB::table('t_permohonan_perawatan')
            ->select(DB::raw('COUNT(*) as total, 
                SUM(CASE WHEN pp_status = "Disetujui" THEN 1 ELSE 0 END) as diterima, 
                SUM(CASE WHEN pp_status = "Ditolak" THEN 1 ELSE 0 END) as ditolak'))
            ->where('isDeleted', 0)
            ->first();
        
        // Calculate totals for overall statistics
        $totalPermohonan = $permohonanInformasi->total + $wbs->total + $pernyataanKeberatan->total + 
                          $pengaduanMasyarakat->total + $permohonanPerawatan->total;
                          
        $totalDiterima = $permohonanInformasi->diterima + $wbs->diterima + $pernyataanKeberatan->diterima + 
                        $pengaduanMasyarakat->diterima + $permohonanPerawatan->diterima;
                        
        $totalDitolak = $permohonanInformasi->ditolak + $wbs->ditolak + $pernyataanKeberatan->ditolak + 
                       $pengaduanMasyarakat->ditolak + $permohonanPerawatan->ditolak;
        
        // Format the result for frontend display
        $result = [
            'periode' => [
                'tahun' => date('Y') - 1 . '/' . date('Y'),
                'pengajuan_total' => $totalPermohonan,
                'pengajuan_diterima' => $totalDiterima,
                'pengajuan_ditolak' => $totalDitolak
            ],
            'jenis_kasus' => [
                'permohonan_informasi' => $permohonanInformasi->total,
                'aduan_masyarakat' => $pengaduanMasyarakat->total,
                'pernyataan_keberatan' => $pernyataanKeberatan->total,
                'whistle_blowing_system' => $wbs->total,
                'permohonan_pemeliharaan' => $permohonanPerawatan->total
            ]
        ];
        
        return $result;
    }
}