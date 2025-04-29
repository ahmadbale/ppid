<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;

class ApiPermohonanInformasiController extends BaseApiController
{
     use TraitsController;
    public function createPermohonanInformasi(Request $request)
    {
        return $this->executeWithAutCreate(
            function() use ($request) {
                // Validasi data terlebih dahulu
                PermohonanInformasiModel::validasiData($request);
                
                // Jika validasi berhasil, buat data permohonan
                $result = PermohonanInformasiModel::createData($request);
                
                // Tambahkan informasi tentang jenis permohonan di respons
                $kategoriPemohon = $request->input('t_permohonan_informasi.pi_kategori_pemohon');
                $result['kategori_pemohon'] = $kategoriPemohon;
                
                return $result;
            },
            'Laman Permohonan Informasi',
        );
    }
}