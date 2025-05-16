<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPIController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Pengajuan';
    public $pagename = 'Verifikasi Pengajuan';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => $this->pagename,
            'list' => ['Home', $this->pagename]
        ];

        $page = (object) [
            'title' => $this->pagename
        ];

        // Hitung jumlah data untuk badge notifikasi
        $jumlahDaftarVerifPermohonanInformasi = PermohonanInformasiModel::where('pi_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pi_verif_isDeleted', 0)
            ->whereNull('pi_sudah_dibaca')
            ->count();

        $jumlahDaftarVerifPernyataanKeberatan = PernyataanKeberatanModel::where('pk_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pk_verif_isDeleted', 0)
            ->whereNull('pk_sudah_dibaca')
            ->count();

        $jumlahDaftarVerifPengaduanMasyarakat = PengaduanMasyarakatModel::where('pm_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pm_verif_isDeleted', 0)
            ->whereNull('pm_sudah_dibaca')
            ->count();

        $jumlahDaftarVerifWBS = WBSModel::where('wbs_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('wbs_verif_isDeleted', 0)
            ->whereNull('wbs_sudah_dibaca')
            ->count();

        $jumlahDaftarVerifPermohonanPerawatan = PermohonanPerawatanModel::where('pp_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pp_verif_isDeleted', 0)
            ->whereNull('pp_sudah_dibaca')
            ->count();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'jumlahDaftarVerifPermohonanInformasi' => $jumlahDaftarVerifPermohonanInformasi,
            'jumlahDaftarVerifPernyataanKeberatan' => $jumlahDaftarVerifPernyataanKeberatan,
            'jumlahDaftarVerifPengaduanMasyarakat' => $jumlahDaftarVerifPengaduanMasyarakat,
            'jumlahDaftarVerifWBS' => $jumlahDaftarVerifWBS,
            'jumlahDaftarVerifPermohonanPerawatan' => $jumlahDaftarVerifPermohonanPerawatan,
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }

    public function daftarVerifPermohonanInformasi()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Permohonan Informasi',
            'list' => ['Home', $this->breadcrumb, 'Verifikasi Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Verifikasi Permohonan Informasi'
        ];

        // Ambil semua data permohonan informasi dengan kriteria minimal
        $permohonanInformasi = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
            ->where('isDeleted', 0)
            ->where('pi_verif_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();

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

            // Validasi status
            if ($permohonan->pi_status !== 'Masuk') {
                return $this->jsonError(new \Exception('Permohonan sudah diverifikasi sebelumnya'), 'Error');
            }

            // Update status menjadi Verifikasi
            $permohonan->pi_status = 'Verifikasi';
            $permohonan->pi_review = session('alias') ?? 'System';
            $permohonan->pi_tanggal_review = now();
            $permohonan->save();

            return $this->jsonSuccess($permohonan, 'Permohonan informasi berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui permohonan');
        }
    }

    public function tolakPermohonan(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'alasan_penolakan' => 'required|string|max:255'
            ], [
                'alasan_penolakan.required' => 'Alasan penolakan wajib diisi',
                'alasan_penolakan.max' => 'Alasan penolakan maksimal 255 karakter'
            ]);

            $permohonan = PermohonanInformasiModel::findOrFail($id);

            // Validasi status
            if ($permohonan->pi_status !== 'Masuk') {
                return $this->jsonError(new \Exception('Permohonan sudah diverifikasi sebelumnya'), 'Error');
            }

            // Update status menjadi Ditolak
            $permohonan->pi_status = 'Ditolak';
            $permohonan->pi_alasan_penolakan = $request->alasan_penolakan;
            $permohonan->pi_review = session('alias') ?? 'System';
            $permohonan->pi_tanggal_review = now();
            $permohonan->save();

            return $this->jsonSuccess($permohonan, 'Permohonan informasi berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak permohonan');
        }
    }

    public function tandaiDibaca($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);

            // Validasi bahwa permohonan sudah disetujui/ditolak
            if (!in_array($permohonan->pi_status, ['Verifikasi', 'Ditolak'])) {
                return $this->jsonError(
                    new \Exception('Anda harus menyetujui/menolak permohonan ini terlebih dahulu'),
                    'Perhatian'
                );
            }

            // Tandai sebagai dibaca
            $permohonan->pi_sudah_dibaca = session('alias') ?? 'System';
            $permohonan->pi_tanggal_dibaca = now();
            $permohonan->save();

            return $this->jsonSuccess($permohonan, 'Permohonan informasi berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai permohonan dibaca');
        }
    }

    public function hapusPermohonan($id)
    {
        try {
            $permohonan = PermohonanInformasiModel::findOrFail($id);

            // Validasi bahwa permohonan sudah dibaca
            if (empty($permohonan->pi_sudah_dibaca)) {
                return $this->jsonError(
                    new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu'),
                    'Perhatian'
                );
            }

            // Update flag pi_verif_isDeleted dan pi_tanggal_dijawab
            $permohonan->pi_verif_isDeleted = 1;
            $permohonan->pi_tanggal_dijawab = now();
            $permohonan->save();

            return $this->jsonSuccess($permohonan, 'Pengajuan ini telah dihapus dari halaman daftar Verifikasi Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus permohonan');
        }
    }
}
