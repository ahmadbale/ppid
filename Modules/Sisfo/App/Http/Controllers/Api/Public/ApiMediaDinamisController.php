<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;


use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\LandingPage\MediaDinamis\MediaDinamisModel;




class ApiMediaDinamisController extends BaseApiController
{
    public function getDataHeroSection()
    {
        return $this->executeWithSystemAuth(
            function() {
                $heroSection = MediaDinamisModel::getDataHeroSection();
                return $heroSection;
            },
            'Laman Hero Section'
        );
    }
    public function getDataDokumentasi()
    {
        return $this->executeWithSystemAuth(
            function() {
                $dokumentasi = MediaDinamisModel::getDataDokumentasi();
                return $dokumentasi;
            },
            'Laman Dokumentasi PPID'
        );
    }
    public function getDataMediaInformasiPublik(Request $request)
{
    return $this->executeWithSystemAuth(
        function() use ($request) {
            // Ambil parameter showAll dari query string, default false
            $showAll = $request->query('showAll', false);
            
            // Konversi string 'true' menjadi boolean true jika diperlukan
            if (is_string($showAll)) {
                $showAll = ($showAll === 'true' || $showAll === '1');
            }
            
            $mediainformasi = MediaDinamisModel::getDataMediaInformasiPublik($showAll);
            return $mediainformasi;
        },
        'Laman Media Informasi Publik'
    );
}
}