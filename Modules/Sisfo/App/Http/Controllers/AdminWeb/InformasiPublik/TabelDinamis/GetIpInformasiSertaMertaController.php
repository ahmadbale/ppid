<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpDinamisTabelModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpMenuUtamaModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpSubMenuUtamaModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpSubMenuModel;

class GetIPInformasiSertaMertaController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Informasi Publik yang Wajib Diumumkan Serta Merta';
    public $pagename = 'AdminWeb/InformasiPublik/GetIPDinamisTabel';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object)[
            'title' => 'Informasi Publik yang Wajib Diumumkan Serta Merta',
            'list' => ['Home', 'Informasi Publik', 'Informasi Serta Merta']
        ];

        $page = (object)[
            'title' => 'Informasi Publik yang Wajib Diumumkan Serta Merta'
        ];

        $activeMenu = 'Informasi Publik Serta Merta';

        try {
            // Ambil data kategori Informasi Serta Merta
            $kategoriInformasiSertaMerta = IpDinamisTabelModel::where('ip_nama_submenu', 'Informasi Serta Merta')
                ->where('isDeleted', 0)
                ->first();

            if (!$kategoriInformasiSertaMerta) {
                return view('sisfo::AdminWeb/InformasiPublik/GetIPDinamisTabel/informasi-serta-merta', [
                    'breadcrumb' => $breadcrumb,
                    'page' => $page,
                    'activeMenu' => $activeMenu,
                    'kategori' => null,
                    'menuUtamaList' => collect(),
                    'lastUpdate' => null,
                    'totalDokumen' => 0,
                    'totalData' => 0,
                    'search' => $search
                ]);
            }

            // Ambil semua data menu utama dengan hierarki lengkap untuk kategori Informasi Serta Merta
            $menuUtamaList = $this->getMenusByKategori($kategoriInformasiSertaMerta->ip_dinamis_tabel_id, $search);

            // Hitung total dokumen yang tersedia
            $totalDokumen = $this->hitungTotalDokumen($menuUtamaList);
            
            // Hitung total semua data (termasuk yang tidak ada dokumen)
            $totalData = $this->hitungTotalData($menuUtamaList);

            // Ambil tanggal terakhir update
            $lastUpdate = $this->getLastUpdateDate($kategoriInformasiSertaMerta->ip_dinamis_tabel_id);

            return view('sisfo::AdminWeb/InformasiPublik/GetIPDinamisTabel/informasi-serta-merta', [
                'breadcrumb' => $breadcrumb,
                'page' => $page,
                'activeMenu' => $activeMenu,
                'kategori' => $kategoriInformasiSertaMerta,
                'menuUtamaList' => $menuUtamaList,
                'lastUpdate' => $lastUpdate,
                'totalDokumen' => $totalDokumen,
                'totalData' => $totalData,
                'search' => $search
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        
        try {
            // Ambil data kategori Informasi Serta Merta
            $kategoriInformasiSertaMerta = IpDinamisTabelModel::where('ip_nama_submenu', 'Informasi Serta Merta')
                ->where('isDeleted', 0)
                ->first();

            if (!$kategoriInformasiSertaMerta) {
                if ($request->ajax()) {
                    return view('sisfo::AdminWeb/InformasiPublik/GetIPDinamisTabel/informasi-serta-merta-data', [
                        'kategori' => null,
                        'menuUtamaList' => collect(),
                        'totalDokumen' => 0,
                        'totalData' => 0,
                        'search' => $search
                    ])->render();
                }
                return redirect()->back()->with('error', 'Kategori Informasi Serta Merta tidak ditemukan');
            }

            // Ambil data menu dengan pencarian
            $menuUtamaList = $this->getMenusByKategori($kategoriInformasiSertaMerta->ip_dinamis_tabel_id, $search);
            $totalDokumen = $this->hitungTotalDokumen($menuUtamaList);
            $totalData = $this->hitungTotalData($menuUtamaList);

            if ($request->ajax()) {
                return view('sisfo::AdminWeb/InformasiPublik/GetIPDinamisTabel/informasi-serta-merta-data', [
                    'kategori' => $kategoriInformasiSertaMerta,
                    'menuUtamaList' => $menuUtamaList,
                    'totalDokumen' => $totalDokumen,
                    'totalData' => $totalData,
                    'search' => $search
                ])->render();
            }

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memuat data'], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    private function getMenusByKategori($kategoriId, $search = '')
    {
        $query = IpMenuUtamaModel::with(['IpSubMenuUtama.IpSubMenu'])
            ->where('fk_t_ip_dinamis_tabel', $kategoriId)
            ->where('isDeleted', 0);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ip_mu', 'like', '%' . $search . '%')
                  ->orWhereHas('IpSubMenuUtama', function ($subQuery) use ($search) {
                      $subQuery->where('nama_ip_smu', 'like', '%' . $search . '%')
                          ->orWhereHas('IpSubMenu', function ($subSubQuery) use ($search) {
                              $subSubQuery->where('nama_ip_sm', 'like', '%' . $search . '%');
                          });
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')
                    ->get();
    }

    private function hitungTotalDokumen($menuUtamaList)
    {
        $total = 0;
        
        foreach ($menuUtamaList as $menuUtama) {
            // Hitung dokumen di level menu utama
            if ($menuUtama->dokumen_ip_mu) {
                $total++;
            }

            // Hitung dokumen di level sub menu utama
            foreach ($menuUtama->IpSubMenuUtama as $subMenuUtama) {
                if ($subMenuUtama->dokumen_ip_smu) {
                    $total++;
                }

                // Hitung dokumen di level sub menu
                foreach ($subMenuUtama->IpSubMenu as $subMenu) {
                    if ($subMenu->dokumen_ip_sm) {
                        $total++;
                    }
                }
            }
        }

        return $total;
    }

    private function hitungTotalData($menuUtamaList)
    {
        $total = 0;
        
        foreach ($menuUtamaList as $menuUtama) {
            // Hitung semua data di level menu utama
            $total++;

            // Hitung semua data di level sub menu utama
            foreach ($menuUtama->IpSubMenuUtama as $subMenuUtama) {
                $total++;

                // Hitung semua data di level sub menu
                foreach ($subMenuUtama->IpSubMenu as $subMenu) {
                    $total++;
                }
            }
        }

        return $total;
    }

    private function getLastUpdateDate($kategoriId)
    {
        // Ambil tanggal terakhir dari semua level dokumen
        $lastUpdateMenuUtama = IpMenuUtamaModel::where('fk_t_ip_dinamis_tabel', $kategoriId)
            ->where('isDeleted', 0)
            ->max('updated_at');

        $lastUpdateSubMenuUtama = DB::table('t_ip_sub_menu_utama')
            ->join('t_ip_menu_utama', 't_ip_sub_menu_utama.fk_ip_menu_utama', '=', 't_ip_menu_utama.ip_menu_utama_id')
            ->where('t_ip_menu_utama.fk_t_ip_dinamis_tabel', $kategoriId)
            ->where('t_ip_sub_menu_utama.isDeleted', 0)
            ->max('t_ip_sub_menu_utama.updated_at');

        $lastUpdateSubMenu = DB::table('t_ip_sub_menu')
            ->join('t_ip_sub_menu_utama', 't_ip_sub_menu.fk_t_ip_sub_menu_utama', '=', 't_ip_sub_menu_utama.ip_sub_menu_utama_id')
            ->join('t_ip_menu_utama', 't_ip_sub_menu_utama.fk_ip_menu_utama', '=', 't_ip_menu_utama.ip_menu_utama_id')
            ->where('t_ip_menu_utama.fk_t_ip_dinamis_tabel', $kategoriId)
            ->where('t_ip_sub_menu.isDeleted', 0)
            ->max('t_ip_sub_menu.updated_at');

        // Ambil yang paling terbaru
        $dates = array_filter([$lastUpdateMenuUtama, $lastUpdateSubMenuUtama, $lastUpdateSubMenu]);
        
        return !empty($dates) ? max($dates) : null;
    }

    public function viewDocument($type, $id)
    {
        try {
            $document = null;
            $filename = '';
            $title = '';

            switch ($type) {
                case 'menu-utama':
                    $menuUtama = IpMenuUtamaModel::findOrFail($id);
                    $document = $menuUtama->dokumen_ip_mu;
                    $filename = $menuUtama->nama_ip_mu;
                    $title = $menuUtama->nama_ip_mu;
                    break;

                case 'sub-menu-utama':
                    $subMenuUtama = IpSubMenuUtamaModel::findOrFail($id);
                    $document = $subMenuUtama->dokumen_ip_smu;
                    $filename = $subMenuUtama->nama_ip_smu;
                    $title = $subMenuUtama->nama_ip_smu;
                    break;

                case 'sub-menu':
                    $subMenu = IpSubMenuModel::findOrFail($id);
                    $document = $subMenu->dokumen_ip_sm;
                    $filename = $subMenu->nama_ip_sm;
                    $title = $subMenu->nama_ip_sm;
                    break;

                default:
                    return response()->json(['error' => 'Tipe dokumen tidak valid'], 400);
            }

            if (!$document || !file_exists(storage_path('app/public/' . $document))) {
                return response()->json(['error' => 'Dokumen tidak ditemukan'], 404);
            }

            // Generate URL untuk dokumen
            $documentUrl = asset('storage/' . $document);
            
            return view('sisfo::AdminWeb.InformasiPublik.GetIPDinamisTabel.view-document-serta-merta', [
                'documentUrl' => $documentUrl,
                'title' => $title,
                'filename' => $filename,
                'type' => $type,
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat membuka dokumen'], 500);
        }
    }

    public function downloadDocument($type, $id)
    {
        try {
            $document = null;
            $filename = '';

            switch ($type) {
                case 'menu-utama':
                    $menuUtama = IpMenuUtamaModel::findOrFail($id);
                    $document = $menuUtama->dokumen_ip_mu;
                    $filename = $menuUtama->nama_ip_mu;
                    break;

                case 'sub-menu-utama':
                    $subMenuUtama = IpSubMenuUtamaModel::findOrFail($id);
                    $document = $subMenuUtama->dokumen_ip_smu;
                    $filename = $subMenuUtama->nama_ip_smu;
                    break;

                case 'sub-menu':
                    $subMenu = IpSubMenuModel::findOrFail($id);
                    $document = $subMenu->dokumen_ip_sm;
                    $filename = $subMenu->nama_ip_sm;
                    break;

                default:
                    return response()->json(['error' => 'Tipe dokumen tidak valid'], 400);
            }

            if (!$document || !file_exists(storage_path('app/public/' . $document))) {
                return response()->json(['error' => 'Dokumen tidak ditemukan'], 404);
            }

            return response()->download(
                storage_path('app/public/' . $document),
                $filename . '.pdf'
            );

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengunduh dokumen'], 500);
        }
    }
}