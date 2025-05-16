<?php 
namespace Modules\Sisfo\App\Http\Controllers\Api\EForm;
use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;

class ApiPengaduanMasyarakatController extends BaseApiController
{
    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request) {
                try {
                    // Validasi data menggunakan model
                    PengaduanMasyarakatModel::validasiData($request);
                    
                    // Buat data baru
                    $result = PengaduanMasyarakatModel::createData($request);

                    // Jika result kosong atau error
                    if (!$result || isset($result['error'])) {
                        return $this->errorResponse(
                            self::AUTH_INVALID_INPUT,
                            'Gagal membuat pengaduan masyarakat',
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
            'pengaduan masyarakat',
            self::ACTION_CREATE
        );
    }
}