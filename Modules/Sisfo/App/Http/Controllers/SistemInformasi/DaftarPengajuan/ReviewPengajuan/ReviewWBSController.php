<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewWBSController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Whistle Blowing System';
    public $pagename = 'Review Pengajuan Whistle Blowing System';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Whistle Blowing System',
            'list' => ['Home', $this->breadcrumb, 'Review Whistle Blowing System']
        ];

        $page = (object) [
            'title' => 'Review Whistle Blowing System'
        ];

        // Ambil daftar WBS untuk review dari model
        $wbs = WBSModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'wbs' => $wbs,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $wbs = WBSModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.approve', [
                'wbs' => $wbs,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data whistle blowing system tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $wbs = WBSModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.decline', [
                'wbs' => $wbs,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data whistle blowing system tidak ditemukan'], 404);
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

            $wbs = WBSModel::findOrFail($id);
            
            $jawaban = $request->jawaban;
            
            // Jika ada file yang diupload
            if ($request->hasFile('jawaban_file')) {
                $file = $request->file('jawaban_file');
                $filename = 'wbs_jawaban_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('wbs/jawaban', $filename, 'public');
                $jawaban = $path;
            }
            
            $result = $wbs->validasiDanSetujuiReview($jawaban);
            return $this->jsonSuccess($result, 'Review whistle blowing system berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui review whistle blowing system');
        }
    }

    public function tolakReview(Request $request, $id)
    {
        try {
            $wbs = WBSModel::findOrFail($id);
            $result = $wbs->validasiDanTolakReview($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Review whistle blowing system berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak review whistle blowing system');
        }
    }

    public function tandaiDibacaReview($id)
    {
        try {
            $wbs = WBSModel::findOrFail($id);
            $result = $wbs->validasiDanTandaiDibacaReview();
            return $this->jsonSuccess($result, 'Review whistle blowing system berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai review sebagai dibaca');
        }
    }

    public function hapusReview($id)
    {
        try {
            $wbs = WBSModel::findOrFail($id);
            $result = $wbs->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}