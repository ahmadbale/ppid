<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;

class ApiReviewPMController extends BaseApiController
{
    /**
     * Daftar pengaduan masyarakat untuk review
     */
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status', 'Verifikasi');

                $query = PengaduanMasyarakatModel::where('isDeleted', 0)
                    ->where('pm_review_isDeleted', 0)
                    ->where('pm_status', $status);

                // Filter berdasarkan status review jika diperlukan
                if ($request->has('review_status')) {
                    if ($request->review_status === 'sudah_dibaca') {
                        $query->where('pm_review_sudah_dibaca', '!=', null);
                    } elseif ($request->review_status === 'belum_dibaca') {
                        $query->whereNull('pm_review_sudah_dibaca');
                    }
                }

                $pengaduanMasyarakat = $query->orderBy('pm_tanggal_verifikasi', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $pengaduanMasyarakat->items(),
                    'pagination' => [
                        'current_page' => $pengaduanMasyarakat->currentPage(),
                        'last_page' => $pengaduanMasyarakat->lastPage(),
                        'per_page' => $pengaduanMasyarakat->perPage(),
                        'total' => $pengaduanMasyarakat->total()
                    ]
                ];
            },
            'daftar review pengaduan masyarakat',
            self::ACTION_GET
        );
    }

    /**
     * Detail untuk modal approval review
     */
    public function getApproveModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PengaduanMasyarakatModel::findOrFail($id);
            },
            'detail pengaduan masyarakat untuk approval review',
            self::ACTION_GET
        );
    }

    /**
     * Detail untuk modal decline review
     */
    public function getDeclineModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PengaduanMasyarakatModel::findOrFail($id);
            },
            'detail pengaduan masyarakat untuk penolakan review',
            self::ACTION_GET
        );
    }

    /**
     * Setujui review pengaduan masyarakat
     */
    public function setujuiReview(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                // Validasi input
                $request->validate([
                    'jawaban' => 'required',
                    'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
                ], [
                    'jawaban.required' => 'Jawaban wajib diisi',
                    'jawaban_file.mimes' => 'File harus berformat: pdf, doc, docx, jpg, jpeg, png',
                    'jawaban_file.max' => 'Ukuran file maksimal 10MB'
                ]);

                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                
                $jawaban = $request->jawaban;
                
                // Handle file upload jika ada
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_pm_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('jawaban_pengaduan_masyarakat', $fileName, 'public');
                    $jawaban = $filePath; // Gunakan path file sebagai jawaban
                }
                
                return $pengaduanMasyarakat->validasiDanSetujuiReview($jawaban);
            },
            'persetujuan review pengaduan masyarakat',
            self::ACTION_UPDATE
        );
    }

    /**
     * Tolak review pengaduan masyarakat
     */
    public function tolakReview(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                // Validasi input
                $request->validate([
                    'alasan_penolakan' => 'required|string|max:500'
                ], [
                    'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
                    'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter'
                ]);

                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                
                return $pengaduanMasyarakat->validasiDanTolakReview($request->alasan_penolakan);
            },
            'penolakan review pengaduan masyarakat',
            self::ACTION_UPDATE
        );
    }

    /**
     * Tandai review sebagai sudah dibaca
     */
    public function tandaiDibacaReview($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                
                return $pengaduanMasyarakat->validasiDanTandaiDibacaReview();
            },
            'penandaan sebagai dibaca review pengaduan masyarakat',
            self::ACTION_UPDATE
        );
    }

    /**
     * Hapus review pengaduan masyarakat
     */
    public function hapusReview($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                
                return $pengaduanMasyarakat->validasiDanHapusReview();
            },
            'review pengaduan masyarakat',
            self::ACTION_DELETE
        );
    }
}