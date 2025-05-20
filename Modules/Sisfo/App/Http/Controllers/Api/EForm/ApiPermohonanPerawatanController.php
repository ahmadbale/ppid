<?php
namespace Modules\Sisfo\App\Http\Controllers\Api\EForm;     
use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;

class ApiPermohonanPerawatanController extends BaseApiController
{
    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request) {
                try {
                    // Validasi data menggunakan model
                    PermohonanPerawatanModel::validasiData($request);
                    
                    // Buat data baru
                    $result = PermohonanPerawatanModel::createData($request);

                    // Jika result kosong atau error
                    if (!$result || isset($result['error'])) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Gagal membuat permohonan perawatan',
                            422
                        );
                    }
                    $data = $result['data'] ?? $result;
                    // Return data sesuai format BaseApiController
                    return $data;

                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED,
                        $e->getMessage(),
                        422
                    );
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        self::SERVER_ERROR,
                        $e->getMessage(),
                        500
                    );
                }
            },
            'permohonan perawatan',
            self::ACTION_CREATE
        );
    }
}