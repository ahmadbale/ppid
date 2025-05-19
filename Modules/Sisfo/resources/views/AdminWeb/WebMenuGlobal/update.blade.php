<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\update.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Menu Global</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateWebMenuGlobal" action="{{ url($webMenuGlobalPath . '/updateData/' . $webMenuGlobal->web_menu_global_id) }}"
        method="POST">
        @csrf

        <div class="form-group">
            <label for="wmg_nama_default">Nama Default Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmg_nama_default" name="web_menu_global[wmg_nama_default]" 
                   value="{{ $webMenuGlobal->wmg_nama_default }}" maxlength="255">
            <div class="invalid-feedback" id="wmg_nama_default_error"></div>
        </div>

        <div class="form-group">
            <label for="fk_web_menu_url">Menu URL</label>
            <select class="form-control" id="fk_web_menu_url" name="web_menu_global[fk_web_menu_url]">
                <option value="null" {{ $webMenuGlobal->fk_web_menu_url === null ? 'selected' : '' }}>Group Menu</option>
                @foreach($menuUrls as $url)
                    <option value="{{ $url->web_menu_url_id }}" {{ $webMenuGlobal->fk_web_menu_url == $url->web_menu_url_id ? 'selected' : '' }}>
                        {{ $url->application->app_nama }} | {{ $url->wmu_nama }} | {{ $url->wmu_keterangan ?? '-' }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Pilih "Group Menu" untuk menu tanpa URL spesifik.</small>
            <div class="invalid-feedback" id="fk_web_menu_url_error"></div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
    $(document).ready(function () {
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
            
            const form = $('#formUpdateWebMenuGlobal');
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
                    button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                }
            });
        });
    });
</script>