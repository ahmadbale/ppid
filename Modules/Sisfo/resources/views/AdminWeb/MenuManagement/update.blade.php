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

    function showModal(modalId) {
        const $modal = $(modalId);
        if ($.fn.modal && typeof $.fn.modal === 'function') {
            $modal.modal('show');
        } else {
            $modal.addClass('show').css('display', 'block');
            $('body').addClass('modal-open');
            if (!$('.modal-backdrop').length) {
                $('<div class="modal-backdrop fade show"></div>').appendTo('body');
            }
        }
    }

    function hideModal(modalId) {
        const $modal = $(modalId);
        if ($.fn.modal && typeof $.fn.modal === 'function') {
            $modal.modal('hide');
        } else {
            $modal.removeClass('show').css('display', 'none');
            $('body').removeClass('modal-open').css('padding-right', '');
            $('.modal-backdrop').remove();
        }
    }

    $(document).on('click', '.edit-menu', function() {
        const menuId = $(this).data('id');
        currentMenuId = menuId;
        
        $('#editMenuForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        
        $.ajax({
            url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const menu = response.menu;
                    currentMenuGlobalId = menu.fk_web_menu_global;
                    currentWebMenuUrl = menu.fk_web_menu_url;
                    currentLevelId = menu.fk_m_hak_akses;
                    
                    $('#editMenuForm').attr('action', `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/update`);
                    
                    $('#edit_menu_global_name').val(menu.menu_global_name);
                    $('#edit_alias').val(menu.wm_menu_nama || '');
                    $('#edit_status').val(menu.wm_status_menu);
                    
                    $('#edit_level_menu').val(menu.fk_m_hak_akses);
                    $('#edit_level_menu_hidden').val(menu.fk_m_hak_akses);
                    
                    originalMenuType = menu.kategori_menu;
                    
                    setupKategoriOptions(originalMenuType);
                    $('#edit_kategori_menu').val(originalMenuType);
                    
                    if (originalMenuType === 'sub_menu') {
                        $('#edit_group_menu_container').show();
                        updateParentMenuOptions(menu.fk_m_hak_akses, $('#edit_parent_id'), menuId);
                        setTimeout(() => {
                            $('#edit_parent_id').val(menu.wm_parent_id);
                        }, 500);
                    } else {
                        $('#edit_group_menu_container').hide();
                    }
                    
                    if (originalMenuType !== 'group_menu') {
                        $('#edit_permissions_container').show();
                        
                        $('#edit_perm_menu, #edit_perm_view, #edit_perm_create, #edit_perm_update, #edit_perm_delete').prop('checked', false);
                        
                        if (menu.permissions) {
                            $('#edit_perm_menu').prop('checked', menu.permissions.menu === true || menu.permissions.menu === 1);
                            $('#edit_perm_view').prop('checked', menu.permissions.view === true || menu.permissions.view === 1);
                            $('#edit_perm_create').prop('checked', menu.permissions.create === true || menu.permissions.create === 1);
                            $('#edit_perm_update').prop('checked', menu.permissions.update === true || menu.permissions.update === 1);
                            $('#edit_perm_delete').prop('checked', menu.permissions.delete === true || menu.permissions.delete === 1);
                        }
                    } else {
                        $('#edit_permissions_container').hide();
                    }
                    
                    showModal('#editMenuModal');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Gagal memuat data menu');
            }
        });
    });

    function setupKategoriOptions(currentType) {
        const $select = $('#edit_kategori_menu');
        $select.empty();
        
        if (currentType === 'group_menu') {
            $select.append('<option value="group_menu">Group Menu</option>');
            $select.prop('disabled', true);
            $('#kategori_info').text('Group menu tidak dapat diubah kategori');
        } else {
            $select.prop('disabled', false);
            $('#kategori_info').text('');
            $select.append('<option value="menu_biasa">Menu Biasa</option>');
            $select.append('<option value="sub_menu">Sub Menu</option>');
        }
    }

    $('#edit_kategori_menu').on('change', function() {
        const selectedKategori = $(this).val();
        
        if (selectedKategori === 'sub_menu') {
            $('#edit_group_menu_container').show();
            updateParentMenuOptions(currentLevelId, $('#edit_parent_id'), currentMenuId);
        } else {
            $('#edit_group_menu_container').hide();
            $('#edit_parent_id').val('');
        }
        
        if (selectedKategori === 'group_menu') {
            $('#edit_permissions_container').hide();
        } else {
            $('#edit_permissions_container').show();
        }
    });

    $('#editMenuModal .permission-checkbox').on('change', function() {
        const $this = $(this);
        const level = parseInt($this.data('level'));
        const isChecked = $this.is(':checked');
        
        if (!isChecked) {
            $('#editMenuModal .permission-checkbox').each(function() {
                const thisLevel = parseInt($(this).data('level'));
                if (thisLevel > level) {
                    $(this).prop('checked', false);
                }
            });
        }
        
        if (isChecked) {
            $('#editMenuModal .permission-checkbox').each(function() {
                const thisLevel = parseInt($(this).data('level'));
                if (thisLevel < level) {
                    $(this).prop('checked', true);
                }
            });
        }
    });

    $('#editMenuForm').on('submit', function(e) {
        e.preventDefault();
        
        $('.is-invalid').removeClass('is-invalid');
        
        let isValid = true;
        if ($('#edit_kategori_menu').val() === 'sub_menu' && !$('#edit_parent_id').val()) {
            $('#edit_parent_id').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            return;
        }
        
        const kategoriMenu = $('#edit_kategori_menu').val();
        
        const formDataObj = {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            '_method': 'PUT',
            'kategori_menu': kategoriMenu,
            'web_menu[wm_menu_nama]': $('#edit_alias').val(),
            'web_menu[wm_status_menu]': $('#edit_status').val(),
            'web_menu[fk_m_hak_akses]': $('#edit_level_menu_hidden').val()
        };

        if (kategoriMenu === 'sub_menu') {
            formDataObj['web_menu[wm_parent_id]'] = $('#edit_parent_id').val();
        }

        if (kategoriMenu !== 'group_menu') {
            formDataObj['web_menu[fk_web_menu_global]'] = currentMenuGlobalId;
        }

        if (kategoriMenu !== 'group_menu') {
            const permissions = {};
            
            if ($('#edit_perm_menu').is(':checked')) {
                permissions['menu'] = '1';
            }
            if ($('#edit_perm_view').is(':checked')) {
                permissions['view'] = '1';
            }
            if ($('#edit_perm_create').is(':checked')) {
                permissions['create'] = '1';
            }
            if ($('#edit_perm_update').is(':checked')) {
                permissions['update'] = '1';
            }
            if ($('#edit_perm_delete').is(':checked')) {
                permissions['delete'] = '1';
            }

            Object.keys(permissions).forEach(key => {
                formDataObj[`web_menu[permissions][${key}]`] = permissions[key];
            });
        }
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: formDataObj,
            success: function(response) {
                if (response.success) {
                    try {
                        hideModal('#editMenuModal');
                    } catch (error) {
                        $('#editMenuModal').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    }
                    
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(response.message || 'Terjadi kesalahan saat memperbarui menu');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                        toastr.error(xhr.responseJSON.errors[key][0]);
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Terjadi kesalahan saat memperbarui menu');
                }
            }
        });
    });

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
                toastr.error('Gagal memuat menu induk');
            }
        });
    }
    
    $(document).on('click', '[data-dismiss="modal"]', function() {
        const targetModal = $(this).closest('.modal');
        hideModal('#' + targetModal.attr('id'));
    });
    
    $(document).on('click', '.modal', function(e) {
        if (e.target === this) {
            hideModal('#' + $(this).attr('id'));
        }
    });
    
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('.modal.show').length) {
            const modalId = '#' + $('.modal.show').attr('id');
            hideModal(modalId);
        }
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        resetModal(this);
    });
    
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