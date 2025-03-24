<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

use Modules\Sisfo\App\Models\Website\Publikasi\Pengumuman\PengumumanDinamisModel;



class ApiPengumumanLandingPageController extends BaseApiController
{
    public function getDataPengumumanLandingPage()
    {
        return $this->execute(
            function() {
                $pengumumanLandingPage = PengumumanDinamisModel::getDataPengumumanLandingPage();
                return $pengumumanLandingPage;
            },
            'Pengumuman LandingPage'
        );
    }
}