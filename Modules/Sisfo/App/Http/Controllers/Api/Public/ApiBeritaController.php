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
            'Laman Berita'
        );
    }
    public function getDetailBeritaById($slug,$berita_id)
    {
        return $this->execute(
            function () use ($slug,$berita_id) {
                $berita = BeritaDinamisModel::getDetailBeritaById($slug,$berita_id);
                return $berita;
            },
            'Laman Berita'
        );
    }
}