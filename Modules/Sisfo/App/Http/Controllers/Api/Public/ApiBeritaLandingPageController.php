<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

use Modules\Sisfo\App\Models\Website\Publikasi\Berita\BeritaDinamisModel;




class ApiBeritaLandingPageController extends BaseApiController
{
    public function getDataBeritaLandingPage()
    {
        return $this->execute(
            function() {
                $beritaLandingPage = BeritaDinamisModel::getDataBeritaLandingPage();
                return $beritaLandingPage;
            },
            'Berita LandingPage'
        );
    }
}