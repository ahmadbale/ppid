<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\create.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

<!-- Add Menu Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMenuForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Hak Akses<span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[fk_m_hak_akses]" id="add_level_menu">
                            <option value="">Pilih Hak Akses</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->hak_akses_id }}">{{ $level->hak_akses_nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Hak Akses wajib diisi</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori Menu<span class="text-danger">*</span></label>
                        <select class="form-control" name="kategori_menu" id="add_kategori_menu">
                            <option value="menu_biasa">- Set sebagai menu biasa</option>
                            <option value="group_menu">- Set sebagai group menu</option>
                            <option value="sub_menu">- Set sebagai sub menu</option>
                        </select>
                        <div class="invalid-feedback">Kategori menu wajib dipilih</div>
                    </div>
                    
                    <div class="form-group" id="add_nama_group_menu_wrapper">
                        <label>Nama Group Menu<span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[wm_parent_id]" id="add_nama_group_menu" disabled>
                            <option value="">Pilih Nama Group Menu</option>
                            @foreach($groupMenus as $menu)
                                <option value="{{ $menu->web_menu_global_id }}">
                                    {{ $menu->wmg_nama_default }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Nama group menu wajib dipilih</div>
                        <small class="form-text text-muted" id="add_group_menu_help">
                            Silahkan pilih group menu yang sesuai
                        </small>
                    </div>
                    
                    <div class="form-group" id="add_nama_menu_wrapper">
                        <label>Nama Menu<span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[fk_web_menu_global]" id="add_nama_menu">
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
                        <small class="form-text text-muted" id="add_menu_help">
                            Silahkan pilih nama menu biasa
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Alias</label>
                        <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="add_menu_alias" 
                            placeholder="Masukkan alias menu (opsional)">
                        <small class="form-text text-muted">
                            Alias akan ditampilkan sebagai nama menu. Jika kosong, nama default akan digunakan.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="web_menu[wm_status_menu]" id="add_status_menu">
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                        <div class="invalid-feedback">Status menu wajib diisi</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
// Form addMenu - gunakan .off() untuk mencegah binding ganda
$('#addMenuForm').off('submit').on('submit', function(e) {
    e.preventDefault();

    // Reset validasi
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    let isValid = true;
    let kategoriMenu = $('#add_kategori_menu').val();
    let namaMenu = $('#add_nama_menu').val();
    let namaGroupMenu = $('#add_nama_group_menu').val();
    let statusMenu = $('#add_status_menu').val();
    let levelMenu = $('#add_level_menu').val();
    
    // Validasi Level Menu
    if (!levelMenu) {
        $('#add_level_menu').addClass('is-invalid');
        $('#add_level_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }
    
    // Validasi berdasarkan kategori menu yang dipilih
    if (kategoriMenu === 'menu_biasa') {
        // Jika menu biasa, nama menu wajib diisi
        if (!namaMenu) {
            $('#add_nama_menu').addClass('is-invalid');
            $('#add_nama_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
    } else if (kategoriMenu === 'group_menu') {
        // Jika group menu, nama group menu wajib diisi
        if (!namaGroupMenu) {
            $('#add_nama_group_menu').addClass('is-invalid');
            $('#add_nama_group_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
    } else if (kategoriMenu === 'sub_menu') {
        // Jika sub menu, nama menu dan nama group menu wajib diisi
        if (!namaMenu) {
            $('#add_nama_menu').addClass('is-invalid');
            $('#add_nama_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
        if (!namaGroupMenu) {
            $('#add_nama_group_menu').addClass('is-invalid');
            $('#add_nama_group_menu').siblings('.invalid-feedback').show();
            isValid = false;
        }
    }

    // Validasi Status
    if (!statusMenu) {
        $('#add_status_menu').addClass('is-invalid');
        $('#add_status_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }

    if (!isValid) {
        return false;
    }

    // Persiapkan data secara manual
    let formData = $(this).serializeArray();
    
    // Jika kategori menu adalah group_menu, set fk_web_menu_global sesuai
    if (kategoriMenu === 'group_menu') {
        // Tambahkan field kosong untuk fk_web_menu_global
        formData.push({
            name: 'web_menu[fk_web_menu_global]',
            value: namaGroupMenu
        });
    }

    // Jika validasi berhasil, lanjutkan submit
    $.ajax({
        url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/store') }}",
        type: 'POST',
        data: $.param(formData),
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            console.error('Error creating menu:', xhr.responseText);
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    toastr.error(errors[key][0]);
                    // Perbaikan untuk menangani field dari array web_menu
                    if (key.startsWith('web_menu.')) {
                        const fieldName = key.replace('web_menu.', '');
                        $(`#add_${fieldName}`).addClass('is-invalid');
                        $(`#add_${fieldName}`).siblings('.invalid-feedback').text(errors[key][0]).show();
                    } else {
                        // Untuk field biasa
                        $(`[name="${key}"]`).addClass('is-invalid');
                        $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                    }
                });
            } else {
                toastr.error('Error creating menu: ' + xhr.statusText);
            }
        }
    });
});

// Event handler untuk dropdown Kategori Menu
$('#add_kategori_menu').off('change').on('change', function() {
    const selectedValue = $(this).val();
    
    if (selectedValue === 'menu_biasa') {
        // Set sebagai menu biasa
        $('#add_nama_group_menu').prop('disabled', true).val('');
        $('#add_nama_menu').prop('disabled', false);
        $('#add_menu_help').text('Silahkan pilih nama menu biasa');
        
        // Update wajib/tidak wajib
        $('#add_nama_menu').siblings('label').find('.text-danger').show();
        $('#add_nama_group_menu').siblings('label').find('.text-danger').hide();
    } 
    else if (selectedValue === 'group_menu') {
        // Set sebagai group menu
        $('#add_nama_menu').prop('disabled', true).val('');
        $('#add_nama_group_menu').prop('disabled', false);
        $('#add_group_menu_help').text('Silahkan pilih nama group menu');
        
        // Update wajib/tidak wajib
        $('#add_nama_menu').siblings('label').find('.text-danger').hide();
        $('#add_nama_group_menu').siblings('label').find('.text-danger').show();
    }
    else if (selectedValue === 'sub_menu') {
        // Set sebagai sub menu
        $('#add_nama_menu').prop('disabled', false);
        $('#add_nama_group_menu').prop('disabled', false);
        $('#add_menu_help').text('Silahkan pilih nama sub menu');
        $('#add_group_menu_help').text('Silahkan pilih group menu induk');
        
        // Update wajib/tidak wajib
        $('#add_nama_menu').siblings('label').find('.text-danger').show();
        $('#add_nama_group_menu').siblings('label').find('.text-danger').show();
    }
});

// Reset form saat modal ditampilkan
$('#addMenuModal').on('show.bs.modal', function() {
    $('#addMenuForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();
    
    // Set default values
    $('#add_kategori_menu').val('menu_biasa').trigger('change');
});
</script>
@endpush