
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $liUploadUrl = WebMenuModel::getDynamicMenuUrl('layanan-informasi-upload');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Layanan Informasi Upload</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateLIUpload" action="{{ url($liUploadUrl . '/updateData/' . $liUpload->lid_upload_id) }}"
        method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="fk_m_li_dinamis">Kategori Layanan Informasi Dinamis <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_li_dinamis" name="t_lid_upload[fk_m_li_dinamis]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($liDinamis as $dinamis)
                    <option value="{{ $dinamis->li_dinamis_id }}" {{ $liUpload->fk_m_li_dinamis == $dinamis->li_dinamis_id ? 'selected' : '' }}>
                        {{ $dinamis->li_dinamis_nama }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_m_li_dinamis_error"></div>
        </div>

        <div class="form-group">
            <label for="lid_upload_type">Tipe Upload <span class="text-danger">*</span></label>
            <select class="form-control" id="lid_upload_type" name="t_lid_upload[lid_upload_type]">
                <option value="">-- Pilih Tipe --</option>
                <option value="link" {{ $liUpload->lid_upload_type == 'link' ? 'selected' : '' }}>Link</option>
                <option value="file" {{ $liUpload->lid_upload_type == 'file' ? 'selected' : '' }}>File</option>
            </select>
            <div class="invalid-feedback" id="lid_upload_type_error"></div>
        </div>

        <div class="form-group" id="valueInputGroup" style="{{ $liUpload->lid_upload_type != 'file' ? 'display:block;' : 'display:none;' }}">
            <label for="lid_upload_value">Link <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="lid_upload_value" name="t_lid_upload[lid_upload_value]" 
                value="{{ $liUpload->lid_upload_type != 'file' ? $liUpload->lid_upload_value : '' }}" 
                placeholder="https://">
            <div class="invalid-feedback" id="lid_upload_value_error"></div>
        </div>

        <div class="form-group" id="fileInputGroup" style="{{ $liUpload->lid_upload_type == 'file' ? 'display:block;' : 'display:none;' }}">
            <label for="lid_upload_file">File Upload</label>
            @if($liUpload->lid_upload_type == 'file' && $liUpload->lid_upload_value)
                <div class="mb-2">
                    <a href="{{ asset('storage/' . $liUpload->lid_upload_value) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-file-pdf"></i> Lihat PDF Saat Ini
                    </a>
                </div>
                <small class="d-block mb-3">Upload file baru untuk mengganti file yang ada</small>
            @endif
            <input type="file" class="form-control" id="lid_upload_file" name="lid_upload_file" accept=".pdf">
            <small class="form-text text-muted">Format file yang diperbolehkan: PDF. Ukuran maksimal 10MB.</small>
            <div class="invalid-feedback" id="lid_upload_file_error"></div>
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
        // Tampilkan/sembunyikan input berdasarkan tipe yang dipilih
        $('#lid_upload_type').on('change', function() {
            var selectedType = $(this).val();
            if (selectedType == 'file') {
                $('#valueInputGroup').hide();
                $('#fileInputGroup').show();
            } else {
                $('#valueInputGroup').show();
                $('#fileInputGroup').hide();
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
            
            const form = $('#formUpdateLIUpload');
            const formData = new FormData(form[0]);
            const button = $(this);
            
            // Tambahkan ID ke form data
            formData.append('id', '{{ $liUpload->lid_upload_id }}');
            
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
                                // Untuk t_lid_upload fields
                                if (key.startsWith('t_lid_upload.')) {
                                    const fieldName = key.replace('t_lid_upload.', '');
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