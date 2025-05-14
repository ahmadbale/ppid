<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\KategoriForm\KategoriFormModel;

class ApiKetentuanPelaporanController extends BaseApiController
{
     public function getDataKetentuanPelaporan()
     {
         return $this->executeWithSystemAuth(
             function() {
                 $getDataKetentuanPelaporan = KategoriFormModel::getDataKetentuanPelaporan();
                 return $getDataKetentuanPelaporan;
             },
             'laman Data Ketentuan Pelaporan'
         );
     }
 }