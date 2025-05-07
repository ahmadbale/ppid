<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class PengaduanMasyarakatController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaduan Masyarakat';
    public $pagename = 'SistemInformasi/EForm/PengaduanMasyarakat';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pengaduan Masyarakat',
            'list' => ['Home', 'Pengaduan Masyarakat']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'pengaduanmasyarakatadmin';

        return view("sisfo::SistemInformasi/EForm/$folder/PengaduanMasyarakat.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PengaduanMasyarakatModel::getTimeline();
        $ketentuanPelaporan = PengaduanMasyarakatModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pengaduan Masyarakat',
            'list' => ['Home', 'Pengaduan Masyarakat', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'PengaduanMasyarakat';

        return view("sisfo::SistemInformasi/EForm/$folder/PengaduanMasyarakat.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PengaduanMasyarakatModel::validasiData($request);
            $result = PengaduanMasyarakatModel::createData($request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Pengaduan Masyarakat berhasil diajukan.'
                ]);
            }

            // Untuk request normal (non-AJAX), lakukan redirect
            if ($folder === 'RPN') {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('pengaduan-masyarakat');
            } else {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('pengaduan-masyarakat-admin');
            }

            return $this->redirectSuccess($redirectUrl, $result['message']);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengajukan pengaduan masyarakat',
                    'errors' => $e->errors()
                ], 422);
            }
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses pendauan masyarakat'
                ], 500);
            }
            return $this->redirectException($e, 'Terjadi kesalahan saat memproses pendauan masyarakat');
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
