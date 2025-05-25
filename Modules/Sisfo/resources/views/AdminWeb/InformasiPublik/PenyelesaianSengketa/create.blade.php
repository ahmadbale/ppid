@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    $penyelesaianSengketaUrl = WebMenuModel::getDynamicMenuUrl('penyelesaian-sengketa');
@endphp

<div class="modal-header">
    <h5 class="modal-title">Tambah Data Penyelesaian Sengketa</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="form-create-sengketa" action="{{ url($penyelesaianSengketaUrl . '/createData') }}" method="POST" 
        enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="ps_kode">Kode Penyelesaian Sengketa <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ps_kode" name="m_penyelesaian_sengketa[ps_kode]" 
                maxlength="20" placeholder="Masukkan kode penyelesaian sengketa">
            <div class="invalid-feedback" id="ps_kode_error"></div>
            <small class="form-text text-muted">Contoh: PS-001</small>
        </div>

        <div class="form-group">
            <label for="ps_nama">Nama Penyelesaian Sengketa <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ps_nama" name="m_penyelesaian_sengketa[ps_nama]" 
                maxlength="255" placeholder="Masukkan nama penyelesaian sengketa">
            <div class="invalid-feedback" id="ps_nama_error"></div>
        </div>

        <div class="form-group">
            <label for="ps_deskripsi">Deskripsi Penyelesaian Sengketa <span class="text-danger">*</span></label>
            <textarea class="form-control" id="ps_deskripsi" name="m_penyelesaian_sengketa[ps_deskripsi]" 
                rows="4" placeholder="Masukkan deskripsi penyelesaian sengketa"></textarea>
            <div class="invalid-feedback" id="ps_deskripsi_error"></div>
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
    // Inisialisasi Summernote pada textarea deskripsi
    $('#ps_deskripsi').summernote({
        placeholder: 'Masukkan deskripsi penyelesaian sengketa...',
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear', 'fontsize', 'fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'height', 'align']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents) {
                $(this).next('.note-editor').removeClass('is-invalid');
                $('#ps_deskripsi_error').html('');
            }
        }
    });

    // CSS untuk validasi error pada summernote
    $('<style>.note-editor.is-invalid {border: 1px solid #dc3545 !important;}</style>').appendTo('head');

    // Reset error saat input berubah
    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
    });

    // Handle submit form
    $('#btnSubmitForm').on('click', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        const form = $('#form-create-sengketa');
        const formData = new FormData(form[0]);
        const button = $(this);
        let isValid = true;

        // Validasi Kode
        const psKode = $('#ps_kode').val();
        if (psKode === '') {
            isValid = false;
            $('#ps_kode').addClass('is-invalid');
            $('#ps_kode_error').html('Kode Penyelesaian Sengketa wajib diisi.');
        } else if (psKode.length > 20) {
            isValid = false;
            $('#ps_kode').addClass('is-invalid');
            $('#ps_kode_error').html('Kode Penyelesaian Sengketa maksimal 20 karakter.');
        }

        // Validasi Nama
        const psNama = $('#ps_nama').val();
        if (psNama === '') {
            isValid = false;
            $('#ps_nama').addClass('is-invalid');
            $('#ps_nama_error').html('Nama Penyelesaian Sengketa wajib diisi.');
        }

        // Validasi Deskripsi
        const psDeskripsi = $('#ps_deskripsi').summernote('code');
        if (psDeskripsi === '' || psDeskripsi === '<p><br></p>') {
            isValid = false;
            $('#ps_deskripsi').next('.note-editor').addClass('is-invalid');
            $('#ps_deskripsi_error').html('Deskripsi wajib diisi.');
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali input Anda'
            });
            return;
        }

        formData.set('m_penyelesaian_sengketa[ps_deskripsi]', psDeskripsi);
        
        button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST', 
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
                        $.each(response.errors, function(key, value) {
                            if (key.startsWith('m_penyelesaian_sengketa.')) {
                                const fieldName = key.replace('m_penyelesaian_sengketa.', '');
                                if (fieldName === 'ps_deskripsi') {
                                    $('#ps_deskripsi').next('.note-editor').addClass('is-invalid');
                                    $('#ps_deskripsi_error').html(value[0]);
                                } else {
                                    $(`#${fieldName}`).addClass('is-invalid');
                                    $(`#${fieldName}_error`).html(value[0]);
                                }
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
            error: function(xhr) {
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