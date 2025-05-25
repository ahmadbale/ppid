<?php
namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\PenyelesaianSengketa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\PenyelesaianSengketa\UploadPSModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\PenyelesaianSengketa\PenyelesaianSengketaModel;

class UploadPSController extends Controller
{
    use TraitsController;
    public $breadcrumb = 'Pengaturan Upload Penyelesaian Sengketa';
    public $pagename = 'AdminWeb/InformasiPublik/UploadPS';

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $breadcrumb = (object) [
            'title' => 'Pengaturan Detail Upload Penyelesaian Sengketa',
            'list' => ['Home', 'Penyelesaian Sengketa Detail Upload']
        ];

        $page = (object) [
            'title' => 'Daftar Upload Penyelesaian Sengketa'
        ];

        $activeMenu = 'upload-penyelesaian-sengketa';

        // Get data with filters
        $uploadPS = UploadPSModel::selectData(10, $search);

        return view("sisfo::AdminWeb/InformasiPublik/UploadPS.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'uploadPS' => $uploadPS,
            'search' => $search
        ]);
    }
     public function getData(Request $request)
     {
          $search = $request->query('search', '');
          $uploadPS = UploadPSModel::selectData(10, $search);
     
          if ($request->ajax()) {
               return view('sisfo::AdminWeb/InformasiPublik/UploadPS.data', compact('uploadPS', 'search'))->render();
          }
     
          return redirect()->route('upload-penyelesaian-sengketa.index');
     }
     public function addData()
     {
          // Get all active Penyelesaian Sengketa for dropdown
          $penyelesaianSengketa = PenyelesaianSengketaModel::where('isDeleted', 0)->get();
          return view("sisfo::AdminWeb/InformasiPublik/UploadPS.create",[
               'penyelesaianSengketa' => $penyelesaianSengketa
          ]);
     }
     public function createData (Request $request)
     { 
          try {
               UploadPSModel::validasiData($request);
               $result = UploadPSModel::createData($request);

               return $this->jsonSuccess(
                    $result['data'] ?? null,
                    $result['message'] ?? 'Data Upload Penyelesaian Sengketa berhasil dibuat.',
               );
          } catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan membuat data Upload Penyelesaian Sengketa');
          }
     } 
     public function editData($id)
     {
          $uploadPS = UploadPSModel::findOrFail($id);
          $penyelesaianSengketa = PenyelesaianSengketaModel::where('isDeleted', 0)->get();
          return view("sisfo::AdminWeb/InformasiPublik/UploadPS.update", [
               'uploadPS' => $uploadPS,
               'penyelesaianSengketa' => $penyelesaianSengketa
          ]);
     }
     public function updateData(Request $request, $id)
     {
          try {
               UploadPSModel::validasiData($request);
               $result = UploadPSModel::updateData($request, $id);

               return $this->jsonSuccess(
                    $result['data'] ?? null,
                    $result['message'] ?? 'Data Upload Penyelesaian Sengketa berhasil diperbarui.'
               );
          } catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan memperbarui data Upload Penyelesaian Sengketa');
          }
     }
     public function detailData($id)
     {
          $uploadPS = UploadPSModel::detailData($id);
          return view("sisfo::AdminWeb/InformasiPublik/UploadPS.detail", [
               'uploadPS' => $uploadPS,
               'tittle' => 'Detail Upload Penyelesaian Sengketa'
          ]);
     }
     public function deleteData(Request $request, $id)
     {
          if ($request->isMethod('get')){
               $uploadPS = UploadPSModel::detailData($id);
               return view("sisfo::AdminWeb/InformasiPublik/UploadPS.delete", [
                    'uploadPS' => $uploadPS
               ]);
          }
          
          try {
               $result = UploadPSModel::deleteData($id);
               return $this->jsonSuccess(
                    $result['data'] ?? null,
                    $result['message'] ?? 'Data Upload Penyelesaian Sengketa berhasil dihapus.'
               );
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan menghapus data Upload Penyelesaian Sengketa');
          }
     }
}