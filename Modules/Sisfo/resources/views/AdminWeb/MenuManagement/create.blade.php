@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
@endphp

@extends('sisfo::layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm menu-page-hero">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Menu Aplikasi
                    </h5>
                    <small>Level: <strong>{{ $level->hak_akses_nama }}</strong></small>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}" 
                       class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="info-message mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <h6><i class="fas fa-info-circle mr-2"></i>Panduan Pengaturan Menu</h6>
                        <ul class="mb-0 pl-3">
                            <li>Centang <strong>"Menu"</strong> untuk menampilkan menu di sidebar</li>
                            <li>Centang <strong>"Lihat"</strong> untuk memberikan akses melihat halaman</li>
                            <li>Centang <strong>"Tambah/Ubah/Hapus"</strong> untuk memberikan akses operasi data</li>
                            <li>Gunakan <strong>"Alias"</strong> untuk mengubah nama tampilan menu (opsional)</li>
                            <li>Toggle <strong>"Status"</strong> untuk mengaktifkan/nonaktifkan menu</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="menu-statistics">
                            <h6><i class="fas fa-chart-pie mr-2"></i>Statistik Menu</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat-card bg-primary text-white rounded p-2 mb-2">
                                        <div class="stat-number">{{ $menuGlobal->count() }}</div>
                                        <small class="stat-label">Total Menu</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    @php
                                        $createdMenusCount = WebMenuModel::where('fk_m_hak_akses', $level->hak_akses_id)
                                            ->where('isDeleted', 0)
                                            ->count();
                                    @endphp
                                    <div class="stat-card bg-success text-white rounded p-2 mb-2">
                                        <div class="stat-number">{{ $createdMenusCount }}</div>
                                        <small class="stat-label">Menu Dibuat</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                @php
                                    $percentage = $menuGlobal->count() > 0 ? round(($createdMenusCount / $menuGlobal->count()) * 100, 1) : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ $percentage }}%" 
                                    aria-valuenow="{{ $percentage }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted d-block text-center mt-1">
                                Kelengkapan: {{ $percentage }}%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="setMenuForm" method="POST" action="{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/createData') }}" class="menu-management-form">
        @csrf
        <input type="hidden" name="hak_akses_id" value="{{ $level->hak_akses_id }}">
        
        @php $menuIndex = 0; @endphp
        
        @foreach($menuGlobal as $menu)
            @if(is_null($menu->wmg_parent_id))
                @if(is_null($menu->fk_web_menu_url))
                    <div class="card shadow-sm mb-4 menu-group-card">
                        <div class="card-header bg-gradient-secondary text-white">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <span class="badge badge-light badge-lg">{{ ++$menuIndex }}</span>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="mb-0">
                                        <i class="fas fa-folder-open mr-2"></i>
                                        {{ $menu->wmg_nama_default }}
                                    </h5>
                                    <small class="text-light">Grup Menu</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="text-light mb-1">Nama Alias</label>
                                        <input type="text" class="form-control form-control-sm track-change" 
                                               data-menu-id="{{ $menu->web_menu_global_id }}"
                                               name="menus[{{ $menu->web_menu_global_id }}][alias]" 
                                               placeholder="Masukkan nama alias (opsional)"
                                               value="{{ $existingMenus[$menu->web_menu_global_id]['alias'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    @php
                                        $defaultStatus = $existingMenus[$menu->web_menu_global_id]['status'] ?? $menu->wmg_status_menu;
                                    @endphp
                                    <label class="text-light d-block mb-1">Status Menu</label>
                                    <button type="button" class="btn btn-sm status-toggle track-change {{ $defaultStatus == 'aktif' ? 'btn-success' : 'btn-danger' }}"
                                            data-menu-id="{{ $menu->web_menu_global_id }}">
                                        <i class="fas fa-{{ $defaultStatus == 'aktif' ? 'check' : 'times' }} mr-1"></i>
                                        {{ $defaultStatus == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][menu_global_id]" value="{{ $menu->web_menu_global_id }}">
                            <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][type]" value="group">
                            <input type="hidden" class="menu-modified" name="menus[{{ $menu->web_menu_global_id }}][modified]" value="0">
                            <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][status]" value="{{ $defaultStatus }}">
                        </div>
                        
                        @php 
                            $subMenus = $menuGlobal->where('wmg_parent_id', $menu->web_menu_global_id); 
                            $subIndex = 1;
                        @endphp
                        @if($subMenus->count() > 0)
                            <div class="card-body">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-list mr-2"></i>Sub Menu ({{ $subMenus->count() }} item)
                                </h6>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="20%">Nama Menu</th>
                                                <th width="15%">Alias</th>
                                                <th width="30%" class="text-center">Hak Akses</th>
                                                <th width="15%" class="text-center">Akses Cepat</th>
                                                <th width="15%" class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subMenus as $subMenu)
                                                @php
                                                    $subDefaultStatus = $existingMenus[$subMenu->web_menu_global_id]['status'] ?? $subMenu->wmg_status_menu;
                                                    $permissions = $existingMenus[$subMenu->web_menu_global_id]['permissions'] ?? [];
                                                @endphp
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-primary">{{ $subIndex++ }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <div>
                                                            <strong>{{ $subMenu->wmg_nama_default }}</strong>
                                                            @if($subMenu->fk_web_menu_url)
                                                                @php
                                                                    $webMenuUrl = WebMenuUrlModel::find($subMenu->fk_web_menu_url);
                                                                @endphp
                                                                @if($webMenuUrl)
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-link mr-1"></i>
                                                                        URL: <code>{{ $webMenuUrl->wmu_nama }}</code>
                                                                    </small>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][menu_global_id]" value="{{ $subMenu->web_menu_global_id }}">
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][type]" value="sub">
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][parent_id]" value="{{ $menu->web_menu_global_id }}">
                                                        <input type="hidden" class="menu-modified" name="menus[{{ $subMenu->web_menu_global_id }}][modified]" value="0">
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" class="form-control form-control-sm track-change" 
                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                               name="menus[{{ $subMenu->web_menu_global_id }}][alias]" 
                                                               placeholder="Nama alias..."
                                                               value="{{ $existingMenus[$subMenu->web_menu_global_id]['alias'] ?? '' }}">
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="permission-group" data-menu-id="{{ $subMenu->web_menu_global_id }}">
                                                            <div class="row text-center">
                                                                <div class="col">
                                                                    <small class="d-block text-muted mb-1">Menu</small>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                               data-level="1"
                                                                               id="menu_{{ $subMenu->web_menu_global_id }}" 
                                                                               name="menus[{{ $subMenu->web_menu_global_id }}][permissions][menu]"
                                                                               {{ in_array('menu', $permissions) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="menu_{{ $subMenu->web_menu_global_id }}"></label>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <small class="d-block text-muted mb-1">Lihat</small>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                               data-level="2"
                                                                               id="view_{{ $subMenu->web_menu_global_id }}" 
                                                                               name="menus[{{ $subMenu->web_menu_global_id }}][permissions][view]"
                                                                               {{ in_array('view', $permissions) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="view_{{ $subMenu->web_menu_global_id }}"></label>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <small class="d-block text-muted mb-1">Tambah</small>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                               data-level="3"
                                                                               id="create_{{ $subMenu->web_menu_global_id }}" 
                                                                               name="menus[{{ $subMenu->web_menu_global_id }}][permissions][create]"
                                                                               {{ in_array('create', $permissions) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="create_{{ $subMenu->web_menu_global_id }}"></label>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <small class="d-block text-muted mb-1">Ubah</small>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                               data-level="3"
                                                                               id="update_{{ $subMenu->web_menu_global_id }}" 
                                                                               name="menus[{{ $subMenu->web_menu_global_id }}][permissions][update]"
                                                                               {{ in_array('update', $permissions) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="update_{{ $subMenu->web_menu_global_id }}"></label>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <small class="d-block text-muted mb-1">Hapus</small>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                               data-level="3"
                                                                               id="delete_{{ $subMenu->web_menu_global_id }}" 
                                                                               name="menus[{{ $subMenu->web_menu_global_id }}][permissions][delete]"
                                                                               {{ in_array('delete', $permissions) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="delete_{{ $subMenu->web_menu_global_id }}"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="btn-group-vertical btn-group-sm">
                                                            <button type="button" class="btn btn-outline-success btn-sm quick-select-all" data-menu-id="{{ $subMenu->web_menu_global_id }}">
                                                                <i class="fas fa-check-square"></i> Pilih Semua
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger btn-sm quick-clear-all" data-menu-id="{{ $subMenu->web_menu_global_id }}">
                                                                <i class="fas fa-times"></i> Bersihkan
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm status-toggle track-change {{ $subDefaultStatus == 'aktif' ? 'btn-success' : 'btn-danger' }}"
                                                                data-menu-id="{{ $subMenu->web_menu_global_id }}">
                                                            <i class="fas fa-{{ $subDefaultStatus == 'aktif' ? 'check' : 'times' }} mr-1"></i>
                                                            {{ $subDefaultStatus == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                                        </button>
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][status]" value="{{ $subDefaultStatus }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="card-body text-center text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada sub menu untuk grup ini</p>
                            </div>
                        @endif
                    </div>
                @else
                    @php
                        $singleDefaultStatus = $existingMenus[$menu->web_menu_global_id]['status'] ?? $menu->wmg_status_menu;
                        $permissions = $existingMenus[$menu->web_menu_global_id]['permissions'] ?? [];
                    @endphp
                    <div class="card shadow-sm mb-3 menu-single-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-1 text-center">
                                    <span class="badge badge-info badge-lg">{{ ++$menuIndex }}</span>
                                </div>
                                <div class="col-md-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-file-alt mr-2 text-primary"></i>
                                            {{ $menu->wmg_nama_default }}
                                        </h6>
                                        @if($menu->fk_web_menu_url)
                                            @php
                                                $webMenuUrl = WebMenuUrlModel::find($menu->fk_web_menu_url);
                                            @endphp
                                            @if($webMenuUrl)
                                                <small class="text-muted">
                                                    <i class="fas fa-link mr-1"></i>
                                                    URL: <code>{{ $webMenuUrl->wmu_nama }}</code>
                                                </small>
                                                <br>
                                            @endif
                                        @endif
                                        <small class="text-muted">Menu Biasa</small>
                                    </div>
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][menu_global_id]" value="{{ $menu->web_menu_global_id }}">
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][type]" value="single">
                                    <input type="hidden" class="menu-modified" name="menus[{{ $menu->web_menu_global_id }}][modified]" value="0">
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="text-muted mb-1">Nama Alias</label>
                                        <input type="text" class="form-control form-control-sm track-change" 
                                               data-menu-id="{{ $menu->web_menu_global_id }}"
                                               name="menus[{{ $menu->web_menu_global_id }}][alias]" 
                                               placeholder="Nama alias..."
                                               value="{{ $existingMenus[$menu->web_menu_global_id]['alias'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted d-block mb-2">Hak Akses</label>
                                    <div class="permission-group" data-menu-id="{{ $menu->web_menu_global_id }}">
                                        <div class="row text-center">
                                            <div class="col">
                                                <small class="d-block text-muted mb-1">Menu</small>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                                           data-level="1"
                                                           id="menu_{{ $menu->web_menu_global_id }}" 
                                                           name="menus[{{ $menu->web_menu_global_id }}][permissions][menu]"
                                                           {{ in_array('menu', $permissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="menu_{{ $menu->web_menu_global_id }}"></label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <small class="d-block text-muted mb-1">Lihat</small>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                                           data-level="2"
                                                           id="view_{{ $menu->web_menu_global_id }}" 
                                                           name="menus[{{ $menu->web_menu_global_id }}][permissions][view]"
                                                           {{ in_array('view', $permissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="view_{{ $menu->web_menu_global_id }}"></label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <small class="d-block text-muted mb-1">Tambah</small>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                                           data-level="3"
                                                           id="create_{{ $menu->web_menu_global_id }}" 
                                                           name="menus[{{ $menu->web_menu_global_id }}][permissions][create]"
                                                           {{ in_array('create', $permissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="create_{{ $menu->web_menu_global_id }}"></label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <small class="d-block text-muted mb-1">Ubah</small>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                                           data-level="3"
                                                           id="update_{{ $menu->web_menu_global_id }}" 
                                                           name="menus[{{ $menu->web_menu_global_id }}][permissions][update]"
                                                           {{ in_array('update', $permissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="update_{{ $menu->web_menu_global_id }}"></label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <small class="d-block text-muted mb-1">Hapus</small>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                                           data-level="3"
                                                           id="delete_{{ $menu->web_menu_global_id }}" 
                                                           name="menus[{{ $menu->web_menu_global_id }}][permissions][delete]"
                                                           {{ in_array('delete', $permissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="delete_{{ $menu->web_menu_global_id }}"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <label class="text-muted d-block mb-1">Akses Cepat</label>
                                    <div class="btn-group-vertical btn-group-sm mb-2">
                                        <button type="button" class="btn btn-outline-success btn-sm quick-select-all" data-menu-id="{{ $menu->web_menu_global_id }}">
                                            <i class="fas fa-check-square"></i> Pilih Semua
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm quick-clear-all" data-menu-id="{{ $menu->web_menu_global_id }}">
                                            <i class="fas fa-times"></i> Bersihkan
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1 text-center">
                                    <label class="text-muted d-block mb-1">Status</label>
                                    <button type="button" class="btn btn-sm status-toggle track-change {{ $singleDefaultStatus == 'aktif' ? 'btn-success' : 'btn-danger' }}"
                                            data-menu-id="{{ $menu->web_menu_global_id }}">
                                        <i class="fas fa-{{ $singleDefaultStatus == 'aktif' ? 'check' : 'times' }} mr-1"></i>
                                        {{ $singleDefaultStatus == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][status]" value="{{ $singleDefaultStatus }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
        
        <div class="floating-action-spacer" aria-hidden="true"></div>

        <div class="floating-action-bar" role="region" aria-label="Aksi formulir menu">
            <div class="floating-action-inner">
                <button type="button" id="toggleActionBarBtn" class="btn btn-light btn-sm floating-hide-btn" title="Sembunyikan panel aksi" aria-label="Sembunyikan panel aksi">
                    <i class="fas fa-angle-right"></i>
                </button>

                <button type="submit" id="saveMenuBtn" class="btn btn-save btn-sm px-4 py-2">
                    <i class="fas fa-save mr-2"></i> Simpan Semua Menu
                </button>
                <button type="button" id="resetMenuConfigBtn" class="btn btn-reset btn-sm px-4 py-2">
                    <i class="fas fa-undo-alt mr-2"></i> Reset Konfigurasi Menu
                </button>
            </div>

            <button type="button" id="showActionBarBtn" class="floating-mini-fab" title="Tampilkan panel aksi" aria-label="Tampilkan panel aksi" style="display: none;">
                <i class="fas fa-angle-left"></i>
                <span class="mini-badge" id="hiddenDirtyBadge" style="display: none;">!</span>
            </button>

            <div id="customDirtyTooltip" class="custom-dirty-tooltip" style="display: none;" role="status" aria-live="polite">
                <span class="tooltip-text">Ada perubahan belum disimpan</span>
                <button type="button" id="closeDirtyTooltipBtn" class="tooltip-close-btn" aria-label="Sembunyikan tooltip perubahan">
                    <i class="fas fa-times"></i>
                </button>
                <span class="custom-dirty-tooltip-arrow"></span>
            </div>
        </div>
    </form>
</div>
@endsection

@push('css')
<style>
    .menu-management-form {position: relative;}
    .menu-page-hero {
        border: 1px solid #bfdbfe;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 10px 22px rgba(30, 64, 175, 0.12);
        background: #f8fbff;
    }
    .menu-page-hero .card-header.bg-primary {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #3b82f6 100%) !important;
        border-bottom: 1px solid rgba(255,255,255,0.15);
    }
    .menu-page-hero .card-header .btn-light {
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
        font-weight: 600;
    }
    .menu-page-hero .card-body {
        background: linear-gradient(180deg, #eef5ff 0%, #ffffff 70%);
    }
    .status-toggle {min-width: 100px; transition: all 0.3s ease;}
    .status-toggle:hover {transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1);}
    .badge-lg {font-size: 0.9rem; padding: 0.5rem 0.75rem;}
    .permission-group {background: #f8f9fa; border-radius: 0.25rem; padding: 0.5rem;}
    .permission-group .col {padding: 0.25rem;}
    .custom-control-label::before {border-radius: 0.25rem;}
    .custom-control-input:checked ~ .custom-control-label::before {background-color: #007bff; border-color: #007bff;}
    .card {transition: all 0.25s ease;}
    .card:hover {transform: translateY(-1px); box-shadow: 0 6px 14px rgba(15, 23, 42, 0.1);}
    .table th {border-top: none; font-weight: 600; color: #495057;}
    .table-hover tbody tr:hover {background-color: rgba(30, 64, 175, 0.06);}
    .bg-gradient-secondary {background: linear-gradient(135deg, #6c757d 0%, #495057 100%);}
    .menu-statistics {background: linear-gradient(180deg, #ffffff 0%, #f0f7ff 100%); border-radius: 0.65rem; padding: 1rem; border: 1px solid #bfdbfe; box-shadow: 0 4px 12px rgba(29, 78, 216, 0.1);}
    .stat-card {transition: all 0.3s ease; cursor: default; border: 1px solid rgba(255,255,255,0.2);}
    .stat-card:hover {transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15);}
    .stat-number {font-size: 1.5rem; font-weight: bold; line-height: 1;}
    .stat-label {font-size: 0.75rem; opacity: 0.9; display: block; margin-top: 0.25rem;}
    .progress-sm {height: 0.5rem; margin-top: 0.5rem;}
    .progress-bar {transition: width 0.6s ease;}
    .info-message {
        background: linear-gradient(135deg, #123e7a 0%, #1d4f91 50%, #2364ba 100%);
        border: 1px solid #1d4f91;
        border-radius: 0.65rem;
        padding: 1.25rem;
        box-shadow: 0 10px 22px rgba(18, 62, 122, 0.28);
        color: #f8fbff;
    }
    .info-message h6,
    .info-message li,
    .info-message small,
    .info-message .text-muted,
    .info-message strong,
    .info-message i {
        color: #f8fbff !important;
    }
    .menu-group-card,
    .menu-single-card {
        border: 1px solid #dbeafe;
        border-radius: 0.7rem;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }
    .menu-group-card .card-header.bg-gradient-secondary {
        background: linear-gradient(135deg, #475569 0%, #64748b 45%, #7b8ba0 100%) !important;
        border-bottom: 1px solid rgba(255,255,255,0.12);
    }
    .menu-single-card {
        border-left: 4px solid #2563eb;
        background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
    }
    .menu-single-card .card-body {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .menu-group-card + .menu-single-card,
    .menu-single-card + .menu-group-card,
    .menu-single-card + .menu-single-card,
    .menu-group-card + .menu-group-card {
        margin-top: 1.1rem;
    }
    .menu-group-card .table,
    .menu-single-card .table {
        border-color: #dbe5f1;
    }
    .menu-group-card .table thead th,
    .menu-single-card .table thead th {
        background: #edf4ff;
        color: #1f3b6f;
        border-bottom: 1px solid #cddcf5;
    }
    .quick-clear-all.btn-outline-danger {
        color: #dc2626;
        border-color: #fca5a5;
        background: #fff5f5;
    }
    .quick-clear-all.btn-outline-danger:hover,
    .quick-clear-all.btn-outline-danger:focus {
        color: #ffffff;
        border-color: #dc2626;
        background: #dc2626;
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
    }
    .floating-action-spacer {
        height: 98px;
    }
    .floating-action-bar {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1040;
        pointer-events: none;
        padding: 0 1rem 1rem;
    }
    .floating-action-inner {
        margin-left: 250px;
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 0.75rem;
        box-shadow: 0 -6px 20px rgba(0, 0, 0, 0.14);
        padding: 0.62rem 0.72rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.42rem;
        pointer-events: auto;
        width: fit-content;
        max-width: calc(100vw - 280px);
        margin-right: 1rem;
        margin-left: auto;
    }
    .floating-action-inner .btn {
        min-height: 34px;
    }
    .floating-action-inner .btn-save,
    .floating-action-inner .btn-reset {
        font-size: 0.88rem;
        font-weight: 600;
    }
    .btn-save {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        border: 1px solid #0f6d33;
        box-shadow: 0 6px 14px rgba(21, 128, 61, 0.3);
    }
    .btn-save:hover,
    .btn-save:focus {
        background: linear-gradient(135deg, #15803d, #166534);
        color: #fff;
    }
    #saveMenuBtn {
        position: relative;
        overflow: visible;
    }
    #saveMenuBtn.has-dirty-indicator::after {
        content: '';
        position: absolute;
        top: -6px;
        right: -6px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ef4444;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.45);
    }
    .btn-reset {
        background: linear-gradient(135deg, #facc15, #eab308);
        color: #4a3300;
        border: 1px solid #d4a106;
        box-shadow: 0 6px 14px rgba(234, 179, 8, 0.28);
    }
    .btn-reset:hover,
    .btn-reset:focus {
        background: linear-gradient(135deg, #eab308, #ca8a04);
        color: #3b2a00;
    }
    .floating-hide-btn {
        width: 30px;
        min-width: 30px;
        min-height: 34px;
        padding: 0;
        border-radius: 0.5rem;
        border: 1px solid #1d4ed8;
        color: #ffffff;
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
    }
    .floating-hide-btn:hover,
    .floating-hide-btn:focus {
        color: #ffffff;
        background: linear-gradient(135deg, #1e40af, #1d4ed8);
    }
    .floating-mini-fab {
        position: absolute;
        right: 1rem;
        bottom: 1rem;
        border: 0;
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        color: #fff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        justify-content: center;
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.38);
        font-size: 1rem;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        pointer-events: auto;
    }
    .floating-mini-fab:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.45);
    }
    .floating-mini-fab .mini-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 22px;
        height: 22px;
        line-height: 22px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        text-align: center;
        font-size: 0.78rem;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.45);
    }
    .custom-dirty-tooltip {
        position: fixed;
        z-index: 1060;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #14532d;
        border: 1px solid #86efac;
        border-radius: 0.65rem;
        padding: 0.4rem 0.62rem 0.4rem 0.72rem;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 10px 20px rgba(22, 163, 74, 0.22);
        pointer-events: auto;
        transition: opacity 0.18s ease, transform 0.18s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .tooltip-text {
        line-height: 1;
    }
    .tooltip-close-btn {
        border: 0;
        background: rgba(20, 83, 45, 0.12);
        color: #14532d;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.62rem;
        cursor: pointer;
    }
    .tooltip-close-btn:hover,
    .tooltip-close-btn:focus {
        background: rgba(20, 83, 45, 0.2);
        color: #0f3b22;
        outline: none;
    }
    .custom-dirty-tooltip-arrow {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: -10px;
        width: 10px;
        height: 10px;
        background: #bbf7d0;
        border-right: 1px solid #86efac;
        border-bottom: 1px solid #86efac;
        transform-origin: center;
        rotate: 45deg;
    }
    .floating-action-bar.is-hidden .floating-action-inner {
        display: none;
    }
    @media (max-width: 768px) {
        .menu-statistics {margin-top: 1rem;}
        .stat-number {font-size: 1.25rem;}
        .floating-action-spacer {
            height: 118px;
        }
        .floating-action-bar {
            padding: 0 0.75rem 0.75rem;
        }
        .floating-action-inner {
            margin-left: 0;
            flex-direction: column;
            align-items: stretch;
            width: calc(100% - 0.5rem);
            max-width: none;
            margin-right: 0;
        }
        .floating-action-inner .btn {
            min-width: 0;
            width: 100%;
            margin-left: 0 !important;
        }
        .floating-hide-btn {
            width: 100%;
        }
        .floating-mini-fab {
            right: 0.75rem;
            bottom: 0.75rem;
        }
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    let changeNotificationShown = false;
    let isDirty = false;
    let tooltipDismissed = false;

    function normalizeInputValue($el) {
        const type = ($el.attr('type') || '').toLowerCase();
        if (type === 'checkbox' || type === 'radio') {
            return $el.prop('checked') ? '1' : '0';
        }
        return $el.val();
    }

    function setStatusButtonState($btn, status) {
        if (status === 'aktif') {
            $btn.removeClass('btn-danger').addClass('btn-success');
            $btn.html('<i class="fas fa-check mr-1"></i>Aktif');
        } else {
            $btn.removeClass('btn-success').addClass('btn-danger');
            $btn.html('<i class="fas fa-times mr-1"></i>Nonaktif');
        }
    }

    function snapshotInitialState() {
        $('.track-change').each(function() {
            const $el = $(this);
            $el.attr('data-initial-value', normalizeInputValue($el));
        });

        $('.status-toggle').each(function() {
            const $btn = $(this);
            const menuId = $btn.data('menu-id');
            const statusVal = $('input[name="menus[' + menuId + '][status]"]').val() || ($btn.hasClass('btn-success') ? 'aktif' : 'nonaktif');
            $btn.attr('data-initial-status', statusVal);
        });

        $('.menu-modified').each(function() {
            $(this).attr('data-initial-modified', $(this).val() || '0');
        });

        refreshDirtyState();
    }

    function updateCustomDirtyTooltipPosition() {
        const $tooltip = $('#customDirtyTooltip');
        const isHiddenMode = $('.floating-action-bar').hasClass('is-hidden');

        // Saat hidden mode: tooltip disembunyikan agar clean, cukup badge merah saja.
        if (!isDirty || isHiddenMode || tooltipDismissed) {
            $tooltip.hide();
            return;
        }

        const $target = $('#saveMenuBtn');
        if ($target.length === 0 || !$target.is(':visible')) {
            $tooltip.hide();
            return;
        }

        $tooltip.show();

        const targetRect = $target[0].getBoundingClientRect();
        const tooltipEl = $tooltip[0];
        const tooltipWidth = tooltipEl.offsetWidth;
        const tooltipHeight = tooltipEl.offsetHeight;
        const margin = 12;

        // Tooltip diposisikan tepat di tengah tombol simpan
        let left = targetRect.left + (targetRect.width / 2) - (tooltipWidth / 2);
        left = Math.max(margin, Math.min(left, window.innerWidth - tooltipWidth - margin));

        // Naikkan tooltip agar panah tidak terlalu mepet tombol
        let top = targetRect.top - tooltipHeight - 12;
        if (top < margin) {
            top = margin;
        }

        $tooltip.css({
            left: `${left}px`,
            top: `${top}px`
        });
    }

    function refreshDirtyState() {
        let dirtyFound = false;

        $('.track-change').each(function() {
            const $el = $(this);
            const current = normalizeInputValue($el);
            const initial = $el.attr('data-initial-value');
            if (current !== initial) {
                dirtyFound = true;
                return false;
            }
        });

        if (!dirtyFound) {
            $('.status-toggle').each(function() {
                const $btn = $(this);
                const menuId = $btn.data('menu-id');
                const currentStatus = $('input[name="menus[' + menuId + '][status]"]').val() || ($btn.hasClass('btn-success') ? 'aktif' : 'nonaktif');
                const initialStatus = $btn.attr('data-initial-status') || 'nonaktif';
                if (currentStatus !== initialStatus) {
                    dirtyFound = true;
                    return false;
                }
            });
        }

        isDirty = dirtyFound;

        const $hiddenBadge = $('#hiddenDirtyBadge');
        const $saveBtn = $('#saveMenuBtn');

        if (isDirty) {
            $hiddenBadge.show();
            $saveBtn.addClass('has-dirty-indicator');
        } else {
            $hiddenBadge.hide();
            $saveBtn.removeClass('has-dirty-indicator');
            tooltipDismissed = false;
        }

        updateCustomDirtyTooltipPosition();
    }
    
    $('.track-change').on('change', function() {
        var menuId = $(this).data('menu-id');
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
        $(this).closest('.card').addClass('border-warning');
        
        if (!changeNotificationShown) {
            toastr.warning('Jangan lupa simpan perubahan Anda', 'Perubahan terdeteksi');
            changeNotificationShown = true;
            setTimeout(() => { changeNotificationShown = false; }, 3000);
        }

        refreshDirtyState();
    });
    
    $('.status-toggle').on('click', function() {
        var $btn = $(this);
        var menuId = $btn.data('menu-id');
        var $hiddenInput = $('input[name="menus[' + menuId + '][status]"]');
        
        $btn.prop('disabled', true);
        
        setTimeout(function() {
            if ($btn.hasClass('btn-success')) {
                $btn.removeClass('btn-success').addClass('btn-danger');
                $btn.html('<i class="fas fa-times mr-1"></i>Nonaktif');
                $hiddenInput.val('nonaktif');
            } else {
                $btn.removeClass('btn-danger').addClass('btn-success');
                $btn.html('<i class="fas fa-check mr-1"></i>Aktif');
                $hiddenInput.val('aktif');
            }
            
            $('input[name="menus[' + menuId + '][modified]"]').val('1');
            $btn.closest('.card').addClass('border-warning');
            $btn.prop('disabled', false);
            refreshDirtyState();
        }, 200);
    });
    
    $('.permission-checkbox').on('change', function() {
        var $this = $(this);
        var menuId = $this.data('menu-id');
        var level = parseInt($this.data('level'));
        var isChecked = $this.is(':checked');
        
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
        $this.closest('.card').addClass('border-warning');
        
        $('.permission-checkbox[data-menu-id="' + menuId + '"]').off('change.hierarchy');
        
        if (!isChecked) {
            $('input.permission-checkbox[data-menu-id="' + menuId + '"]').each(function() {
                if (parseInt($(this).data('level')) > level) {
                    $(this).prop('checked', false);
                }
            });
        }
        
        if (isChecked) {
            $('input.permission-checkbox[data-menu-id="' + menuId + '"]').each(function() {
                if (parseInt($(this).data('level')) < level) {
                    $(this).prop('checked', true);
                }
            });
        }
        
        setTimeout(() => {
            $('.permission-checkbox[data-menu-id="' + menuId + '"]').on('change.hierarchy', function() {
                $(this).trigger('change');
            });
            refreshDirtyState();
        }, 100);
    });
    
    $('#setMenuForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Apakah Anda yakin ingin menyimpan semua perubahan?')) {
            return;
        }
        
        var formData = new FormData(this);
        
        $('.permission-checkbox').each(function() {
            var $checkbox = $(this);
            var name = $checkbox.attr('name');
            
            if (name) {
                formData.delete(name);
                if ($checkbox.is(':checked')) {
                    formData.append(name, '1');
                }
            }
        });
        
        $('.status-toggle').each(function() {
            var $btn = $(this);
            var menuId = $btn.data('menu-id');
            var hiddenInputName = 'menus[' + menuId + '][status]';
            
            formData.delete(hiddenInputName);
            formData.append(hiddenInputName, $btn.hasClass('btn-success') ? 'aktif' : 'nonaktif');
        });
        
        var $submitBtn = $('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
        
        toastr.info('Sedang menyimpan perubahan...', 'Proses Simpan');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message, 'Berhasil!');
                    $('.border-warning').removeClass('border-warning');
                    
                    setTimeout(function() {
                        window.location.href = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}";
                    }, 1500);
                } else {
                    toastr.error(response.message, 'Gagal!');
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan Semua Menu');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Terjadi kesalahan saat menyimpan menu';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                toastr.error(errorMsg, 'Error!');
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan Semua Menu');
            }
        });
    });
    
    $('.quick-select-all').on('click', function() {
        const menuId = $(this).data('menu-id');
        const $btn = $(this);
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        
        const $checkboxes = $(`input.permission-checkbox[data-menu-id="${menuId}"]`);
        $checkboxes.off('change.hierarchy').prop('checked', true);
        
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
        $(this).closest('.card').addClass('border-warning');
        
        toastr.success('Semua hak akses untuk menu ini telah dipilih', 'Pilih Semua');
        
        setTimeout(() => {
            $btn.prop('disabled', false).html('<i class="fas fa-check-square"></i> Pilih Semua');
            $checkboxes.on('change.hierarchy', function() { $(this).trigger('change'); });
            refreshDirtyState();
        }, 500);
    });
    
    $('.quick-clear-all').on('click', function() {
        const menuId = $(this).data('menu-id');
        const $btn = $(this);
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Membersihkan...');
        
        const $checkboxes = $(`input.permission-checkbox[data-menu-id="${menuId}"]`);
        $checkboxes.off('change.hierarchy').prop('checked', false);
        
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
        $(this).closest('.card').addClass('border-warning');
        
        toastr.info('Semua hak akses untuk menu ini telah dibersihkan', 'Bersihkan');
        
        setTimeout(() => {
            $btn.prop('disabled', false).html('<i class="fas fa-times"></i> Bersihkan');
            $checkboxes.on('change.hierarchy', function() { $(this).trigger('change'); });
            refreshDirtyState();
        }, 500);
    });

    $('#resetMenuConfigBtn').on('click', function() {
        if (!confirm('Reset semua konfigurasi ke kondisi awal saat halaman dibuka?')) {
            return;
        }

        $('.track-change').each(function() {
            const $el = $(this);
            const initialValue = $el.attr('data-initial-value');
            const type = ($el.attr('type') || '').toLowerCase();

            if (type === 'checkbox' || type === 'radio') {
                $el.prop('checked', initialValue === '1');
            } else {
                $el.val(initialValue);
            }
        });

        $('.status-toggle').each(function() {
            const $btn = $(this);
            const menuId = $btn.data('menu-id');
            const initialStatus = $btn.attr('data-initial-status') || 'nonaktif';

            setStatusButtonState($btn, initialStatus);
            $('input[name="menus[' + menuId + '][status]"]').val(initialStatus);
        });

        $('.menu-modified').each(function() {
            const initialModified = $(this).attr('data-initial-modified') || '0';
            $(this).val(initialModified);
        });

        $('.border-warning').removeClass('border-warning');
        toastr.info('Konfigurasi menu berhasil dikembalikan ke kondisi awal', 'Reset Berhasil');
        refreshDirtyState();
    });

    $('#toggleActionBarBtn').on('click', function() {
        $('.floating-action-bar').addClass('is-hidden');
        $('#showActionBarBtn').show();
        updateCustomDirtyTooltipPosition();
    });

    $('#showActionBarBtn').on('click', function() {
        $('.floating-action-bar').removeClass('is-hidden');
        $(this).hide();
        updateCustomDirtyTooltipPosition();
    });

    $('#closeDirtyTooltipBtn').on('click', function(e) {
        e.preventDefault();
        tooltipDismissed = true;
        $('#customDirtyTooltip').hide();
    });

    $(window).on('resize scroll', function() {
        updateCustomDirtyTooltipPosition();
    });

    snapshotInitialState();
});
</script>
@endpush
