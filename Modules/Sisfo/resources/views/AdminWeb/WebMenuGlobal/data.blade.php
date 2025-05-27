<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\data.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
            <i class="fas fa-sitemap"></i> 
            Total: {{ $webMenuGlobals->count() }} menu
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
    @if($webMenuGlobals->count() > 0)
        @php
            $processedMenus = collect();
            $currentLevel = 0;
        @endphp
        
        @foreach($webMenuGlobals as $menu)
            @if($menu->wmg_parent_id === null)
                {{-- Menu Utama --}}
                @php $processedMenus->push($menu->web_menu_global_id); @endphp
                
                <div class="menu-item main-menu mb-3" data-menu-id="{{ $menu->web_menu_global_id }}">
                    <div class="card shadow-sm border-left-primary">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        @if($menu->wmg_kategori_menu === 'Group Menu' && $menu->children->where('isDeleted', 0)->count() > 0)
                                            <button type="button" class="btn btn-sm btn-link p-0 mr-2 toggle-submenu" data-target="submenu-{{ $menu->web_menu_global_id }}">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        @else
                                            <span class="mr-2" style="width: 24px;"></span>
                                        @endif
                                        
                                        @if($menu->wmg_kategori_menu === 'Group Menu')
                                            <i class="fas fa-folder text-success mr-2"></i>
                                        @else
                                            <i class="fas fa-file text-primary mr-2"></i>
                                        @endif
                                        
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $menu->wmg_nama_default }}</h6>
                                            <small class="text-muted">
                                                <span class="badge badge-{{ $menu->wmg_kategori_menu === 'Group Menu' ? 'success' : 'primary' }} badge-sm">
                                                    {{ $menu->wmg_kategori_menu }}
                                                </span>
                                                • Urutan: {{ $menu->wmg_urutan_menu }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    @if($menu->fk_web_menu_url)
                                        <div>
                                            <strong>{{ $menu->WebMenuUrl->wmu_nama }}</strong>
                                            @if($menu->WebMenuUrl->application)
                                                <br><small class="text-muted">{{ $menu->WebMenuUrl->application->app_nama }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge badge-warning">Group Menu</span>
                                    @endif
                                </div>
                                
                                <div class="col-md-2 text-center">
                                    <span class="badge badge-{{ $menu->wmg_status_menu === 'aktif' ? 'success' : 'danger' }}">
                                        {{ ucfirst($menu->wmg_status_menu) }}
                                    </span>
                                </div>
                                
                                <div class="col-md-3 text-right">
                                    <div class="btn-group btn-group-sm">
                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'update'))
                                            <button class="btn btn-warning" onclick="modalAction('{{ url($webMenuGlobalPath . '/editData/' . $menu->web_menu_global_id) }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-info" onclick="modalAction('{{ url($webMenuGlobalPath . '/detailData/' . $menu->web_menu_global_id) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'delete'))
                                            <button class="btn btn-danger" onclick="modalAction('{{ url($webMenuGlobalPath . '/deleteData/' . $menu->web_menu_global_id) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Submenu - PERBAIKAN: menghapus style display:none --}}
                    @if($menu->wmg_kategori_menu === 'Group Menu')
                        @php
                            // PERBAIKAN: Cek apakah ini berasal dari hierarki normal (dengan relasi) atau pencarian
                            if ($menu->relationLoaded('children') && $menu->children->count() > 0) {
                                // Mode hierarki normal
                                $subMenus = $menu->children;
                            } else {
                                // Mode pencarian
                                $subMenus = $webMenuGlobals->filter(function($item) use ($menu) {
                                    return $item->wmg_parent_id === $menu->web_menu_global_id;
                                })->sortBy('wmg_urutan_menu');
                            }
                        @endphp
                        
                        @if($subMenus->count() > 0)
                            <div class="submenu-container mt-2 ml-4 menu-debug" id="submenu-{{ $menu->web_menu_global_id }}">
                                @foreach($subMenus as $subMenu)
                                    @php $processedMenus->push($subMenu->web_menu_global_id); @endphp
                                    
                                    <div class="menu-item sub-menu mb-2" data-menu-id="{{ $subMenu->web_menu_global_id }}">
                                        <div class="card border-left-info">
                                            <div class="card-body p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-level-up-alt text-muted mr-2" style="transform: rotate(90deg); font-size: 0.8em;"></i>
                                                            <i class="fas fa-file text-info mr-2"></i>
                                                            <div>
                                                                <h6 class="mb-0">{{ $subMenu->wmg_nama_default }}</h6>
                                                                <small class="text-muted">
                                                                    <span class="badge badge-info badge-sm">Sub Menu</span>
                                                                    • Urutan: {{ $subMenu->wmg_urutan_menu }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        @if($subMenu->fk_web_menu_url && isset($subMenu->WebMenuUrl))
                                                            <div>
                                                                <strong>{{ $subMenu->WebMenuUrl->wmu_nama }}</strong>
                                                                @if(isset($subMenu->WebMenuUrl->application))
                                                                    <br><small class="text-muted">{{ $subMenu->WebMenuUrl->application->app_nama }}</small>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="col-md-2 text-center">
                                                        <span class="badge badge-{{ $subMenu->wmg_status_menu === 'aktif' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($subMenu->wmg_status_menu) }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="col-md-3 text-right">
                                                        <div class="btn-group btn-group-sm">
                                                            @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'update'))
                                                                <button class="btn btn-warning btn-sm" onclick="modalAction('{{ url($webMenuGlobalPath . '/editData/' . $subMenu->web_menu_global_id) }}')">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endif
                                                            <button class="btn btn-info btn-sm" onclick="modalAction('{{ url($webMenuGlobalPath . '/detailData/' . $subMenu->web_menu_global_id) }}')">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'delete'))
                                                                <button class="btn btn-danger btn-sm" onclick="modalAction('{{ url($webMenuGlobalPath . '/deleteData/' . $subMenu->web_menu_global_id) }}')">
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
                    @endif
                </div>
            @endif
        @endforeach
        
        {{-- Menu yang belum diproses (untuk kasus pencarian submenu) --}}
        @foreach($webMenuGlobals as $menu)
            @if(!$processedMenus->contains($menu->web_menu_global_id))
                <div class="menu-item standalone-menu mb-3" data-menu-id="{{ $menu->web_menu_global_id }}">
                    <div class="card border-left-warning">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file text-warning mr-2"></i>
                                        <div>
                                            <h6 class="mb-0">{{ $menu->wmg_nama_default }}</h6>
                                            <small class="text-muted">
                                                <span class="badge badge-warning badge-sm">{{ $menu->wmg_kategori_menu }}</span>
                                                • Urutan: {{ $menu->wmg_urutan_menu }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    @if($menu->fk_web_menu_url && isset($menu->WebMenuUrl))
                                        <div>
                                            <strong>{{ $menu->WebMenuUrl->wmu_nama }}</strong>
                                            @if(isset($menu->WebMenuUrl->application))
                                                <br><small class="text-muted">{{ $menu->WebMenuUrl->application->app_nama }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-2 text-center">
                                    <span class="badge badge-{{ $menu->wmg_status_menu === 'aktif' ? 'success' : 'danger' }}">
                                        {{ ucfirst($menu->wmg_status_menu) }}
                                    </span>
                                </div>
                                
                                <div class="col-md-3 text-right">
                                    <div class="btn-group btn-group-sm">
                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'update'))
                                            <button class="btn btn-warning" onclick="modalAction('{{ url($webMenuGlobalPath . '/editData/' . $menu->web_menu_global_id) }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-info" onclick="modalAction('{{ url($webMenuGlobalPath . '/detailData/' . $menu->web_menu_global_id) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'delete'))
                                            <button class="btn btn-danger" onclick="modalAction('{{ url($webMenuGlobalPath . '/deleteData/' . $menu->web_menu_global_id) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">
                @if(!empty($search))
                    Tidak ada menu yang cocok dengan pencarian "{{ $search }}"
                @else
                    Tidak ada data menu global
                @endif
            </h5>
        </div>
    @endif
</div>