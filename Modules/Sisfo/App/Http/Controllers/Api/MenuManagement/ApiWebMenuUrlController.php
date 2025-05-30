<?php 
namespace Modules\Sisfo\App\Http\Controllers\Api\MenuManagement;
use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\ApplicationModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Illuminate\Validation\ValidationException;

class ApiWebMenuUrlController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                return WebMenuUrlModel::selectData($perPage, $search);
            },
            'menu URL',
            self::ACTION_GET
        );
    }

    public function createData(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                try {
                    // Ambil data dari request
                    $fkMApplication = $request->input('fk_m_application') ?? $request->json('fk_m_application');
                    $wmuNama = $request->input('wmu_nama') ?? $request->json('wmu_nama');
                    $wmuKeterangan = $request->input('wmu_keterangan') ?? $request->json('wmu_keterangan');

                    // Validasi data dasar
                    if (empty($fkMApplication)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Aplikasi (fk_m_application) harus dipilih',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    if (empty($wmuNama)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Nama URL menu (wmu_nama) harus diisi',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    // Struktur ulang data sesuai dengan format yang diharapkan model
                    $request->merge([
                        'web_menu_url' => [
                            'fk_m_application' => $fkMApplication,
                            'wmu_nama' => $wmuNama,
                            'wmu_keterangan' => $wmuKeterangan
                        ]
                    ]);

                    // Validasi data menggunakan model
                    WebMenuUrlModel::validasiData($request);

                    // Buat data baru
                    $result = WebMenuUrlModel::createData($request);
                    
                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::SERVER_ERROR,
                            $result['message'] ?? 'Gagal membuat URL menu',
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
                        'Terjadi kesalahan saat membuat URL menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu URL',
            self::ACTION_CREATE
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthentication(
            function () use ($request, $id) {
                try {
                    // Cek apakah URL menu ada
                    $existingData = WebMenuUrlModel::find($id);
                    
                    if (!$existingData) {
                        return $this->errorResponse(
                            self::RESOURCE_NOT_FOUND,
                            'URL menu tidak ditemukan',
                            self::HTTP_NOT_FOUND
                        );
                    }

                    // Ambil data dari request untuk pembaruan parsial
                    $fkMApplication = $request->input('fk_m_application') ?? $existingData->fk_m_application;
                    $wmuNama = $request->input('wmu_nama') ?? $existingData->wmu_nama;
                    $wmuKeterangan = $request->input('wmu_keterangan') ?? $existingData->wmu_keterangan;

                    // Validasi data dasar
                    if (empty($fkMApplication)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Aplikasi (fk_m_application) harus dipilih',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    if (empty($wmuNama)) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Nama URL menu (wmu_nama) harus diisi',
                            self::HTTP_UNPROCESSABLE_ENTITY
                        );
                    }

                    // Struktur ulang data sesuai dengan format yang diharapkan model
                    $request->merge([
                        'web_menu_url' => [
                            'fk_m_application' => $fkMApplication,
                            'wmu_nama' => $wmuNama,
                            'wmu_keterangan' => $wmuKeterangan
                        ]
                    ]);

                    // Validasi data menggunakan model
                    WebMenuUrlModel::validasiData($request);

                    // Update data
                    $result = WebMenuUrlModel::updateData($request, $id);

                    if (!$result['success']) {
                        return $this->errorResponse(
                            self::SERVER_ERROR,
                            $result['message'] ?? 'Gagal memperbarui URL menu',
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
                        'Terjadi kesalahan saat memperbarui URL menu: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'menu URL',
            self::ACTION_UPDATE
        );
    }

  public function detailData($id)
     {
          return $this->executeWithAuthentication(
               function () use ($id) {
                    // Ambil detail data URL menu
                    $webMenuUrl = WebMenuUrlModel::detailData($id);
                    
                    if (!$webMenuUrl) {
                         return $this->errorResponse(
                         self::RESOURCE_NOT_FOUND,
                         'URL menu tidak ditemukan',
                         self::HTTP_NOT_FOUND
                         );
                    }
     
                    return $webMenuUrl;
               },
               'menu URL',
               self::ACTION_GET
          );
     }
     public function deleteData($id)
     {
         return $this->executeWithAuthentication(
             function () use ($id) {
                 try {
                     $result = WebMenuUrlModel::deleteData($id);
                     
                     if (!$result['success']) {
                         return $this->errorResponse(
                             self::SERVER_ERROR,
                             $result['message'] ?? 'Gagal menghapus menu global',
                             self::HTTP_INTERNAL_SERVER_ERROR
                         );
                     }
 
                     return true;
                 } catch (\Exception $e) {
                     return $this->errorResponse(
                         self::SERVER_ERROR,
                         'Terjadi kesalahan saat menghapus menu global: ' . $e->getMessage(),
                         self::HTTP_INTERNAL_SERVER_ERROR
                     );
                 }
             },
             'menu url',
             self::ACTION_DELETE
         );
     }
 
}