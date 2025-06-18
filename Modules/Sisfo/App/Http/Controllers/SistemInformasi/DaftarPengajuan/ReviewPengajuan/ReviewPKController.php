<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPKController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Pernyataan Keberatan';
    public $pagename = 'Review Pengajuan Pernyataan Keberatan';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Pernyataan Keberatan',
            'list' => ['Home', $this->breadcrumb, 'Review Pernyataan Keberatan']
        ];

        $page = (object) [
            'title' => 'Review Pernyataan Keberatan'
        ];

        // Ambil daftar pernyataan keberatan untuk review dari model
        $pernyataanKeberatan = PernyataanKeberatanModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPernyataanKeberatan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'pernyataanKeberatan' => $pernyataanKeberatan,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPernyataanKeberatan.approve', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data pernyataan keberatan tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPernyataanKeberatan.decline', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data pernyataan keberatan tidak ditemukan'], 404);
        }
    }

    public function setujuiReview(Request $request, $id)
    {
        try {
            // Validasi input jawaban
            $request->validate([
                'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ], [
                'jawaban_file.max' => 'Ukuran file maksimal 10MB'
            ]);

            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            
            $jawaban = $request->jawaban;
            
            // Jika ada file yang diupload
            if ($request->hasFile('jawaban_file')) {
                $file = $request->file('jawaban_file');
                $filename = 'pk_jawaban_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pernyataan_keberatan/jawaban', $filename, 'public');
                $jawaban = $path;
            }
            
            $result = $pernyataanKeberatan->validasiDanSetujuiReview($jawaban);
            return $this->jsonSuccess($result, 'Review pernyataan keberatan berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui review pernyataan keberatan');
        }
    }

    public function tolakReview(Request $request, $id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanTolakReview($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Review pernyataan keberatan berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak review pernyataan keberatan');
        }
    }

    public function tandaiDibacaReview($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanTandaiDibacaReview();
            return $this->jsonSuccess($result, 'Review pernyataan keberatan berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai review sebagai dibaca');
        }
    }

    public function hapusReview($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $result = $pernyataanKeberatan->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}