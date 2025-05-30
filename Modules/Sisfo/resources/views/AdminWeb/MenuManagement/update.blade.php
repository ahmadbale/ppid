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

                        <!-- Level Menu (Read Only) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Level Menu</label>
                                <select class="form-control" id="edit_level_menu" name="web_menu[fk_m_hak_akses]" readonly disabled>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->hak_akses_id }}">{{ $level->hak_akses_nama }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="edit_level_menu_hidden" name="web_menu[fk_m_hak_akses]">
                                <span class="text-muted">Level menu tidak dapat diubah setelah dibuat</span>
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
    let currentLevelId = null;

    // Function untuk menampilkan modal dengan fallback
    function showModal(modalId) {
        const $modal = $(modalId);
        if ($.fn.modal && typeof $.fn.modal === 'function') {
            // Bootstrap modal tersedia
            $modal.modal('show');
        } else {
            // Fallback: tampilkan manual
            $modal.addClass('show').css('display', 'block');
            $('body').addClass('modal-open');
            
            // Tambahkan backdrop
            if (!$('.modal-backdrop').length) {
                $('<div class="modal-backdrop fade show"></div>').appendTo('body');
            }
        }
    }

    // Function untuk menyembunyikan modal dengan fallback
    function hideModal(modalId) {
        const $modal = $(modalId);
        if ($.fn.modal && typeof $.fn.modal === 'function') {
            // Bootstrap modal tersedia
            $modal.modal('hide');
        } else {
            // Fallback: sembunyikan manual
            $modal.removeClass('show').css('display', 'none');
            $('body').removeClass('modal-open').css('padding-right', '');
            $('.modal-backdrop').remove();
        }
    }

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
                    currentLevelId = menu.fk_m_hak_akses;
                    
                    // Set form action dengan path yang benar
                    $('#editMenuForm').attr('action', `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/update`);
                    
                    // Fill form fields
                    $('#edit_menu_global_name').val(menu.menu_global_name);
                    $('#edit_alias').val(menu.wm_menu_nama || '');
                    $('#edit_status').val(menu.wm_status_menu);
                    
                    // Set level menu (read only)
                    $('#edit_level_menu').val(menu.fk_m_hak_akses);
                    $('#edit_level_menu_hidden').val(menu.fk_m_hak_akses);
                    
                    // Determine kategori menu
                    originalMenuType = menu.kategori_menu;
                    
                    // Setup kategori menu options
                    setupKategoriOptions(originalMenuType);
                    $('#edit_kategori_menu').val(originalMenuType);
                    
                    // Handle parent menu if sub menu
                    if (originalMenuType === 'sub_menu') {
                        $('#edit_group_menu_container').show();
                        // Load parent menus untuk level yang sama
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
                    
                    // Show modal after data is loaded
                    showModal('#editMenuModal');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading menu data:', error);
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
            
            // Menu biasa dan sub menu bisa saling berubah, tapi tidak bisa menjadi group menu
            $select.append('<option value="menu_biasa">Menu Biasa</option>');
            $select.append('<option value="sub_menu">Sub Menu</option>');
        }
    }

    // Handle kategori menu change
    $('#edit_kategori_menu').on('change', function() {
        const selectedKategori = $(this).val();
        
        if (selectedKategori === 'sub_menu') {
            $('#edit_group_menu_container').show();
            // Load parent menus untuk level yang sama (tidak berubah)
            updateParentMenuOptions(currentLevelId, $('#edit_parent_id'), currentMenuId);
        } else {
            $('#edit_group_menu_container').hide();
            $('#edit_parent_id').val('');
        }
        
        // Hide/show permissions based on kategori
        if (selectedKategori === 'group_menu') {
            $('#edit_permissions_container').hide();
        } else {
            $('#edit_permissions_container').show();
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

    // PERBAIKAN: Form submission dengan penanganan modal yang lebih robust
    $('#editMenuForm').on('submit', function(e) {
        e.preventDefault();
        
        // Remove validation errors
        $('.is-invalid').removeClass('is-invalid');
        
        // Validate
        let isValid = true;
        
        // Validate parent menu for sub menu
        if ($('#edit_kategori_menu').val() === 'sub_menu' && !$('#edit_parent_id').val()) {
            $('#edit_parent_id').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            return;
        }
        
        // Collect form data
        let formData = $(this).serializeArray();
        
        // PERBAIKAN: Pastikan kategori_menu selalu dikirim
        const kategoriMenu = $('#edit_kategori_menu').val();
        
        // Hapus kategori_menu yang mungkin sudah ada di formData
        formData = formData.filter(item => item.name !== 'kategori_menu');
        
        // Tambahkan kategori_menu dengan nilai yang benar
        formData.push({
            name: 'kategori_menu',
            value: kategoriMenu
        });
        
        // PERBAIKAN: Hanya tambahkan fk_web_menu_global untuk menu_biasa dan sub_menu
        if (kategoriMenu !== 'group_menu') {
            formData.push({
                name: 'web_menu[fk_web_menu_global]',
                value: currentMenuGlobalId
            });
        }
        
        console.log('Data yang akan dikirim:', formData); // Untuk debug
        
        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: $.param(formData),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // PERBAIKAN: Gunakan function hideModal yang aman
                    try {
                        hideModal('#editMenuModal');
                    } catch (error) {
                        console.error('Error saat menutup modal:', error);
                        // Fallback manual jika semua cara gagal
                        $('#editMenuModal').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    }
                    
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    console.error('Error response:', response);
                    toastr.error(response.message || 'Terjadi kesalahan saat memperbarui menu');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                        toastr.error(xhr.responseJSON.errors[key][0]);
                        console.error('Validation error [' + key + ']:', xhr.responseJSON.errors[key][0]);
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
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
                        targetSelect.append(`
                            <option value="${menu.web_menu_id}">
                                ${menu.display_name}
                            </option>
                        `);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading parent menus:', error);
                toastr.error('Gagal memuat menu induk');
            }
        });
    }
    
    // PERBAIKAN: Event handler untuk tombol close modal
    $(document).on('click', '[data-dismiss="modal"]', function() {
        const targetModal = $(this).closest('.modal');
        hideModal('#' + targetModal.attr('id'));
    });
    
    // PERBAIKAN: Event handler untuk backdrop click
    $(document).on('click', '.modal', function(e) {
        if (e.target === this) {
            hideModal('#' + $(this).attr('id'));
        }
    });
    
    // PERBAIKAN: Event handler untuk ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('.modal.show').length) {
            const modalId = '#' + $('.modal.show').attr('id');
            hideModal(modalId);
        }
    });
    
    // Reset modal on close dengan event yang lebih umum
    $(document).on('hidden.bs.modal', '.modal', function() {
        resetModal(this);
    });
    
    // PERBAIKAN: Function untuk reset modal
    function resetModal(modal) {
        const $modal = $(modal);
        const $form = $modal.find('form');
        
        if ($form.length) {
            $form[0].reset();
        }
        
        $modal.find('.is-invalid').removeClass('is-invalid');
        $modal.find('#edit_kategori_menu').prop('disabled', false);
        $modal.find('#edit_parent_id').empty().append('<option value="">-- Pilih Group Menu --</option>');
        $modal.find('#kategori_info').text('');
    }
});
</script>
@endpush