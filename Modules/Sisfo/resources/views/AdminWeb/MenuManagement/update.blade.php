@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

<!-- Edit Menu Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMenuForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="web_menu[web_menu_id]" id="edit_menu_id">
                <input type="hidden" name="web_menu[set_akses_menu_id]" id="edit_set_akses_menu_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Hak Akses<span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[fk_m_hak_akses]" id="edit_level_menu">
                            <option value="">Pilih Hak Akses</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->hak_akses_id }}">{{ $level->hak_akses_nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Hak Akses wajib diisi</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori Menu<span class="text-danger">*</span></label>
                        <select class="form-control" name="kategori_menu" id="edit_kategori_menu">
                            <option value="menu_biasa">- Set sebagai menu biasa</option>
                            <option value="group_menu">- Set sebagai group menu</option>
                            <option value="sub_menu">- Set sebagai sub menu</option>
                        </select>
                        <div class="invalid-feedback">Kategori menu wajib dipilih</div>
                    </div>
                    
                    <div class="form-group" id="edit_nama_group_menu_wrapper">
                        <label>Nama Group Menu<span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[wm_parent_id]" id="edit_nama_group_menu" disabled>
                            <option value="">Pilih Nama Group Menu</option>
                            <!-- Untuk "Set sebagai group menu" -->
                            <optgroup label="Group Menu" id="edit_group_menu_options">
                                @foreach($groupMenusGlobal as $menu)
                                    <option value="{{ $menu->web_menu_global_id }}" data-menu-type="global">
                                        {{ $menu->wmg_nama_default }}
                                    </option>
                                @endforeach
                            </optgroup>
                            
                            <!-- Untuk "Set sebagai sub menu" -->
                            <optgroup label="Menu Utama" id="edit_sub_menu_options" style="display:none;">
                                @foreach($groupMenusFromWebMenu as $menu)
                                    <option value="{{ $menu->web_menu_id }}" data-menu-type="parent">
                                        {{ $menu->wm_menu_nama ?: $menu->WebMenuGlobal->wmg_nama_default }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        <div class="invalid-feedback">Nama group menu wajib dipilih</div>
                        <small class="form-text text-muted" id="edit_group_menu_help">
                            Silahkan pilih group menu yang sesuai
                        </small>
                    </div>
                    
                    <div class="form-group" id="edit_nama_menu_wrapper">
                        <label>Nama Menu<span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[fk_web_menu_global]" id="edit_nama_menu">
                            <option value="">Pilih Nama Menu</option>
                            @foreach($nonGroupMenus as $menu)
                                <option value="{{ $menu->web_menu_global_id }}">
                                    {{ $menu->wmg_nama_default }} 
                                    @if($menu->WebMenuUrl && $menu->WebMenuUrl->application)
                                        ({{ $menu->WebMenuUrl->application->app_nama }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Nama menu wajib dipilih</div>
                        <small class="form-text text-muted" id="edit_menu_help">
                            Silahkan pilih nama menu biasa
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Alias</label>
                        <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="edit_menu_alias">
                        <small class="form-text text-muted">
                            Alias akan ditampilkan sebagai nama menu. Jika kosong, nama default akan digunakan.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[wm_status_menu]" id="edit_status_menu">
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                        <div class="invalid-feedback">Status menu wajib diisi</div>
                    </div>

                    <hr>
                    
                    <!-- Pengaturan Hak Akses -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Pengaturan Hak Akses untuk Menu Ini</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Level Hak Akses:</label>
                                <select class="form-control" id="edit_hak_akses_kode" name="hak_akses_kode">
                                    <option value="">-- Pilih Level --</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->hak_akses_kode }}">{{ $level->hak_akses_nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="edit-hak-akses-table-container" style="display: none;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tampil Menu</th>
                                            <th>Lihat</th>
                                            <th>Tambah</th>
                                            <th>Ubah</th>
                                            <th>Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="edit_hak_menu" name="hak_menu" value="1">
                                                    <label class="custom-control-label" for="edit_hak_menu"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="edit_hak_view" name="hak_view" value="1">
                                                    <label class="custom-control-label" for="edit_hak_view"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="edit_hak_create" name="hak_create" value="1">
                                                    <label class="custom-control-label" for="edit_hak_create"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="edit_hak_update" name="hak_update" value="1">
                                                    <label class="custom-control-label" for="edit_hak_update"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="edit_hak_delete" name="hak_delete" value="1">
                                                    <label class="custom-control-label" for="edit_hak_delete"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Pengaturan hak akses ini akan diterapkan untuk semua pengguna dengan level yang dipilih.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitEditMenu">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
// Variabel untuk menyimpan level asli dari menu
let originalMenuLevel = '';

// Event handler untuk dropdown Kategori Menu
$('#edit_kategori_menu').off('change').on('change', function() {
    const selectedValue = $(this).val();
    
    if (selectedValue === 'menu_biasa') {
        // Set sebagai menu biasa
        $('#edit_nama_group_menu').prop('disabled', true).val('');
        $('#edit_nama_menu').prop('disabled', false);
        $('#edit_menu_help').text('Silahkan pilih nama menu biasa');
        
        // Update wajib/tidak wajib
        $('#edit_nama_menu').siblings('label').find('.text-danger').show();
        $('#edit_nama_group_menu').siblings('label').find('.text-danger').hide();
        
        // Tampilkan opsi yang sesuai
        $('#edit_group_menu_options').show();
        $('#edit_sub_menu_options').hide();
    } 
    else if (selectedValue === 'group_menu') {
        // Set sebagai group menu
        $('#edit_nama_menu').prop('disabled', true).val('');
        $('#edit_nama_group_menu').prop('disabled', false);
        $('#edit_group_menu_help').text('Silahkan pilih nama group menu');
        
        // Update wajib/tidak wajib
        $('#edit_nama_menu').siblings('label').find('.text-danger').hide();
        $('#edit_nama_group_menu').siblings('label').find('.text-danger').show();
        
        // Tampilkan opsi yang sesuai
        $('#edit_group_menu_options').show();
        $('#edit_sub_menu_options').hide();
    }
    else if (selectedValue === 'sub_menu') {
        // Set sebagai sub menu
        $('#edit_nama_menu').prop('disabled', false);
        $('#edit_nama_group_menu').prop('disabled', false);
        $('#edit_menu_help').text('Silahkan pilih nama sub menu');
        $('#edit_group_menu_help').text('Silahkan pilih group menu induk');
        
        // Update wajib/tidak wajib
        $('#edit_nama_menu').siblings('label').find('.text-danger').show();
        $('#edit_nama_group_menu').siblings('label').find('.text-danger').show();
        
        // Tampilkan opsi yang sesuai
        $('#edit_group_menu_options').hide();
        $('#edit_sub_menu_options').show();
    }
});

// Event handler untuk hak akses dropdown di form edit
$('#edit_hak_akses_kode').change(function() {
    const menuId = $('#edit_menu_id').val();
    const hakAksesKode = $(this).val();
    
    if (hakAksesKode) {
        // Tampilkan tabel hak akses
        $('#edit-hak-akses-table-container').show();
        
        // Ambil pengaturan hak akses yang sudah ada untuk menu ini dan level
        $.ajax({
            url: "{{ url('/HakAkses/getHakAksesData') }}/" + hakAksesKode,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Cek apakah menu ada dalam data yang dikembalikan
                if (data[menuId]) {
                    // Set nilai checkbox berdasarkan pengaturan yang ada
                    $('#edit_hak_menu').prop('checked', data[menuId].ha_menu === 1);
                    $('#edit_hak_view').prop('checked', data[menuId].ha_view === 1);
                    $('#edit_hak_create').prop('checked', data[menuId].ha_create === 1);
                    $('#edit_hak_update').prop('checked', data[menuId].ha_update === 1);
                    $('#edit_hak_delete').prop('checked', data[menuId].ha_delete === 1);
                } else {
                    // Reset checkbox jika tidak ada pengaturan
                    $('#edit_hak_menu').prop('checked', false);
                    $('#edit_hak_view').prop('checked', false);
                    $('#edit_hak_create').prop('checked', false);
                    $('#edit_hak_update').prop('checked', false);
                    $('#edit_hak_delete').prop('checked', false);
                }
            },
            error: function(xhr) {
                console.error('Error loading hak akses data:', xhr.responseText);
                // Reset checkbox jika terjadi error
                $('#edit_hak_menu').prop('checked', false);
                $('#edit_hak_view').prop('checked', false);
                $('#edit_hak_create').prop('checked', false);
                $('#edit_hak_update').prop('checked', false);
                $('#edit_hak_delete').prop('checked', false);
            }
        });
    } else {
        $('#edit-hak-akses-table-container').hide();
    }
});

// Form editMenu
$('#editMenuForm').off('submit').on('submit', function(e) {
    e.preventDefault();

    // Reset validasi
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    let isValid = true;
    let kategoriMenu = $('#edit_kategori_menu').val();
    let namaMenu = $('#edit_nama_menu').val();
    let namaGroupMenu = $('#edit_nama_group_menu').val();
    let statusMenu = $('#edit_status_menu').val();
    let levelMenu = $('#edit_level_menu').val();

    // Validasi Level Menu
    if (!levelMenu) {
        $('#edit_level_menu').addClass('is-invalid');
        $('#edit_level_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }
    
    // Validasi berdasarkan kategori menu yang dipilih
    if (kategoriMenu === 'menu_biasa') {
        // Jika menu biasa, nama menu wajib diisi
        if (!namaMenu) {
            $('#edit_nama_menu').addClass('is-invalid');
            $('#edit_nama_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
    } else if (kategoriMenu === 'group_menu') {
        // Jika group menu, nama group menu wajib diisi
        if (!namaGroupMenu) {
            $('#edit_nama_group_menu').addClass('is-invalid');
            $('#edit_nama_group_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
    } else if (kategoriMenu === 'sub_menu') {
        // Jika sub menu, nama menu dan nama group menu wajib diisi
        if (!namaMenu) {
            $('#edit_nama_menu').addClass('is-invalid');
            $('#edit_nama_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
        if (!namaGroupMenu) {
            $('#edit_nama_group_menu').addClass('is-invalid');
            $('#edit_nama_group_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
    }

    // Validasi Status
    if (!statusMenu) {
        $('#edit_status_menu').addClass('is-invalid');
        $('#edit_status_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }

    if (!isValid) {
        return false;
    }

    // Validasi menu SAR
    if (originalMenuLevel === 'SAR' && '{{ Auth::user()->level->hak_akses_kode }}' !== 'SAR') {
        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
        return false;
    }

    // Disable button selama pengiriman
    const $submitBtn = $('#submitEditMenu');
    const originalBtnText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    
    // Persiapkan data form
    let formData = $(this).serializeArray();
    
    // Jika kategori menu adalah group_menu, set fk_web_menu_global sesuai
    if (kategoriMenu === 'group_menu') {
        // Tambahkan field untuk fk_web_menu_global
        formData = formData.filter(item => item.name !== 'web_menu[fk_web_menu_global]');
        formData.push({
            name: 'web_menu[fk_web_menu_global]',
            value: namaGroupMenu
        });
    }
    
    let menuId = $('#edit_menu_id').val();
    
    $.ajax({
        url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/update`,
        type: 'PUT',
        data: $.param(formData),
        success: function(response) {
            if (response.success) {
                // Jika update menu berhasil, periksa apakah perlu update hak akses
                const hakAksesKode = $('#edit_hak_akses_kode').val();
                
                if (hakAksesKode) {
                    // Siapkan data hak akses
                    const hakAksesData = {
                        _token: "{{ csrf_token() }}",
                        hak_akses_kode: hakAksesKode,
                        menu_akses: {}
                    };
                    
                    // Tambahkan izin menu
                    hakAksesData.menu_akses[menuId] = {
                        menu: $('#edit_hak_menu').is(':checked') ? 1 : 0,
                        view: $('#edit_hak_view').is(':checked') ? 1 : 0,
                        create: $('#edit_hak_create').is(':checked') ? 1 : 0,
                        update: $('#edit_hak_update').is(':checked') ? 1 : 0,
                        delete: $('#edit_hak_delete').is(':checked') ? 1 : 0
                    };
                    
                    // Kirim permintaan update hak akses
                    $.ajax({
                        url: "{{ url('/HakAkses/updateData') }}",
                        type: 'POST',
                        data: hakAksesData,
                        success: function(hakAksesResponse) {
                            toastr.success(response.message + ' dan hak akses berhasil diperbarui');
                            setTimeout(() => window.location.reload(), 1500);
                        },
                        error: function(xhr) {
                            toastr.success(response.message);
                            toastr.warning('Namun terjadi kesalahan saat memperbarui hak akses');
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    });
                } else {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1500);
                }
            } else {
                toastr.error(response.message);
                $submitBtn.html(originalBtnText).prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.error('Error updating menu:', xhr.responseText);
            toastr.error('Terjadi kesalahan saat memperbarui menu');
            $submitBtn.html(originalBtnText).prop('disabled', false);
            
            // Handle validation errors
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    toastr.error(errors[key][0]);
                });
            }
        }
    });
});

// Modifikasi fungsi edit untuk menampilkan data dengan benar
$(document).off('click', '.edit-menu').on('click', '.edit-menu', function() {
    let menuId = $(this).data('id');
    
    $('#edit_menu_id').val(menuId);

    $.ajax({
        url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/edit`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // Set nilai form berdasarkan data dari response
                $('#edit_level_menu').val(response.menu.fk_m_hak_akses || '');
                $('#edit_menu_alias').val(response.menu.wm_menu_nama);
                $('#edit_status_menu').val(response.menu.wm_status_menu);
                
                // Simpan level asli menu untuk validasi
                originalMenuLevel = response.menu.hak_akses_kode;
                
                // Tentukan kategori menu berdasarkan data
                if (response.menu.wm_parent_id) {
                    // Ini adalah sub menu
                    $('#edit_kategori_menu').val('sub_menu');
                    // Pilih opsi yang sesuai berdasarkan web_menu_id, bukan web_menu_global_id
                    $('#edit_nama_group_menu').val(response.menu.wm_parent_id);
                    $('#edit_nama_menu').val(response.menu.fk_web_menu_global);
                    
                    // Tampilkan opsi sub menu
                    $('#edit_group_menu_options').hide();
                    $('#edit_sub_menu_options').show();
                } else if (response.menu.fk_web_menu_url === null) {
                    // Ini adalah group menu
                    $('#edit_kategori_menu').val('group_menu');
                    $('#edit_nama_group_menu').val(response.menu.fk_web_menu_global);
                    
                    // Tampilkan opsi group menu
                    $('#edit_group_menu_options').show();
                    $('#edit_sub_menu_options').hide();
                } else {
                    // Ini adalah menu biasa
                    $('#edit_kategori_menu').val('menu_biasa');
                    $('#edit_nama_menu').val(response.menu.fk_web_menu_global);
                    
                    // Tampilkan opsi menu biasa
                    $('#edit_group_menu_options').show();
                    $('#edit_sub_menu_options').hide();
                }
                
                // Trigger change event untuk setup form sesuai kategori
                $('#edit_kategori_menu').trigger('change');
                
                // Set the hak akses dropdown to the menu's level code
                if (response.menu.hak_akses_kode) {
                    $('#edit_hak_akses_kode').val(response.menu.hak_akses_kode).trigger('change');
                }
                
                // Show edit modal
                $('#editMenuModal').modal('show');
            } else {
                toastr.error(response.message || 'Gagal mengambil data menu');
            }
        },
        error: function(xhr) {
            console.error('Error fetching menu data:', xhr.responseText);
            toastr.error('Error mengambil data menu');
        }
    });
});

// Reset form saat modal ditutup
$('#editMenuModal').on('hidden.bs.modal', function() {
    $('#editMenuForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();
    $('#edit_level_menu').prop('disabled', false);
    $('#edit_hak_akses_kode').val('');
    $('#edit-hak-akses-table-container').hide();
    originalMenuLevel = '';
});
</script>
@endpush