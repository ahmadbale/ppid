<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\MenuManagement;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Illuminate\Validation\ValidationException;

class ApiWebMenuGlobalController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                return WebMenuGlobalModel::selectData($perPage, $search);
            },
            'menu global',
            self::ACTION_GET
        );
    }

    public function createData(Request $request)
    {
        return $this->executeWithAuthentication( // ✅ Gunakan method yang ada
            function () use ($request) {
                try {
                    // Ambil data dari request
                    $wmgNamaDefault = $request->input('wmg_nama_default') ?? $request->json('wmg_nama_default');
                    $wmgKategoriMenu = $request->input('wmg_kategori_menu') ?? $request->json('wmg_kategori_menu') ?? 'Menu Biasa';
                    $fkWebMenuUrl = $request->input('fk_web_menu_url') ?? $request->json('fk_web_menu_url');
                    $wmgParentId = $request->input('wmg_parent_id') ?? $request->json('wmg_parent_id');
                    $wmgStatusMenu = $request->input('wmg_status_menu') ?? $request->json('wmg_status_menu') ?? 'aktif';

                    // Validasi data dasar
                    if (empty($wmgNamaDefault) && $wmgNamaDefault !== '0') {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Nama default menu (wmg_nama_default) harus diisi',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    // Validasi berdasarkan kategori menu
                    if ($wmgKategoriMenu === 'Sub Menu' && empty($wmgParentId)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Sub menu harus memiliki menu induk (wmg_parent_id)',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    if ($wmgKategoriMenu !== 'Group Menu' && empty($fkWebMenuUrl)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Menu URL (fk_web_menu_url) harus diisi untuk ' . $wmgKategoriMenu,
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    // Struktur ulang data sesuai dengan format yang diharapkan model
                    $request->merge([
                        'web_menu_global' => [
                            'wmg_nama_default' => $wmgNamaDefault,
                            'wmg_kategori_menu' => $wmgKategoriMenu,
                            'fk_web_menu_url' => ($wmgKategoriMenu === 'Group Menu') ? null : 
                                (($fkWebMenuUrl === '' || $fkWebMenuUrl === 'null') ? null : $fkWebMenuUrl),
                            'wmg_parent_id' => ($wmgKategoriMenu === 'Sub Menu') ? $wmgParentId : null,
                            'wmg_status_menu' => $wmgStatusMenu
                        ]
                    ]);

                    // Validasi data menggunakan model
                    WebMenuGlobalModel::validasiData($request);

                    // Buat data baru
                    $result = WebMenuGlobalModel::createData($request);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::SERVER_ERROR,
                            $result['message'] ?? 'Gagal membuat menu global',
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }

                    return $result['data'];
                } catch (ValidationException $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        $e->getMessage(),
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat membuat menu global: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu global',
            self::ACTION_CREATE
        );
    }

    public function updateData(Request $request, $id)
{
    return $this->executeWithAuthentication(
        function () use ($request, $id) {
            try {
                // 1. Ambil data existing dari database
                $existingData = WebMenuGlobalModel::find($id);
                
                if (!$existingData) {
                    return $this->errorResponse(
                        self::RESOURCE_NOT_FOUND,
                        'Menu global tidak ditemukan',
                        self::HTTP_NOT_FOUND
                    );
                }

                // 2. Merge dengan data existing (untuk partial update)
                $wmgNamaDefault = $request->input('wmg_nama_default') ?? $existingData->wmg_nama_default;
                $wmgKategoriMenu = $request->input('wmg_kategori_menu') ?? $existingData->wmg_kategori_menu;
                $fkWebMenuUrl = $request->input('fk_web_menu_url') ?? $existingData->fk_web_menu_url;
                $wmgParentId = $request->input('wmg_parent_id') ?? $existingData->wmg_parent_id;
                $wmgStatusMenu = $request->input('wmg_status_menu') ?? $existingData->wmg_status_menu;

                // 3. Validasi data dasar
                if (empty($wmgNamaDefault) && $wmgNamaDefault !== '0') {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Nama default menu (wmg_nama_default) harus diisi',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                // 4. Validasi berdasarkan kategori menu
                if ($wmgKategoriMenu === 'Sub Menu' && empty($wmgParentId)) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Sub menu harus memiliki menu induk (wmg_parent_id)',
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                if ($wmgKategoriMenu !== 'Group Menu' && empty($fkWebMenuUrl)) {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Menu URL (fk_web_menu_url) harus diisi untuk ' . $wmgKategoriMenu,
                        self::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                // 5. Struktur ulang data dengan data lengkap
                $request->merge([
                    'web_menu_global' => [
                        'wmg_nama_default' => $wmgNamaDefault,
                        'wmg_kategori_menu' => $wmgKategoriMenu,
                        'fk_web_menu_url' => ($wmgKategoriMenu === 'Group Menu') ? null : 
                            (($fkWebMenuUrl === '' || $fkWebMenuUrl === 'null') ? null : $fkWebMenuUrl),
                        'wmg_parent_id' => ($wmgKategoriMenu === 'Sub Menu') ? $wmgParentId : null,
                        'wmg_status_menu' => $wmgStatusMenu
                    ]
                ]);

                // 6. Validasi menggunakan model
                WebMenuGlobalModel::validasiData($request);

                // 7. Update data
                $result = WebMenuGlobalModel::updateData($request, $id);

                if (!$result['success']) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $result['message'] ?? 'Gagal memperbarui menu global',
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                return $result['data'];
            } catch (ValidationException $e) {
                return $this->errorResponse(
                    self::VALIDATION_FAILED,
                    $e->getMessage(),
                    self::HTTP_UNPROCESSABLE_ENTITY
                );
            } catch (\Exception $e) {
                return $this->errorResponse(
                    self::SERVER_ERROR,
                    'Terjadi kesalahan saat memperbarui menu global: ' . $e->getMessage(),
                    self::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        },
        'menu global',
        self::ACTION_UPDATE
    );
}
    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    $result = WebMenuGlobalModel::deleteData($id);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::SERVER_ERROR,
                            $result['message'] ?? 'Gagal menghapus menu global',
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }

                    return true; // Return boolean untuk delete operation
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        'Terjadi kesalahan saat menghapus menu global: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu global',
            self::ACTION_DELETE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                try {
                    return WebMenuGlobalModel::detailData($id);
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::RESOURCE_NOT_FOUND,
                        'Menu global tidak ditemukan',
                        self::HTTP_NOT_FOUND
                    );
                }
            },
            'menu global',
            self::ACTION_GET
        );
    }

    public function getMenuUrl()
    {
        return $this->executeWithAuthentication(
            function () {
                $menuUrls = WebMenuUrlModel::with('application')->where('isDeleted', 0)->get();
                return $menuUrls;
            },
            'menu URL',
            self::ACTION_GET
        );
    }
}