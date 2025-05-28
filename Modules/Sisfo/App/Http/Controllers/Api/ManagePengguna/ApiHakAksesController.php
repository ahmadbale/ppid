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
            'hak akses',
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
    
                // Validasi data dasar menggunakan konstanta dari BaseApiController
                if (empty($hakAksesKode) || empty($hakAksesNama)) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Data hak_akses_kode dan hak_akses_nama harus diisi',
                        self::HTTP_UNPROCESSABLE_ENTITY
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
                
                // Cek hasil dengan pola yang konsisten
                if (!$result || isset($result['error']) || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal membuat hak akses';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $errors
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'hak akses',
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
                    // Format request tidak valid - gunakan konstanta dari BaseApiController
                    return $this->errorResponse(
                        self::INVALID_REQUEST_FORMAT,
                        'Harap sertakan hak_akses_kode dan hak_akses_nama',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                // Validasi data
                HakAksesModel::validasiData($request);

                // Proses update data
                $result = HakAksesModel::updateData($request, $id);

                // Cek hasil update dengan pola yang konsisten
                if (!$result || (is_array($result) && isset($result['success']) && $result['success'] === false)) {
                    $errors = isset($result['errors']) ? $result['errors'] : null;
                    $message = isset($result['message']) ? $result['message'] : 'Gagal memperbarui hak akses';
                    
                    return $this->errorResponse(
                        $message,
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        $errors
                    );
                }

                // Return data hasil update atau null jika tidak ada
                return $result['data'] ?? $result;
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
                
                // Cek hasil dengan pola yang konsisten
                if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                    return $this->errorResponse(
                        $result['message'] ?? 'Gagal menghapus hak akses',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                
                return $result['data'] ?? $result;
            },
            'hak akses',
            self::ACTION_DELETE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                return HakAksesModel::detailData($id);
            },
            'hak akses',
            self::ACTION_GET
        );
    }
}