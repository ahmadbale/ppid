@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $regulasiDinamisUrl = WebMenuModel::getDynamicMenuUrl('detail-regulasi');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Regulasi</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateRegulasi" action="{{ url($regulasiDinamisUrl . '/updateData/' . $regulasi->regulasi_id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="fk_t_kategori_regulasi">Kategori Regulasi <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_t_kategori_regulasi" name="t_regulasi[fk_t_kategori_regulasi]" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriRegulasi as $kategori)
                    <option value="{{ $kategori->kategori_reg_id }}" {{ $regulasi->fk_t_kategori_regulasi == $kategori->kategori_reg_id ? 'selected' : '' }}>
                        {{ $kategori->kr_nama_kategori }} ({{ $kategori->kr_kategori_reg_kode }})
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_t_kategori_regulasi_error"></div>
        </div>

        <div class="form-group">
            <label for="reg_judul">Judul Regulasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="reg_judul" name="t_regulasi[reg_judul]" maxlength="255" value="{{ $regulasi->reg_judul }}" required>
            <div class="invalid-feedback" id="reg_judul_error"></div>
        </div>

        <div class="form-group">
            <label for="reg_sinopsis">Sinopsis <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reg_sinopsis" name="t_regulasi[reg_sinopsis]" rows="4" required>{{ $regulasi->reg_sinopsis }}</textarea>
            <div class="invalid-feedback" id="reg_sinopsis_error"></div>
        </div>

        <div class="form-group">
            <label for="reg_tipe_dokumen">Tipe Dokumen <span class="text-danger">*</span></label>
            <select class="form-control" id="reg_tipe_dokumen" name="t_regulasi[reg_tipe_dokumen]" required>
                <option value="file" {{ $regulasi->reg_tipe_dokumen == 'file' ? 'selected' : '' }}>File</option>
                <option value="link" {{ $regulasi->reg_tipe_dokumen == 'link' ? 'selected' : '' }}>Link</option>
            </select>
            <div class="invalid-feedback" id="reg_tipe_dokumen_error"></div>
        </div>

        <div class="form-group" id="fileUploadDiv" {{ $regulasi->reg_tipe_dokumen == 'link' ? 'style=display:none;' : '' }}>
           <label for="reg_dokumen_file">File Dokumen</label>
           <div class="custom-file">
               <input type="file" class="custom-file-input" id="reg_dokumen_file" name="reg_dokumen_file" required>
               <label class="custom-file-label" for="reg_dokumen_file">Pilih file baru</label>
           </div>
           <small class="form-text text-muted">Format yang diizinkan: PDF, DOC, DOCX. Ukuran maksimal: 5MB</small>
           <div class="invalid-feedback" id="reg_dokumen_file_error"></div>
           
           @if($regulasi->reg_tipe_dokumen == 'file')
           <p class="mb-1">File saat ini:</p>
               <a href="{{ Storage::url($regulasi->reg_dokumen) }}" target="_blank" class="btn btn-sm btn-primary">
                 <i class="fas fa-file-pdf"></i> Lihat Dokumen
               </a>
           </div>
           @endif

        <div class="form-group" id="linkUrlDiv" {{ $regulasi->reg_tipe_dokumen == 'file' ? 'style=display:none;' : '' }}>
            <label for="reg_dokumen">URL Dokumen <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="reg_dokumen" name="t_regulasi[reg_dokumen]" placeholder="https://..." value="{{ $regulasi->reg_tipe_dokumen == 'link' ? $regulasi->reg_dokumen : '' }}" required>
            <div class="invalid-feedback" id="reg_dokumen_error"></div>
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
    $(document).ready(function() {
        // Handle dokumen tipe toggle menggunakan dropdown
        $('#reg_tipe_dokumen').on('change', function() {
            if ($(this).val() === 'file') {
                $('#fileUploadDiv').show();
                $('#linkUrlDiv').hide();
                $('#reg_dokumen').val(''); // Reset URL saat beralih ke file
            } else {
                $('#fileUploadDiv').hide();
                $('#linkUrlDiv').show();
                $('#reg_dokumen_file').val(''); // Reset file saat beralih ke link
                $('.custom-file-label').text('Pilih file');
            }
        });

        // Tampilkan nama file yang dipilih
        $('#reg_dokumen_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Pilih file baru');
        });

        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });

        // Handle submit form with client-side validation
        $('#btnSubmitForm').on('click', function() {
    // Reset validation states
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    // Get form values
    const form = $('#formUpdateRegulasi')[0];
    const formData = new FormData(form);
    const button = $(this);

    // Client-side validation rules
    const validationRules = {
        'fk_t_kategori_regulasi': {
            required: true,
            message: 'Kategori Regulasi wajib dipilih'
        },
        'reg_judul': {
            required: true,
            minLength: 3,
            maxLength: 255,
            message: 'Judul Regulasi wajib diisi (3-255 karakter)'
        },
        'reg_sinopsis': {
            required: true,
            minLength: 10,
            message: 'Sinopsis wajib diisi (minimal 10 karakter)'
        },
        'reg_tipe_dokumen': {
            required: true,
            message: 'Tipe dokumen wajib dipilih'
        }
    };

    // Validation function
    const validateField = (fieldName, value) => {
        const rules = validationRules[fieldName];
        if (!rules) return true;

        if (rules.required && !value) {
            $(`#${fieldName}`).addClass('is-invalid');
            $(`#${fieldName}_error`).html(rules.message);
            return false;
        }

        if (rules.minLength && value.length < rules.minLength) {
            $(`#${fieldName}`).addClass('is-invalid');
            $(`#${fieldName}_error`).html(`Minimal ${rules.minLength} karakter`);
            return false;
        }

        if (rules.maxLength && value.length > rules.maxLength) {
            $(`#${fieldName}`).addClass('is-invalid');
            $(`#${fieldName}_error`).html(`Maksimal ${rules.maxLength} karakter`);
            return false;
        }

        return true;
    };

    // Perform validation
    let isValid = true;

    // Validate regular fields
    Object.keys(validationRules).forEach(fieldName => {
        const value = $(`#${fieldName}`).val()?.trim();
        if (!validateField(fieldName, value)) {
            isValid = false;
        }
    });

    // Special validation for document type
    const tipeDokumen = $('#reg_tipe_dokumen').val();
    if (tipeDokumen === 'file') {
        const file = $('#reg_dokumen_file')[0].files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                $('#reg_dokumen_file').addClass('is-invalid');
                $('#reg_dokumen_file_error').html('Format file harus PDF, DOC, atau DOCX');
                isValid = false;
            }
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                $('#reg_dokumen_file').addClass('is-invalid');
                $('#reg_dokumen_file_error').html('Ukuran file maksimal 5MB');
                isValid = false;
            }
        }
    } else if (tipeDokumen === 'link') {
        const url = $('#reg_dokumen').val().trim();
        if (url && !/^https?:\/\//i.test(url)) {
            $('#reg_dokumen').addClass('is-invalid');
            $('#reg_dokumen_error').html('URL harus dimulai dengan "http://" atau "https://"');
            isValid = false;
        }
    }

    // Show error message if validation fails
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: 'Mohon periksa kembali input Anda.'
        });
        return false;
    }

    // Show loading state
    button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

    // Submit form if validation passes
    Swal.fire({
        icon: 'success',
        title: 'Data Berhasil Diperbarui!',
        text: 'Data regulasi telah berhasil diperbarui.',
        confirmButtonText: 'OK'
    }).then(() => {
        $('#myModal').modal('hide');
        reloadTable();
    });
});
    });
</script>
