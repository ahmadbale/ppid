@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

@extends('sisfo::layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Pembuatan Menu untuk Hak Akses ({{ $level->hak_akses_nama }})</h3>
        <div class="card-tools">
            <a href="{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button type="submit" form="setMenuForm" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>
    
    <form id="setMenuForm" method="POST" action="{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/store-set-menu') }}">
        @csrf
        <input type="hidden" name="hak_akses_id" value="{{ $level->hak_akses_id }}">
        
        <div class="card-body">
            @php $menuIndex = 0; @endphp
            
            @foreach($menuGlobal as $menu)
                @if(is_null($menu->wmg_parent_id))
                    @if(is_null($menu->fk_web_menu_url))
                        <!-- Group Menu -->
                        <div class="menu-group-container mb-4 border rounded p-3">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-1">
                                    <span class="badge badge-secondary">{{ ++$menuIndex }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>{{ $menu->wmg_nama_default }}</strong>
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][menu_global_id]" value="{{ $menu->web_menu_global_id }}">
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][type]" value="group">
                                    <input type="hidden" class="menu-modified" name="menus[{{ $menu->web_menu_global_id }}][modified]" value="0">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control track-change" 
                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                           name="menus[{{ $menu->web_menu_global_id }}][alias]" 
                                           placeholder="Masukkan Alias (Opsional)"
                                           value="{{ $existingMenus[$menu->web_menu_global_id]['alias'] ?? '' }}">
                                </div>
                                <div class="col-md-3 text-right">
                                    @php
                                        $defaultStatus = $existingMenus[$menu->web_menu_global_id]['status'] ?? $menu->wmg_status_menu;
                                    @endphp
                                    <button type="button" class="btn btn-sm status-toggle track-change {{ $defaultStatus == 'aktif' ? 'btn-success' : 'btn-danger' }}"
                                            data-menu-id="{{ $menu->web_menu_global_id }}">
                                        {{ $defaultStatus == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][status]" 
                                           value="{{ $defaultStatus }}">
                                </div>
                            </div>
                            
                            <!-- Sub Menu -->
                            @php 
                                $subMenus = $menuGlobal->where('wmg_parent_id', $menu->web_menu_global_id); 
                                $subIndex = 1;
                            @endphp
                            @if($subMenus->count() > 0)
                                <div class="sub-menu-container ml-5">
                                    <p class="font-weight-bold">Sub Menu</p>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="20%">Nama Menu</th>
                                                <th width="20%">Alias</th>
                                                <th width="35%">Tampil Menu | Lihat | Tambah | Ubah | Hapus</th>
                                                <th width="20%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subMenus as $subMenu)
                                                @php
                                                    $subDefaultStatus = $existingMenus[$subMenu->web_menu_global_id]['status'] ?? $subMenu->wmg_status_menu;
                                                @endphp
                                                <tr>
                                                    <td>{{ $subIndex++ }}</td>
                                                    <td>
                                                        {{ $subMenu->wmg_nama_default }}
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][menu_global_id]" value="{{ $subMenu->web_menu_global_id }}">
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][type]" value="sub">
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][parent_id]" value="{{ $menu->web_menu_global_id }}">
                                                        <input type="hidden" class="menu-modified" name="menus[{{ $subMenu->web_menu_global_id }}][modified]" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm track-change" 
                                                               data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                               name="menus[{{ $subMenu->web_menu_global_id }}][alias]" 
                                                               placeholder="Opsional"
                                                               value="{{ $existingMenus[$subMenu->web_menu_global_id]['alias'] ?? '' }}">
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $permissions = $existingMenus[$subMenu->web_menu_global_id]['permissions'] ?? [];
                                                        @endphp
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                   data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                   data-level="1"
                                                                   id="menu_{{ $subMenu->web_menu_global_id }}" 
                                                                   name="menus[{{ $subMenu->web_menu_global_id }}][permissions][menu]"
                                                                   {{ in_array('menu', $permissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="menu_{{ $subMenu->web_menu_global_id }}"></label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                   data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                   data-level="2"
                                                                   id="view_{{ $subMenu->web_menu_global_id }}" 
                                                                   name="menus[{{ $subMenu->web_menu_global_id }}][permissions][view]"
                                                                   {{ in_array('view', $permissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="view_{{ $subMenu->web_menu_global_id }}"></label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                   data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                   data-level="3"
                                                                   id="create_{{ $subMenu->web_menu_global_id }}" 
                                                                   name="menus[{{ $subMenu->web_menu_global_id }}][permissions][create]"
                                                                   {{ in_array('create', $permissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="create_{{ $subMenu->web_menu_global_id }}"></label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                   data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                   data-level="3"
                                                                   id="update_{{ $subMenu->web_menu_global_id }}" 
                                                                   name="menus[{{ $subMenu->web_menu_global_id }}][permissions][update]"
                                                                   {{ in_array('update', $permissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="update_{{ $subMenu->web_menu_global_id }}"></label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                                   data-menu-id="{{ $subMenu->web_menu_global_id }}"
                                                                   data-level="3"
                                                                   id="delete_{{ $subMenu->web_menu_global_id }}" 
                                                                   name="menus[{{ $subMenu->web_menu_global_id }}][permissions][delete]"
                                                                   {{ in_array('delete', $permissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="delete_{{ $subMenu->web_menu_global_id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm status-toggle track-change {{ $subDefaultStatus == 'aktif' ? 'btn-success' : 'btn-danger' }}"
                                                                data-menu-id="{{ $subMenu->web_menu_global_id }}">
                                                            {{ $subDefaultStatus == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                                        </button>
                                                        <input type="hidden" name="menus[{{ $subMenu->web_menu_global_id }}][status]" 
                                                               value="{{ $subDefaultStatus }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Menu Biasa -->
                        @php
                            $singleDefaultStatus = $existingMenus[$menu->web_menu_global_id]['status'] ?? $menu->wmg_status_menu;
                        @endphp
                        <div class="menu-item-container mb-3 border rounded p-3">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <span class="badge badge-secondary">{{ ++$menuIndex }}</span>
                                </div>
                                <div class="col-md-2">
                                    <strong>{{ $menu->wmg_nama_default }}</strong>
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][menu_global_id]" value="{{ $menu->web_menu_global_id }}">
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][type]" value="single">
                                    <input type="hidden" class="menu-modified" name="menus[{{ $menu->web_menu_global_id }}][modified]" value="0">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control track-change" 
                                           data-menu-id="{{ $menu->web_menu_global_id }}"
                                           name="menus[{{ $menu->web_menu_global_id }}][alias]" 
                                           placeholder="Alias (Opsional)"
                                           value="{{ $existingMenus[$menu->web_menu_global_id]['alias'] ?? '' }}">
                                </div>
                                <div class="col-md-4 text-center">
                                    <label class="mb-0">Tampil Menu | Lihat | Tambah | Ubah | Hapus</label>
                                    <div>
                                        @php
                                            $permissions = $existingMenus[$menu->web_menu_global_id]['permissions'] ?? [];
                                        @endphp
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                   data-menu-id="{{ $menu->web_menu_global_id }}"
                                                   data-level="1"
                                                   id="menu_{{ $menu->web_menu_global_id }}" 
                                                   name="menus[{{ $menu->web_menu_global_id }}][permissions][menu]"
                                                   {{ in_array('menu', $permissions) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="menu_{{ $menu->web_menu_global_id }}"></label>
                                        </div>
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                   data-menu-id="{{ $menu->web_menu_global_id }}"
                                                   data-level="2"
                                                   id="view_{{ $menu->web_menu_global_id }}" 
                                                   name="menus[{{ $menu->web_menu_global_id }}][permissions][view]"
                                                   {{ in_array('view', $permissions) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="view_{{ $menu->web_menu_global_id }}"></label>
                                        </div>
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                   data-menu-id="{{ $menu->web_menu_global_id }}"
                                                   data-level="3"
                                                   id="create_{{ $menu->web_menu_global_id }}" 
                                                   name="menus[{{ $menu->web_menu_global_id }}][permissions][create]"
                                                   {{ in_array('create', $permissions) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="create_{{ $menu->web_menu_global_id }}"></label>
                                        </div>
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input permission-checkbox track-change" 
                                                   data-menu-id="{{ $menu->web_menu_global_id }}"
                                                   data-level="3"
                                                   id="update_{{ $menu->web_menu_global_id }}" 
                                                   name="menus[{{ $menu->web_menu_global_id }}][permissions][update]"
                                                   {{ in_array('update', $permissions) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="update_{{ $menu->web_menu_global_id }}"></label>
                                        </div>
                                        <div class="custom-control custom-checkbox custom-control-inline">
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
                                <div class="col-md-2 text-center">
                                    <button type="button" class="btn btn-sm status-toggle track-change {{ $singleDefaultStatus == 'aktif' ? 'btn-success' : 'btn-danger' }}"
                                            data-menu-id="{{ $menu->web_menu_global_id }}">
                                        {{ $singleDefaultStatus == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                    <input type="hidden" name="menus[{{ $menu->web_menu_global_id }}][status]" 
                                           value="{{ $singleDefaultStatus }}">
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    </form>
</div>
@endsection

@push('css')
<style>
    .status-toggle {
        width: 80px;
    }
    
    .menu-group-container {
        background-color: #f8f9fa;
    }
    
    .sub-menu-container {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 5px;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Track changes
    $('.track-change').on('change', function() {
        var menuId = $(this).data('menu-id');
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
    });
    
    // Toggle status button
    $('.status-toggle').on('click', function() {
        var $btn = $(this);
        var menuId = $btn.data('menu-id');
        var $hiddenInput = $('input[name="menus[' + menuId + '][status]"]');
        
        if ($btn.hasClass('btn-success')) {
            $btn.removeClass('btn-success').addClass('btn-danger').text('Nonaktif');
            $hiddenInput.val('nonaktif');
        } else {
            $btn.removeClass('btn-danger').addClass('btn-success').text('Aktif');
            $hiddenInput.val('aktif');
        }
        
        // Mark as modified
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
    });
    
    // Permission checkbox hierarchy
    $('.permission-checkbox').on('change', function() {
        var $this = $(this);
        var menuId = $this.data('menu-id');
        var level = parseInt($this.data('level'));
        var isChecked = $this.is(':checked');
        
        // Mark as modified
        $('input[name="menus[' + menuId + '][modified]"]').val('1');
        
        // If unchecking
        if (!isChecked) {
            // Uncheck all lower level permissions
            $('input.permission-checkbox[data-menu-id="' + menuId + '"]').each(function() {
                var thisLevel = parseInt($(this).data('level'));
                if (thisLevel > level) {
                    $(this).prop('checked', false);
                }
            });
        }
        
        // If checking
        if (isChecked) {
            // Check all higher level permissions
            $('input.permission-checkbox[data-menu-id="' + menuId + '"]').each(function() {
                var thisLevel = parseInt($(this).data('level'));
                if (thisLevel < level) {
                    $(this).prop('checked', true);
                }
            });
        }
    });
    
    // Form submission
    $('#setMenuForm').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.href = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}";
                    }, 1500);
                } else {
                    toastr.error(response.message);
                    $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan saat menyimpan menu');
                $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
            }
        });
    });
});
</script>
@endpush