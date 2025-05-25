<?php
namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\PenyelesaianSengketa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\PenyelesaianSengketa\PenyelesaianSengketaModel;

class PenyelesaianSengketaController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Penyelesaian Sengketa';
    public $pagename = 'AdminWeb/InformasiPublik/PenyelesaianSengketa';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Penyelesaian Sengketa',
            'list' => ['Home', 'Pengaturan Penyelesaian Sengketa']
        ];

        $page = (object) [
            'title' => 'Daftar Penyelesaian Sengketa'
        ];

        $activeMenu = 'Penyelesaian Sengketa';
        
        // Gunakan pagination dan pencarian
        $penyelesaianSengketa = PenyelesaianSengketaModel::selectData(10, $search);

        return view('sisfo::AdminWeb/InformasiPublik/PenyelesaianSengketa.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'penyelesaianSengketa' => $penyelesaianSengketa,
            'search' => $search
        ]);
    }
     public function getData(Request $request)
     {
          $search = $request->query('search', '');
          $penyelesaianSengketa = PenyelesaianSengketaModel::selectData(10, $search);
          
          if ($request->ajax()) {
               return view('sisfo::AdminWeb/InformasiPublik/PenyelesaianSengketa.data', compact('penyelesaianSengketa', 'search'))->render();
          }
          
          return redirect()->route('penyelesaian-sengketa.index');
     }
     public function addData()
     {
          return view('sisfo::AdminWeb/InformasiPublik/PenyelesaianSengketa.create');
     }
     public function createData(Request $request)
     {
          try {
             PenyelesaianSengketaModel::validasiData($request);
             $result = PenyelesaianSengketaModel::createData($request);
             return $this ->jsonSuccess(
               $result['data'] ?? null,
               $result['message'] ?? 'Data Penyelesaian Sengketa berhasil dibuat',
             );
          } catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi kesalahan membuat data Penyelesaian Sengketa');
          }
     }

     public function editData($id)
     {
          $penyelesaianSengketa = PenyelesaianSengketaModel::detailData($id);
          return view('sisfo::AdminWeb/InformasiPublik/PenyelesaianSengketa.update', [
               'penyelesaianSengketa' => $penyelesaianSengketa
          ]);
     }

     public function updateData(Request $request, $id)
     {
          try {
               PenyelesaianSengketaModel::validasiData($request);
               $result = PenyelesaianSengketaModel::updateData($request, $id);
               return $this->jsonSuccess(
                    $result['data'] ?? null,
                    $result['message'] ?? 'Data Penyelesaian Sengketa berhasil diperbarui'
               );
          } catch (ValidationException $e) {
               return $this->jsonValidationError($e);
          } catch (\Exception $e) {
               return $this->jsonError($e, 'Terjadi Kesalahan saat memperbarui data Penyelesaian Sengketa');
          }
     }
     public function detailData($id)
     {
          $penyelesaianSengketa = PenyelesaianSengketaModel::detailData($id);
          return view('sisfo::AdminWeb/InformasiPublik/PenyelesaianSengketa.detail', [
               'penyelesaianSengketa' => $penyelesaianSengketa,
               'tittle' => 'Detail Penyelesaian Sengketa'
          ]);
     }
     public function deleteData(Request $request, $id)
     {
          if ($request->isMethod('get')){
               $penyelesaianSengketa = PenyelesaianSengketaModel::detailData($id);
               return view('sisfo::AdminWeb/InformasiPublik/PenyelesaianSengketa.delete', [
                    'penyelesaianSengketa' => $penyelesaianSengketa
               ]);
          }
          try {
               $result = PenyelesaianSengketaModel::deleteData($id);
           // Penting: Periksa apakah result memiliki status success=false
           if (isset($result['success']) && $result['success'] === false) {
               return response()->json([
                   'success' => false,
                   'message' => $result['message'] ?? 'Gagal menghapus Penyelesaian Sengketa'
               ]);
           }
           return $this->jsonSuccess(
               $result['data'] ?? null, 
               $result['message'] ?? 'Data Peyelesaian Sengketa berhasil dihapus'
           );
       } catch (\Exception $e) {
           return $this->jsonError($e, 'Terjadi kesalahan saat menghapus data Penyelesaian Sengketa');
       }
     }
}