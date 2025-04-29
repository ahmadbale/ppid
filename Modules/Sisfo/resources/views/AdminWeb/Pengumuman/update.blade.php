@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $detailPengumumanUrl = WebMenuModel::getDynamicMenuUrl('detail-pengumuman');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Pengumuman</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdatePengumuman" action="{{ url($detailPengumumanUrl . '/updateData/' . $detailPengumuman->pengumuman_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pengumuman_id" value="{{ $detailPengumuman->pengumuman_id }}">

        <div class="form-group">
            <label for="kategori_pengumuman">Kategori Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_pengumuman" name="t_pengumuman[fk_m_pengumuman_dinamis]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriPengumuman as $kategori)
                    <option value="{{ $kategori->pengumuman_dinamis_id }}" {{ $detailPengumuman->fk_m_pengumuman_dinamis == $kategori->pengumuman_dinamis_id ? 'selected' : '' }}>
                        {{ $kategori->pd_nama_submenu }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_pengumuman_error"></div>
        </div>

        <div class="form-group">
            <label for="tipe_pengumuman">Tipe Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="tipe_pengumuman" name="up_type">
                <option value="">-- Pilih Tipe --</option>
                <option value="link" {{ $detailPengumuman->UploadPengumuman->up_type == 'link' ? 'selected' : '' }}>Link</option>
                <option value="file" {{ $detailPengumuman->UploadPengumuman->up_type == 'file' ? 'selected' : '' }}>File</option>
                <option value="konten" {{ $detailPengumuman->UploadPengumuman->up_type == 'konten' ? 'selected' : '' }}>Konten</option>
            </select>
            <div class="invalid-feedback" id="tipe_pengumuman_error"></div>
        </div>

        <!-- Judul Pengumuman (disembunyikan untuk tipe link) -->
        <div class="form-group" id="judul_container" style="{{ $detailPengumuman->UploadPengumuman->up_type == 'link' ? 'display: none;' : '' }}">
            <label for="judul_pengumuman">Judul Pengumuman <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_pengumuman" name="t_pengumuman[peg_judul]" maxlength="255" value="{{ $detailPengumuman->peg_judul }}">
            <div class="invalid-feedback" id="judul_pengumuman_error"></div>
        </div>

        <!-- Thumbnail (disembunyikan untuk tipe link) -->
        <div class="form-group" id="thumbnail_container" style="{{ $detailPengumuman->UploadPengumuman->up_type == 'link' ? 'display: none;' : '' }}">
            <label for="thumbnail">Thumbnail {{ $detailPengumuman->UploadPengumuman->up_thumbnail ? '' : '<span class="text-danger">*</span>' }}</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="thumbnail" name="up_thumbnail" accept="image/*">
                <label class="custom-file-label" for="thumbnail">{{ $detailPengumuman->UploadPengumuman->up_thumbnail ? 'Ganti thumbnail' : 'Pilih file' }}</label>
            </div>
            <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 10MB</small>
            <div class="invalid-feedback" id="thumbnail_error"></div>
            <div class="mt-2" id="thumbnail_preview" style="{{ $detailPengumuman->UploadPengumuman->up_thumbnail ? '' : 'display: none;' }}">
                @if($detailPengumuman->UploadPengumuman->up_thumbnail)
                    <img src="{{ asset('storage/' . $detailPengumuman->UploadPengumuman->up_thumbnail) }}" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
                @else
                    <img src="" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
                @endif
            </div>
        </div>

        <!-- URL (hanya untuk tipe link) -->
        <div class="form-group" id="url_container" style="{{ $detailPengumuman->UploadPengumuman->up_type == 'link' ? '' : 'display: none;' }}">
            <label for="url">URL <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="url" name="up_value" placeholder="https://..." value="{{ $detailPengumuman->UploadPengumuman->up_type == 'link' ? $detailPengumuman->UploadPengumuman->up_value : '' }}">
            <div class="invalid-feedback" id="url_error"></div>
        </div>

        <!-- File (hanya untuk tipe file) -->
        <div class="form-group" id="file_container" style="{{ $detailPengumuman->UploadPengumuman->up_type == 'file' ? '' : 'display: none;' }}">
            <label for="file">File {{ $detailPengumuman->UploadPengumuman->up_value ? '' : '<span class="text-danger">*</span>' }}</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="up_value">
                <label class="custom-file-label" for="file">{{ $detailPengumuman->UploadPengumuman->up_value ? 'Ganti file' : 'Pilih file' }}</label>
            </div>
            <small class="form-text text-muted">Ukuran maksimal: 50MB</small>
            <div class="invalid-feedback" id="file_error"></div>
            @if($detailPengumuman->UploadPengumuman->up_type == 'file' && $detailPengumuman->UploadPengumuman->up_value)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $detailPengumuman->UploadPengumuman->up_value) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-file mr-1"></i> Lihat File
                    </a>
                    <span class="ml-2 text-muted">{{ basename($detailPengumuman->UploadPengumuman->up_value) }}</span>
                </div>
            @endif
        </div>

        <!-- Konten (hanya untuk tipe konten) -->
        <div class="form-group" id="konten_container" style="{{ $detailPengumuman->UploadPengumuman->up_type == 'konten' ? '' : 'display: none;' }}">
            <label for="konten">Konten <span class="text-danger">*</span></label>
            <textarea id="konten" name="up_konten" class="form-control">{{ $detailPengumuman->UploadPengumuman->up_type == 'konten' ? $detailPengumuman->UploadPengumuman->up_konten : '' }}</textarea>
            <div class="invalid-feedback" id="konten_error"></div>
        </div>

        <div class="form-group">
            <label for="status_pengumuman">Status Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="status_pengumuman" name="t_pengumuman[status_pengumuman]">
                <option value="">-- Pilih Status --</option>
                <option value="aktif" {{ $detailPengumuman->status_pengumuman == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak aktif" {{ $detailPengumuman->status_pengumuman == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            <div class="invalid-feedback" id="status_pengumuman_error"></div>
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
        function validateForm() {
            let isValid = true;

            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            $('.note-editor').removeClass('border border-danger');

            const tipe = $('#tipe_pengumuman').val();
            const kategori = $('#kategori_pengumuman').val();
            const judul = $('#judul_pengumuman').val().trim();
            const url = $('#url').val().trim();
            const file = $('#file').val();
            const konten = $('#konten').val().trim();
            const status = $('#status_pengumuman').val();
            const thumbnail = $('#thumbnail').val();
            const hasOldThumbnail = $('#thumbnail_image').attr('src') !== '';

            // Validasi kategori
            if (!kategori) {
                $('#kategori_pengumuman').addClass('is-invalid');
                $('#kategori_pengumuman_error').text('Kategori harus dipilih');
                isValid = false;
            }

            // Validasi tipe
            if (!tipe) {
                $('#tipe_pengumuman').addClass('is-invalid');
                $('#tipe_pengumuman_error').text('Tipe harus dipilih');
                isValid = false;
            }

            // Validasi berdasarkan tipe
            if (tipe === 'link') {
                if (!url) {
                    $('#url').addClass('is-invalid');
                    $('#url_error').text('URL wajib diisi');
                    isValid = false;
                }
            } else {
                // Tipe file/konten membutuhkan judul
                if (!judul) {
                    $('#judul_pengumuman').addClass('is-invalid');
                    $('#judul_pengumuman_error').text('Judul wajib diisi');
                    isValid = false;
                }

                // Tipe file: wajib isi file jika tidak ada file lama
                if (tipe === 'file' && !file && !$('#file_container').find('a').length) {
                    $('#file').addClass('is-invalid');
                    $('#file_error').text('File wajib diunggah');
                    isValid = false;
                }

                // Tipe konten: konten tidak boleh kosong
                if (tipe === 'konten' && !konten) {
                    $('.note-editor').addClass('border border-danger');
                    $('#konten_error').text('Konten wajib diisi');
                    isValid = false;
                }

                // Thumbnail validasi: wajib isi jika tidak ada thumbnail sebelumnya
                if (!hasOldThumbnail && !thumbnail) {
                    $('#thumbnail').addClass('is-invalid');
                    $('#thumbnail_error').text('Thumbnail wajib diunggah');
                    isValid = false;
                }
            }

            // Validasi status
            if (!status) {
                $('#status_pengumuman').addClass('is-invalid');
                $('#status_pengumuman_error').text('Status harus dipilih');
                isValid = false;
            }

            return isValid;
        }

        $('#btnSubmitForm').off('click').on('click', function () {
            if (!validateForm()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Gagal',
                    text: 'Silakan periksa kembali input yang masih kosong atau tidak sesuai.',
                });
                return;
            }

            const form = $('#formUpdatePengumuman');
            const formData = new FormData(form[0]);
            const button = $(this);

            button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('.modal').modal('hide');
                        if (typeof reloadTable === 'function') {
                            reloadTable();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat menyimpan data.',
                        });
                    }
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                    });
                },
                complete: function () {
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                }
            });
        });

        // Tampilkan/ubah input sesuai tipe saat perubahan tipe dipilih
        $('#tipe_pengumuman').on('change', function () {
            const tipe = $(this).val();
            $('#judul_container, #thumbnail_container, #url_container, #file_container, #konten_container').hide();

            if (tipe === 'link') {
                $('#url_container').show();
            } else if (tipe === 'file') {
                $('#judul_container, #thumbnail_container, #file_container').show();
            } else if (tipe === 'konten') {
                $('#judul_container, #thumbnail_container, #konten_container').show();
            }
        }).trigger('change');
    });
</script>
