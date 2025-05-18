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
                        <label>Kategori Menu</label>
                        <select class="form-control" name="web_menu[wm_parent_id]" id="add_parent_id">
                            <option value="">-Set Sebagai Menu Utama</option>
                        </select>
                        <small class="form-text text-muted">Jika memilih kategori menu, jenis menu akan otomatis menyesuaikan dengan jenis menu induk.</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Menu<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="add_menu_nama">
                        <div class="invalid-feedback">Nama menu wajib diisi</div>
                    </div>
                    <div class="form-group">
                        <label>URL Menu</label>
                        <select class="form-control" name="web_menu[fk_web_menu_url]" id="add_menu_url">
                            <option value="">Pilih URL</option>
                            <option value="">Null - Menu Utama dengan Sub Menu</option>
                            @foreach($menuUrls as $url)
                                <option value="{{ $url->web_menu_url_id }}">{{ $url->wmu_nama }}</option>
                            @endforeach
                        </select>
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
    let menuNama = $('#add_menu_nama').val().trim();
    let statusMenu = $('#add_status_menu').val();
    let levelMenu = $('#add_level_menu').val();
    // URL menu bisa kosong untuk menu utama dengan submenu
    let menuUrl = $('#add_menu_url').val();

    // Validasi Level Menu
    if (!levelMenu) {
        $('#add_level_menu').addClass('is-invalid');
        $('#add_level_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }

    // Validasi Nama Menu
    if (!menuNama) {
        $('#add_menu_nama').addClass('is-invalid');
        $('#add_menu_nama').siblings('.invalid-feedback').show();
        isValid = false;
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

    // Jika validasi berhasil, lanjutkan submit
    $.ajax({
        url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/store') }}",
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    toastr.error(errors[key][0]);
                    $(`[name="${key}"]`).addClass('is-invalid');
                    $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                });
            } else {
                toastr.error('Error creating menu');
            }
        }
    });
});

// Event handler untuk dropdown Hak Akses
$('#add_level_menu').off('change').on('change', function() {
    let hakAksesId = $(this).val();
    updateParentMenuOptions(hakAksesId, $('#add_parent_id'));
});
</script>
@endpush