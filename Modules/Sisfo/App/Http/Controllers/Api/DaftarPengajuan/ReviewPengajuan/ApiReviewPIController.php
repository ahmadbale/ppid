<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;

class ApiReviewPIController extends BaseApiController
{
    /**
     * Daftar permohonan informasi untuk review
     */
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status', 'Verifikasi');

                $query = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                    ->where('isDeleted', 0)
                    ->where('pi_review_isDeleted', 0)
                    ->where('pi_status', $status);

                // Filter berdasarkan status review jika diperlukan
                if ($request->has('review_status')) {
                    if ($request->review_status === 'sudah_dibaca') {
                        $query->where('pi_review_sudah_dibaca', '!=', null);
                    } elseif ($request->review_status === 'belum_dibaca') {
                        $query->whereNull('pi_review_sudah_dibaca');
                    }
                }

                $permohonanInformasi = $query->orderBy('pi_tanggal_verifikasi', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $permohonanInformasi->items(),
                    'pagination' => [
                        'current_page' => $permohonanInformasi->currentPage(),
                        'last_page' => $permohonanInformasi->lastPage(),
                        'per_page' => $permohonanInformasi->perPage(),
                        'total' => $permohonanInformasi->total()
                    ]
                ];
            },
            'daftar review permohonan informasi',
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
                return PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                    ->findOrFail($id);
            },
            'detail permohonan informasi untuk approval review',
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
                return PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                    ->findOrFail($id);
            },
            'detail permohonan informasi untuk penolakan review',
            self::ACTION_GET
        );
    }

    /**
     * Setujui review permohonan informasi
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

                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                
                $jawaban = $request->jawaban;
                
                // Handle file upload jika ada
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_pi_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('jawaban_permohonan_informasi', $fileName, 'public');
                    $jawaban = $filePath; // Gunakan path file sebagai jawaban
                }
                
                return $permohonanInformasi->validasiDanSetujuiReview($jawaban);
            },
            'persetujuan review permohonan informasi',
            self::ACTION_UPDATE
        );
    }

    /**
     * Tolak review permohonan informasi
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

                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                
                return $permohonanInformasi->validasiDanTolakReview($request->alasan_penolakan);
            },
            'penolakan review permohonan informasi',
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
                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                
                return $permohonanInformasi->validasiDanTandaiDibacaReview();
            },
            'penandaan sebagai dibaca review permohonan informasi',
            self::ACTION_UPDATE
        );
    }

    /**
     * Hapus review permohonan informasi
     */
    public function hapusReview($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                
                return $permohonanInformasi->validasiDanHapusReview();
            },
            'review permohonan informasi',
            self::ACTION_DELETE
        );
    }
}