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
// Variabel untuk menyimpan dropdown ID yang dipilih
let addFormPendingParentId = null;

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
    let parentId = $('#add_parent_id').val();
    
    // Log untuk debugging
    console.log('Creating menu with parent ID:', parentId);

    // Validasi Level Menu
    if (!levelMenu && !parentId) {
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

    // Persiapkan data secara manual untuk memastikan wm_parent_id dikirim dengan benar
    let formData = $(this).serializeArray();
    
    // Pastikan parent_id dikirim dengan benar
    let parentIdFound = false;
    for (let i = 0; i < formData.length; i++) {
        if (formData[i].name === 'web_menu[wm_parent_id]') {
            formData[i].value = parentId;
            parentIdFound = true;
            break;
        }
    }
    
    // Jika parent_id tidak ditemukan, tambahkan secara manual
    if (!parentIdFound && parentId) {
        formData.push({
            name: 'web_menu[wm_parent_id]',
            value: parentId
        });
    }

    // Jika validasi berhasil, lanjutkan submit
    $.ajax({
        url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/store') }}",
        type: 'POST',
        data: $.param(formData), // Gunakan data yang sudah disiapkan secara manual
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

// Event handler untuk dropdown Hak Akses
$('#add_level_menu').off('change').on('change', function() {
    let hakAksesId = $(this).val();
    // Simpan nilai parent dropdown yang telah dipilih
    addFormPendingParentId = $('#add_parent_id').val();
    updateAddFormParentMenuOptions(hakAksesId);
});

// Fungsi khusus untuk form add - agar tidak konflik dengan form update
function updateAddFormParentMenuOptions(hakAksesId) {
    // Reset dropdown
    const targetSelect = $('#add_parent_id');
    targetSelect.empty().append('<option value="">-Set Sebagai Menu Utama</option>');

    // Jika tidak ada hakAksesId, tidak perlu memuat data
    if (!hakAksesId) return;

    // Dapatkan URL dinamis
    const dynamicUrl = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/get-parent-menus') }}";

    // Lakukan request AJAX untuk mendapatkan parent menu berdasarkan level
    $.ajax({
        url: `${dynamicUrl}/${hakAksesId}`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.parentMenus) {
                response.parentMenus.forEach(function(menu) {
                    // Gunakan display_name yang sudah diproses di server
                    targetSelect.append(`
                        <option value="${menu.web_menu_id}" data-level="${hakAksesId}">
                            ${menu.display_name}
                        </option>
                    `);
                });
                
                // Setelah dropdown diisi, atur nilai jika ada addFormPendingParentId
                if (addFormPendingParentId) {
                    console.log('Setting parent ID in create form to:', addFormPendingParentId);
                    targetSelect.val(addFormPendingParentId);
                    // Reset pendingParentId setelah digunakan
                    addFormPendingParentId = null;
                }
            }
        },
        error: function() {
            toastr.error('Gagal memuat menu induk');
        }
    });
}

// Reset form saat modal ditampilkan
$('#addMenuModal').on('show.bs.modal', function() {
    $('#addMenuForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();
    $('#add_parent_id').empty().append('<option value="">-Set Sebagai Menu Utama</option>');
    addFormPendingParentId = null;
});
</script>
@endpush