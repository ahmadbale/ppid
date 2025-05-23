<?php
namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\LayananInformasi;

use Illuminate\Http\Request;  
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\LayananInformasi\LIDinamisModel;

class LIDinamisController extends Controller
{
     use TraitsController;
     public $breadcrumb = 'Pengaturan Layanan Informasi Dinamis';
     public $pagename = 'AdminWeb/LIDinamis';
     
     public function index(Request $request)
     {
         $search = $request->query('search', '');
         $breadcrumb = (object) [
             'title' => 'Pengaturan Layanan Informasi Dinamis',
             'list' => ['Home', 'Layanan Informasi Dinamis']
         ];

         $page = (object) [
             'title' => 'Daftar Layanan Informasi Dinamis'
         ];
         
         $activeMenu = 'layanan-informasi-Dinamis';
         
         // Get data with filters
         $liDinamis = LIDinamisModel::selectData(10, $search);

         return view("sisfo::AdminWeb/LayananInformasi/LIDinamis.index", [
             'breadcrumb' => $breadcrumb,
             'page' => $page,
             'activeMenu' => $activeMenu,
             'liDinamis' => $liDinamis,
             'search' => $search
         ]);
     }
     public function getData(Request $request)
     {
         $search = $request->query('search', '');
         $liDinamis = LIDinamisModel::selectData(10, $search);
         
         if ($request->ajax()) {
             return view('sisfo::AdminWeb/LayananInformasi/LIDinamis.data', compact('liDinamis', 'search'))->render();
         }
           return redirect()->route('layanan-informasi-Dinamis.index');
     }
     public function addData()
     {
          return view("sisfo::AdminWeb/LayananInformasi/LIDinamis.create");
     }
     
     public function createData(Request $request)
     {
          try 
          {
               LIDinamisModel::validasiData($request);
               $result = LIDinamisModel::createData($request);
               
               // PERBAIKAN: Ganti responseJson dengan jsonSuccess
               return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Layanan Informasi Dinamis berhasil dibuat'
               );
          }catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          }catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan saat membuat Layanan Informasi Dinamis');
          }
          
     }
     public function editData($id)
     {
          $liDinamis = LIDinamisModel::findOrFail($id);
          return view("sisfo::AdminWeb/LayananInformasi/LIDinamis.update", [
               'liDinamis' => $liDinamis,
          ]);
     }
     public function updateData(Request $request, $id)
     {
          try 
          {
               LIDinamisModel::validasiData($request);
               $result = LIDinamisModel::updateData($request, $id);
               
               return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Layanan Informasi Dinamis berhasil diperbarui'
               );
          }catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          }catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Layanan Informasi Dinamis');
          }
          
     }
     public function detailData($id)
     {
          $liDinamis = LIDinamisModel::findOrFail($id);
          return view("sisfo::AdminWeb/LayananInformasi/LIDinamis.detail", [
               'liDinamis' => $liDinamis,
               'title' => 'Detail Layanan Informasi Dinamis'
          ]);
     }
     public function deleteData(Request $request, $id)
     {
          if  ($request->isMethod('get')){
               $liDinamis = LIDinamisModel::findOrFail($id);
               
               return view("sisfo::AdminWeb/LayananInformasi/LIDinamis.delete", [
                    'liDinamis' => $liDinamis
               ]);
          }
          try 
          {
               $result = LIDinamisModel::deleteData($id);
               if (isset($result['success']) && $result['success'] === false) {
                    return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal menghapus Layanan Informasi Dinamis'

               ]);
               }
               
               return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Layanan Informasi Dinamis berhasil dihapus'
               );
          }catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan saat menghapus Layanan Informasi Dinamis');
          }
          
     }
}