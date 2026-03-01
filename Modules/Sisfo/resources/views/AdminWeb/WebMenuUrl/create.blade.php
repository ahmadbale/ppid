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

        // Reset state - PENTING: Bersihkan semua state sebelumnya
        $('#field-configurator').hide();
        $('#fieldConfigBody').empty();
        $('#wmu_akses_tabel').removeClass('is-invalid is-valid');
        $('#wmu_akses_tabel_error').html('').css('display', 'none'); // Explicit hide
        $('#wmu_akses_tabel_success').hide().html('').removeClass('alert alert-warning alert-success alert-danger alert-info');
        $('#btnSubmit').prop('disabled', false);

        console.log('🔍 CREATE MODE - Cek Tabel:', tableName);
        console.log('📤 Request data:', {
            action: 'validateTable',
            table_name: tableName,
            menu_url_id: 'NOT SENT (CREATE MODE)'
        });

        $.ajax({
            url: baseUrl,
            method: 'POST',
            data: { 
                action: 'validateTable', 
                table_name: tableName,
                // JANGAN kirim menu_url_id di CREATE mode
                _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
            },
            dataType: 'json',
            cache: false, // Disable AJAX cache
            success: function(res) {
                console.log('📥 Response received:', res);
                console.log('isDuplicate (root):', res.isDuplicate);
                console.log('hasChanges (root):', res.hasChanges);
                console.log('isDuplicate (data):', res.data?.isDuplicate);
                console.log('hasChanges (data):', res.data?.hasChanges);
                console.log('existingMenuId:', res.existingMenuId);

                // Support both root-level and nested data structure
                const isDuplicate = res.isDuplicate || res.data?.isDuplicate || false;
                const hasChanges = res.hasChanges || res.data?.hasChanges || false;
                const existingMenuId = res.existingMenuId || res.data?.existingMenuId || null;
                const existingMenuName = res.existingMenuName || res.data?.existingMenuName || null;

                console.log('🎯 Final values after fallback:');
                console.log('  isDuplicate:', isDuplicate);
                console.log('  hasChanges:', hasChanges);
                console.log('  existingMenuId:', existingMenuId);

                if (!res.success && isDuplicate && !hasChanges) {
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
                $('#wmu_akses_tabel_error').html('').css('display', 'none');

                if (isDuplicate && hasChanges) {
                    // ⚠️ Tabel sudah terdaftar TAPI ada perubahan struktur
                    // → Tidak bisa create ulang di sini, arahkan ke Edit
                    console.log('🚨 KONDISI: isDuplicate + hasChanges');
                    console.log('Existing Menu ID:', existingMenuId);
                    console.log('Field configurator HARUS DISEMBUNYIKAN');
                    
                    const editUrl = '{{ url("management-menu-url/editData") }}/' + existingMenuId;
                    $('#wmu_akses_tabel').addClass('is-invalid').removeClass('is-valid');
                    $('#wmu_akses_tabel_error').html(
                        'Tabel <strong>' + tableName + '</strong> sudah terdaftar sebagai menu master ' +
                        '(<strong>' + (existingMenuName || '-') + '</strong>), namun terdeteksi perubahan struktur tabel. ' +
                        '<br><span class="badge badge-warning mt-2 py-2 px-3" style="font-size: 13px; cursor: pointer;" onclick="modalAction(\'' + editUrl + '\')">' +
                        '<i class="fas fa-external-link-alt mr-2"></i>Klik di sini untuk membuka halaman Edit dan memperbarui konfigurasi</span>'
                    ).css('display', 'block');
                    $('#wmu_akses_tabel_success').hide();
                    $('#field-configurator').hide(); // PAKSA hide field configurator
                    $('#fieldConfigBody').empty(); // Bersihkan isi tabel
                    $('#btnSubmit').prop('disabled', true);
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cek Tabel');
                    return;
                } else {
                    // ✅ Tabel baru, belum terdaftar → panggil autoGenerateFields
                    console.log('✅ KONDISI: Tabel Baru - Generate Fields');
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
                        cache: false,
                        success: function(genRes) {
                            console.log('✅ Auto-generate fields response:', genRes);
                            if (genRes.success && genRes.data) {
                                const fields = Array.isArray(genRes.data) ? genRes.data : Object.values(genRes.data);
                                console.log('Rendering', fields.length, 'fields');
                                renderFieldConfigs(fields);
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Auto-generate error:', xhr);
                            WebMenuUrlShared.showError('Gagal Generate Field', xhr.responseJSON?.message || 'Terjadi kesalahan');
                        }
                    });
                }
            },
            error: function(xhr) {
                console.error('❌ Validate table error:', xhr);
                $('#wmu_akses_tabel').addClass('is-invalid').removeClass('is-valid');
                $('#wmu_akses_tabel_error').html(xhr.responseJSON?.message || 'Gagal mengecek tabel').css('display', 'block');
                $('#field-configurator').hide();
                $('#fieldConfigBody').empty();
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cek Tabel');
                console.log('✅ Cek Tabel completed');
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
                if (res.success === true) {
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
                } else if (res.success === false) {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Gagal!', 
                        text: res.message || 'Terjadi kesalahan' 
                    });
                } else {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Perhatian', 
                        text: 'Response tidak valid dari server' 
                    });
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
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error!', 
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server' 
                    });
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