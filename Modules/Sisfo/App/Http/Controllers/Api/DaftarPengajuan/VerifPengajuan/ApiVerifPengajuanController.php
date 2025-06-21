<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;

class ApiVerifPengajuanController extends BaseApiController
{
    /**
     * Mendapatkan dashboard verifikasi pengajuan
     */
    public function index()
    {
        return $this->executeWithAuthentication(
            function ($user) {
                // Ambil jumlah verifikasi dari masing-masing model
                $jumlahVerifikasi = [
                    'permohonanInformasi' => PermohonanInformasiModel::hitungJumlahVerifikasi(),
                    'pernyataanKeberatan' => PernyataanKeberatanModel::hitungJumlahVerifikasi(),
                    'pengaduanMasyarakat' => PengaduanMasyarakatModel::hitungJumlahVerifikasi(),
                    'wbs' => WBSModel::hitungJumlahVerifikasi(),
                    'permohonanPerawatan' => PermohonanPerawatanModel::hitungJumlahVerifikasi()
                ];

                return [
                    'jumlahDaftarVerifPermohonanInformasi' => $jumlahVerifikasi['permohonanInformasi'],
                    'jumlahDaftarVerifPernyataanKeberatan' => $jumlahVerifikasi['pernyataanKeberatan'],
                    'jumlahDaftarVerifPengaduanMasyarakat' => $jumlahVerifikasi['pengaduanMasyarakat'],
                    'jumlahDaftarVerifWBS' => $jumlahVerifikasi['wbs'],
                    'jumlahDaftarVerifPermohonanPerawatan' => $jumlahVerifikasi['permohonanPerawatan'],
                    'total' => array_sum($jumlahVerifikasi)
                ];
            },
            'dashboard verifikasi pengajuan',
            self::ACTION_GET
        );
    }

    /**
     * Mendapatkan ringkasan status verifikasi
     */
    public function getSummary()
    {
        return $this->executeWithAuthentication(
            function ($user) {
                $summary = [
                    'permohonanInformasi' => [
                        'total' => PermohonanInformasiModel::where('isDeleted', 0)->where('pi_verifikasi_isDeleted', 0)->count(),
                        'menunggu' => PermohonanInformasiModel::hitungJumlahVerifikasi(),
                        'disetujui' => PermohonanInformasiModel::where('pi_status', 'Verifikasi')->where('isDeleted', 0)->count(),
                        'ditolak' => PermohonanInformasiModel::where('pi_status', 'Ditolak')->where('isDeleted', 0)->count()
                    ],
                    'pernyataanKeberatan' => [
                        'total' => PernyataanKeberatanModel::where('isDeleted', 0)->where('pk_verifikasi_isDeleted', 0)->count(),
                        'menunggu' => PernyataanKeberatanModel::hitungJumlahVerifikasi(),
                        'disetujui' => PernyataanKeberatanModel::where('pk_status', 'Verifikasi')->where('isDeleted', 0)->count(),
                        'ditolak' => PernyataanKeberatanModel::where('pk_status', 'Ditolak')->where('isDeleted', 0)->count()
                    ],
                    'pengaduanMasyarakat' => [
                        'total' => PengaduanMasyarakatModel::where('isDeleted', 0)->where('pm_verifikasi_isDeleted', 0)->count(),
                        'menunggu' => PengaduanMasyarakatModel::hitungJumlahVerifikasi(),
                        'disetujui' => PengaduanMasyarakatModel::where('pm_status', 'Verifikasi')->where('isDeleted', 0)->count(),
                        'ditolak' => PengaduanMasyarakatModel::where('pm_status', 'Ditolak')->where('isDeleted', 0)->count()
                    ],
                    'wbs' => [
                        'total' => WBSModel::where('isDeleted', 0)->where('wbs_verifikasi_isDeleted', 0)->count(),
                        'menunggu' => WBSModel::hitungJumlahVerifikasi(),
                        'disetujui' => WBSModel::where('wbs_status', 'Verifikasi')->where('isDeleted', 0)->count(),
                        'ditolak' => WBSModel::where('wbs_status', 'Ditolak')->where('isDeleted', 0)->count()
                    ],
                    'permohonanPerawatan' => [
                        'total' => PermohonanPerawatanModel::where('isDeleted', 0)->where('pp_verifikasi_isDeleted', 0)->count(),
                        'menunggu' => PermohonanPerawatanModel::hitungJumlahVerifikasi(),
                        'disetujui' => PermohonanPerawatanModel::where('pp_status', 'Verifikasi')->where('isDeleted', 0)->count(),
                        'ditolak' => PermohonanPerawatanModel::where('pp_status', 'Ditolak')->where('isDeleted', 0)->count()
                    ]
                ];

                return $summary;
            },
            'ringkasan verifikasi',
            self::ACTION_GET
        );
    }
}