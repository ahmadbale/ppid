<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Routing\Controller;


class DashboardAdminController extends Controller
{
    use TraitsController;

    public function index() {
        $breadcrumb = (object) [
            'title' => 'Pintasan Menu',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';
        // dummy
        $permintaanTerbaru = [
            [
                'tanggal' => '2024-04-15',
                'nama' => 'Rizky Ananda',
                'status' => 'Pending',
                'jenis' => 'Permohonan Informasi',
            ],
            [
                'tanggal' => '2024-04-14',
                'nama' => 'Nadia Fadhilah',
                'status' => 'Pending',
                'jenis' => 'WBS',
            ],
            [
                'tanggal' => '2024-04-13',
                'nama' => 'Andi Surya',
                'status' => 'Diproses',
                'jenis' => 'Pengaduan Masyarakat',
            ],
            [
                'tanggal' => '2024-04-12',
                'nama' => 'Dian Pratama',
                'status' => 'Diproses',
                'jenis' => 'Permohonan Perawatan Sarana',
            ],
            [
                'tanggal' => '2024-04-12',
                'nama' => 'Dian Pratama',
                'status' => 'Ditolak',
                'jenis' => 'Permohonan Perawatan Sarana',
            ],
        ];

        $menuBoxes = [
            [
                'jumlah' => 150,
                'status' => 'Masuk',
                'bg' => 'primary', // biru terang: cocok untuk masuk
                'icon' => 'fas fa-inbox' // ikon masuk/inbox
            ],
            [
                'jumlah' => 53,
                'status' => 'Diproses',
                'bg' => 'warning', // kuning: cocok untuk proses
                'icon' => 'fas fa-spinner' // ikon loading/proses
            ],
            [
                'jumlah' => 44,
                'status' => 'Disetujui',
                'bg' => 'success', // hijau: cocok untuk status positif
                'icon' => 'fas fa-check-circle' // ikon centang
            ],
            [
                'jumlah' => 65,
                'status' => 'Ditolak',
                'bg' => 'danger', // merah: cocok untuk status gagal
                'icon' => 'fas fa-times-circle' // ikon silang/tolak
            ],
        ];


        return view('sisfo::dashboardADM', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 'permintaanTerbaru' => $permintaanTerbaru, 'menuBoxes' => $menuBoxes]);

    }
}
