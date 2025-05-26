<?php 
namespace Modules\Sisfo\App\Http\Controllers\Api\Public;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\LayananInformasi\LIDinamisModel;

class ApiLIDinamisController extends BaseApiController
{
    public function getDataLayananInformasiDinamis()
    {
        return $this->executeWithSystemAuth(
            function() {
                $getDataLayananInformasiDinamis = LIDinamisModel::getDataLayananInformasiDinamis();
                return $getDataLayananInformasiDinamis;
            },
            'laman Layanan Informasi Dinamis'
        );
    }
}