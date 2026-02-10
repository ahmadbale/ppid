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

        // Ambil daftar pernyataan keberatan dari model
        $pernyataanKeberatan = PernyataanKeberatanModel::getDaftarVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPernyataanKeberatan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'pernyataanKeberatan' => $pernyataanKeberatan,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function getData()
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::getDaftarVerifikasi();

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPernyataanKeberatan.data', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data'], 500);
        }
    }

    public function editData($id)
    {
        try {
            $actionType = request()->query('type', 'approve');
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                ->findOrFail($id);
            
            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPernyataanKeberatan.update', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'actionType' => $actionType,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateData($id)
    {
        try {
            $action = request()->input('action');
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                ->findOrFail($id);
            
            if ($action === 'approve') {
                $result = $pernyataanKeberatan->validasiDanSetujuiPermohonan();
                return $this->jsonSuccess($result, 'Pernyataan Keberatan berhasil disetujui');
            }
            
            if ($action === 'decline') {
                $alasanPenolakan = request()->input('alasan_penolakan');
                if (!$alasanPenolakan) {
                    throw new \Exception('Alasan penolakan harus diisi');
                }
                $result = $pernyataanKeberatan->validasiDanTolakPermohonan($alasanPenolakan);
                return $this->jsonSuccess($result, 'Pernyataan Keberatan berhasil ditolak');
            }
            
            if ($action === 'read') {
                $result = $pernyataanKeberatan->validasiDanTandaiDibaca();
                return $this->jsonSuccess($result, 'Pernyataan Keberatan berhasil ditandai dibaca');
            }
            
            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memproses permohonan');
        }
    }

    public function deleteData($id)
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
