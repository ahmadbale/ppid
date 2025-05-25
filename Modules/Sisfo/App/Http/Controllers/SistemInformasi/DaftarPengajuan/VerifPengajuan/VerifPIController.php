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

    public function getApproveModal($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.approve', [
                'permohonan' => $permohonan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data permohonan tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.decline', [
                'permohonan' => $permohonan,
                'daftarPengajuanUrl' => $this->daftarPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data permohonan tidak ditemukan'], 404);
        }
    }

    public function setujuiPermohonan($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonan->validasiDanSetujuiPermohonan();
            return $this->jsonSuccess($result, 'Permohonan informasi berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui permohonan');
        }
    }

    public function tolakPermohonan(Request $request, $id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonan->validasiDanTolakPermohonan($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Permohonan informasi berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak permohonan');
        }
    }

    public function tandaiDibaca($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonan->validasiDanTandaiDibaca();
            return $this->jsonSuccess($result, 'Permohonan informasi berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai permohonan dibaca');
        }
    }

    public function hapusPermohonan($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonan->validasiDanHapusPermohonan();
            return $this->jsonSuccess($result, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus permohonan');
        }
    }
}
