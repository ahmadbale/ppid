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

    public function getData()
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::getDaftarReview();
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPernyataanKeberatan.data', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data'], 500);
        }
    }

    public function editData($id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                ->findOrFail($id);
            $actionType = request()->query('type', 'approve');

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPernyataanKeberatan.update', [
                'pernyataanKeberatan' => $pernyataanKeberatan,
                'actionType' => $actionType,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data pernyataan keberatan tidak ditemukan'], 404);
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
            $action = $request->input('action');

            if ($action === 'approve') {
                // Validasi input jawaban
                $request->validate([
                    'jawaban' => 'required',
                    'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
                ], [
                    'jawaban.required' => 'Jawaban wajib diisi',
                    'jawaban_file.mimes' => 'File harus berformat: pdf, doc, docx, jpg, jpeg, png',
                    'jawaban_file.max' => 'Ukuran file maksimal 10MB'
                ]);

                $jawaban = $request->jawaban;
                
                // Jika ada file yang diupload
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_pk_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('jawaban_pernyataan_keberatan', $fileName, 'public');
                    $jawaban = $filePath; // Gunakan path file sebagai jawaban
                }
                
                $result = $pernyataanKeberatan->validasiDanSetujuiReview($jawaban);
                return $this->jsonSuccess($result, 'Review pernyataan keberatan berhasil disetujui');
            }

            if ($action === 'decline') {
                $result = $pernyataanKeberatan->validasiDanTolakReview($request->alasan_penolakan);
                return $this->jsonSuccess($result, 'Review pernyataan keberatan berhasil ditolak');
            }

            if ($action === 'read') {
                $result = $pernyataanKeberatan->validasiDanTandaiDibacaReview();
                return $this->jsonSuccess($result, 'Review pernyataan keberatan berhasil ditandai dibaca');
            }

            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memproses data');
        }
    }

    public function deleteData($id)
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