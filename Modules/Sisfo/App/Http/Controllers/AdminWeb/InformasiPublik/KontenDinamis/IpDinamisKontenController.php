<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\KontenDinamis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\KontenDinamis\IpDinamisKontenModel;


class IpDinamisKontenController extends Controller
{
     use TraitsController;
     
     public $breadcrumb = 'Pengaturan IpDinamis Konten';
     public $pagename = 'AdminWeb/InformasiPublik/IpDinamisKonten';

     public function index(Request $request)
     {
          $search = $request->query('search', '');

          $breadcrumb = (object)[
           'title'=> 'Pengaturan IpDinamis Konten',
           'list'=> ['Home','Pengaturan IpDinamis Konten']
          ];
          $page = (object) [
              'title' =>'Daftar IpDinamis Konten'
          ];
          
          $activeMenu = 'ipdinamis-konten';
          
          $ipDinamisKonten = IpDinamisKontenModel::selectData(10, $search);

          
        return view('sisfo::AdminWeb/InformasiPublik/IpDinamisKonten.index', [
          'breadcrumb' => $breadcrumb,
          'page' => $page,
          'activeMenu' => $activeMenu,
          'ipDinamisKonten' => $ipDinamisKonten,
          'search' => $search
      ]);
          
     }
     public function getData(Request $request)
     {
         $search = $request->query('search', '');
         $ipDinamisKonten = IpDinamisKontenModel::selectData(10, $search);
         
         if ($request->ajax()) {
             return view('sisfo::AdminWeb/InformasiPublik/IpDinamisKonten.data', compact('ipDinamisKonten', 'search'))->render();
        }
         
         return redirect()->route('ipdinamis-konten.index');
     }
     public function addData()
     {
          return view("sisfo::AdminWeb/InformasiPublik/IpDinamisKonten.create");
     }
     public function createData(Request $request)
    {
        try {
            IpDinamisKontenModel::validasiData($request);
            $result = IpDinamisKontenModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data IpDinamis Konten berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat data IpDinamis Konten');
        }
    }
    public function editData($id)
    {
        $ipDinamisKonten = IpDinamisKontenModel::detailData($id);

        return view('sisfo::AdminWeb/InformasiPublik/IpDinamisKonten.update', [
            'ipDinamisKonten' => $ipDinamisKonten
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            IpDinamisKontenModel::validasiData($request);
            $result = IpDinamisKontenModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data IpDinamis Konten berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui data IpDinamis Konten');
        }
    }
    public function detailData($id)
    {
        $ipDinamisKonten = IpDinamisKontenModel::detailData($id);
        
        return view('sisfo::AdminWeb/InformasiPublik/IpDinamisKonten.detail', [
            'ipDinamisKonten' => $ipDinamisKonten,
            'title' => 'Detail IpDinamis Konten'
        ]);
    }
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $ipDinamisKonten = IpDinamisKontenModel::detailData($id);
            
            return view('sisfo::AdminWeb/InformasiPublik/IpDinamisKonten.delete', [
                'ipDinamisKonten' => $ipDinamisKonten
            ]);
        }
        
        try {
            $result = IpDinamisKontenModel::deleteData($id);
             // Penting: Periksa apakah result memiliki status success=false
             if (isset($result['success']) && $result['success'] === false) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menghapus IpDinamis Konten'
                ]);
            }
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data IpDinamis Konten berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus data IpDinamis Konten');
        }
    }

     
}