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
                                <input type="text" class="form-control" id="edit_alias" name="web_menu[wm_menu_nama]"
                                    placeholder="Masukkan alias menu">
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
                                <select class="form-control" id="edit_level_menu" name="web_menu[fk_m_hak_akses]"
                                    readonly disabled>
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
                                <label class="font-weight-bold">
                                    <i class="fas fa-shield-alt mr-2 text-primary"></i>
                                    Pengaturan Hak Akses
                                </label>
                                <small class="text-muted d-block mb-3">Pilih hak akses yang diinginkan untuk menu
                                    ini</small>

                                <div class="card border-light">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <!-- Tampil Menu -->
                                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                <div class="permission-item text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input permission-checkbox"
                                                            id="edit_perm_menu" name="web_menu[permissions][menu]"
                                                            data-level="1">
                                                        <label class="custom-control-label"
                                                            for="edit_perm_menu"></label>
                                                    </div>
                                                    <div class="permission-info mt-2">
                                                        <i class="fas fa-bars text-primary mb-2"
                                                            style="font-size: 1.5rem;"></i>
                                                        <div class="permission-title font-weight-bold">Menu</div>
                                                        <small class="text-muted">Menu tampil di sidebar</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Lihat -->
                                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                <div class="permission-item text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input permission-checkbox"
                                                            id="edit_perm_view" name="web_menu[permissions][view]"
                                                            data-level="2">
                                                        <label class="custom-control-label"
                                                            for="edit_perm_view"></label>
                                                    </div>
                                                    <div class="permission-info mt-2">
                                                        <i class="fas fa-eye text-info mb-2"
                                                            style="font-size: 1.5rem;"></i>
                                                        <div class="permission-title font-weight-bold">Lihat</div>
                                                        <small class="text-muted">Akses melihat halaman</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tambah -->
                                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                <div class="permission-item text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input permission-checkbox"
                                                            id="edit_perm_create" name="web_menu[permissions][create]"
                                                            data-level="3">
                                                        <label class="custom-control-label"
                                                            for="edit_perm_create"></label>
                                                    </div>
                                                    <div class="permission-info mt-2">
                                                        <i class="fas fa-plus text-success mb-2"
                                                            style="font-size: 1.5rem;"></i>
                                                        <div class="permission-title font-weight-bold">Tambah</div>
                                                        <small class="text-muted">Membuat data baru</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ubah -->
                                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                <div class="permission-item text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input permission-checkbox"
                                                            id="edit_perm_update" name="web_menu[permissions][update]"
                                                            data-level="3">
                                                        <label class="custom-control-label"
                                                            for="edit_perm_update"></label>
                                                    </div>
                                                    <div class="permission-info mt-2">
                                                        <i class="fas fa-edit text-warning mb-2"
                                                            style="font-size: 1.5rem;"></i>
                                                        <div class="permission-title font-weight-bold">Ubah</div>
                                                        <small class="text-muted">Mengubah data</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Hapus -->
                                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                <div class="permission-item text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input permission-checkbox"
                                                            id="edit_perm_delete" name="web_menu[permissions][delete]"
                                                            data-level="3">
                                                        <label class="custom-control-label"
                                                            for="edit_perm_delete"></label>
                                                    </div>
                                                    <div class="permission-info mt-2">
                                                        <i class="fas fa-trash text-danger mb-2"
                                                            style="font-size: 1.5rem;"></i>
                                                        <div class="permission-title font-weight-bold">Hapus</div>
                                                        <small class="text-muted">Menghapus data</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Quick Actions -->
                                            <div class="col-lg-2 col-md-12 col-sm-12 mb-3">
                                                <div class="quick-actions text-center">
                                                    <div class="mb-2">
                                                        <small class="text-muted font-weight-bold">Aksi Cepat:</small>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm btn-block mb-1"
                                                        id="edit_select_all">
                                                        <i class="fas fa-check-double mr-1"></i>Pilih Semua
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-sm btn-block"
                                                        id="edit_clear_all">
                                                        <i class="fas fa-times mr-1"></i>Bersihkan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Permission Level Info -->
                                        <div class="alert alert-info mt-3 mb-0"
                                            style="background-color: #f8f9fa; border-color: #dee2e6;">
                                            <small>
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Catatan:</strong>
                                                Hak akses bersifat hierarkis. Memilih akses tingkat tinggi akan otomatis
                                                memilih akses tingkat rendah.
                                            </small>
                                        </div>
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
        $(document).ready(function () {
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

            // Event handler untuk tombol Quick Actions
            $('#edit_select_all').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Centang semua checkbox permissions
                $('#edit_permissions_container .permission-checkbox').each(function () {
                    $(this).prop('checked', true);

                    // Trigger visual feedback
                    const $permissionItem = $(this).closest('.permission-item');
                    $permissionItem.addClass('selected');
                });

                toastr.success('Semua hak akses dipilih');
            });

            $('#edit_clear_all').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Hapus centang semua checkbox permissions
                $('#edit_permissions_container .permission-checkbox').each(function () {
                    $(this).prop('checked', false);

                    // Trigger visual feedback
                    const $permissionItem = $(this).closest('.permission-item');
                    $permissionItem.removeClass('selected');
                });

                toastr.info('Semua hak akses dibersihkan');
            });

            $(document).on('click', '.edit-menu', function () {
                const menuId = $(this).data('id');
                currentMenuId = menuId;

                $('#editMenuForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');

                // Reset visual feedback
                $('.permission-item').removeClass('selected');

                $.ajax({
                    url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/editData/${menuId}`,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            const menu = response.menu;
                            currentMenuGlobalId = menu.fk_web_menu_global;
                            currentWebMenuUrl = menu.fk_web_menu_url;
                            currentLevelId = menu.fk_m_hak_akses;

                            $('#editMenuForm').attr('action', `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/updateData/${menuId}`);

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

                                // Reset semua checkbox
                                $('#edit_perm_menu, #edit_perm_view, #edit_perm_create, #edit_perm_update, #edit_perm_delete').prop('checked', false);
                                $('.permission-item').removeClass('selected');

                                // Set checkbox berdasarkan data permissions
                                if (menu.permissions) {
                                    const checkboxes = [
                                        { id: '#edit_perm_menu', value: menu.permissions.menu },
                                        { id: '#edit_perm_view', value: menu.permissions.view },
                                        { id: '#edit_perm_create', value: menu.permissions.create },
                                        { id: '#edit_perm_update', value: menu.permissions.update },
                                        { id: '#edit_perm_delete', value: menu.permissions.delete }
                                    ];

                                    checkboxes.forEach(checkbox => {
                                        const isChecked = checkbox.value === true || checkbox.value === 1;
                                        $(checkbox.id).prop('checked', isChecked);

                                        // Visual feedback
                                        if (isChecked) {
                                            $(checkbox.id).closest('.permission-item').addClass('selected');
                                        }
                                    });
                                }
                            } else {
                                $('#edit_permissions_container').hide();
                            }

                            showModal('#editMenuModal');
                        }
                    },
                    error: function (xhr, status, error) {
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

            $('#edit_kategori_menu').on('change', function () {
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

            // Enhanced permission hierarchy logic dengan visual feedback
            $('#editMenuModal .permission-checkbox').on('change', function () {
                const $this = $(this);
                const level = parseInt($this.data('level'));
                const isChecked = $this.is(':checked');

                // Visual feedback untuk item yang diklik
                const $permissionItem = $this.closest('.permission-item');
                if (isChecked) {
                    $permissionItem.addClass('selected');
                } else {
                    $permissionItem.removeClass('selected');
                }

                // Hierarchy logic - jika uncheck, uncheck semua level di atasnya
                if (!isChecked) {
                    $('#editMenuModal .permission-checkbox').each(function () {
                        const thisLevel = parseInt($(this).data('level'));
                        if (thisLevel > level) {
                            $(this).prop('checked', false);
                            $(this).closest('.permission-item').removeClass('selected');
                        }
                    });
                }

                // Hierarchy logic - jika check, check semua level di bawahnya
                if (isChecked) {
                    $('#editMenuModal .permission-checkbox').each(function () {
                        const thisLevel = parseInt($(this).data('level'));
                        if (thisLevel < level) {
                            $(this).prop('checked', true);
                            $(this).closest('.permission-item').addClass('selected');
                        }
                    });
                }
            });

            $('#editMenuForm').on('submit', function (e) {
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

                // Debug logging
                console.log('🔍 Update Menu Debug:', {
                    url: $(this).attr('action'),
                    method: 'POST',
                    hasMethodSpoofing: formDataObj['_method'],
                    formData: formDataObj
                });

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST', // ✅ Gunakan POST, bukan PUT
                    data: formDataObj,
                    success: function (response) {
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
                    error: function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function (key) {
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

                const dynamicUrl = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/getData') }}";

                $.ajax({
                    url: dynamicUrl,
                    type: 'GET',
                    data: { 
                        action: 'parent-menus',
                        hak_akses_id: hakAksesId,
                        exclude_id: excludeId 
                    },
                    success: function (response) {
                        if (response.success && response.parentMenus) {
                            response.parentMenus.forEach(function (menu) {
                                targetSelect.append(`
                                <option value="${menu.web_menu_id}">
                                    ${menu.display_name}
                                </option>
                            `);
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        toastr.error('Gagal memuat menu induk');
                    }
                });
            }

            $(document).on('click', '[data-dismiss="modal"]', function () {
                const targetModal = $(this).closest('.modal');
                hideModal('#' + targetModal.attr('id'));
            });

            $(document).on('click', '.modal', function (e) {
                if (e.target === this) {
                    hideModal('#' + $(this).attr('id'));
                }
            });

            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && $('.modal.show').length) {
                    const modalId = '#' + $('.modal.show').attr('id');
                    hideModal(modalId);
                }
            });

            $(document).on('hidden.bs.modal', '.modal', function () {
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

                // Reset visual feedback
                $modal.find('.permission-item').removeClass('selected');
            }
        });
    </script>
@endpush

@push('css')
    <style>
        .permission-item {
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            cursor: pointer;
        }

        .permission-item:hover {
            background-color: #e9ecef;
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
        }

        /* Styling untuk item yang selected */
        .permission-item.selected {
            background-color: #d4edda !important;
            border-color: #28a745 !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
        }

        .permission-item.selected .permission-info {
            color: #155724;
        }

        .permission-item.selected .permission-info i {
            color: #28a745 !important;
            animation: pulse 1s;
        }

        .permission-item input[type="checkbox"]:checked+label::before {
            background-color: #28a745;
            border-color: #28a745;
        }

        .permission-item input[type="checkbox"]:checked+label::after {
            color: white;
        }

        .permission-title {
            font-size: 0.9rem;
            color: #495057;
            transition: color 0.3s ease;
        }

        .quick-actions {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .quick-actions .btn {
            transition: all 0.3s ease;
        }

        .quick-actions .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        /* Animation for feedback */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Custom checkbox styling */
        .custom-control-label {
            position: relative;
            margin-bottom: 0;
            min-height: 1.25rem;
            cursor: pointer;
        }

        .custom-control-label::before {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 0.375rem;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .custom-control-label::after {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .permission-info i {
            transition: all 0.3s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .permission-item {
                min-height: 100px;
                padding: 10px;
            }

            .permission-info i {
                font-size: 1.2rem !important;
            }

            .permission-title {
                font-size: 0.8rem;
            }

            .quick-actions {
                min-height: auto;
                padding: 10px;
            }
        }
    </style>
@endpush