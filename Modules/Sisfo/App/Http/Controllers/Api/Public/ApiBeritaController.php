<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Illuminate\Http\Request;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\Publikasi\Berita\BeritaDinamisModel;




class ApiBeritaController extends BaseApiController
{
    public function getDataBerita()
    {
        return $this->execute(
            function() {
                $berita = BeritaDinamisModel::getDataBerita();
                return $berita;
            },
            'Menu Berita'
        );
    }
}