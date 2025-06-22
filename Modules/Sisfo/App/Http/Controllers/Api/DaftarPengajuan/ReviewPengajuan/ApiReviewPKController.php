<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;

class ApiReviewPKController extends BaseApiController
{
    /**
     * Daftar pernyataan keberatan untuk review
     */
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status', 'Verifikasi');

                $query = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                    ->where('isDeleted', 0)
                    ->where('pk_review_isDeleted', 0)
                    ->where('pk_status', $status);

                // Filter berdasarkan status review jika diperlukan
                if ($request->has('review_status')) {
                    if ($request->review_status === 'sudah_dibaca') {
                        $query->where('pk_review_sudah_dibaca', '!=', null);
                    } elseif ($request->review_status === 'belum_dibaca') {
                        $query->whereNull('pk_review_sudah_dibaca');
                    }
                }

                $pernyataanKeberatan = $query->orderBy('pk_tanggal_verifikasi', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $pernyataanKeberatan->items(),
                    'pagination' => [
                        'current_page' => $pernyataanKeberatan->currentPage(),
                        'last_page' => $pernyataanKeberatan->lastPage(),
                        'per_page' => $pernyataanKeberatan->perPage(),
                        'total' => $pernyataanKeberatan->total()
                    ]
                ];
            },
            'daftar review pernyataan keberatan',
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
                return PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                    ->findOrFail($id);
            },
            'detail pernyataan keberatan untuk approval review',
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
                return PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                    ->findOrFail($id);
            },
            'detail pernyataan keberatan untuk penolakan review',
            self::ACTION_GET
        );
    }

    /**
     * Setujui review pernyataan keberatan
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

                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                
                $jawaban = $request->jawaban;
                
                // Handle file upload jika ada
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_pk_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('jawaban_pernyataan_keberatan', $fileName, 'public');
                    $jawaban = $filePath; // Gunakan path file sebagai jawaban
                }
                
                return $pernyataanKeberatan->validasiDanSetujuiReview($jawaban);
            },
            'persetujuan review pernyataan keberatan',
            self::ACTION_UPDATE
        );
    }

    /**
     * Tolak review pernyataan keberatan
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

                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                
                return $pernyataanKeberatan->validasiDanTolakReview($request->alasan_penolakan);
            },
            'penolakan review pernyataan keberatan',
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
                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                
                return $pernyataanKeberatan->validasiDanTandaiDibacaReview();
            },
            'penandaan sebagai dibaca review pernyataan keberatan',
            self::ACTION_UPDATE
        );
    }

    /**
     * Hapus review pernyataan keberatan
     */
    public function hapusReview($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                
                return $pernyataanKeberatan->validasiDanHapusReview();
            },
            'review pernyataan keberatan',
            self::ACTION_DELETE
        );
    }
}