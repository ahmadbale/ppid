<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPIController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Pengajuan Permohonan Informasi';
    public $pagename = 'Verifikasi Pengajuan Permohonan Informasi';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Permohonan Informasi',
            'list' => ['Home', $this->breadcrumb, 'Verifikasi Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Verifikasi Permohonan Informasi'
        ];

        // Ambil daftar permohonan dari model
        $permohonanInformasi = PermohonanInformasiModel::getDaftarVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'permohonanInformasi' => $permohonanInformasi,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function getData()
    {
        try {
            $permohonanInformasi = PermohonanInformasiModel::getDaftarVerifikasi();

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.data', [
                'permohonanInformasi' => $permohonanInformasi,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data'], 500);
        }
    }

    public function updateData($id)
    {
        try {
            $action = request()->query('action', 'view');
            $permohonan = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                ->findOrFail($id);
            
            if ($action === 'view') {
                $actionType = request()->query('type', 'approve');
                return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.update', [
                    'permohonanInformasi' => $permohonan,
                    'actionType' => $actionType,
                    'daftarPengajuanUrl' => $this->daftarPengajuanUrl
                ])->render();
            }
            
            if ($action === 'approve') {
                $result = $permohonan->validasiDanSetujuiPermohonan();
                return $this->jsonSuccess($result, 'Permohonan informasi berhasil disetujui');
            }
            
            if ($action === 'decline') {
                $alasanPenolakan = request()->input('alasan_penolakan');
                $result = $permohonan->validasiDanTolakPermohonan($alasanPenolakan);
                return $this->jsonSuccess($result, 'Permohonan informasi berhasil ditolak');
            }
            
            if ($action === 'read') {
                $result = $permohonan->validasiDanTandaiDibaca();
                return $this->jsonSuccess($result, 'Permohonan informasi berhasil ditandai dibaca');
            }
            
            return response()->json(['error' => 'Invalid action'], 400);
            
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonan->validasiDanHapusPermohonan();
            return $this->jsonSuccess($result, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
