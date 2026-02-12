<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPPController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Pengajuan Permohonan Perawatan Sarana dan Prasarana';
    public $pagename = 'Verifikasi Pengajuan Permohonan Perawatan Sarana dan Prasarana';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Permohonan Perawatan',
            'list' => ['Home', $this->breadcrumb, 'Verifikasi Permohonan Perawatan']
        ];

        $page = (object) [
            'title' => 'Verifikasi Permohonan Perawatan'
        ];

        // Ambil daftar permohonan perawatan dari model
        $permohonanPerawatan = PermohonanPerawatanModel::getDaftarVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanPerawatan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'permohonanPerawatan' => $permohonanPerawatan,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function getData()
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::getDaftarVerifikasi();
            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanPerawatan.data', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data'], 500);
        }
    }

    public function editData($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $actionType = request()->query('type', 'approve');

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanPerawatan.update', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'actionType' => $actionType,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Permohonan Perawatan tidak ditemukan'], 404);
        }
    }

    public function updateData($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $action = request()->input('action');

            if ($action === 'approve') {
                $result = $permohonanPerawatan->validasiDanSetujuiPermohonan();
                return $this->jsonSuccess($result, 'Permohonan Perawatan berhasil disetujui');
            }

            if ($action === 'decline') {
                $alasanPenolakan = request()->input('alasan_penolakan');
                $result = $permohonanPerawatan->validasiDanTolakPermohonan($alasanPenolakan);
                return $this->jsonSuccess($result, 'Permohonan Perawatan berhasil ditolak');
            }

            if ($action === 'read') {
                $result = $permohonanPerawatan->validasiDanTandaiDibaca();
                return $this->jsonSuccess($result, 'Permohonan Perawatan berhasil ditandai dibaca');
            }

            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memproses Permohonan Perawatan');
        }
    }

    public function deleteData($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanHapusPermohonan();
            return $this->jsonSuccess($result, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus Permohonan Perawatan');
        }
    }
}