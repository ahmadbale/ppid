<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;

class ApiReviewPPController extends BaseApiController
{
    /**
     * Daftar permohonan perawatan untuk review
     */
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status', 'Verifikasi');

                $query = PermohonanPerawatanModel::where('isDeleted', 0)
                    ->where('pp_review_isDeleted', 0)
                    ->where('pp_status', $status);

                // Filter berdasarkan status review jika diperlukan
                if ($request->has('review_status')) {
                    if ($request->review_status === 'sudah_dibaca') {
                        $query->where('pp_review_sudah_dibaca', '!=', null);
                    } elseif ($request->review_status === 'belum_dibaca') {
                        $query->whereNull('pp_review_sudah_dibaca');
                    }
                }

                $permohonanPerawatan = $query->orderBy('pp_tanggal_verifikasi', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $permohonanPerawatan->items(),
                    'pagination' => [
                        'current_page' => $permohonanPerawatan->currentPage(),
                        'last_page' => $permohonanPerawatan->lastPage(),
                        'per_page' => $permohonanPerawatan->perPage(),
                        'total' => $permohonanPerawatan->total()
                    ]
                ];
            },
            'daftar review permohonan perawatan',
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
                return PermohonanPerawatanModel::findOrFail($id);
            },
            'detail permohonan perawatan untuk approval review',
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
                return PermohonanPerawatanModel::findOrFail($id);
            },
            'detail permohonan perawatan untuk penolakan review',
            self::ACTION_GET
        );
    }

    /**
     * Setujui review permohonan perawatan
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

                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                
                $jawaban = $request->jawaban;
                
                // Handle file upload jika ada
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_pp_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('jawaban_permohonan_perawatan', $fileName, 'public');
                    $jawaban = $filePath; // Gunakan path file sebagai jawaban
                }
                
                return $permohonanPerawatan->validasiDanSetujuiReview($jawaban);
            },
            'persetujuan review permohonan perawatan',
            self::ACTION_UPDATE
        );
    }

    /**
     * Tolak review permohonan perawatan
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

                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                
                return $permohonanPerawatan->validasiDanTolakReview($request->alasan_penolakan);
            },
            'penolakan review permohonan perawatan',
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
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                
                return $permohonanPerawatan->validasiDanTandaiDibacaReview();
            },
            'penandaan sebagai dibaca review permohonan perawatan',
            self::ACTION_UPDATE
        );
    }

    /**
     * Hapus review permohonan perawatan
     */
    public function hapusReview($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                
                return $permohonanPerawatan->validasiDanHapusReview();
            },
            'review permohonan perawatan',
            self::ACTION_DELETE
        );
    }
}