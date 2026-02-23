{{-- Modal Delete Confirmation --}}
<div class="modal-header bg-danger">
    <h5 class="modal-title text-white">
        <i class="fas fa-trash-alt mr-2"></i>Hapus {{ $pageTitle ?? 'Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formDelete" action="{{ url($menuConfig->wmu_nama . '/deleteData/' . $detailData->$pkColumn) }}" method="POST">
    @csrf
    @method('DELETE')
    
    <div class="modal-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Peringatan!</strong> Anda akan menghapus data berikut:
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach($fields->take(5) as $field)
                        <div class="col-md-12 mb-2">
                            <strong>{{ $field->wmfc_field_label }}:</strong>
                            <span class="ml-2">
                                @php
                                    $columnName = $field->wmfc_column_name;
                                    $value = $detailData->$columnName ?? '-';
                                    
                                    if ($field->wmfc_field_type === 'date' && $value !== '-') {
                                        $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                                    } elseif ($field->wmfc_field_type === 'date2' && $value !== '-') {
                                        $value = \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
                                    }
                                @endphp
                                {{ $value }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="alert alert-danger mt-3">
            <i class="fas fa-info-circle mr-2"></i>
            Data yang dihapus <strong>tidak dapat dikembalikan</strong>. Pastikan Anda yakin sebelum melanjutkan.
        </div>
        
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="confirmDelete" required>
                <label class="custom-control-label" for="confirmDelete">
                    Saya yakin ingin menghapus data ini
                </label>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Batal
        </button>
        <button type="submit" class="btn btn-danger" id="btnDelete" disabled>
            <i class="fas fa-trash mr-1"></i>Hapus Data
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // ==========================================
    // CHECKBOX CONFIRM - Enable/Disable Button
    // ==========================================
    $(document).on('change', '#confirmDelete', function() {
        const isChecked = $(this).is(':checked');
        $('#btnDelete').prop('disabled', !isChecked);
    });

    // ==========================================
    // MODAL SHOWN - Reset State
    // ==========================================
    $(document).on('shown.bs.modal', '#myModal', function() {
        const checkbox = $('#confirmDelete');
        const button = $('#btnDelete');
        
        if (checkbox.length && button.length) {
            checkbox.prop('checked', false);
            button.prop('disabled', true);
        }
    });

    // ==========================================
    // FORM DELETE - Submit Handler
    // ==========================================
    $(document).on('submit', '#formDelete', function(e) {
        e.preventDefault();
        
        const form = $(this);
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Data akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const submitBtn = $('#btnDelete');
                const originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menghapus...');
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#myModal').modal('hide');
                            
                            // Force remove backdrop
                            setTimeout(function() {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                            }, 300);
                            
                            // Show success alert
                            setTimeout(function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dihapus!',
                                    text: response.message || 'Data berhasil dihapus',
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745'
                                }).then((result) => {
                                    if (typeof window.reloadTable === 'function') {
                                        window.reloadTable();
                                    } else {
                                        location.reload();
                                    }
                                });
                            }, 400);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data'
                        });
                    }
                });
            }
        });
    });
});
</script>
