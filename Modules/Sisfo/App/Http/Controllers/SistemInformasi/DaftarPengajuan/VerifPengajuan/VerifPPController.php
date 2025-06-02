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

    public function getApproveModal($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanPerawatan.approve', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Permohonan Perawatan tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanPerawatan.decline', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Permohonan Perawatan tidak ditemukan'], 404);
        }
    }

    public function setujuiPermohonan($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanSetujuiPermohonan();
            return $this->jsonSuccess($result, 'Permohonan Perawatan berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui Permohonan Perawatan');
        }
    }

    public function tolakPermohonan(Request $request, $id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $alasanPenolakan = $request->input('alasan_penolakan');
            $result = $permohonanPerawatan->validasiDanTolakPermohonan($alasanPenolakan);
            return $this->jsonSuccess($result, 'Permohonan Perawatan berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak Permohonan Perawatan');
        }
    }

    public function tandaiDibaca($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanTandaiDibaca();
            return $this->jsonSuccess($result, 'Permohonan Perawatan berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai dibaca Permohonan Perawatan');
        }
    }

    public function hapusPermohonan($id)
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