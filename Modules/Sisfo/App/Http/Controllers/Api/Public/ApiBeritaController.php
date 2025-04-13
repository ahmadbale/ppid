<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\Publikasi\Berita\BeritaDinamisModel;

class ApiBeritaController extends BaseApiController
{
    public function getDataBerita()
    {
        return $this->execute(
            function () {
                $berita = BeritaDinamisModel::getDataBerita();
                return $berita;
            },
            'Menu Berita'
        );
    }
    public function getDetailBeritaById($berita_id)
    {
        return $this->execute(
            function () use ($berita_id) {
                $berita = BeritaDinamisModel::getDetailBeritaById($berita_id);
                return $berita;
            },
            'Detail Berita'
        );
    }
}