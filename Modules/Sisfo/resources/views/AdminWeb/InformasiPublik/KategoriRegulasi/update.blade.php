<div class="modal-header">
    <h5 class="modal-title">Ubah Kategori Regulasi</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateKategoriRegulasi" action="{{ url('adminweb/informasipublik/kategori-regulasi/updateData/' . $kategoriRegulasi->kategori_reg_id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="fk_regulasi_dinamis">Regulasi Dinamis <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_regulasi_dinamis" name="m_kategori_regulasi[fk_regulasi_dinamis]">
                <option value="">Pilih Regulasi Dinamis</option>
                @foreach($regulasiDinamis as $regulasi)
                    <option value="{{ $regulasi->regulasi_dinamis_id }}" 
                        {{ $kategoriRegulasi->fk_regulasi_dinamis == $regulasi->regulasi_dinamis_id ? 'selected' : '' }}>
                        {{ $regulasi->rd_judul_reg_dinamis }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_regulasi_dinamis_error"></div>
        </div>

        <div class="form-group">
            <label for="kr_kategori_reg_kode">Kode Kategori Regulasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kr_kategori_reg_kode" name="m_kategori_regulasi[kr_kategori_reg_kode]" maxlength="20" 
            value="{{ $kategoriRegulasi->kr_kategori_reg_kode }}">
            <div class="invalid-feedback" id="kr_kategori_reg_kode_error"></div>
        </div>

        <div class="form-group">
            <label for="kr_nama_kategori">Nama Kategori Regulasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kr_nama_kategori" name="m_kategori_regulasi[kr_nama_kategori]" maxlength="200"
            value="{{ $kategoriRegulasi->kr_nama_kategori }}">
            <div class="invalid-feedback" id="kr_nama_kategori_error"></div>
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

        // Handle submit form with client-side validation
        $('#btnSubmitForm').on('click', function() {
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Ambil nilai input
            const regulasiDinamis = $('#fk_regulasi_dinamis').val().trim();
            const kodeKategori = $('#kr_kategori_reg_kode').val().trim();
            const namaKategori = $('#kr_nama_kategori').val().trim();
            const form = $('#formUpdateKategoriRegulasi');
            const formData = new FormData(form[0]);
            const button = $(this);

            // Validasi client-side
            let isValid = true;

            // Validasi Regulasi Dinamis
            if (regulasiDinamis === '') {
                $('#fk_regulasi_dinamis').addClass('is-invalid');
                $('#fk_regulasi_dinamis_error').html('Regulasi Dinamis wajib dipilih.');
                isValid = false;
            }

            // Validasi Kode Kategori
            if (kodeKategori === '') {
                $('#kr_kategori_reg_kode').addClass('is-invalid');
                $('#kr_kategori_reg_kode_error').html('Kode Kategori Regulasi wajib diisi.');
                isValid = false;
            }

            // Validasi Nama Kategori
            if (namaKategori === '') {
                $('#kr_nama_kategori').addClass('is-invalid');
                $('#kr_nama_kategori_error').html('Nama Kategori Regulasi wajib diisi.');
                isValid = false;
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
                success: function (response) {
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
                        $('#btnSubmitForm').attr('disabled', false).html('Simpan Perubahan');
                    }
                },
                error: function (xhr) {
                    // Enable button
                    $('#btnSubmitForm').attr('disabled', false).html('Simpan Perubahan');

                    // Handle validation errors
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
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
