{{-- Modal Create - Web Menu URL --}}
@include('sisfo::components.web-menu-url.modal-styles')
@include('sisfo::components.web-menu-url.shared-scripts')

<div class="modal-header bg-primary">
    <h5 class="modal-title text-white">
        <i class="fas fa-plus-circle mr-2"></i>Tambah URL Menu
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreate" method="POST" action="{{ url('management-menu-url/createData') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">

        {{-- Aplikasi --}}
        <div class="form-group">
            <label for="fk_m_application">Aplikasi <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_application" name="web_menu_url[fk_m_application]">
                <option value="">Pilih Aplikasi</option>
                @foreach($applications as $app)
                    <option value="{{ $app->application_id }}">{{ $app->app_nama }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_m_application_error"></div>
        </div>

        {{-- Nama URL Menu --}}
        <div class="form-group">
            <label for="wmu_nama">Nama URL Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmu_nama"
                   name="web_menu_url[wmu_nama]" maxlength="255"
                   placeholder="Contoh: kategori-footer, web-banner">
            <small class="text-muted">Gunakan huruf kecil dan tanda hubung (-). Ini akan menjadi URL halaman.</small>
            <div class="invalid-feedback" id="wmu_nama_error"></div>
        </div>

        {{-- Keterangan --}}
        <div class="form-group">
            <label for="wmu_keterangan">Keterangan</label>
            <textarea class="form-control" id="wmu_keterangan"
                      name="web_menu_url[wmu_keterangan]" rows="2" maxlength="1000"
                      placeholder="Deskripsi singkat tentang URL menu ini"></textarea>
            <div class="invalid-feedback" id="wmu_keterangan_error"></div>
        </div>

        {{-- Kategori Menu --}}
        <div class="form-group">
            <label for="wmu_kategori_menu">Kategori Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmu_kategori_menu" name="web_menu_url[wmu_kategori_menu]">
                <option value="">Pilih Kategori</option>
                <option value="master">Master (CRUD Otomatis)</option>
                <option value="custom">Custom (Controller Manual)</option>
                <option value="pengajuan">Pengajuan (Coming Soon)</option>
            </select>
            <div class="invalid-feedback" id="wmu_kategori_menu_error"></div>
        </div>

        {{-- Section berdasarkan kategori --}}
        @include('sisfo::components.web-menu-url.section-custom', ['mode' => 'create'])
        @include('sisfo::components.web-menu-url.section-master', ['mode' => 'create'])
        @include('sisfo::components.web-menu-url.section-pengajuan')

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Batal
        </button>
        <button type="button" class="btn btn-primary" id="btnSubmit">
            <i class="fas fa-save mr-1"></i>Simpan
        </button>
    </div>
</form>

<script>
$(document).ready(function() {

    // ==========================================
    // KATEGORI MENU - Toggle section
    // ==========================================
    $('#wmu_kategori_menu').on('change', function() {
        const val = $(this).val();
        $('#section-custom, #section-master, #section-pengajuan').hide();
        if (val === 'custom')    $('#section-custom').show();
        if (val === 'master')    $('#section-master').show();
        if (val === 'pengajuan') $('#section-pengajuan').show();
    });

    // ==========================================
    // CEK TABEL - AJAX validate & auto-generate fields
    // ==========================================
    $('#btnCekTabel').on('click', function() {
        const tableName = $('#wmu_akses_tabel').val().trim();
        if (!tableName) {
            $('#wmu_akses_tabel').addClass('is-invalid');
            $('#wmu_akses_tabel_error').text('Nama tabel wajib diisi');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengecek...');

        const baseUrl = '{{ url("management-menu-url") }}';

        // Reset state
        $('#field-configurator').hide();
        $('#fieldConfigBody').empty();
        $('input[name="existing_menu_id"]').remove();
        $('input[name="is_update"]').remove();
        $('#wmu_akses_tabel').removeClass('is-invalid is-valid');
        $('#wmu_akses_tabel_error').html('').css('display', '');
        $('#wmu_akses_tabel_success').hide().html('').removeClass('alert alert-warning alert-success alert-danger alert-info');
        $('#btnSubmit').prop('disabled', false);

        $.ajax({
            url: baseUrl,
            method: 'POST',
            data: { 
                action: 'validateTable', 
                table_name: tableName,
                _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
            },
            dataType: 'json',
            success: function(res) {
                if (!res.success && res.isDuplicate && !res.hasChanges) {
                    // ❌ Tabel sudah terdaftar, tanpa perubahan → BLOCK
                    $('#wmu_akses_tabel').addClass('is-invalid').removeClass('is-valid');
                    $('#wmu_akses_tabel_error').html(res.message || 'Tabel sudah terdaftar').css('display', 'block');
                    $('#wmu_akses_tabel_success').hide();
                    $('#field-configurator').hide();
                    $('#btnSubmit').prop('disabled', true);
                    return;
                }

                if (!res.success) {
                    // ❌ Tabel tidak ada / error lain
                    $('#wmu_akses_tabel').addClass('is-invalid').removeClass('is-valid');
                    $('#wmu_akses_tabel_error').html(res.message || 'Tabel tidak ditemukan').css('display', 'block');
                    $('#wmu_akses_tabel_success').hide();
                    $('#field-configurator').hide();
                    return;
                }

                // ✅ Tabel valid — bersihkan error
                $('#wmu_akses_tabel').removeClass('is-invalid is-valid');
                $('#wmu_akses_tabel_error').html('').css('display', '');

                if (res.isDuplicate && res.hasChanges) {
                    // ⚠️ Tabel sudah terdaftar TAPI ada perubahan → boleh daftar ulang
                    $('#wmu_akses_tabel_success')
                        .removeClass('alert-success alert-danger alert-info')
                        .addClass('alert alert-warning')
                        .html('<i class="fas fa-info-circle mr-1"></i>' + res.message)
                        .show();
                    // Sisipkan hidden input: existing_menu_id & is_update
                    $('<input type="hidden" name="existing_menu_id">').val(res.existingMenuId).appendTo('#formCreate');
                    $('<input type="hidden" name="is_update" value="1">').appendTo('#formCreate');
                    // Render fields dari response
                    renderFieldConfigs(res.fields || []);
                } else {
                    // ✅ Tabel baru, belum terdaftar → panggil autoGenerateFields
                    $('#wmu_akses_tabel_success')
                        .removeClass('alert-warning alert-danger alert-info')
                        .addClass('alert alert-success')
                        .html('<i class="fas fa-info-circle mr-1"></i>' + (res.message || 'Tabel valid'))
                        .show();
                    $.ajax({
                        url: baseUrl,
                        method: 'POST',
                        data: { 
                            action: 'autoGenerateFields', 
                            table_name: tableName,
                            _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                        },
                        dataType: 'json',
                        success: function(genRes) {
                            if (genRes.success && genRes.data) {
                                const fields = Array.isArray(genRes.data) ? genRes.data : Object.values(genRes.data);
                                renderFieldConfigs(fields);
                            }
                        },
                        error: function(xhr) {
                            WebMenuUrlShared.showError('Gagal Generate Field', xhr.responseJSON?.message || 'Terjadi kesalahan');
                        }
                    });
                }
            },
            error: function(xhr) {
                $('#wmu_akses_tabel').addClass('is-invalid').removeClass('is-valid');
                $('#wmu_akses_tabel_error').html(xhr.responseJSON?.message || 'Gagal mengecek tabel');
                $('#field-configurator').hide();
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cek Tabel');
            }
        });
    });

    // Helper: render fields ke tabel konfigurasi
    function renderFieldConfigs(fields) {
        $('#fieldConfigBody').empty();
        fields.forEach(function(field, index) {
            try {
                const row = WebMenuUrlShared.createFieldRow(field, index);
                $('#fieldConfigBody').append(row);
            } catch (e) {
                console.error('Error creating row for ' + field.wmfc_column_name, e);
            }
        });
        $('#field-configurator').show();
        WebMenuUrlShared.initializeFieldTypeValidations();
        $('[data-toggle="tooltip"]').tooltip({ html: true });
    }

    // ==========================================
    // BUTTON SUBMIT - AJAX submit
    // ==========================================
    $(document).off('click', '#btnSubmit').on('click', '#btnSubmit', function(e) {
        e.preventDefault();

        const form     = $('#formCreate');
        const btn      = $(this);
        const origHtml = btn.html();

        if (btn.prop('disabled')) return false;

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        const formAction = form.attr('action');
        if (!formAction || formAction === '' || formAction === 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Terjadi kesalahan konfigurasi form. Silakan refresh halaman.' });
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

        $.ajax({
            url: formAction,
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.success) {
                    $('#myModal').modal('hide');
                    setTimeout(function() {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 300);
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'URL menu berhasil ditambahkan',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(function() {
                            if (typeof window.reloadTable === 'function') {
                                window.reloadTable();
                            } else {
                                location.reload();
                            }
                        });
                    }, 400);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message || 'Terjadi kesalahan' });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    $.each(errors, function(field, messages) {
                        const key = field.replace('web_menu_url.', '');
                        $('#' + key + '_error').html(messages[0]);
                        $('[name="web_menu_url[' + key + ']"]').addClass('is-invalid');
                    });
                    let errorList = '<ul class="text-left mb-0">';
                    $.each(errors, function(k, v) { errorList += '<li>' + v[0] + '</li>'; });
                    errorList += '</ul>';
                    Swal.fire({ icon: 'warning', title: 'Validasi Gagal', html: errorList });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server' });
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(origHtml);
            }
        });
    });

    // ==========================================
    // MODAL SHOWN - Reset state
    // ==========================================
    $(document).off('shown.bs.modal', '#myModal').on('shown.bs.modal', '#myModal', function() {
        $('#btnSubmit').prop('disabled', false);
    });

    // ==========================================
    // INPUT CHANGE - Clear validation errors (hanya pada input form, bukan alert)
    // ==========================================
    $(document).on('input change', '#formCreate input, #formCreate select, #formCreate textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').html('');
    });

});
</script>