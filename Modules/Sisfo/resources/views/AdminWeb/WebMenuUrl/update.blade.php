{{-- Modal Update - Web Menu URL --}}
@include('sisfo::components.web-menu-url.modal-styles')
@include('sisfo::components.web-menu-url.shared-scripts')

<div class="modal-header bg-warning">
    <h5 class="modal-title text-white">
        <i class="fas fa-edit mr-2"></i>Edit URL Menu
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formUpdate" action="{{ url('management-menu-url/updateData/' . $webMenuUrl->web_menu_url_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-body">

        {{-- Aplikasi --}}
        <div class="form-group">
            <label for="fk_m_application">Aplikasi <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_application" name="web_menu_url[fk_m_application]">
                <option value="">Pilih Aplikasi</option>
                @foreach($applications as $app)
                    <option value="{{ $app->application_id }}"
                        {{ $webMenuUrl->fk_m_application == $app->application_id ? 'selected' : '' }}>
                        {{ $app->app_nama }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_m_application_error"></div>
        </div>

        {{-- Nama URL Menu --}}
        <div class="form-group">
            <label for="wmu_nama">Nama URL Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmu_nama"
                   name="web_menu_url[wmu_nama]" maxlength="255"
                   value="{{ $webMenuUrl->wmu_nama }}">
            <small class="text-muted">Gunakan huruf kecil dan tanda hubung (-). Ini akan menjadi URL halaman.</small>
            <div class="invalid-feedback" id="wmu_nama_error"></div>
        </div>

        {{-- Keterangan --}}
        <div class="form-group">
            <label for="wmu_keterangan">Keterangan</label>
            <textarea class="form-control" id="wmu_keterangan"
                      name="web_menu_url[wmu_keterangan]" rows="2" maxlength="1000">{{ $webMenuUrl->wmu_keterangan }}</textarea>
            <div class="invalid-feedback" id="wmu_keterangan_error"></div>
        </div>

        {{-- Kategori Menu (readonly) --}}
        <div class="form-group">
            <label for="wmu_kategori_menu">Kategori Menu</label>
            <input type="text" class="form-control" value="{{ ucfirst($webMenuUrl->wmu_kategori_menu) }}" readonly>
            <input type="hidden" name="web_menu_url[wmu_kategori_menu]" value="{{ $webMenuUrl->wmu_kategori_menu }}">
            <small class="text-muted">Kategori tidak dapat diubah setelah dibuat.</small>
        </div>

        {{-- Section berdasarkan kategori --}}
        @include('sisfo::components.web-menu-url.section-custom', ['mode' => 'update', 'webMenuUrl' => $webMenuUrl])
        @include('sisfo::components.web-menu-url.section-master', ['mode' => 'update', 'webMenuUrl' => $webMenuUrl])

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Batal
        </button>
        <button type="button" class="btn btn-warning" id="btnUpdate">
            <i class="fas fa-save mr-1"></i>Update
        </button>
    </div>
</form>

<script>
$(document).ready(function() {

    // ==========================================
    // TAMPILKAN section sesuai kategori existing
    // ==========================================
    const kategori = '{{ $webMenuUrl->wmu_kategori_menu }}';
    if (kategori === 'custom')    $('#section-custom').show();
    if (kategori === 'master')    $('#section-master').show();

    // ==========================================
    // RE-CHECK TABEL (untuk kategori master)
    // ==========================================
    $('#btnCheckTable').on('click', function() {
        const tableName = $('#wmu_akses_tabel').val().trim();
        const menuUrlId = '{{ $webMenuUrl->web_menu_url_id }}';
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengecek...');

        // ✅ FIX: POST ke management-menu-url (bukan /{wmu_nama}) agar tidak kena route dinamis
        const checkUrl = '{{ url("management-menu-url") }}';

        $.ajax({
            url: checkUrl,
            method: 'POST',
            data: { 
                action: 'validateTable', 
                table_name: tableName, 
                menu_url_id: menuUrlId,
                _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    const hasChanges = res.hasChanges || res.data?.hasChanges || false;

                    if (hasChanges) {
                        // Ada perubahan struktur → info + disable update
                        const changesHtml = WebMenuUrlShared.buildChangesDetailHtml(res.changes || res.data?.changes || {});
                        $('#alertTableInfo')
                            .removeClass('alert-info alert-success alert-danger')
                            .addClass('alert alert-warning')
                            .show();
                        $('#alertTableIcon').attr('class', 'fas fa-info-circle mr-2');
                        $('#alertTableInfoText').html(
                            '<strong>Terdeteksi perubahan struktur tabel!</strong>' +
                            '<br>Perubahan tidak dapat disimpan langsung. Silakan buat menu baru untuk mendaftarkan ulang.' +
                            changesHtml
                        );
                        // Sembunyikan field config, disable tombol Update
                        $('#fieldConfigsSection').hide();
                        $('#btnUpdate').prop('disabled', true);
                    } else {
                        // Tidak ada perubahan → tampilkan field config, enable update
                        $('#alertTableInfo')
                            .removeClass('alert-warning alert-danger')
                            .addClass('alert alert-success')
                            .show();
                        $('#alertTableIcon').attr('class', 'fas fa-info-circle mr-2');
                        $('#alertTableInfoText').html(
                            '<strong>Tabel ditemukan dan struktur tidak berubah.</strong>'
                        );

                        // Tampilkan field config dari data merge
                        const fields = res.fields || res.data?.fields || [];
                        if (fields.length > 0) {
                            $('#tbodyFieldConfigs').empty();
                            fields.forEach(function(field, index) {
                                try {
                                    const row = WebMenuUrlShared.createFieldRow(field, index);
                                    $('#tbodyFieldConfigs').append(row);
                                } catch (e) {
                                    console.error('Error creating row for ' + field.wmfc_column_name, e);
                                }
                            });
                            $('#fieldConfigsSection').show();
                            WebMenuUrlShared.initializeFieldTypeValidations();
                            $('[data-toggle="tooltip"]').tooltip({ html: true });
                        }

                        // Enable tombol Update
                        $('#btnUpdate').prop('disabled', false);
                    }
                } else {
                    // Tabel tidak ditemukan atau error
                    $('#alertTableInfo')
                        .removeClass('alert-info alert-success alert-warning')
                        .addClass('alert alert-danger')
                        .show();
                    $('#alertTableIcon').attr('class', 'fas fa-info-circle mr-2');
                    $('#alertTableInfoText').html(res.message || 'Tabel tidak ditemukan');
                    // Sembunyikan field config, disable update
                    $('#fieldConfigsSection').hide();
                    $('#btnUpdate').prop('disabled', true);
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Gagal mengecek tabel' });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Re-Check Tabel');
            }
        });
    });

    // ==========================================
    // BUTTON UPDATE - AJAX submit
    // ==========================================
    $(document).off('click', '#btnUpdate').on('click', '#btnUpdate', function(e) {
        e.preventDefault();

        const form     = $('#formUpdate');
        const btn      = $(this);
        const origHtml = btn.html();

        if (btn.prop('disabled')) return false;

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Mengupdate...');

        $.ajax({
            url: form.attr('action'),
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
                            text: res.message || 'URL menu berhasil diperbarui',
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
        $('#btnUpdate').prop('disabled', false);
    });

    // ==========================================
    // INPUT CHANGE - Clear validation errors (hanya pada input form, bukan alert)
    // ==========================================
    $(document).on('input change', '#formUpdate input, #formUpdate select, #formUpdate textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').html('');
    });

});
</script>
