<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Sisfo\App\Models\SistemInformasi\DashboardAdm\DashboardAdminModel;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get filter parameters from URL
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');
            $jenisInformasi = $request->get('jenisInformasi');

            // AMBIL DATA REAL DARI DATABASE dengan filter jika ada
            if ($startDate || $endDate || $jenisInformasi) {
                // Jika ada filter dari URL, gunakan filtered data
                $statistik = DashboardAdminModel::getFilteredStatistics($startDate, $endDate, $jenisInformasi);
                $permintaanTerbaru = DashboardAdminModel::getPermintaanTerbaru(10, $startDate, $endDate, $jenisInformasi);
                $distributionData = DashboardAdminModel::getFilteredDistributionData($startDate, $endDate, $jenisInformasi);
                $chartData = DashboardAdminModel::getFilteredChartData($startDate, $endDate, $jenisInformasi);
            } else {
                // Data default tanpa filter
                $statistik = DashboardAdminModel::getDashboardStatistics();
                $permintaanTerbaru = DashboardAdminModel::getPermintaanTerbaru(10);
                $distributionData = DashboardAdminModel::getDistributionData();
                $chartData = DashboardAdminModel::getChartData();
            }

            $menuBoxes = DashboardAdminModel::getMenuBoxesData();

            // Setup breadcrumb dan page
            $breadcrumb = (object)[
                'title' => 'Dashboard Admin',
                'list' => ['Home', 'Dashboard']
            ];

            $page = (object)[
                'title' => 'Dashboard Administrasi PPID'
            ];

            $activeMenu = 'dashboard';

            return view('sisfo::dashboardADM', compact(
                'menuBoxes', 
                'permintaanTerbaru', 
                'distributionData',
                'chartData',
                'statistik',
                'breadcrumb',
                'page',
                'activeMenu'
            ));

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Dashboard Admin Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback data jika ada error
            return view('sisfo::dashboardADM', [
                'menuBoxes' => [
                    ['bg' => 'info', 'jumlah' => 0, 'status' => 'Total', 'icon' => 'fas fa-clipboard-list'],
                    ['bg' => 'warning', 'jumlah' => 0, 'status' => 'Diproses', 'icon' => 'fas fa-clock'],
                    ['bg' => 'success', 'jumlah' => 0, 'status' => 'Disetujui', 'icon' => 'fas fa-check'],
                    ['bg' => 'danger', 'jumlah' => 0, 'status' => 'Ditolak', 'icon' => 'fas fa-times']
                ],
                'permintaanTerbaru' => collect([]),
                'distributionData' => [],
                'chartData' => ['labels' => [], 'datasets' => []],
                'statistik' => ['periode' => ['pengajuan_total' => 0, 'tahun' => date('Y')]],
                'breadcrumb' => (object)['title' => 'Dashboard Admin', 'list' => ['Home', 'Dashboard']],
                'page' => (object)['title' => 'Dashboard Administrasi PPID'],
                'activeMenu' => 'dashboard'
            ]);
        }
    }

    public function filterData(Request $request)
    {
        try {
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');
            $jenisInformasi = $request->get('jenisInformasi');

            Log::info('Filter request received:', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'jenisInformasi' => $jenisInformasi
            ]);

            // Validasi input tanggal
            if ($startDate && $endDate && $startDate > $endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
                ], 400);
            }

            // AMBIL DATA REAL YANG SUDAH DIFILTER DARI DATABASE
            $filteredData = DashboardAdminModel::getPermintaanTerbaru(50, $startDate, $endDate, $jenisInformasi);
            $filteredStatistics = DashboardAdminModel::getFilteredStatistics($startDate, $endDate, $jenisInformasi);
            $filteredChartData = DashboardAdminModel::getFilteredChartData($startDate, $endDate, $jenisInformasi);
            $filteredDistributionData = DashboardAdminModel::getFilteredDistributionData($startDate, $endDate, $jenisInformasi);

            Log::info('Filter results:', [
                'data_count' => $filteredData->count(),
                'statistics' => $filteredStatistics,
                'has_chart_data' => !empty($filteredChartData),
                'has_distribution_data' => !empty($filteredDistributionData)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'permintaan_terbaru' => $filteredData,
                    'statistik' => $filteredStatistics,
                    'chart_data' => $filteredChartData,
                    'distribution_data' => $filteredDistributionData,
                    'total_filtered' => $filteredData->count()
                ],
                'message' => 'Data berhasil difilter'
            ]);

        } catch (\Exception $e) {
            Log::error('Filter Dashboard Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memfilter data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function lihatSemua(Request $request)
    {
        try {
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');
            $jenisInformasi = $request->get('jenisInformasi');
            $limit = $request->get('limit', 100); // Default 100 data

            Log::info('Lihat Semua request:', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'jenisInformasi' => $jenisInformasi,
                'limit' => $limit
            ]);

            // Ambil data dengan limit yang lebih besar menggunakan method yang sudah ada
            $data = DashboardAdminModel::getPermintaanTerbaru($limit, $startDate, $endDate, $jenisInformasi);

            Log::info('Lihat Semua results:', [
                'data_count' => $data->count(),
                'first_item' => $data->first()
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $data->count(),
                'message' => 'Data berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            Log::error('Lihat Semua Dashboard Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
}