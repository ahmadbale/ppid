<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPMController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Pengaduan Masyarakat';
    public $pagename = 'Review Pengajuan Pengaduan Masyarakat';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Pengaduan Masyarakat',
            'list' => ['Home', $this->breadcrumb, 'Review Pengaduan Masyarakat']
        ];

        $page = (object) [
            'title' => 'Review Pengaduan Masyarakat'
        ];

        // Ambil daftar pengaduan masyarakat untuk review dari model
        $pengaduanMasyarakat = PengaduanMasyarakatModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPengaduanMasyarakat.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'pengaduanMasyarakat' => $pengaduanMasyarakat,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPengaduanMasyarakat.approve', [
                'pengaduanMasyarakat' => $pengaduanMasyarakat,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data pengaduan masyarakat tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPengaduanMasyarakat.decline', [
                'pengaduanMasyarakat' => $pengaduanMasyarakat,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data pengaduan masyarakat tidak ditemukan'], 404);
        }
    }

    public function setujuiReview(Request $request, $id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $result = $pengaduanMasyarakat->validasiDanSetujuiReview($request->jawaban);
            return $this->jsonSuccess($result, 'Review pengaduan masyarakat berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui review pengaduan masyarakat');
        }
    }

    public function tolakReview(Request $request, $id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $result = $pengaduanMasyarakat->validasiDanTolakReview($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Review pengaduan masyarakat berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak review pengaduan masyarakat');
        }
    }

    public function tandaiDibacaReview($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $result = $pengaduanMasyarakat->validasiDanTandaiDibacaReview();
            return $this->jsonSuccess($result, 'Review pengaduan masyarakat berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai review sebagai dibaca');
        }
    }

    public function hapusReview($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $result = $pengaduanMasyarakat->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}