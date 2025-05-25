<?php
namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\LayananInformasi;

use Illuminate\Http\Request;  
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\LayananInformasi\LIDUploadModel;
use Modules\Sisfo\App\Models\Website\LayananInformasi\LIDinamisModel;

class LIDUploadController extends Controller
{
     use TraitsController;
     public $breadcrumb = 'Pengaturan Layanan Informasi Upload';
     public $pagename = 'AdminWeb/LayananInformasi/LIDUpload';
     
     public function index(Request $request)
     {
         $search = $request->query('search', '');
         $breadcrumb = (object) [
             'title' => 'Pengaturan Layanan Informasi Upload',
             'list' => ['Home', 'Layanan Informasi Upload']
         ];

         $page = (object) [
             'title' => 'Daftar Layanan Informasi Upload'
         ];
         
         $activeMenu = 'layanan-informasi-upload';
         
         // Get data with filters
         $liUpload = LIDUploadModel::selectData(10, $search);

         return view("sisfo::AdminWeb/LayananInformasi/LIDUpload.index", [
             'breadcrumb' => $breadcrumb,
             'page' => $page,
             'activeMenu' => $activeMenu,
             'liUpload' => $liUpload,
             'search' => $search
         ]);
     }

     public function getData(Request $request)
     {
         $search = $request->query('search', '');
         $liUpload = LIDUploadModel::selectData(10, $search);
         
         if ($request->ajax()) {
             return view('sisfo::AdminWeb/LayananInformasi/LIDUpload.data', compact('liUpload', 'search'))->render();
         }
         
         return redirect()->route('layanan-informasi-upload.index');
     }

     public function addData()
     {
          // Get all active LI Dinamis for dropdown
          $liDinamis = LIDinamisModel::where('isDeleted', 0)->get();
          return view("sisfo::AdminWeb/LayananInformasi/LIDUpload.create", [
               'liDinamis' => $liDinamis
          ]);
     }
     
     public function createData(Request $request)
     {
          try 
          {
               LIDUploadModel::validasiData($request);
               $result = LIDUploadModel::createData($request);
               
               return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Layanan Informasi Upload berhasil dibuat'
               );
          } catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan saat membuat Layanan Informasi Upload');
          }
          
     }

     public function editData($id)
     {
          $liUpload = LIDUploadModel::detailData($id);
          $liDinamis = LIDinamisModel::where('isDeleted', 0)->get();
          
          return view("sisfo::AdminWeb/LayananInformasi/LIDUpload.update", [
               'liUpload' => $liUpload,
               'liDinamis' => $liDinamis
          ]);
     }

     public function updateData(Request $request, $id)
     {
          try 
          {
               LIDUploadModel::validasiData($request);
               $result = LIDUploadModel::updateData($request, $id);
               
               return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Layanan Informasi Upload berhasil diperbarui'
               );
          } catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Layanan Informasi Upload');
          }
          
     }

     public function detailData($id)
     {
          $liUpload = LIDUploadModel::detailData($id);
          return view("sisfo::AdminWeb/LayananInformasi/LIDUpload.detail", [
               'liUpload' => $liUpload,
               'title' => 'Detail Layanan Informasi Upload'
          ]);
     }

     public function deleteData(Request $request, $id)
     {
          if ($request->isMethod('get')) {
               $liUpload = LIDUploadModel::detailData($id);
               return view("sisfo::AdminWeb/LayananInformasi/LIDUpload.delete", [
                    'liUpload' => $liUpload
               ]);
          }
          
          try {
               $result = LIDUploadModel::deleteData($id);
               
               return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Layanan Informasi Upload berhasil dihapus'
               );
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan saat menghapus Layanan Informasi Upload');
          }
          
     }
}