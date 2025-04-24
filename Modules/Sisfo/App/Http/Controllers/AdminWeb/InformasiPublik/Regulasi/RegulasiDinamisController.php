<?php
namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\InformasiPublik\Regulasi\RegulasiDinamisModel;
use Illuminate\Support\Facades\Log;

class RegulasiDinamisController extends Controller
{
    use TraitsController;
    
    public $breadcrumb = 'Pengaturan Regulasi Dinamis';
    public $pagename = 'AdminWeb/InformasiPublik/RegulasiDinamis';
    
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Regulasi Dinamis',
            'list' => ['Home', 'Website', 'Regulasi Dinamis']
        ];

        $page = (object) [
            'title' => 'Daftar Regulasi Dinamis'
        ];

        $activeMenu = 'regulasi-dinamis';

        // Gunakan pagination dan pencarian
        $RegulasiDinamis = RegulasiDinamisModel::selectData(10, $search);

        return view("sisfo::AdminWeb/InformasiPublik/RegulasiDinamis.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'RegulasiDinamis' => $RegulasiDinamis,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $RegulasiDinamis = RegulasiDinamisModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::AdminWeb/InformasiPublik/RegulasiDinamis.data', compact('RegulasiDinamis', 'search'))->render();
        }
        
        return redirect()->route('regulasi-dinamis.index');
    }

    public function addData()
    {
        return view("sisfo::AdminWeb/InformasiPublik/RegulasiDinamis.create");
       
    }

    public function createData(Request $request)
    {
        try {
            RegulasiDinamisModel::validasiData($request);
            $result = RegulasiDinamisModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi dinamis berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat regulasi dinamis');
        }
    }

    public function editData($id)
    {
            $RegulasiDinamis = RegulasiDinamisModel::detailData($id);

            return view("sisfo::AdminWeb/InformasiPublik/RegulasiDinamis.update", [
                'RegulasiDinamis' => $RegulasiDinamis
            ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            RegulasiDinamisModel::validasiData($request);
            $result = RegulasiDinamisModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi dinamis berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui regulasi dinamis');
        }
    }

    public function detailData($id)
    {
            $RegulasiDinamis = RegulasiDinamisModel::detailData($id);
        
            return view("sisfo::AdminWeb/InformasiPublik/RegulasiDinamis.detail", [
                'RegulasiDinamis' => $RegulasiDinamis,
                'title' => 'Detail Regulasi Dinamis'
            ]);
    }

    public function deleteData(Request $request, $id)
    {
        try {
            if ($request->isMethod('get')) {
                $RegulasiDinamis = RegulasiDinamisModel::detailData($id);
                
                return view("sisfo::AdminWeb/InformasiPublik/RegulasiDinamis.delete", [
                    'RegulasiDinamis' => $RegulasiDinamis
                ]);
            }
            
            $result = RegulasiDinamisModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi dinamis berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus regulasi dinamis');
        }
    }
}