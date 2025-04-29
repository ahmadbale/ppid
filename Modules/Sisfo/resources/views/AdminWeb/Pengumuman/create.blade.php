@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $detailPengumumanUrl = WebMenuModel::getDynamicMenuUrl('detail-pengumuman');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah Pengumuman Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formCreatePengumuman" action="{{ url($detailPengumumanUrl . '/createData') }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="kategori_pengumuman">Kategori Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_pengumuman" name="t_pengumuman[fk_m_pengumuman_dinamis]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriPengumuman as $kategori)
                    <option value="{{ $kategori->pengumuman_dinamis_id }}">{{ $kategori->pd_nama_submenu }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_pengumuman_error"></div>
        </div>

        <div class="form-group">
            <label for="tipe_pengumuman">Tipe Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="tipe_pengumuman" name="up_type">
                <option value="">-- Pilih Tipe --</option>
                <option value="link">Link</option>
                <option value="file">File</option>
                <option value="konten">Konten</option>
            </select>
            <div class="invalid-feedback" id="tipe_pengumuman_error"></div>
        </div>

        <!-- Judul Pengumuman (disembunyikan untuk tipe link) -->
        <div class="form-group" id="judul_container" style="display: none;">
            <label for="judul_pengumuman">Judul Pengumuman <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_pengumuman" name="t_pengumuman[peg_judul]"
                maxlength="255">
            <div class="invalid-feedback" id="judul_pengumuman_error"></div>
        </div>

        <!-- Thumbnail (disembunyikan untuk tipe link) -->
        <div class="form-group" id="thumbnail_container" style="display: none;">
            <label for="thumbnail">Thumbnail <span class="text-danger">*</span></label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="thumbnail" name="up_thumbnail" accept="image/*">
                <label class="custom-file-label" for="thumbnail">Pilih file</label>
            </div>
            <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 10MB</small>
            <div class="invalid-feedback" id="thumbnail_error"></div>
            <div class="mt-2" id="thumbnail_preview" style="display: none;">
                <img src="" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
            </div>
        </div>

        <!-- URL (hanya untuk tipe link) -->
        <div class="form-group" id="url_container" style="display: none;">
            <label for="url">URL <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="url" name="up_value" placeholder="https://...">
            <div class="invalid-feedback" id="url_error"></div>
        </div>

        <!-- File (hanya untuk tipe file) -->
        <div class="form-group" id="file_container" style="display: none;">
            <label for="file">File <span class="text-danger">*</span></label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="up_value">
                <label class="custom-file-label" for="file">Pilih file</label>
            </div>
            <small class="form-text text-muted">Ukuran maksimal: 50MB</small>
            <div class="invalid-feedback" id="file_error"></div>
        </div>

        <!-- Konten (hanya untuk tipe konten) -->
        <div class="form-group" id="konten_container" style="display: none;">
            <label for="konten">Konten <span class="text-danger">*</span></label>
            <textarea id="konten" name="up_konten" class="form-control"></textarea>
            <div class="invalid-feedback" id="konten_error"></div>
        </div>

        <div class="form-group">
            <label for="status_pengumuman">Status Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="status_pengumuman" name="t_pengumuman[status_pengumuman]">
                <option value="">-- Pilih Status --</option>
                <option value="aktif">Aktif</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
            <div class="invalid-feedback" id="status_pengumuman_error"></div>
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
    $(document).ready(function () {
        // Preview thumbnail saat dipilih
        $('#thumbnail').on('change', function () {
            const file = this.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10MB

                if (file.size > maxSize) {
                    $('#thumbnail').addClass('is-invalid');
                    $('#thumbnail_error').html('Ukuran maksimal thumbnail 10MB.');
                    $('#thumbnail_preview').hide();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#thumbnail_image').attr('src', e.target.result);
                    $('#thumbnail_preview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#thumbnail_preview').hide();
            }
        });

        // Tampilkan nama file pada label input
        $('.custom-file-input').on('change', function () {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass('selected').html(fileName);
        });

        // Tombol submit
        $('#btnSubmitForm').off('click').on('click', function () {
            console.log('Tombol submit diklik');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            $('.note-editor').removeClass('border border-danger');

            const form = $('#formCreatePengumuman');
            const formData = new FormData(form[0]);
            const button = $(this);
            let isValid = true;

            const kategori = $('#kategori_pengumuman').val();
            const tipe = $('#tipe_pengumuman').val();
            const judul = $('#judul_pengumuman').val();
            const url = $('#url').val();
            const file = $('#file')[0].files[0];
            const thumbnail = $('#thumbnail')[0].files[0];
            const konten = $('#konten').val();
            const status = $('#status_pengumuman').val();

            // Validasi client-side
            if (!kategori) {
                $('#kategori_pengumuman').addClass('is-invalid');
                $('#kategori_pengumuman_error').html('Kategori wajib dipilih.');
                isValid = false;
            }

            if (!tipe) {
                $('#tipe_pengumuman').addClass('is-invalid');
                $('#tipe_pengumuman_error').html('Tipe pengumuman wajib dipilih.');
                isValid = false;
            }

            if (tipe !== 'link' && !judul) {
                $('#judul_pengumuman').addClass('is-invalid');
                $('#judul_pengumuman_error').html('Judul wajib diisi.');
                isValid = false;
            }

            if (tipe === 'link' && !url) {
                $('#url').addClass('is-invalid');
                $('#url_error').html('URL wajib diisi.');
                isValid = false;
            }

            if (tipe === 'file' && !file) {
                $('#file').addClass('is-invalid');
                $('#file_error').html('File wajib dipilih.');
                isValid = false;
            } else if (file && file.size > 50 * 1024 * 1024) {
                $('#file').addClass('is-invalid');
                $('#file_error').html('Ukuran maksimal file 50MB.');
                isValid = false;
            }

            if (tipe === 'konten' && !konten.trim()) {
                $('.note-editor').addClass('border border-danger');
                $('#konten_error').html('Konten wajib diisi.').show();
                isValid = false;
            }

            if (tipe !== 'link' && !thumbnail) {
                $('#thumbnail').addClass('is-invalid');
                $('#thumbnail_error').html('Thumbnail wajib dipilih.');
                isValid = false;
            } else if (thumbnail) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(thumbnail.type)) {
                    $('#thumbnail').addClass('is-invalid');
                    $('#thumbnail_error').html('Hanya gambar JPG, PNG, dan GIF yang diizinkan.');
                    isValid = false;
                } else if (thumbnail.size > 10 * 1024 * 1024) {
                    $('#thumbnail').addClass('is-invalid');
                    $('#thumbnail_error').html('Ukuran maksimal thumbnail 10MB.');
                    isValid = false;
                }
            }

            if (!status) {
                $('#status_pengumuman').addClass('is-invalid');
                $('#status_pengumuman_error').html('Status wajib dipilih.');
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon periksa kembali input Anda'
                });
                return;
            }

            // Tambahkan pengumuman_id null untuk validasi
            formData.append('pengumuman_id', null);

            // Loading
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
                        } else {
                            console.warn('Fungsi reloadTable tidak ditemukan');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function (key, value) {
                                if (key.startsWith('t_pengumuman.')) {
                                    const fieldName = key.replace('t_pengumuman.', '');
                                    if (fieldName === 'fk_m_pengumuman_dinamis') {
                                        $('#kategori_pengumuman').addClass('is-invalid');
                                        $('#kategori_pengumuman_error').html(value[0]);
                                    } else if (fieldName === 'peg_judul') {
                                        $('#judul_pengumuman').addClass('is-invalid');
                                        $('#judul_pengumuman_error').html(value[0]);
                                    } else if (fieldName === 'status_pengumuman') {
                                        $('#status_pengumuman').addClass('is-invalid');
                                        $('#status_pengumuman_error').html(value[0]);
                                    }
                                } else if (key === 'up_type') {
                                    $('#tipe_pengumuman').addClass('is-invalid');
                                    $('#tipe_pengumuman_error').html(value[0]);
                                } else if (key === 'up_value') {
                                    if (tipe === 'link') {
                                        $('#url').addClass('is-invalid');
                                        $('#url_error').html(value[0]);
                                    } else {
                                        $('#file').addClass('is-invalid');
                                        $('#file_error').html(value[0]);
                                    }
                                } else if (key === 'up_thumbnail') {
                                    $('#thumbnail').addClass('is-invalid');
                                    $('#thumbnail_error').html(value[0]);
                                } else if (key === 'up_konten') {
                                    $('.note-editor').addClass('border border-danger');
                                    $('#konten_error').html(value[0]).show();
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
                error: function (xhr) {
                    console.error('AJAX Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                    });
                },
                complete: function () {
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                }
            });
        });

        // Trigger perubahan tampilan berdasarkan tipe
        $('#tipe_pengumuman').trigger('change');
    });
</script>
