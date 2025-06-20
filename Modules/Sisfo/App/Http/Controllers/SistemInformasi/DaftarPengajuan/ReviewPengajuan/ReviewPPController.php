<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPPController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Permohonan Perawatan';
    public $pagename = 'Review Pengajuan Permohonan Perawatan';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Permohonan Perawatan',
            'list' => ['Home', $this->breadcrumb, 'Review Permohonan Perawatan']
        ];

        $page = (object) [
            'title' => 'Review Permohonan Perawatan'
        ];

        // Ambil daftar Permohonan Perawatan untuk review dari model
        $permohonanPerawatan = PermohonanPerawatanModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'permohonanPerawatan' => $permohonanPerawatan,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.approve', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data permohonan perawatan tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.decline', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data permohonan perawatan tidak ditemukan'], 404);
        }
    }

    public function setujuiReview(Request $request, $id)
    {
        try {
            // Validasi input jawaban dengan file
            $request->validate([
                'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ], [
                'jawaban_file.max' => 'Ukuran file maksimal 10MB'
            ]);

            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            
            $jawaban = $request->jawaban;
            
            // Jika ada file yang diupload
            if ($request->hasFile('jawaban_file')) {
                $file = $request->file('jawaban_file');
                $filename = 'pp_jawaban_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('permohonan_perawatan/jawaban', $filename, 'public');
                $jawaban = $path;
            }
            
            $result = $permohonanPerawatan->validasiDanSetujuiReview($jawaban);
            return $this->jsonSuccess($result, 'Review permohonan perawatan berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui review permohonan perawatan');
        }
    }

    public function tolakReview(Request $request, $id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanTolakReview($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Review permohonan perawatan berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak review permohonan perawatan');
        }
    }

    public function tandaiDibacaReview($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanTandaiDibacaReview();
            return $this->jsonSuccess($result, 'Review permohonan perawatan berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai review sebagai dibaca');
        }
    }

    public function hapusReview($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}