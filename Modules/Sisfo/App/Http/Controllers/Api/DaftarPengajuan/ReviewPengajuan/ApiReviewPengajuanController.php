<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;

class ApiReviewPengajuanController extends BaseApiController
{
    /**
     * Mendapatkan dashboard review pengajuan
     */
    public function index()
    {
        return $this->executeWithAuthentication(
            function ($user) {
                // Ambil jumlah review dari masing-masing model
                $jumlahReview = [
                    'permohonanInformasi' => PermohonanInformasiModel::hitungJumlahReview(),
                    'pernyataanKeberatan' => PernyataanKeberatanModel::hitungJumlahReview(),
                    'pengaduanMasyarakat' => PengaduanMasyarakatModel::hitungJumlahReview(),
                    'wbs' => WBSModel::hitungJumlahReview(),
                    'permohonanPerawatan' => PermohonanPerawatanModel::hitungJumlahReview(),
                ];

                return [
                    'jumlahDaftarReviewPermohonanInformasi' => $jumlahReview['permohonanInformasi'],
                    'jumlahDaftarReviewPernyataanKeberatan' => $jumlahReview['pernyataanKeberatan'],
                    'jumlahDaftarReviewPengaduanMasyarakat' => $jumlahReview['pengaduanMasyarakat'],
                    'jumlahDaftarReviewWBS' => $jumlahReview['wbs'],
                    'jumlahDaftarReviewPermohonanPerawatan' => $jumlahReview['permohonanPerawatan'],
                    'totalReview' => array_sum($jumlahReview)
                ];
            },
            'dashboard review pengajuan',
            self::ACTION_GET
        );
    }

    /**
     * Mendapatkan ringkasan detail review pengajuan
     */
    public function getSummary()
    {
        return $this->executeWithAuthentication(
            function ($user) {
                $summary = [];

                // Summary Permohonan Informasi
                $summary['permohonan_informasi'] = [
                    'total' => PermohonanInformasiModel::where('pi_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pi_review_isDeleted', 0)
                        ->count(),
                    'belum_dibaca' => PermohonanInformasiModel::where('pi_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pi_review_isDeleted', 0)
                        ->where(function($q) {
                            $q->whereNull('pi_review_sudah_dibaca')
                              ->orWhere('pi_review_sudah_dibaca', 0);
                        })
                        ->count(),
                    'sudah_dibaca' => PermohonanInformasiModel::where('pi_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pi_review_isDeleted', 0)
                        ->where('pi_review_sudah_dibaca', 1)
                        ->count(),
                ];

                // Summary Pernyataan Keberatan
                $summary['pernyataan_keberatan'] = [
                    'total' => PernyataanKeberatanModel::where('pk_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pk_review_isDeleted', 0)
                        ->count(),
                    'belum_dibaca' => PernyataanKeberatanModel::where('pk_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pk_review_isDeleted', 0)
                        ->where(function($q) {
                            $q->whereNull('pk_review_sudah_dibaca')
                              ->orWhere('pk_review_sudah_dibaca', 0);
                        })
                        ->count(),
                    'sudah_dibaca' => PernyataanKeberatanModel::where('pk_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pk_review_isDeleted', 0)
                        ->where('pk_review_sudah_dibaca', 1)
                        ->count(),
                ];

                // Summary Pengaduan Masyarakat
                $summary['pengaduan_masyarakat'] = [
                    'total' => PengaduanMasyarakatModel::where('pm_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pm_review_isDeleted', 0)
                        ->count(),
                    'belum_dibaca' => PengaduanMasyarakatModel::where('pm_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pm_review_isDeleted', 0)
                        ->where(function($q) {
                            $q->whereNull('pm_review_sudah_dibaca')
                              ->orWhere('pm_review_sudah_dibaca', 0);
                        })
                        ->count(),
                    'sudah_dibaca' => PengaduanMasyarakatModel::where('pm_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pm_review_isDeleted', 0)
                        ->where('pm_review_sudah_dibaca', 1)
                        ->count(),
                ];

                // Summary WBS
                $summary['whistle_blowing_system'] = [
                    'total' => WBSModel::where('wbs_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('wbs_review_isDeleted', 0)
                        ->count(),
                    'belum_dibaca' => WBSModel::where('wbs_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('wbs_review_isDeleted', 0)
                        ->where(function($q) {
                            $q->whereNull('wbs_review_sudah_dibaca')
                              ->orWhere('wbs_review_sudah_dibaca', 0);
                        })
                        ->count(),
                    'sudah_dibaca' => WBSModel::where('wbs_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('wbs_review_isDeleted', 0)
                        ->where('wbs_review_sudah_dibaca', 1)
                        ->count(),
                ];

                // Summary Permohonan Perawatan
                $summary['permohonan_perawatan'] = [
                    'total' => PermohonanPerawatanModel::where('pp_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pp_review_isDeleted', 0)
                        ->count(),
                    'belum_dibaca' => PermohonanPerawatanModel::where('pp_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pp_review_isDeleted', 0)
                        ->where(function($q) {
                            $q->whereNull('pp_review_sudah_dibaca')
                              ->orWhere('pp_review_sudah_dibaca', 0);
                        })
                        ->count(),
                    'sudah_dibaca' => PermohonanPerawatanModel::where('pp_status', 'Verifikasi')
                        ->where('isDeleted', 0)
                        ->where('pp_review_isDeleted', 0)
                        ->where('pp_review_sudah_dibaca', 1)
                        ->count(),
                ];

                return $summary;
            },
            'summary review pengajuan',
            self::ACTION_GET
        );
    }
}