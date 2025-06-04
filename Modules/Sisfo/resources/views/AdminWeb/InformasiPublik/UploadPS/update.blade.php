@php 
use Modules\Sisfo\App\Models\Website\WebMenuModel;
$uploadPSUrl = WebMenuModel::getDynamicMenuUrl('upload-penyelesaian-sengketa');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Layanan Informasi Upload</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="formUpdateUploadPS" action="{{ url($uploadPSUrl . '/updateData/' . $uploadPS->upload_ps_id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="fk_m_penyelesaian_sengketa">Kategori Upload Penyelesaian Sengketa <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_penyelesaian_sengketa" name="t_upload_ps[fk_m_penyelesaian_sengketa]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($penyelesaianSengketa as $kategori)
                    <option value="{{ $kategori->penyelesaian_sengketa_id }}" {{ $uploadPS->fk_m_penyelesaian_sengketa == $kategori->penyelesaian_sengketa_id ? 'selected' : '' }}>
                        {{ $kategori->ps_nama }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_m_penyelesaian_sengketa_error"></div>
        </div>

        <div class="form-group">
            <label for="kategori_upload_ps">Tipe Upload <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_upload_ps" name="t_upload_ps[kategori_upload_ps]">
                <option value="">-- Pilih Tipe Data Upload --</option>
                <option value="link" {{ $uploadPS->kategori_upload_ps == 'link' ? 'selected' : '' }}>Link</option>
                <option value="file" {{ $uploadPS->kategori_upload_ps == 'file' ? 'selected' : '' }}>File</option>
            </select>
            <div class="invalid-feedback" id="kategori_upload_ps_error"></div>
        </div>

        <div class="form-group" id="valueInputGroup" style="{{ $uploadPS->kategori_upload_ps != 'file' ? 'display:block;' : 'display:none;' }}">
            <label for="upload_ps_value">Link <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="upload_ps_value" name="t_upload_ps[upload_ps]"
                    value="{{ $uploadPS->kategori_upload_ps != 'file' ? $uploadPS->upload_ps : '' }}"
                    placeholder="https://">
            <div class="invalid-feedback" id="upload_ps_value_error"></div>
        </div>
            
        <div class="form-group" id="fileInputGroup" style="{{ $uploadPS->kategori_upload_ps == 'file' ? 'display:block;' : 'display:none;' }}">
            <label for="upload_ps_file">File Upload</label>
            @if($uploadPS->kategori_upload_ps == 'file' && $uploadPS->upload_ps)
                <div class="mb-2">
                    <a href="{{ asset('storage/' . $uploadPS->upload_ps) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-file-pdf"></i> Lihat PDF Saat Ini
                    </a>
                </div>
                <small class="d-block mb-3">Upload file baru untuk mengganti file yang ada</small>
            @endif
            <input type="file" class="form-control" id="upload_ps_file" name="upload_ps_file" accept=".pdf">
            <small class="form-text text-muted">Format file yang diperbolehkan: PDF. Ukuran maksimal 5MB.</small>
            <div class="invalid-feedback" id="upload_ps_file_error"></div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-success" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>
<script>
    $(document).ready(function () {
        // Tampilkan/sembunyikan input berdasarkan tipe yang dipilih
        $('#kategori_upload_ps').on('change', function() {
            var selectedType = $(this).val();
            if (selectedType == 'file') {
                $('#valueInputGroup').hide();
                $('#fileInputGroup').show();
                // Reset nilai input link
                $('#upload_ps_value').val('');
            } else if (selectedType == 'link') {
                $('#valueInputGroup').show();
                $('#fileInputGroup').hide();
                // Reset nilai input file
                $('#upload_ps_file').val('');
            } else {
                // Jika tidak ada yang dipilih, sembunyikan keduanya
                $('#valueInputGroup').hide();
                $('#fileInputGroup').hide();
            }
        });

        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });

        // Fungsi validasi client-side
        function validateForm() {
            let isValid = true;

            // Validasi kategori penyelesaian sengketa
            const kategoriPS = $('#fk_m_penyelesaian_sengketa').val();
            if (!kategoriPS) {
                $('#fk_m_penyelesaian_sengketa').addClass('is-invalid');
                $('#fk_m_penyelesaian_sengketa_error').html('Kategori Upload Penyelesaian Sengketa wajib dipilih');
                isValid = false;
            }

            // Validasi tipe upload
            const tipeUpload = $('#kategori_upload_ps').val();
            if (!tipeUpload) {
                $('#kategori_upload_ps').addClass('is-invalid');
                $('#kategori_upload_ps_error').html('Tipe Upload wajib dipilih');
                isValid = false;
            }

            // Validasi sesuai tipe yang dipilih
            if (tipeUpload === 'link') {
                const link = $('#upload_ps_value').val().trim();
                if (!link) {
                    $('#upload_ps_value').addClass('is-invalid');
                    $('#upload_ps_value_error').html('URL link wajib diisi');
                    isValid = false;
                } else if (!isValidURL(link)) {
                    $('#upload_ps_value').addClass('is-invalid');
                    $('#upload_ps_value_error').html('Format URL tidak valid');
                    isValid = false;
                }
            } else if (tipeUpload === 'file') {
                // Untuk file, kita hanya perlu validasi jika ada file yang dipilih
                const fileInput = $('#upload_ps_file')[0];
                if (fileInput.files.length > 0) {
                    // Validasi tipe file
                    const file = fileInput.files[0];
                    if (file.type !== 'application/pdf') {
                        $('#upload_ps_file').addClass('is-invalid');
                        $('#upload_ps_file_error').html('File harus berformat PDF');
                        isValid = false;
                    } else if (file.size > 5 * 1024 * 1024) { // 5MB
                        $('#upload_ps_file').addClass('is-invalid');
                        $('#upload_ps_file_error').html('Ukuran file maksimal 5MB');
                        isValid = false;
                    }
                }
         
            }

            return isValid;
        }

        // Helper function untuk validasi URL
        function isValidURL(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        }

        // Handle submit form
        $('#btnSubmitForm').on('click', function() {
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            
            // Jalankan validasi client-side
            if (!validateForm()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon periksa kembali input Anda.'
                });
                return;
            }
            
            const form = $('#formUpdateUploadPS');
            const formData = new FormData(form[0]);
            const button = $(this);
            
            // Tambahkan ID ke form data
            formData.append('id', '{{ $uploadPS->upload_ps_id }}');
            
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
                                // Untuk t_upload_ps fields
                                if (key.startsWith('t_upload_ps.')) {
                                    const fieldName = key.replace('t_upload_ps.', '');
                                    if (fieldName === 'upload_ps' && $('#kategori_upload_ps').val() === 'link') {
                                        $('#upload_ps_value').addClass('is-invalid');
                                        $('#upload_ps_value_error').html(value[0]);
                                    } else {
                                        $(`#${fieldName}`).addClass('is-invalid');
                                        $(`#${fieldName}_error`).html(value[0]);
                                    }
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