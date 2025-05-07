<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class PernyataanKeberatanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pernyataan Keberatan';
    public $pagename = 'SistemInformasi/EForm/PernyataanKeberatan';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pernyataan Keberatan',
            'list' => ['Home', 'Pernyataan Keberatan']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pernyataan Keberatan'
        ];

        $activeMenu = 'pernyataankeberatanadminn';

        return view("sisfo::SistemInformasi/EForm/$folder/PernyataanKeberatan.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PernyataanKeberatanModel::getTimeline();
        $ketentuanPelaporan = PernyataanKeberatanModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pernyataan Keberatan',
            'list' => ['Home', 'Pernyataan Keberatan', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pernyataan Keberatan'
        ];

        $activeMenu = 'PernyataanKeberatan';

        return view("sisfo::SistemInformasi/EForm/$folder/PernyataanKeberatan.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }
    
    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PernyataanKeberatanModel::validasiData($request);
            $result = PernyataanKeberatanModel::createData($request);

            // PERBAIKAN: Untuk request AJAX, kembalikan langsung response JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Pernyataan Keberatan berhasil diajukan.'
                ]);
            }

            // Untuk request normal (non-AJAX), lakukan redirect
            if ($folder === 'RPN') {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan');
            } else {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan-admin');
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
                    'message' => 'Terjadi kesalahan saat memproses pernyataan keberatan'
                ], 500);
            }
            return $this->redirectException($e, 'Terjadi kesalahan saat memproses pernyataan keberatan');
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
