<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;

class ApiVerifPKController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status');

                $query = PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                    ->where('isDeleted', 0)
                    ->where('pk_verifikasi_isDeleted', 0);

                if ($status) {
                    $query->where('pk_status', $status);
                }

                $pernyataanKeberatan = $query->orderBy('created_at', 'desc')
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
            'daftar verifikasi pernyataan keberatan',
            self::ACTION_GET
        );
    }

    public function getApproveModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                    ->findOrFail($id);
            },
            'detail pernyataan keberatan untuk approval',
            self::ACTION_GET
        );
    }

    public function getDeclineModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PernyataanKeberatanModel::with(['PkDiriSendiri', 'PkOrangLain'])
                    ->findOrFail($id);
            },
            'detail pernyataan keberatan untuk penolakan',
            self::ACTION_GET
        );
    }

    public function setujuiPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                return $pernyataanKeberatan->validasiDanSetujuiPermohonan();
            },
            'persetujuan pernyataan keberatan',
            self::ACTION_UPDATE
        );
    }

    public function tolakPermohonan(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                // Validasi input
                $request->validate([
                    'alasan_penolakan' => 'required|string|max:255'
                ], [
                    'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
                    'alasan_penolakan.max' => 'Alasan penolakan maksimal 255 karakter'
                ]);

                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                return $pernyataanKeberatan->validasiDanTolakPermohonan($request->alasan_penolakan);
            },
            'penolakan pernyataan keberatan',
            self::ACTION_UPDATE
        );
    }

    public function tandaiDibaca($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                return $pernyataanKeberatan->validasiDanTandaiDibaca();
            },
            'penandaan sebagai dibaca pernyataan keberatan',
            self::ACTION_UPDATE
        );
    }

    public function hapusPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pernyataanKeberatan = PernyataanKeberatanModel::findOrFail($id);
                return $pernyataanKeberatan->validasiDanHapusPermohonan();
            },
            'pernyataan keberatan',
            self::ACTION_DELETE
        );
    }
}