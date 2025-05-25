<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPKController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Pengajuan Pernyataan Keberatan';
    public $pagename = 'Verifikasi Pengajuan Pernyataan Keberatan';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Pernyataan Keberatan',
            'list' => ['Home', $this->breadcrumb, 'Verifikasi Pernyataan Keberatan']
        ];

        $page = (object) [
            'title' => 'Verifikasi Pernyataan Keberatan'
        ];

        // Ambil daftar pernyataan keberataan dari model
        $pernyataanKeberatan = PernyataanKeberatanModel::getDaftarVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPernyataanKeberatan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'pernyataanKeberatan' => $pernyataanKeberatan,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PiDiriSendiri', 'PiOrangLain'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPernyataanKeberatan.approve', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Pernyataan Keberatan tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPernyataanKeberatan.decline', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Pernyataan Keberatan tidak ditemukan'], 404);
        }
    }

    public function setujuiPermohonan($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanSetujuiPermohonan();
            return $this->jsonSuccess($result, 'Pernyataan Keberatan berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui Pernyataan Keberatan');
        }
    }

    public function tolakPermohonan(Request $request, $id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanTolakPermohonan($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Pernyataan Keberatan berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak Pernyataan Keberatan');
        }
    }

    public function tandaiDibaca($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanTandaiDibaca();
            return $this->jsonSuccess($result, 'Pernyataan Keberatan berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai Pernyataan Keberatan dibaca');
        }
    }

    public function hapusPermohonan($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanHapusPermohonan();
            return $this->jsonSuccess($result, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus Pernyataan Keberatan');
        }
    }
}
