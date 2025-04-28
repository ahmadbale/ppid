<div class="modal-header">
    <h5 class="modal-title">Tambah Berita Dinamis</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-berita-dinamis">
    <div class="modal-body">
        <div class="form-group">
            <label for="bd_nama_submenu">Nama Submenu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="bd_nama_submenu" name="bd_nama_submenu" maxlength="100">
            <div class="invalid-feedback" id="bd_nama_submenu_error"></div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function () {
            $(this).removeClass('is-invalid');
            // const errorId = `#error-${$(this).attr('id')}`;
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });

        // Form submission with client-side validation
        $('#form-create-berita-dinamis').on('submit', function (e) {
            e.preventDefault();

            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Ambil nilai input
            const namaSubmenu = $('#bd_nama_submenu').val().trim();
            const form = $('#form-create-berita-dinamis');
            const formData = new FormData(form[0]);
            const button = $('#btn-save');

            // Validasi client-side
            let isValid = true;

            // Validasi Nama Submenu
            if (namaSubmenu === '') {
                $('#bd_nama_submenu').addClass('is-invalid');
                $('#bd_nama_submenu_error').html('Nama Submenu wajib diisi.');
                isValid = false;
            } else if (namaSubmenu.length > 100) {
                $('#bd_nama_submenu').addClass('is-invalid');
                $('#bd_nama_submenu_error').html('Maksimal 100 karakter.');
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
                url: '{{ url("adminweb/berita-dinamis/createData") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                        $('#btn-save').attr('disabled', false).html('Simpan');
                    }
                },
                error: function (xhr) {
                    // Enable button
                    $('#btn-save').attr('disabled', false).html('Simpan');

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
