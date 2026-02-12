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

    public function getData()
    {
        try {
            $whistleBlowingSystem = WBSModel::getDaftarVerifikasi();
            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifWBS.data', [
                'whistleBlowingSystem' => $whistleBlowingSystem,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data'], 500);
        }
    }

    public function editData($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $actionType = request()->query('type', 'approve');

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifWBS.update', [
                'whistleBlowingSystem' => $whistleBlowingSystem,
                'actionType' => $actionType,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Whistle Blowing System tidak ditemukan'], 404);
        }
    }

    public function updateData($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $action = request()->input('action');

            if ($action === 'approve') {
                $result = $whistleBlowingSystem->validasiDanSetujuiPermohonan();
                return $this->jsonSuccess($result, 'Whistle Blowing System berhasil disetujui');
            }

            if ($action === 'decline') {
                $alasanPenolakan = request()->input('alasan_penolakan');
                $result = $whistleBlowingSystem->validasiDanTolakPermohonan($alasanPenolakan);
                return $this->jsonSuccess($result, 'Whistle Blowing System berhasil ditolak');
            }

            if ($action === 'read') {
                $result = $whistleBlowingSystem->validasiDanTandaiDibaca();
                return $this->jsonSuccess($result, 'Whistle Blowing System berhasil ditandai dibaca');
            }

            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memproses Whistle Blowing System');
        }
    }

    public function deleteData($id)
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