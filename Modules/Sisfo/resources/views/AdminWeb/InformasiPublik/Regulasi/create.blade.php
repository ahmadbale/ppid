<div class="modal-header">
    <h5 class="modal-title">Tambah Regulasi Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formCreateRegulasi" action="{{ url('adminweb/informasipublik/regulasi/createData') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="fk_t_kategori_regulasi">Kategori Regulasi <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_t_kategori_regulasi" name="t_regulasi[fk_t_kategori_regulasi]">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriRegulasi as $kategori)
                    <option value="{{ $kategori->kategori_reg_id }}">
                        {{ $kategori->kr_nama_kategori }} ({{ $kategori->kr_kategori_reg_kode }})
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_t_kategori_regulasi_error"></div>
        </div>

        <div class="form-group">
            <label for="reg_judul">Judul Regulasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="reg_judul" name="t_regulasi[reg_judul]" maxlength="255">
            <div class="invalid-feedback" id="reg_judul_error"></div>
        </div>

        <div class="form-group">
            <label for="reg_sinopsis">Sinopsis <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reg_sinopsis" name="t_regulasi[reg_sinopsis]" rows="4"></textarea>
            <div class="invalid-feedback" id="reg_sinopsis_error"></div>
        </div>

        <div class="form-group">
            <label for="reg_tipe_dokumen">Tipe Dokumen <span class="text-danger">*</span></label>
            <select class="form-control" id="reg_tipe_dokumen" name="t_regulasi[reg_tipe_dokumen]">
                <option value="file">File</option>
                <option value="link">Link</option>
            </select>
            <div class="invalid-feedback" id="reg_tipe_dokumen_error"></div>
        </div>

        <div class="form-group" id="fileUploadDiv">
            <label for="reg_dokumen_file">File Dokumen <span class="text-danger">*</span></label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="reg_dokumen_file" name="reg_dokumen_file">
                <div class="invalid-feedback" id="reg_dokumen_file_error"></div>
                <label class="custom-file-label" for="reg_dokumen_file">Pilih file</label>
            </div>
            <small class="form-text text-muted">Format yang diizinkan: PDF, DOC, DOCX. Ukuran maksimal: 5MB</small>
        </div>

        <div class="form-group" id="linkUrlDiv" style="display: none;">
            <label for="reg_dokumen">URL Dokumen <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="reg_dokumen" name="t_regulasi[reg_dokumen]" placeholder="https://...">
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
            $(this).next('.custom-file-label').html(fileName || 'Pilih file');
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
            const form = $('#formCreateRegulasi');
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
                if (!file) {
                    $('#reg_dokumen_file').addClass('is-invalid');
                    $('#reg_dokumen_file_error').html('File Dokumen wajib dipilih.');
                    isValid = false;
                }
            } else if (tipeDokumen === 'link') {
                const url = $('#reg_dokumen').val().trim();
                if (url === '') {
                    $('#reg_dokumen').addClass('is-invalid');
                    $('#reg_dokumen_error').html('URL Dokumen wajib diisi.');
                    isValid = false;
                } else if (!/^https?:\/\//i.test(url)) {
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

            // Kirim data form menggunakan AJAX
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Close modal and refresh data table
                            $('#myModal').modal('hide');
                            reloadTable();
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        // Enable button
                        $('#btnSubmitForm').attr('disabled', false).html('Simpan');
                    }
                },
                error: function(xhr) {
                    // Enable button
                    $('#btnSubmitForm').attr('disabled', false).html('Simpan');

                    // Handle validation errors
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#error-' + key).text(value[0]);
                        });
                    } else {
                        // Show general error message
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>
