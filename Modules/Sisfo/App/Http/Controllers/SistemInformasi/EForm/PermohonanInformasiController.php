<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class PermohonanInformasiController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Permohonan Informasi';
    public $pagename = 'SistemInformasi/EForm/PermohonanInformasi';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'permohonaninformasi';

        return view("sisfo::SistemInformasi/EForm/$folder/PermohonanInformasi.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PermohonanInformasiModel::getTimeline();
        $ketentuanPelaporan = PermohonanInformasiModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view("sisfo::SistemInformasi/EForm/$folder/PermohonanInformasi.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            // Dapatkan folder untuk menentukan redirect
            $folder = $this->getUserFolder();
            
            PermohonanInformasiModel::validasiData($request);
            $result = PermohonanInformasiModel::createData($request);

            // PERBAIKAN: Untuk request AJAX, kembalikan langsung response JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Permohonan Informasi berhasil diajukan.'
                ]);
            }

            // Untuk request normal (non-AJAX), lakukan redirect
            if ($folder === 'RPN') {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi');
            } else {
                $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi-admin');
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
                    'message' => 'Terjadi kesalahan saat mengajukan permohonan: ' . $e->getMessage()
                ], 500);
            }
            
            return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan permohonan: ' . $e->getMessage());
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