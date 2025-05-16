<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiHakAksesController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                return HakAksesModel::selectData($perPage, $search);
            },
            'level',
            self::ACTION_GET
        );
    }
    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request) {
                // Ambil data dari request, baik JSON maupun form-data
                $hakAksesKode = $request->input('hak_akses_kode') ?? $request->json('hak_akses_kode');
                $hakAksesNama = $request->input('hak_akses_nama') ?? $request->json('hak_akses_nama');
    
                // Validasi data dasar
                if (empty($hakAksesKode) || empty($hakAksesNama)) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Data hak_akses_kode dan hak_akses_nama harus diisi',
                        422
                    );
                }
    
                // Struktur ulang data
                $request->merge([
                    'm_hak_akses' => [
                        'hak_akses_kode' => $hakAksesKode,
                        'hak_akses_nama' => $hakAksesNama
                    ]
                ]);
    
                // Validasi data menggunakan model
                HakAksesModel::validasiData($request);
                
                // Buat data baru
                $result = HakAksesModel::createData($request);
                return $result['data'];
            },
            'hak akses', // Nama resource yang lebih spesifik
            self::ACTION_CREATE
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                // Restrukturisasi request agar sesuai dengan ekspektasi model
                if ($request->has('hak_akses_kode') && $request->has('hak_akses_nama')) {
                    // Format API langsung
                    $request->merge([
                        'm_hak_akses' => [
                            'hak_akses_kode' => $request->input('hak_akses_kode'),
                            'hak_akses_nama' => $request->input('hak_akses_nama')
                        ]
                    ]);
                } elseif (!$request->has('m_hak_akses')) {
                    // Format request tidak valid
                    return $this->errorResponse(
                        self::INVALID_REQUEST_FORMAT . '. Harap sertakan hak_akses_kode dan hak_akses_nama',
                        null,
                        422
                    );
                }

                // Validasi data
                HakAksesModel::validasiData($request);

                // Proses update data
                $result = HakAksesModel::updateData($request, $id);

                // Return data hasil update atau null jika tidak ada
                return $result['data'] ?? null;
            },
            'hak akses',
            self::ACTION_UPDATE
        );
    }


    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                $result = HakAksesModel::deleteData($id);
                return $result['data'];
            },
            'level',
            self::ACTION_DELETE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                return HakAksesModel::detailData($id);
            },
            'level',
            self::ACTION_GET
        );
    }
}