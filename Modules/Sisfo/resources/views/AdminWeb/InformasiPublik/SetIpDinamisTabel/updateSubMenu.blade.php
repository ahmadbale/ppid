<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\updateSubMenu.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Sub Menu: {{ $ipSubMenu->nama_ip_sm }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateSubMenu" action="{{ url($setIpDinamisTabelUrl . '/updateSubMenu/' . $ipSubMenu->ip_sub_menu_id) }}"
        method="POST" enctype="multipart/form-data">
        @csrf

        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i> 
            <strong>Menu Utama:</strong> {{ $ipSubMenu->IpSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }} <br>
            <strong>Sub Menu Utama:</strong> {{ $ipSubMenu->IpSubMenuUtama->nama_ip_smu ?? 'N/A' }} <br>
            <strong>Sub Menu:</strong> {{ $ipSubMenu->nama_ip_sm }}
        </div>

        <div class="card border-info mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-file-edit mr-2"></i>Informasi Sub Menu
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_ip_sm">Nama Sub Menu <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_ip_sm" name="nama_ip_sm" maxlength="255"
                        value="{{ $ipSubMenu->nama_ip_sm }}" required>
                    <div class="invalid-feedback" id="nama_ip_sm_error"></div>
                </div>

                <div class="form-group">
                    <label for="dokumen_ip_sm">Dokumen Sub Menu (PDF)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="dokumen_ip_sm" name="dokumen_ip_sm" accept=".pdf">
                        <label class="custom-file-label" for="dokumen_ip_sm">
                            @if($ipSubMenu->dokumen_ip_sm)
                                {{ basename($ipSubMenu->dokumen_ip_sm) }} (Ganti file)
                            @else
                                Pilih file PDF (maks. 5 MB)
                            @endif
                        </label>
                    </div>
                    <div class="invalid-feedback" id="dokumen_ip_sm_error"></div>
                    <small class="text-muted">Format: PDF, Ukuran maksimal: 5 MB</small>
                    
                    @if($ipSubMenu->dokumen_ip_sm)
                    <div class="mt-2">
                        <a href="{{ Storage::url($ipSubMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-file-pdf"></i> Lihat Dokumen Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <input type="hidden" name="existing_dokumen" value="{{ $ipSubMenu->dokumen_ip_sm }}">
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-info" form="formUpdateSubMenu">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
    $(document).ready(function () {
        // Custom file input handler
        $(document).on('change', '.custom-file-input', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file PDF (maks. 5 MB)');
        });

        // Form submission
        $('button[form="formUpdateSubMenu"]').on('click', function(e) {
            e.preventDefault();
            const form = $('#formUpdateSubMenu');
            const button = $(this);
            const formData = new FormData(form[0]);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

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
                            text: response.message || 'Sub Menu berhasil diperbarui'
                        });
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).html(value[0]);
                            });
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat menyimpan data'
                        });
                    }
                },
                error: function(xhr) {
                    console.log('Error details:', xhr.responseText);
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

        // Remove validation errors on input
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });
    });
</script>