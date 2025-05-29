@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
@endphp

<!-- Modal Update Menu -->
<div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMenuForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <!-- Informasi Menu -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Menu Global</label>
                                <input type="text" class="form-control" id="edit_menu_global_name" readonly>
                            </div>
                        </div>

                        <!-- Alias -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Alias (Opsional)</label>
                                <input type="text" class="form-control" id="edit_alias" name="web_menu[wm_menu_nama]" placeholder="Masukkan alias menu">
                                <span class="text-muted">Kosongkan jika ingin menggunakan nama default</span>
                            </div>
                        </div>

                        <!-- Kategori Menu -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Kategori Menu</label>
                                <select class="form-control" id="edit_kategori_menu" name="kategori_menu">
                                    <!-- Options akan diisi secara dinamis -->
                                </select>
                                <span class="text-muted" id="kategori_info"></span>
                            </div>
                        </div>

                        <!-- Nama Group Menu (untuk sub menu) -->
                        <div class="col-md-12" id="edit_group_menu_container" style="display: none;">
                            <div class="form-group">
                                <label>Nama Group Menu</label>
                                <select class="form-control" id="edit_parent_id" name="web_menu[wm_parent_id]">
                                    <option value="">-- Pilih Group Menu --</option>
                                </select>
                                <div class="invalid-feedback">
                                    Nama group menu wajib dipilih untuk sub menu
                                </div>
                            </div>
                        </div>

                        <!-- Level Menu -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Level Menu</label>
                                <select class="form-control" id="edit_level_menu" name="web_menu[fk_m_hak_akses]" required>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->hak_akses_id }}">{{ $level->hak_akses_nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Level menu wajib dipilih
                                </div>
                            </div>
                        </div>

                        <!-- Hak Akses (tidak untuk group menu) -->
                        <div class="col-md-12" id="edit_permissions_container">
                            <div class="form-group">
                                <label>Hak Akses</label>
                                <div class="d-flex align-items-center">
                                    <label class="mb-0 mr-3">Tampil Menu | Lihat | Tambah | Ubah | Hapus</label>
                                </div>
                                <div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input permission-checkbox" 
                                               id="edit_perm_menu" name="web_menu[permissions][menu]" data-level="1">
                                        <label class="custom-control-label" for="edit_perm_menu"></label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input permission-checkbox" 
                                               id="edit_perm_view" name="web_menu[permissions][view]" data-level="2">
                                        <label class="custom-control-label" for="edit_perm_view"></label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input permission-checkbox" 
                                               id="edit_perm_create" name="web_menu[permissions][create]" data-level="3">
                                        <label class="custom-control-label" for="edit_perm_create"></label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input permission-checkbox" 
                                               id="edit_perm_update" name="web_menu[permissions][update]" data-level="3">
                                        <label class="custom-control-label" for="edit_perm_update"></label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input permission-checkbox" 
                                               id="edit_perm_delete" name="web_menu[permissions][delete]" data-level="3">
                                        <label class="custom-control-label" for="edit_perm_delete"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" id="edit_status" name="web_menu[wm_status_menu]" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                                <div class="invalid-feedback">
                                    Status menu wajib dipilih
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    let originalMenuType = '';
    let currentMenuId = null;
    let currentMenuGlobalId = null;
    let currentWebMenuUrl = null;

    // Edit Menu Handler - Use delegated event handler
    $(document).on('click', '.edit-menu', function() {
        const menuId = $(this).data('id');
        currentMenuId = menuId;
        
        // Reset form
        $('#editMenuForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        
        // Load menu data
        $.ajax({
            url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const menu = response.menu;
                    currentMenuGlobalId = menu.fk_web_menu_global;
                    currentWebMenuUrl = menu.fk_web_menu_url;
                    
                    // Set form action
                    $('#editMenuForm').attr('action', `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}`);
                    
                    // Fill form fields
                    $('#edit_menu_global_name').val(menu.menu_global_name);
                    $('#edit_alias').val(menu.wm_menu_nama || '');
                    $('#edit_status').val(menu.wm_status_menu);
                    $('#edit_level_menu').val(menu.fk_m_hak_akses);
                    
                    // Determine kategori menu
                    originalMenuType = menu.kategori_menu;
                    
                    // Setup kategori menu options
                    setupKategoriOptions(originalMenuType);
                    $('#edit_kategori_menu').val(originalMenuType);
                    
                    // Handle parent menu if sub menu
                    if (originalMenuType === 'sub_menu') {
                        $('#edit_group_menu_container').show();
                        updateParentMenuOptions(menu.fk_m_hak_akses, $('#edit_parent_id'), menuId);
                        
                        // Set parent value after dropdown is loaded
                        setTimeout(() => {
                            $('#edit_parent_id').val(menu.wm_parent_id);
                        }, 500);
                    } else {
                        $('#edit_group_menu_container').hide();
                    }
                    
                    // Handle permissions
                    if (originalMenuType !== 'group_menu') {
                        $('#edit_permissions_container').show();
                        
                        // Load permissions for this menu
                        if (menu.permissions) {
                            $('#edit_perm_menu').prop('checked', menu.permissions.menu);
                            $('#edit_perm_view').prop('checked', menu.permissions.view);
                            $('#edit_perm_create').prop('checked', menu.permissions.create);
                            $('#edit_perm_update').prop('checked', menu.permissions.update);
                            $('#edit_perm_delete').prop('checked', menu.permissions.delete);
                        }
                    } else {
                        $('#edit_permissions_container').hide();
                    }
                    
                    // Disable level menu if sub menu (will inherit from parent)
                    if (originalMenuType === 'sub_menu') {
                        $('#edit_level_menu').prop('disabled', true);
                    } else {
                        $('#edit_level_menu').prop('disabled', false);
                    }
                }
            },
            error: function() {
                toastr.error('Gagal memuat data menu');
            }
        });
    });

    // Setup kategori options based on current menu type
    function setupKategoriOptions(currentType) {
        const $select = $('#edit_kategori_menu');
        $select.empty();
        
        if (currentType === 'group_menu') {
            // Group menu tidak bisa berubah kategori
            $select.append('<option value="group_menu">Group Menu</option>');
            $select.prop('disabled', true);
            $('#kategori_info').text('Group menu tidak dapat diubah kategori');
        } else {
            $select.prop('disabled', false);
            $('#kategori_info').text('');
            
            // Menu biasa dan sub menu bisa diubah, tapi tidak bisa menjadi group menu
            $select.append('<option value="menu_biasa">Menu Biasa</option>');
            $select.append('<option value="sub_menu">Sub Menu</option>');
        }
    }

    // Handle kategori menu change
    $('#edit_kategori_menu').on('change', function() {
        const selectedKategori = $(this).val();
        const hakAksesId = $('#edit_level_menu').val();
        
        if (selectedKategori === 'sub_menu') {
            $('#edit_group_menu_container').show();
            $('#edit_level_menu').prop('disabled', true);
            updateParentMenuOptions(hakAksesId, $('#edit_parent_id'), currentMenuId);
        } else {
            $('#edit_group_menu_container').hide();
            $('#edit_parent_id').val('');
            $('#edit_level_menu').prop('disabled', false);
        }
        
        // Hide/show permissions based on kategori
        if (selectedKategori === 'group_menu') {
            $('#edit_permissions_container').hide();
        } else {
            $('#edit_permissions_container').show();
        }
    });

    // Handle level menu change
    $('#edit_level_menu').on('change', function() {
        const hakAksesId = $(this).val();
        const kategori = $('#edit_kategori_menu').val();
        
        if (kategori === 'sub_menu') {
            updateParentMenuOptions(hakAksesId, $('#edit_parent_id'), currentMenuId);
        }
    });

    // Permission checkbox hierarchy
    $('#editMenuModal .permission-checkbox').on('change', function() {
        const $this = $(this);
        const level = parseInt($this.data('level'));
        const isChecked = $this.is(':checked');
        
        if (!isChecked) {
            // Uncheck all lower level permissions
            $('#editMenuModal .permission-checkbox').each(function() {
                const thisLevel = parseInt($(this).data('level'));
                if (thisLevel > level) {
                    $(this).prop('checked', false);
                }
            });
        }
        
        if (isChecked) {
            // Check all higher level permissions
            $('#editMenuModal .permission-checkbox').each(function() {
                const thisLevel = parseInt($(this).data('level'));
                if (thisLevel < level) {
                    $(this).prop('checked', true);
                }
            });
        }
    });

    // Form submission
    $('#editMenuForm').on('submit', function(e) {
        e.preventDefault();
        
        // Remove validation errors
        $('.is-invalid').removeClass('is-invalid');
        
        // Validate
        let isValid = true;
        
        // Add web_menu_global_id to form data
        $('<input>')
            .attr('type', 'hidden')
            .attr('name', 'web_menu[fk_web_menu_global]')
            .val(currentMenuGlobalId)
            .appendTo(this);

        // Enable level menu temporarily for submission
        $('#edit_level_menu').prop('disabled', false);
        
        // Validate parent menu for sub menu
        if ($('#edit_kategori_menu').val() === 'sub_menu' && !$('#edit_parent_id').val()) {
            $('#edit_parent_id').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            return;
        }
        
        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editMenuModal').modal('hide');
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                        toastr.error(xhr.responseJSON.errors[key][0]);
                    });
                } else {
                    toastr.error('Terjadi kesalahan saat memperbarui menu');
                }
            }
        });
    });

    // Function to update parent menu options
    function updateParentMenuOptions(hakAksesId, targetSelect, excludeId = null) {
        targetSelect.empty().append('<option value="">-- Pilih Group Menu --</option>');
        
        if (!hakAksesId) return;
        
        const dynamicUrl = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/get-parent-menus') }}";
        
        $.ajax({
            url: `${dynamicUrl}/${hakAksesId}`,
            type: 'GET',
            data: { exclude_id: excludeId },
            success: function(response) {
                if (response.success && response.parentMenus) {
                    response.parentMenus.forEach(function(menu) {
                        // Only add if it's a group menu
                        if (menu.fk_web_menu_global && !menu.WebMenuUrl) {
                            targetSelect.append(`
                                <option value="${menu.web_menu_id}">
                                    ${menu.display_name}
                                </option>
                            `);
                        }
                    });
                }
            },
            error: function() {
                toastr.error('Gagal memuat menu induk');
            }
        });
    }
});
</script>
@endpush