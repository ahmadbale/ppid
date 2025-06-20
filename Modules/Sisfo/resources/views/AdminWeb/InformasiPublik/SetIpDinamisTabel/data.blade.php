<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\data.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
            <i class="fas fa-table"></i>
            Total: {{ $setIpDinamisTabel->count() }} Set Informasi Publik
            @if(!empty($search))
                | Hasil pencarian: "{{ $search }}"
            @endif
        </div>
        <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="expandAll()">
                <i class="fas fa-expand-arrows-alt"></i> Expand All
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                <i class="fas fa-compress-arrows-alt"></i> Collapse All
            </button>
        </div>
    </div>
</div>

<div class="menu-tree-container">
    @if($setIpDinamisTabel->count() > 0)
        @php
            $processedMenus = collect();
        @endphp
        
        @foreach($setIpDinamisTabel as $menuUtama)
            @if(!$menuUtama->processed ?? false)
                @php $processedMenus->push($menuUtama->ip_menu_utama_id); @endphp
                
                {{-- Menu Utama Level 1 --}}
                <div class="menu-item main-menu mb-3" data-menu-id="{{ $menuUtama->ip_menu_utama_id }}">
                    <div class="card shadow-sm border-left-primary">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        @if($menuUtama->IpSubMenuUtama && $menuUtama->IpSubMenuUtama->count() > 0)
                                            <button type="button" class="btn btn-sm btn-link p-0 mr-2 toggle-submenu" data-target="submenu-utama-{{ $menuUtama->ip_menu_utama_id }}">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        @else
                                            <span class="mr-2" style="width: 24px;"></span>
                                        @endif
                                        
                                        <i class="fas fa-folder text-primary mr-2"></i>
                                        
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $menuUtama->nama_ip_mu }}</h6>
                                            <small class="text-muted">
                                                <span class="badge badge-primary badge-sm">Menu Utama</span>
                                                @if($menuUtama->IpDinamisTabel)
                                                    • {{ $menuUtama->IpDinamisTabel->ip_nama_submenu }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div>
                                        @if($menuUtama->dokumen_ip_mu)
                                            <i class="fas fa-file-pdf text-danger mr-1"></i>
                                            <span class="text-success">Memiliki Dokumen</span>
                                        @else
                                            <i class="fas fa-sitemap text-info mr-1"></i>
                                            <span class="text-info">Memiliki Sub Menu</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    @php
                                        $totalSubMenuUtama = $menuUtama->IpSubMenuUtama ? $menuUtama->IpSubMenuUtama->count() : 0;
                                        $totalSubMenu = 0;
                                        if($menuUtama->IpSubMenuUtama) {
                                            foreach($menuUtama->IpSubMenuUtama as $smu) {
                                                $totalSubMenu += $smu->IpSubMenu ? $smu->IpSubMenu->count() : 0;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex flex-column">
                                        <small><strong>Sub Menu Utama:</strong> {{ $totalSubMenuUtama }}</small>
                                        <small><strong>Sub Menu:</strong> {{ $totalSubMenu }}</small>
                                        <small class="text-primary"><strong>Level:</strong> 
                                            @if($totalSubMenu > 0)
                                                3 Level
                                            @elseif($totalSubMenuUtama > 0)
                                                2 Level
                                            @else
                                                1 Level
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-2 text-right">
                                    <div class="btn-group btn-group-sm">
                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'update'))
                                            <button class="btn btn-warning" onclick="modalAction('{{ url($setIpDinamisTabelUrl . '/editData/' . $menuUtama->ip_menu_utama_id) }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-info" onclick="modalAction('{{ url($setIpDinamisTabelUrl . '/detailData/' . $menuUtama->ip_menu_utama_id) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'delete'))
                                            <button class="btn btn-danger" onclick="modalAction('{{ url($setIpDinamisTabelUrl . '/deleteData/' . $menuUtama->ip_menu_utama_id) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Sub Menu Utama Level 2 --}}
                    @if($menuUtama->IpSubMenuUtama && $menuUtama->IpSubMenuUtama->count() > 0)
                        <div class="submenu-container mt-2 ml-4" id="submenu-utama-{{ $menuUtama->ip_menu_utama_id }}">
                            @foreach($menuUtama->IpSubMenuUtama as $subMenuUtama)
                                <div class="menu-item sub-menu-utama mb-2" data-menu-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}">
                                    <div class="card border-left-success">
                                        <div class="card-body p-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        @if($subMenuUtama->IpSubMenu && $subMenuUtama->IpSubMenu->count() > 0)
                                                            <button type="button" class="btn btn-sm btn-link p-0 mr-2 toggle-submenu" data-target="submenu-{{ $subMenuUtama->ip_sub_menu_utama_id }}">
                                                                <i class="fas fa-chevron-right"></i>
                                                            </button>
                                                        @else
                                                            <span class="mr-2" style="width: 24px;"></span>
                                                        @endif
                                                        
                                                        <i class="fas fa-level-up-alt text-muted mr-2" style="transform: rotate(90deg); font-size: 0.8em;"></i>
                                                        <i class="fas fa-folder text-success mr-2"></i>
                                                        
                                                        <div>
                                                            <h6 class="mb-0">{{ $subMenuUtama->nama_ip_smu }}</h6>
                                                            <small class="text-muted">
                                                                <span class="badge badge-success badge-sm">Sub Menu Utama</span>
                                                                @if($subMenuUtama->IpSubMenu)
                                                                    • {{ $subMenuUtama->IpSubMenu->count() }} Sub Menu
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div>
                                                        @if($subMenuUtama->dokumen_ip_smu)
                                                            <i class="fas fa-file-pdf text-danger mr-1"></i>
                                                            <span class="text-success">Memiliki Dokumen</span>
                                                        @else
                                                            <i class="fas fa-sitemap text-info mr-1"></i>
                                                            <span class="text-info">Memiliki Sub Menu</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="d-flex flex-column">
                                                        <small><strong>Sub Menu:</strong> {{ $subMenuUtama->IpSubMenu ? $subMenuUtama->IpSubMenu->count() : 0 }}</small>
                                                        <small class="text-success"><strong>Level:</strong> 
                                                            @if($subMenuUtama->IpSubMenu && $subMenuUtama->IpSubMenu->count() > 0)
                                                                3 Level
                                                            @else
                                                                2 Level
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 text-right">
                                                    <div class="btn-group btn-group-sm">
                                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'update'))
                                                            <button class="btn btn-warning btn-sm" onclick="editSubMenuUtama('{{ $subMenuUtama->ip_sub_menu_utama_id }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-info btn-sm" onclick="detailSubMenuUtama('{{ $subMenuUtama->ip_sub_menu_utama_id }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'delete'))
                                                            <button class="btn btn-danger btn-sm" onclick="deleteSubMenuUtama('{{ $subMenuUtama->ip_sub_menu_utama_id }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Sub Menu Level 3 --}}
                                    @if($subMenuUtama->IpSubMenu && $subMenuUtama->IpSubMenu->count() > 0)
                                        <div class="submenu-container mt-2 ml-4" id="submenu-{{ $subMenuUtama->ip_sub_menu_utama_id }}">
                                            @foreach($subMenuUtama->IpSubMenu as $subMenu)
                                                <div class="menu-item sub-menu mb-2" data-menu-id="{{ $subMenu->ip_sub_menu_id }}">
                                                    <div class="card border-left-info">
                                                        <div class="card-body p-2">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-4">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="mr-2" style="width: 24px;"></span>
                                                                        <i class="fas fa-level-up-alt text-muted mr-2" style="transform: rotate(90deg); font-size: 0.8em;"></i>
                                                                        <i class="fas fa-file text-info mr-2"></i>
                                                                        
                                                                        <div>
                                                                            <h6 class="mb-0">{{ $subMenu->nama_ip_sm }}</h6>
                                                                            <small class="text-muted">
                                                                                <span class="badge badge-info badge-sm">Sub Menu</span>
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-3">
                                                                    <div>
                                                                        @if($subMenu->dokumen_ip_sm)
                                                                            <i class="fas fa-file-pdf text-danger mr-1"></i>
                                                                            <span class="text-success">Memiliki Dokumen</span>
                                                                        @else
                                                                            <i class="fas fa-times text-muted mr-1"></i>
                                                                            <span class="text-muted">Tidak ada dokumen</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-3">
                                                                    <div class="d-flex flex-column">
                                                                        <small class="text-info"><strong>Level:</strong> 3 Level</small>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-2 text-right">
                                                                    <div class="btn-group btn-group-sm">
                                                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'update'))
                                                                            <button class="btn btn-warning btn-sm" onclick="editSubMenu('{{ $subMenu->ip_sub_menu_id }}')">
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>
                                                                        @endif
                                                                        <button class="btn btn-info btn-sm" onclick="detailSubMenu('{{ $subMenu->ip_sub_menu_id }}')">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'delete'))
                                                                            <button class="btn btn-danger btn-sm" onclick="deleteSubMenu('{{ $subMenu->ip_sub_menu_id }}')">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    @else
        <div class="text-center py-5">
            <i class="fas fa-table fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">
                @if(!empty($search))
                    Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                @else
                    Tidak ada data Set Informasi Publik Dinamis Tabel
                @endif
            </h5>
        </div>
    @endif
</div>