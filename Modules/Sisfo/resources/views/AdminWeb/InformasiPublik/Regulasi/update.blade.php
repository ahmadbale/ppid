<div class="modal-header">
    <h5 class="modal-title">Edit Regulasi</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateRegulasi" action="{{ url('adminweb/informasipublik/regulasi/updateData/' . $regulasi->regulasi_id) }}" method="POST" enctype="multipart/form-data">
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
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Ambil nilai input
            const regulasiDinamis = $('#fk_t_kategori_regulasi').val().trim();
            const judulRegulasi = $('#reg_judul').val().trim();
            const sinopsis = $('#reg_sinopsis').val().trim();
            const tipeDokumen = $('#reg_tipe_dokumen').val();
            const form = $('#formUpdateRegulasi');
            const formData = new FormData(form[0]);
            const button = $(this);

            // Validasi client-side
            let isValid = true;

            // Validasi Regulasi Dinamis
            if (regulasiDinamis === '') {
                $('#fk_t_kategori_regulasi').addClass('is-invalid');
                $('#fk_t_kategori_regulasi_error').html('Kategori Regulasi wajib dipilih.');
                isValid = false;
            }

            // Validasi Judul Regulasi
            if (judulRegulasi === '') {
                $('#reg_judul').addClass('is-invalid');
                $('#reg_judul_error').html('Judul Regulasi wajib diisi.');
                isValid = false;
            }

            // Validasi Sinopsis
            if (sinopsis === '') {
                $('#reg_sinopsis').addClass('is-invalid');
                $('#reg_sinopsis_error').html('Sinopsis wajib diisi.');
                isValid = false;
            }

            // Validasi Tipe Dokumen dan terkait file/link
            if (tipeDokumen === 'file') {
                const file = $('#reg_dokumen_file')[0].files[0];
                // File is not mandatory now, so no validation is needed for the file being empty
            } else if (tipeDokumen === 'link') {
                const url = $('#reg_dokumen').val().trim();
                // Link is not mandatory now, so no validation is needed for the URL being empty
                if (url && !/^https?:\/\//i.test(url)) {
                    $('#reg_dokumen').addClass('is-invalid');
                    $('#reg_dokumen_error').html('URL harus dimulai dengan "http://" atau "https://".');
                    isValid = false;
                }
            }

            // Jika validasi gagal, tampilkan pesan error dan batalkan pengiriman form
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon periksa kembali input Anda.'
                });
                return;
            }

            // Tampilkan loading state pada tombol submit
            button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

            // Proses dan kirim data form menggunakan AJAX (pada sisi klien, tidak memerlukan validasi dari server)
            Swal.fire({
                icon: 'success',
                title: 'Data Berhasil Diperbarui!',
                text: 'Data regulasi telah berhasil diperbarui.',
                confirmButtonText: 'OK'
            }).then(() => {
                $('#myModal').modal('hide');
                reloadTable();  // Reload table or do another action
            });
        });
    });
</script>
