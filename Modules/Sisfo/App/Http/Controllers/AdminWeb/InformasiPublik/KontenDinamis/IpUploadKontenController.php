<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\KontenDinamis;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\KontenDinamis\IpUploadKontenModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\KontenDinamis\IpDinamisKontenModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class IpUploadKontenController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Detail Upload Konten';
    public $pagename = 'AdminWeb/InformasiPublik/IpUploadDetailKonten';


    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Detail Upload Konten',
            'list' => ['Home', 'Pengaturan Detail Upload Konten']
        ];

        $page = (object) [
            'title' => 'Daftar Detail Upload Konten'
        ];

        $activeMenu = 'ipupload-detail-konten';
        $ipDinamisKonten = IpDinamisKontenModel::where('isDeleted', 0)->get();
        
        $ipUploadKonten = IpUploadKontenModel::selectData(10, $search);

        return view("sisfo::AdminWeb/InformasiPublik/IpUploadDetailKonten.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'dinamisKonten' => $ipDinamisKonten,
            'ipUploadKonten' => $ipUploadKonten,
            'search' => $search
        ]);
    }


    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $ipUploadKonten = IpUploadKontenModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::AdminWeb/InformasiPublik/IpUploadDetailKonten.data', compact('ipUploadKonten', 'search'))->render();
        }
        
        return redirect()->route('ipupload-detail-konten.index');
    }

    public function addData()
    {
        $ipDinamisKonten = IpDinamisKontenModel::where('isDeleted', 0)->get();
        return view("sisfo::AdminWeb/InformasiPublik/IpUploadDetailKonten.create", compact('ipDinamisKonten'));
    }

    public function createData(Request $request)
    {
        try {
            IpUploadKontenModel::validasiData($request);
            $result = IpUploadKontenModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Detail upload konten berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat detail upload konten');
        }
    }

    public function editData($id)
    {
        $ipUploadKonten = IpUploadKontenModel::detailData($id);
        $ipDinamisKonten = IpDinamisKontenModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb/InformasiPublik/IpUploadDetailKonten.update", [
            'ipUploadKonten' => $ipUploadKonten,
            'ipDinamisKonten' => $ipDinamisKonten
        ]);
        
    }

    
    public function updateData(Request $request, $id)
    {
        try {
            IpUploadKontenModel::validasiData($request);
            $result = IpUploadKontenModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail upload konten berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui detail upload konten');
        }
    }


    public function detailData($id)
    {
        $ipUploadKonten = IpUploadKontenModel::detailData($id);
        
        return view("sisfo::AdminWeb/InformasiPublik/IpUploadDetailKonten.detail", [
            'ipUploadKonten' => $ipUploadKonten,
            'title' => 'Detail Upload Konten'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $ipUploadKonten = IpUploadKontenModel::detailData($id);
            
            return view("sisfo::AdminWeb/InformasiPublik/IpUploadDetailKonten.delete", [
                'ipUploadKonten' => $ipUploadKonten
            ]);
        }
        
        try {
            $result = IpUploadKontenModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail upload konten berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus detail upload konten');
        }
    }
}