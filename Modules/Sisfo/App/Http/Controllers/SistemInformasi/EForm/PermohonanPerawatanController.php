<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

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

        $activeMenu = 'permohonansaranadanprasarana';

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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Permohonan Perawatan Sarana dan Prasarana berhasil diajukan.'
                ]);
            }

            // Untuk request normal (non-AJAX), lakukan redirect
            if ($folder === 'RPN') {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana');
            } else {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana-admin');
            }

            return $this->redirectSuccess($redirectUrl, $result['message']);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi data gagal. Silakan periksa kembali data Anda.',
                    'errors' => $e->errors()
                ], 422);
            }
        
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses permohonan'
                ], 500);
            }
            return $this->redirectException($e, 'Terjadi kesalahan saat memproses permohonan');
        }
    }


    private function getUserFolder()
    {
        $hakAksesKode = Auth::user()->level->hak_akses_kode;
        
        // Jika user adalah RPN, gunakan folder RPN
        // Jika tidak (ADM, ADT, atau lainnya), gunakan folder ADM
        return ($hakAksesKode === 'RPN') ? 'RPN' : 'ADM';
    }
}
