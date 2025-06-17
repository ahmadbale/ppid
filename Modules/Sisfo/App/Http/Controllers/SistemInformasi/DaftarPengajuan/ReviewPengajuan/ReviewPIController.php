<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPIController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Permohonan Informasi';
    public $pagename = 'Review Pengajuan Permohonan Informasi';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Permohonan Informasi',
            'list' => ['Home', $this->breadcrumb, 'Review Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Review Permohonan Informasi'
        ];

        // Ambil daftar permohonan untuk review dari model
        $permohonanInformasi = PermohonanInformasiModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanInformasi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'permohonanInformasi' => $permohonanInformasi,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getApproveModal($id)
    {
        try {
            $permohonanInformasi = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanInformasi.approve', [
                'permohonanInformasi' => $permohonanInformasi,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data permohonan tidak ditemukan'], 404);
        }
    }

    public function getDeclineModal($id)
    {
        try {
            $permohonanInformasi = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                ->findOrFail($id);

            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanInformasi.decline', [
                'permohonanInformasi' => $permohonanInformasi,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data permohonan tidak ditemukan'], 404);
        }
    }

    public function setujuiReview(Request $request, $id)
    {
        try {
            // Validasi input jawaban
            $request->validate([
                'jawaban' => 'required',
                'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ], [
                'jawaban.required' => 'Jawaban wajib diisi',
                'jawaban_file.mimes' => 'File harus berformat: pdf, doc, docx, jpg, jpeg, png',
                'jawaban_file.max' => 'Ukuran file maksimal 10MB'
            ]);

            $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
            
            $jawaban = $request->jawaban;
            
            // Jika ada file yang diupload
            if ($request->hasFile('jawaban_file')) {
                $file = $request->file('jawaban_file');
                $fileName = 'jawaban_pi_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('jawaban_permohonan_informasi', $fileName, 'public');
                $jawaban = $filePath; // Gunakan path file sebagai jawaban
            }
            
            $result = $permohonanInformasi->validasiDanSetujuiReview($jawaban);
            return $this->jsonSuccess($result, 'Review permohonan informasi berhasil disetujui');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menyetujui review permohonan');
        }
    }

    public function tolakReview(Request $request, $id)
    {
        try {
            $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonanInformasi->validasiDanTolakReview($request->alasan_penolakan);
            return $this->jsonSuccess($result, 'Review permohonan informasi berhasil ditolak');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menolak review permohonan');
        }
    }

    public function tandaiDibacaReview($id)
    {
        try {
            $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonanInformasi->validasiDanTandaiDibacaReview();
            return $this->jsonSuccess($result, 'Review permohonan informasi berhasil ditandai dibaca');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menandai review permohonan dibaca');
        }
    }

    public function hapusReview($id)
    {
        try {
            $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
            $result = $permohonanInformasi->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review permohonan');
        }
    }
}