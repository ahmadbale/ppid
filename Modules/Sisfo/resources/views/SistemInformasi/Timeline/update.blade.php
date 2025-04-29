<!-- views/SistemInformasi/Timeline/update.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $timelineUrl = WebMenuModel::getDynamicMenuUrl('timeline');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Timeline</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateTimeline" action="{{ url($timelineUrl . '/updateData/' . $timeline->timeline_id) }}"
        method="POST" enctype="multipart/form-data">
        @csrf

        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle mr-2"></i> Perubahan yang dilakukan akan memperbarui seluruh langkah timeline
        </div>

        <div class="form-group">
            <label for="kategori_form">Kategori Form <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_form" name="t_timeline[fk_m_kategori_form]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($TimelineKategoriForm as $kategori)
                    <option value="{{ $kategori->kategori_form_id }}" {{ $timeline->fk_m_kategori_form == $kategori->kategori_form_id ? 'selected' : '' }}>
                        {{ $kategori->kf_nama }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_form_error"></div>
        </div>

        <div class="form-group">
            <label for="judul_timeline">Judul Timeline <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_timeline" name="t_timeline[judul_timeline]" maxlength="255"
                value="{{ $timeline->judul_timeline }}">
            <div class="invalid-feedback" id="judul_timeline_error"></div>
        </div>

        <div class="form-group">
            <label for="timeline_file">File Timeline (PDF) <span class="text-muted">(Opsional)</span></label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="timeline_file" name="timeline_file" accept=".pdf">
                <label class="custom-file-label" for="timeline_file">
                    @if($timeline->timeline_file)
                        {{ $timeline->timeline_file }} (Ganti file)
                    @else
                        Pilih file PDF (maks 5 MB)
                    @endif
                </label>
            </div>
            <div class="invalid-feedback" id="timeline_file_error"></div>
            <small class="form-text text-muted">
                Hanya file PDF dengan ukuran maksimal 5 MB. 
            </small>
            
            @if($timeline->timeline_file)
            <div class="mt-2">
                <p class="mb-1">File saat ini:</p>
                <a href="{{ Storage::url($timeline->timeline_file) }}" target="_blank" class="btn btn-sm btn-primary">
                    <i class="fas fa-file-pdf"></i> Lihat Dokumen
                </a>
            </div>
            @endif
        </div>

        <div class="mt-4 mb-3">
            <label>Langkah-langkah Timeline <span class="text-danger">*</span></label>
        </div>

        <div id="langkah_container">
            @foreach($timeline->langkahTimeline as $index => $langkah)
                <div class="form-group langkah-item" data-index="{{ $index + 1 }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="langkah_timeline_{{ $index + 1 }}" class="langkah-label">
                            Langkah {{ $index + 1 }}
                        </label>
                        <button type="button" class="btn btn-sm btn-danger delete-langkah"
                            data-langkah="{{ $langkah->langkah_timeline }}" data-index="{{ $index + 1 }}">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="langkah_timeline_{{ $index + 1 }}"
                            name="langkah_timeline_{{ $index + 1 }}" required maxlength="255"
                            value="{{ $langkah->langkah_timeline }}">
                        <div class="input-group-append">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-sort"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-right mt-3 mb-3">
            <button type="button" class="btn btn-success btn-sm" id="addLangkahBtn">
                <i class="fas fa-plus"></i> Tambah Langkah Timeline
            </button>
        </div>

        <input type="hidden" id="jumlah_langkah_timeline" name="jumlah_langkah_timeline" value="{{ $jumlahLangkah }}">
        <input type="hidden" name="existing_file" value="{{ $timeline->timeline_file }}">
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary" form="formUpdateTimeline">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
    $(document).ready(function () {
        // Tambahan untuk custom file input
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file PDF (maks. 5 MB)');
        });

        let currentLangkahCount = parseInt($('#jumlah_langkah_timeline').val());
        // Array untuk menyimpan indeks yang dihapus
        let deletedLangkahIndices = [];

        // Fungsi untuk menata ulang indeks langkah timeline yang terlihat
        function reindexVisibleSteps() {
            // Hanya mengubah label yang ditampilkan, bukan id atau name dari input
            $('.langkah-item:visible').each(function (idx) {
                $(this).find('.langkah-label').text(`Langkah ${idx + 1}`);
            });
        }

        // Fungsi untuk menghapus langkah timeline
        $(document).on('click', '.delete-langkah', function (e) {
            e.preventDefault();
            const langkahText = $(this).data('langkah');
            const index = $(this).data('index');
            const stepId = $(this).data('id') || null;

            // Konfirmasi hapus
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah anda yakin ingin menghapus langkah "${langkahText}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Sembunyikan elemen langkah dari DOM tapi tidak dihapus
                    $(this).closest('.langkah-item').hide();

                    // Tambahkan indeks yang dihapus ke daftar
                    deletedLangkahIndices.push(index);

                    // Buat hidden input untuk menandai langkah yang dihapus
                    $('#formUpdateTimeline').append(`<input type="hidden" name="deleted_step_${index}" value="1">`);

                    // Kurangi jumlah langkah yang terlihat
                    currentLangkahCount--;
                    $('#jumlah_langkah_timeline').val(currentLangkahCount);

                    // Set nilai langkah ke string kosong tapi tetap ada di form
                    // Ini penting untuk tetap mempertahankan indeks asli
                    $(`#langkah_timeline_${index}`).prop('required', false);

                    // Tata ulang label langkah yang masih terlihat
                    reindexVisibleSteps();

                    // Tampilkan pesan sukses
                    Swal.fire(
                        'Berhasil!',
                        `Langkah "${langkahText}" berhasil dihapus.`,
                        'success'
                    );
                }
            });
        });

        // Fungsi untuk menambahkan langkah timeline baru
        $('#addLangkahBtn').on('click', function (e) {
            e.preventDefault();

            // Tambah jumlah langkah
            currentLangkahCount++;
            $('#jumlah_langkah_timeline').val(currentLangkahCount);

            // Cari indeks baru yang belum digunakan
            let newIndex = 1;
            while ($(`#langkah_timeline_${newIndex}`).length > 0) {
                newIndex++;
            }

            // Buat elemen langkah baru
            const newLangkahHtml = `
            <div class="form-group langkah-item" data-index="${newIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="langkah_timeline_${newIndex}" class="langkah-label">
                        Langkah ${currentLangkahCount}
                    </label>
                    <button type="button" class="btn btn-sm btn-danger delete-langkah" 
                            data-langkah="" 
                            data-index="${newIndex}">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" id="langkah_timeline_${newIndex}"
                        name="langkah_timeline_${newIndex}" required maxlength="255"
                        value="">
                    <div class="input-group-append">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-sort"></i>
                        </span>
                    </div>
                </div>
            </div>
        `;

            // Tambahkan elemen ke container
            $('#langkah_container').append(newLangkahHtml);

            // Focus ke input baru
            $(`#langkah_timeline_${newIndex}`).focus();

            // Update atribut data-langkah saat user mengetik
            $(`#langkah_timeline_${newIndex}`).on('input', function () {
                $(this).closest('.langkah-item').find('.delete-langkah').attr('data-langkah', $(this).val());
            });
        });

        // Submit form
        $('button[form="formUpdateTimeline"]').on('click', function(e) {
            e.preventDefault();
            const form = $('#formUpdateTimeline');
            const button = $(this);
            const formData = new FormData(form[0]);

            // Reset error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Validasi ukuran file PDF
            const fileInput = $('#timeline_file')[0];
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size; // ukuran dalam bytes
                const maxSize = 5 * 1024 * 1024; // 5 MB

                if (fileSize > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'File PDF maksimal 5 MB'
                    });
                    return;
                }
            }

            // Periksa apakah semua langkah yang terlihat telah diisi
            let allFieldsFilled = true;
            $('.langkah-item:visible input').each(function () {
                if (!$(this).val()) {
                    allFieldsFilled = false;
                    return false; // keluar dari loop
                }
            });

            if (!allFieldsFilled) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Semua langkah timeline harus diisi'
                });
                return;
            }

            // Tambahkan daftar indeks yang dihapus ke form
            form.append(`<input type="hidden" name="deleted_indices" value="${JSON.stringify(deletedLangkahIndices)}">`);

            // Loading state
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
                            // Tampilkan error
                            $.each(response.errors, function(key, value) {
                                if (key.startsWith('t_timeline.')) {
                                    const fieldName = key.replace('t_timeline.', '');
                                    if (fieldName === 'fk_m_kategori_form') {
                                        $('#kategori_form').addClass('is-invalid');
                                        $('#kategori_form_error').html(value[0]);
                                    } else {
                                        $(`#${fieldName}`).addClass('is-invalid');
                                        $(`#${fieldName}_error`).html(value[0]);
                                    }
                                } else {
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
                                icon:'error',
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
                    button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                }
            });
        });
    });
</script>
</document>