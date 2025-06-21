<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;

class ApiVerifPIController extends BaseApiController
{
    /**
     * Mendapatkan daftar verifikasi permohonan informasi
     */
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status');

                $query = PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                    ->where('isDeleted', 0)
                    ->where('pi_verifikasi_isDeleted', 0);

                if ($status) {
                    $query->where('pi_status', $status);
                }

                $permohonanInformasi = $query->orderBy('created_at', 'desc')
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
            'daftar verifikasi permohonan informasi',
            self::ACTION_GET
        );
    }

    /**
     * Mendapatkan modal approval 
     */
    public function getApproveModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                    ->findOrFail($id);
            },
            'detail permohonan informasi untuk approval',
            self::ACTION_GET
        );
    }

    /**
     * Mendapatkan modal decline 
     */
    public function getDeclineModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PermohonanInformasiModel::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
                    ->findOrFail($id);
            },
            'detail permohonan informasi untuk penolakan',
            self::ACTION_GET
        );
    }

    /**
     * Menyetujui permohonan informasi
     */
    public function setujuiPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                return $permohonanInformasi->validasiDanSetujuiPermohonan();
            },
            'persetujuan permohonan informasi',
            self::ACTION_UPDATE
        );
    }

    /**
     * Menolak permohonan informasi 
     */

public function tolakPermohonan(Request $request, $id)
{
    return $this->executeWithAuthAndValidation(
        function ($user) use ($request, $id) {
            // Validasi input request
            $request->validate([
                'alasan_penolakan' => 'required|string|max:500'
            ], [
                'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
                'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter'
            ]);
            
            // Cari data permohonan informasi
            $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
            
            // Jalankan method model untuk tolak permohonan
            return $permohonanInformasi->validasiDanTolakPermohonan($request->alasan_penolakan);
        },
        'penolakan permohonan informasi',
        self::ACTION_UPDATE
    );
}
    /**
     * Menandai sebagai sudah dibaca - sama dengan web tandaiDibaca
     */
    public function tandaiDibaca($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                return $permohonanInformasi->validasiDanTandaiDibaca();
            },
            'penandaan sebagai dibaca permohonan informasi',
            self::ACTION_UPDATE
        );
    }

    /**
     * Menghapus permohonan informasi - sama dengan web hapusPermohonan
     */
    public function hapusPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanInformasi = PermohonanInformasiModel::findOrFail($id);
                return $permohonanInformasi->validasiDanHapusPermohonan();
            },
            'permohonan informasi',
            self::ACTION_DELETE
        );
    }
}