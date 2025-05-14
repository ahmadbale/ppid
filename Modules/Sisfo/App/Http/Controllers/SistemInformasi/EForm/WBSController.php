<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class WBSController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Whistle Blowing System';
    public $pagename = 'SistemInformasi/EForm/WBS';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Whistle Blowing System',
            'list' => ['Home', 'Whistle Blowing System']
        ];

        $page = (object) [
            'title' => 'whistleblowingsystem'
        ];

        $activeMenu = 'WBS';

        return view("sisfo::SistemInformasi/EForm/$folder/WBS.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = WBSModel::getTimeline();
        $ketentuanPelaporan = WBSModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Whistle Blowing System',
            'list' => ['Home', 'Whistle Blowing System', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Whistle Blowing System'
        ];

        $activeMenu = 'WBS';

        return view("sisfo::SistemInformasi/EForm/$folder/WBS.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            WBSModel::validasiData($request);
            $result = WBSModel::createData($request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Whistle Blowing System berhasil diajukan.'
                ]);
            }

            // Untuk request normal (non-AJAX), lakukan redirect
            if ($folder === 'RPN') {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('whistle-blowing-system');
            } else {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('whistle-blowing-system-admin');
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengajukan pengajuan: ' . $e->getMessage()
                ], 500);
            }
            
            return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan pengajuan: ' . $e->getMessage());
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
