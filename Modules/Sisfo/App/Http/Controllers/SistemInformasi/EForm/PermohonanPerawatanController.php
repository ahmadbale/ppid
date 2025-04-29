<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PermohonanPerawatanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Permohonan Pemeliharaan Sarana Prasarana';
    public $pagename = 'SistemInformasi/EForm/PermohonanPerawatan';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Pemeliharaan Sarana Prasarana',
            'list' => ['Home', 'Permohonan Pemeliharaan Sarana Prasarana']
        ];

        $page = (object) [
            'title' => 'Permohonan Pemeliharaan Sarana Prasarana'
        ];

        $activeMenu = 'PermohonanPerawatan';

        return view("sisfo::SistemInformasi/EForm/$folder/PermohonanPerawatan.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PermohonanPerawatanModel::getTimeline();
        $ketentuanPelaporan = PermohonanPerawatanModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Pemeliharaan Sarana Prasarana',
            'list' => ['Home', 'Permohonan Pemeliharaan Sarana Prasarana', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Pemeliharaan Sarana Prasarana'
        ];

        $activeMenu = 'PermohonanPerawatan';

        return view("sisfo::SistemInformasi/EForm/$folder/PermohonanPerawatan.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PermohonanPerawatanModel::validasiData($request);
            $result = PermohonanPerawatanModel::createData($request);

            // if ($result['success']) {
            //     return $this->redirectSuccess("/SistemInformasi/EForm/$folder/PermohonanPerawatan", $result['message']);
            // }

            if ($request->ajax()) { // << cek kalau request dari AJAX
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => $result['message']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ]);
                }
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan permohonan'
            ]);
        }
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses permohonan'
                ]);
            }
            return $this->redirectException($e, 'Terjadi kesalahan saat memproses permohonan');
        }
    }


    private function getUserFolder()
    {
        $levelKode = Auth::user()->level->level_kode;
        return ($levelKode === 'ADM' || $levelKode === 'RPN') ? $levelKode : abort(403);
    }
}
