<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPMController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Pengajuan Pengaduan Masyarakat';
    public $pagename = 'Verifikasi Pengajuan Pengaduan Masyarakat';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Pengaduan Masyarakat',
            'list' => ['Home', $this->breadcrumb, 'Verifikasi Pengaduan Masyarakat']
        ];

        $page = (object) [
            'title' => 'Verifikasi Pengaduan Masyarakat'
        ];

        // Ambil daftar pengaduan masyarakat dari model
        $pengaduanMasyarakat = PengaduanMasyarakatModel::getDaftarVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPengaduanMasyarakat.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'pengaduanMasyarakat' => $pengaduanMasyarakat,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function getData()
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::getDaftarVerifikasi();
            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPengaduanMasyarakat.data', [
                'pengaduanMasyarakat' => $pengaduanMasyarakat,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data'], 500);
        }
    }

    public function editData($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $actionType = request()->query('type', 'approve');

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPengaduanMasyarakat.update', [
                'pengaduanMasyarakat' => $pengaduanMasyarakat,
                'actionType' => $actionType,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Pengaduan Masyarakat tidak ditemukan'], 404);
        }
    }

    public function updateData($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $action = request()->input('action');

            if ($action === 'approve') {
                $result = $pengaduanMasyarakat->validasiDanSetujuiPermohonan();
                return $this->jsonSuccess($result, 'Pengaduan Masyarakat berhasil disetujui');
            }

            if ($action === 'decline') {
                $alasanPenolakan = request()->input('alasan_penolakan');
                $result = $pengaduanMasyarakat->validasiDanTolakPermohonan($alasanPenolakan);
                return $this->jsonSuccess($result, 'Pengaduan Masyarakat berhasil ditolak');
            }

            if ($action === 'read') {
                $result = $pengaduanMasyarakat->validasiDanTandaiDibaca();
                return $this->jsonSuccess($result, 'Pengaduan Masyarakat berhasil ditandai dibaca');
            }

            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memproses Pengaduan Masyarakat');
        }
    }

    public function deleteData($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $result = $pengaduanMasyarakat->validasiDanHapusPermohonan();
            return $this->jsonSuccess($result, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus Pengaduan Masyarakat');
        }
    }
}