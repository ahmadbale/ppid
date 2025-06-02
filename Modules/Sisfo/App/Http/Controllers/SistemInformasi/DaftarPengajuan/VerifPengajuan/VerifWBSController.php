<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifWBSController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Whistle Blowing System';
    public $pagename = 'Verifikasi Whistle Blowing System';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Whistle Blowing System',
            'list' => ['Home', $this->breadcrumb, 'Verifikasi Whistle Blowing System']
        ];

        $page = (object) [
            'title' => 'Verifikasi Whistle Blowing System'
        ];

        // Ambil daftar WBS dari model
        $whistleBlowingSystem = WBSModel::getDaftarVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifWBS.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'whistleBlowingSystem' => $whistleBlowingSystem,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifWBS.approve', [
                'whistleBlowingSystem' => $whistleBlowingSystem,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Whistle Blowing System tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifWBS.decline', [
                'whistleBlowingSystem' => $whistleBlowingSystem,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Whistle Blowing System tidak ditemukan'], 404);
        }
    }

    public function setujuiPermohonan($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanSetujuiPermohonan();
            return $this->jsonSuccess($result, 'Whistle Blowing System berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui Whistle Blowing System');
        }
    }

    public function tolakPermohonan(Request $request, $id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $alasanPenolakan = $request->input('alasan_penolakan');
            $result = $whistleBlowingSystem->validasiDanTolakPermohonan($alasanPenolakan);
            return $this->jsonSuccess($result, 'Whistle Blowing System berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak Whistle Blowing System');
        }
    }

    public function tandaiDibaca($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanTandaiDibaca();
            return $this->jsonSuccess($result, 'Whistle Blowing System berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai dibaca Whistle Blowing System');
        }
    }

    public function hapusPermohonan($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanHapusPermohonan();
            return $this->jsonSuccess($result, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus Whistle Blowing System');
        }
    }
}