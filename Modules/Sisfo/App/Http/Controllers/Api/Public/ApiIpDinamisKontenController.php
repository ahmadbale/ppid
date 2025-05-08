<?php
namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\KontenDinamis\IpDinamisKontenModel;


class ApiIpDinamisKontenController extends BaseApiController
{
     public function getDataIPDaftarInformasi()
     {
         return $this->execute(
             function() {
                 $getDataIPDaftarInformasi = IpDinamisKontenModel::getDataIPDaftarInformasi();
                 return $getDataIPDaftarInformasi;
             },
             'laman IP Daftar Informasi'
         );
     }
 }