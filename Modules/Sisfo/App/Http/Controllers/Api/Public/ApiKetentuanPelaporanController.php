<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\KategoriForm\KategoriFormModel;

class ApiKetentuanPelaporanController extends BaseApiController
{
     public function getDataKententuanPelaporan()
     {
         return $this->execute(
             function() {
                 $getDataKetentuanPelaporan = KategoriFormModel::getDataKententuanPelaporan();
                 return $getDataKetentuanPelaporan;
             },
             'laman Data Ketentuan Pelaporan'
         );
     }
 }