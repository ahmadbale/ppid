@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $IpdinamisKontenUrl = WebMenuModel::getDynamicMenuUrl('dinamis-konten');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah IpDinamis Konten</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="formCreateIpDinamisKonten" action="{{ url($IpdinamisKontenUrl . '/createData') }}" 
        method="POST">
        @csrf
        <div class="form-group">
            <label for="kd_nama_konten_dinamis">Nama Konten Dinamis <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kd_nama_konten_dinamis"
                name="m_ip_dinamis_konten[kd_nama_konten_dinamis]" maxlength="100">
            <div class="invalid-feedback" id="kd_nama_konten_dinamis_error"></div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan
    </button>
</div>

<script>
    $(document).ready(function() {
        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });

        // Form submission
        $('#btnSubmitForm').on('click', function() {
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Validasi inputan
            let isValid = true;
            const namaKontenDinamis = $('#kd_nama_konten_dinamis').val();

            if (namaKontenDinamis === '') {
                isValid = false;
                $('#kd_nama_konten_dinamis').addClass('is-invalid');
                $('#kd_nama_konten_dinamis_error').html('Nama Konten Dinamis wajib diisi.');
            } else if (namaKontenDinamis.length > 100) {
                $('#kd_nama_konten_dinamis').addClass('is-invalid');
                $('#kd_nama_konten_dinamis_error').html('Maksimal 100 karakter.');
                isValid = false;
            }

            // Jika validasi gagal, jangan kirim form
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon periksa kembali input Anda'
                });
                return;
            }

            const form = $('#formCreateIpDinamisKonten');
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
                                // Untuk m_ip_dinamis_konten fields
                                if (key.startsWith('m_ip_dinamis_konten.')) {
                                    const fieldName = key.replace(
                                        'm_ip_dinamis_konten.', '');
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
                                text: response.message ||
                                    'Terjadi kesalahan saat menyimpan data'
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
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled',
                        false);
                }
            });
        });
    });
</script>
