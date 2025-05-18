<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\update.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

<!-- Edit Menu Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
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
                        <label>Kategori Menu</label>
                        <select class="form-control" name="web_menu[wm_parent_id]" id="edit_parent_id">
                            <option value="">-Set Sebagai Menu Utama</option>
                            <!-- Menu parent akan diisi menggunakan JavaScript -->
                        </select>
                        <small class="form-text text-muted">Jika memilih kategori menu, jenis menu akan otomatis
                            menyesuaikan dengan jenis menu induk.</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="edit_menu_nama">
                        <div class="invalid-feedback">Nama menu wajib diisi</div>
                    </div>
                    <div class="form-group">
                        <label>URL Menu</label>
                        <select class="form-control" name="web_menu[fk_web_menu_url]" id="edit_menu_url">
                            <option value="">Pilih URL</option>
                            <option value="">Null - Menu Utama dengan Sub Menu</option>
                            @foreach($menuUrls as $url)
                                <option value="{{ $url->web_menu_url_id }}">{{ $url->wmu_nama }}</option>
                            @endforeach
                        </select>
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

// Event handler untuk dropdown Hak Akses
$('#edit_level_menu').off('change').on('change', function() {
    let hakAksesId = $(this).val();
    let menuId = $('#edit_menu_id').val();
    updateParentMenuOptions(hakAksesId, $('#edit_parent_id'), menuId);
});

// Form editMenu
$('#editMenuForm').off('submit').on('submit', function(e) {
    e.preventDefault();
    console.log('Edit form submitted');

    // Reset validasi
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    let isValid = true;
    let menuNama = $('#edit_menu_nama').val().trim();
    let statusMenu = $('#edit_status_menu').val();
    let levelMenu = $('#edit_level_menu').val();

    // Validasi Nama Menu
    if (!menuNama) {
        $('#edit_menu_nama').addClass('is-invalid');
        $('#edit_menu_nama').siblings('.invalid-feedback').show();
        isValid = false;
    }

    // Validasi Status
    if (!statusMenu) {
        $('#edit_status_menu').addClass('is-invalid');
        $('#edit_status_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }

    // Validasi Level Menu jika tidak ada parent
    if (!$('#edit_parent_id').val() && !levelMenu) {
        $('#edit_level_menu').addClass('is-invalid');
        $('#edit_level_menu').siblings('.invalid-feedback').show();
        isValid = false;
    }

    if (!isValid) {
        console.log('Form tidak valid');
        return false;
    }

    // Validasi menu SAR
    if (originalMenuLevel === 'SAR' && '{{ Auth::user()->level->hak_akses_kode }}' !== 'SAR') {
        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
        return false;
    }

    // Jika ada parent, pastikan level menu diambil dari parent
    if ($('#edit_parent_id').val()) {
        // Aktifkan kembali level_menu agar dikirim dengan form
        $('#edit_level_menu').prop('disabled', false);
    }

    let menuId = $('#edit_menu_id').val();
    console.log('Submitting form for menu ID:', menuId);
    
    $.ajax({
        url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/update`,
        type: 'PUT',
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
            console.error('Error updating menu:', xhr.responseText);
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    toastr.error(errors[key][0]);
                    // Tandai field yang error
                    $(`[name="${key}"]`).addClass('is-invalid');
                    $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                });
            } else {
                toastr.error('Error updating menu: ' + xhr.statusText);
            }
        }
    });
});

// Event handler untuk tombol Edit
$(document).off('click', '.edit-menu').on('click', '.edit-menu', function() {
    let menuId = $(this).data('id');
    let hakAksesKode = $(this).data('level-kode');
    
    $('#edit_menu_id').val(menuId);

    $.ajax({
        url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/edit`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // Set nilai form berdasarkan data dari response
                $('#edit_menu_nama').val(response.menu.wm_menu_nama);
                $('#edit_menu_url').val(response.menu.fk_web_menu_url);
                $('#edit_status_menu').val(response.menu.wm_status_menu);
                
                // Set level menu
                $('#edit_level_menu').val(response.menu.fk_m_hak_akses || '');
                
                // Perbarui dropdown Kategori Menu berdasarkan Level yang dipilih
                // Pass menu ID untuk dikecualikan dari daftar parent
                updateParentMenuOptions(
                    response.menu.fk_m_hak_akses, 
                    $('#edit_parent_id'),
                    menuId // Kirim menuId untuk dikecualikan
                );
                
                // Set selected parent - set setelah dropdown diupdate
                // Kita tambahkan delay untuk memastikan dropdown sudah terisi
                setTimeout(function() {
                    $('#edit_parent_id').val(response.menu.wm_parent_id);
                }, 500); // Tambah delay lebih lama untuk memastikan dropdown sudah terisi
                
                // Disable/enable level field based on parent
                if (response.menu.wm_parent_id) {
                    $('#edit_level_menu').prop('disabled', true);
                } else {
                    $('#edit_level_menu').prop('disabled', false);
                }
                
                // Store original menu level for validation
                originalMenuLevel = response.menu.hak_akses_kode;
                
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
</script>
@endpush