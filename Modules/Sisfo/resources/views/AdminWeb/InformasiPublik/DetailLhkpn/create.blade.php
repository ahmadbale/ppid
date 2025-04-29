<div class="modal-header">
    <h5 class="modal-title">Tambah Detail LHKPN Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
  <form id="formCreateDetailLhkpn" action="{{ url('adminweb/informasipublik/detail-lhkpn/createData') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="fk_m_lhkpn">Tahun LHKPN <span class="text-danger">*</span></label>
      <select class="form-control" id="fk_m_lhkpn" name="t_detail_lhkpn[fk_m_lhkpn]">
        <option value="">-- Pilih Tahun LHKPN --</option>
        @foreach ($tahunList as $item)
          <option value="{{ $item->lhkpn_id }}">{{ $item->lhkpn_tahun }} - {{ $item->lhkpn_judul_informasi }}</option>
        @endforeach
      </select>
      <div class="invalid-feedback" id="fk_m_lhkpn_error"></div>
    </div>

    <div class="form-group">
      <label for="dl_nama_karyawan">Nama Karyawan <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="dl_nama_karyawan" name="t_detail_lhkpn[dl_nama_karyawan]" maxlength="100" placeholder="Masukkan nama karyawan">
      <div class="invalid-feedback" id="dl_nama_karyawan_error"></div>
    </div>


<div class="form-group">
  <label for="dl_file_lhkpn">File LHKPN (PDF) <span class="text-danger">*</span></label>
  <div class="custom-file">
      <input type="file"
             class="custom-file-input"
             id="dl_file_lhkpn"
             name="dl_file_lhkpn"
             accept=".pdf">
      <label class="custom-file-label" for="dl_file_lhkpn">Pilih file (PDF, ukuran maksimal 2.5MB)</label>
      <div class="invalid-feedback" id="dl_file_lhkpn_error"></div>
  </div>
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
    // Update label file upload
    $('input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih file');
    });

    // Hapus error saat input berubah
    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
    });

    function validateLhkpnForm() {
        let isValid = true;

        // Reset semua error
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        // Validasi Tahun LHKPN
        const tahunLhkpn = $('#fk_m_lhkpn').val();
        if (!tahunLhkpn) {
            $('#fk_m_lhkpn').addClass('is-invalid');
            $('#fk_m_lhkpn_error').html('Tahun LHKPN wajib dipilih');
            isValid = false;
        }

        // Validasi Nama Karyawan
        const namaKaryawan = $('#dl_nama_karyawan').val().trim();
        if (!namaKaryawan) {
            $('#dl_nama_karyawan').addClass('is-invalid');
            $('#dl_nama_karyawan_error').html('Nama Karyawan wajib diisi');
            isValid = false;
        } else if (namaKaryawan.length < 3) {
            $('#dl_nama_karyawan').addClass('is-invalid');
            $('#dl_nama_karyawan_error').html('Nama Karyawan minimal 3 karakter');
            isValid = false;
        } else if (namaKaryawan.length > 100) {
            $('#dl_nama_karyawan').addClass('is-invalid');
            $('#dl_nama_karyawan_error').html('Nama Karyawan maksimal 100 karakter');
            isValid = false;
        }

        // Validasi File LHKPN
        const fileLhkpn = $('#dl_file_lhkpn')[0].files[0];
        if (!fileLhkpn) {
            $('#dl_file_lhkpn').addClass('is-invalid');
            $('#dl_file_lhkpn_error').html('File LHKPN wajib diunggah');
            isValid = false;
        } else {
            const fileType = fileLhkpn.type;
            if (fileType !== 'application/pdf') {
                $('#dl_file_lhkpn').addClass('is-invalid');
                $('#dl_file_lhkpn_error').html('File harus dalam format PDF');
                isValid = false;
            }
            const maxSize = 2.5 * 1024 * 1024;
            if (fileLhkpn.size > maxSize) {
                $('#dl_file_lhkpn').addClass('is-invalid');
                $('#dl_file_lhkpn_error').html('Ukuran file tidak boleh melebihi 2.5MB');
                // Reset file input dan label
                $('#dl_file_lhkpn').val('');
                $('#dl_file_lhkpn').next('.custom-file-label').html('Pilih file');
                isValid = false;
            }
        }
        return isValid;
    }

    // Handle Submit Form
    $('#btnSubmitForm').on('click', function() {
        if (!validateLhkpnForm()) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali input Anda.'
            });
            return;
        }

        const form = $('#formCreateDetailLhkpn');
        const formData = new FormData(form[0]);
        const button = $(this);

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

                    if (typeof reloadTable === 'function') {
                        reloadTable();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data berhasil disimpan'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                });
            },
            complete: function() {
                button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
            }
        });
    });
});
</script>
