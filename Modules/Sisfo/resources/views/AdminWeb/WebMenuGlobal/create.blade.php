<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\create.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah Menu Global Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
</div>
  
<div class="modal-body">
    <form id="formCreateWebMenuGlobal" action="{{ url($webMenuGlobalPath . '/createData') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="wmg_nama_default">Nama Default Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmg_nama_default" name="web_menu_global[wmg_nama_default]" maxlength="255">
            <div class="invalid-feedback" id="wmg_nama_default_error"></div>
        </div>

        <div class="form-group">
            <label for="wmg_kategori_menu">Kategori Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_kategori_menu" name="web_menu_global[wmg_kategori_menu]">
                <option value="">Pilih Kategori Menu</option>
                <option value="Menu Biasa">Menu Biasa</option>
                <option value="Group Menu">Group Menu</option>
                <option value="Sub Menu">Sub Menu</option>
            </select>
            <div class="invalid-feedback" id="wmg_kategori_menu_error"></div>
            <small class="form-text text-muted">
                <strong>Menu Biasa:</strong> Menu standalone tanpa submenu<br>
                <strong>Group Menu:</strong> Menu kelompok yang memiliki submenu<br>
                <strong>Sub Menu:</strong> Menu anak dari Group Menu
            </small>
        </div>

        <div class="form-group" id="parent_menu_group" style="display: none;">
            <label for="wmg_parent_id">Menu Induk <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_parent_id" name="web_menu_global[wmg_parent_id]">
                <option value="">Pilih Menu Induk</option>
                @foreach($parentMenus as $parent)
                    <option value="{{ $parent->web_menu_global_id }}">{{ $parent->wmg_nama_default }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="wmg_parent_id_error"></div>
        </div>

        <div class="form-group" id="menu_url_group">
            <label for="fk_web_menu_url">Menu URL <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_web_menu_url" name="web_menu_global[fk_web_menu_url]">
                <option value="">Pilih Menu URL</option>
                @foreach($menuUrls as $url)
                    <option value="{{ $url->web_menu_url_id }}">
                        {{ $url->application->app_nama }} | {{ $url->wmu_nama }} | {{ $url->wmu_keterangan ?? '-' }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_web_menu_url_error"></div>
        </div>

        <div class="form-group">
            <label for="wmg_status_menu">Status Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_status_menu" name="web_menu_global[wmg_status_menu]">
                <option value="">Pilih Status</option>
                <option value="aktif" selected>Aktif</option>
                <option value="nonaktif">Non-aktif</option>
            </select>
            <div class="invalid-feedback" id="wmg_status_menu_error"></div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Informasi:</strong> Urutan menu akan ditentukan secara otomatis oleh sistem berdasarkan data yang ada.
        </div>
    </form>
</div>
  
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-success" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan
    </button>
</div>
  
<script>
    $(document).ready(function () {
        // Handle kategori menu change
        $('#wmg_kategori_menu').on('change', function() {
            const kategori = $(this).val();
            
            if (kategori === 'Sub Menu') {
                $('#parent_menu_group').show();
                $('#menu_url_group').show();
                $('#wmg_parent_id').attr('required', true);
                $('#fk_web_menu_url').attr('required', true);
            } else if (kategori === 'Group Menu') {
                $('#parent_menu_group').hide();
                $('#menu_url_group').hide();
                $('#wmg_parent_id').attr('required', false).val('');
                $('#fk_web_menu_url').attr('required', false).val('');
            } else if (kategori === 'Menu Biasa') {
                $('#parent_menu_group').hide();
                $('#menu_url_group').show();
                $('#wmg_parent_id').attr('required', false).val('');
                $('#fk_web_menu_url').attr('required', true);
            } else {
                $('#parent_menu_group').hide();
                $('#menu_url_group').hide();
                $('#wmg_parent_id').attr('required', false);
                $('#fk_web_menu_url').attr('required', false);
            }
        });

        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });
  
        // Handle submit form
        $('#btnSubmitForm').on('click', function() {
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            
            const form = $('#formCreateWebMenuGlobal');
            const formData = new FormData(form[0]);
            const button = $(this);
            
            // Tampilkan loading state pada tombol submit
            button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#myModal').modal('hide');
                        reloadTable();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    } else {
                        if (response.errors) {
                            // Tampilkan pesan error pada masing-masing field
                            $.each(response.errors, function(key, value) {
                                // Untuk web_menu_global fields
                                if (key.startsWith('web_menu_global.')) {
                                    const fieldName = key.replace('web_menu_global.', '');
                                    $(`#${fieldName}`).addClass('is-invalid');
                                    $(`#${fieldName}_error`).html(value[0]);
                                } else {
                                    // Untuk field biasa
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}_error`).html(value[0]);
                                }
                            });
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: 'Mohon periksa kembali input Anda'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                    });
                },
                complete: function() {
                    // Kembalikan tombol submit ke keadaan semula
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                }
            });
        });
    });
</script>