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

        // Ubah variabel dari $wbs menjadi $whistleBlowingSystem agar konsisten dengan view
        $whistleBlowingSystem = WBSModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'whistleBlowingSystem' => $whistleBlowingSystem, // Ubah nama variabel
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.approve', [
                'whistleBlowingSystem' => $whistleBlowingSystem, // Ubah nama variabel
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data whistle blowing system tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.decline', [
                'whistleBlowingSystem' => $whistleBlowingSystem, // Ubah nama variabel
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data whistle blowing system tidak ditemukan'], 404);
        }
    }

    public function setujuiReview(Request $request, $id)
    {
        try {
            // Validasi input jawaban dengan file - sesuaikan dengan pola permohonan informasi
            $request->validate([
                'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ], [
                'jawaban_file.mimes' => 'File harus berformat: pdf, doc, docx, jpg, jpeg, png',
                'jawaban_file.max' => 'Ukuran file maksimal 10MB'
            ]);

            $whistleBlowingSystem = WBSModel::findOrFail($id);
            
            $jawaban = $request->jawaban;
            
            // Jika ada file yang diupload
            if ($request->hasFile('jawaban_file')) {
                $file = $request->file('jawaban_file');
                $fileName = 'jawaban_wbs_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('jawaban_whistle_blowing_system', $fileName, 'public');
                $jawaban = $filePath; // Gunakan path file sebagai jawaban
            }
            
            $result = $whistleBlowingSystem->validasiDanSetujuiReview($jawaban);
            return $this->jsonSuccess($result, 'Review whistle blowing system berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui review whistle blowing system');
        }
    }

    public function tolakReview(Request $request, $id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanTolakReview($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Review whistle blowing system berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak review whistle blowing system');
        }
    }

    public function tandaiDibacaReview($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanTandaiDibacaReview();
            return $this->jsonSuccess($result, 'Review whistle blowing system berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai review sebagai dibaca');
        }
    }

    public function hapusReview($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}