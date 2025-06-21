<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;

class ApiReviewWBSController extends BaseApiController
{
    /**
     * Daftar whistle blowing system untuk review
     */
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status', 'Verifikasi');

                $query = WBSModel::where('isDeleted', 0)
                    ->where('wbs_review_isDeleted', 0)
                    ->where('wbs_status', $status);

                // Filter berdasarkan status review jika diperlukan
                if ($request->has('review_status')) {
                    if ($request->review_status === 'sudah_dibaca') {
                        $query->where('wbs_review_sudah_dibaca', '!=', null);
                    } elseif ($request->review_status === 'belum_dibaca') {
                        $query->whereNull('wbs_review_sudah_dibaca');
                    }
                }

                $whistleBlowingSystem = $query->orderBy('wbs_tanggal_verifikasi', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $whistleBlowingSystem->items(),
                    'pagination' => [
                        'current_page' => $whistleBlowingSystem->currentPage(),
                        'last_page' => $whistleBlowingSystem->lastPage(),
                        'per_page' => $whistleBlowingSystem->perPage(),
                        'total' => $whistleBlowingSystem->total()
                    ]
                ];
            },
            'daftar review whistle blowing system',
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
                return WBSModel::findOrFail($id);
            },
            'detail whistle blowing system untuk approval review',
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
                return WBSModel::findOrFail($id);
            },
            'detail whistle blowing system untuk penolakan review',
            self::ACTION_GET
        );
    }

    /**
     * Setujui review whistle blowing system
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

                $whistleBlowingSystem = WBSModel::findOrFail($id);
                
                $jawaban = $request->jawaban;
                
                // Handle file upload jika ada
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_wbs_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('wbs/jawaban', $fileName, 'public');
                    $jawaban = $filePath; // Gunakan path file sebagai jawaban
                }
                
                return $whistleBlowingSystem->validasiDanSetujuiReview($jawaban);
            },
            'persetujuan review whistle blowing system',
            self::ACTION_UPDATE
        );
    }

    /**
     * Tolak review whistle blowing system
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

                $whistleBlowingSystem = WBSModel::findOrFail($id);
                
                return $whistleBlowingSystem->validasiDanTolakReview($request->alasan_penolakan);
            },
            'penolakan review whistle blowing system',
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
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                
                return $whistleBlowingSystem->validasiDanTandaiDibacaReview();
            },
            'penandaan sebagai dibaca review whistle blowing system',
            self::ACTION_UPDATE
        );
    }

    /**
     * Hapus review whistle blowing system
     */
    public function hapusReview($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                
                return $whistleBlowingSystem->validasiDanHapusReview();
            },
            'review whistle blowing system',
            self::ACTION_DELETE
        );
    }
}