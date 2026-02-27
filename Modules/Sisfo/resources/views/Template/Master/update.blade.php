{{-- Modal Update Form --}}
<div class="modal-header bg-warning">
    <h5 class="modal-title text-white">
        <i class="fas fa-edit mr-2"></i>{{ $pageTitle ?? 'Edit Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formUpdate" action="{{ url($menuConfig->wmu_nama . '/updateData/' . $existingData->$pkColumn) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="modal-body">
        @foreach($formFields as $field)
            <div class="row mb-2">
                <div class="col-12">
                    @include('sisfo::components.form-field-type', [
                        'field' => $field,
                        'value' => $field['value'],
                        'mode' => 'update'
                    ])
                </div>
            </div>
        @endforeach
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

{{-- Modal FK Search --}}
<div class="modal fade" id="modalFkSearchEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="fas fa-search mr-2"></i>Pilih Data
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="searchFkInputEdit" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableFkSearchEdit">
                        <thead>
                            <tr id="fkTableHeadersEdit"></tr>
                        </thead>
                        <tbody id="fkTableBodyEdit"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ==========================================
    // BUTTON UPDATE - Event Handler
    // ==========================================
    $(document).off('click', '#btnUpdate').on('click', '#btnUpdate', function(e) {
        e.preventDefault();
        
        const form = $('#formUpdate');
        const submitBtn = $(this);
        const originalText = submitBtn.html();
        
        if (submitBtn.prop('disabled')) {
            return false;
        }
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        // Disable button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Mengupdate...');
        
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
                            title: 'Berhasil!',
                            text: response.message || 'Data berhasil diupdate',
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
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    
                    $.each(errors, function(field, messages) {
                        const input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        // Jika field bertipe search (ada _display input), highlight juga
                        $('#' + field + '_display').addClass('is-invalid');
                        $('#error-' + field).html(messages[0]).css('display', 'block');
                    });
                    
                    let errorMsg = '<ul class="text-left mb-0">';
                    $.each(errors, function(key, value) {
                        errorMsg += '<li>' + value[0] + '</li>';
                    });
                    errorMsg += '</ul>';
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        html: errorMsg
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupdate data'
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // ==========================================
    // MODAL SHOWN - Reset Button State
    // ==========================================
    $(document).off('shown.bs.modal', '#myModal').on('shown.bs.modal', '#myModal', function() {
        const updateBtn = $('#btnUpdate');
        if (updateBtn.length) {
            updateBtn.prop('disabled', false);
        }
    });
    
    // ==========================================
    // INPUT CHANGE - Remove Validation Error
    // ==========================================
    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').html('');
    });
    
    // ==========================================
    // IMAGE UPLOAD - Preview
    // ==========================================
    $(document).on('change', '.image-upload', function() {
        const input = this;
        const previewId = 'preview_' + $(this).attr('name');
        const previewContainer = $('#' + previewId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.html(
                    '<img src="' + e.target.result + '" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">' +
                    '<br><small class="form-text text-muted">Preview gambar baru</small>'
                );
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // ==========================================
    // CRITERIA AUTO-TRANSFORM (Uppercase/Lowercase)
    // ==========================================
    $(document).on('input', 'input[data-case], textarea[data-case]', function() {
        const caseType = $(this).data('case');
        const cursorPos = this.selectionStart;
        const cursorEnd = this.selectionEnd;
        
        if (caseType === 'uppercase') {
            $(this).val($(this).val().toUpperCase());
        } else if (caseType === 'lowercase') {
            $(this).val($(this).val().toLowerCase());
        }
        
        // Restore cursor position
        this.setSelectionRange(cursorPos, cursorEnd);
    });
    
    // ==========================================
    // FK SEARCH - Foreign Key Functionality
    // ==========================================
    let currentFkFieldEdit = null;
    
    $(document).on('click', '.btn-search-fk', function() {
        const fieldName = $(this).data('column');
        const fkTable = $(this).data('fk-table');
        const rawDisplay = $(this).data('fk-display');
        const displayColumns = Array.isArray(rawDisplay) ? rawDisplay : JSON.parse(rawDisplay);
        
        currentFkFieldEdit = fieldName;
        
        $.ajax({
            url: '{{ url($menuConfig->wmu_nama) }}/getFkData',
            data: { 
                table: fkTable,
                columns: displayColumns 
            },
            success: function(response) {
                renderFkTableEdit(response.data, displayColumns, response.pkColumn);
                $('#modalFkSearchEdit').modal('show');
            }
        });
    });
    
    function renderFkTableEdit(data, columns, pkColumn) {
        let headers = '<th width="50">No</th>';
        columns.forEach(col => {
            headers += '<th>' + col.toUpperCase() + '</th>';
        });
        headers += '<th width="80" class="text-center">Aksi</th>';
        $('#fkTableHeadersEdit').html(headers);
        
        let rows = '';
        data.forEach((row, index) => {
            rows += '<tr>';
            rows += '<td>' + (index + 1) + '</td>';
            columns.forEach(col => {
                rows += '<td>' + (row[col] || '-') + '</td>';
            });
            rows += '<td class="text-center">';
            rows += '<button type="button" class="btn btn-sm btn-success btn-select-fk-edit" data-id="' + row[pkColumn] + '">';
            rows += '<i class="fas fa-check"></i>';
            rows += '</button></td>';
            rows += '</tr>';
        });
        $('#fkTableBodyEdit').html(rows);
    }
    
    $('#searchFkInputEdit').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#tableFkSearchEdit tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
        });
    });
    
    $(document).on('click', '.btn-select-fk-edit', function() {
        const selectedId = $(this).data('id');
        $('#' + currentFkFieldEdit).val(selectedId);
        $('#' + currentFkFieldEdit + '_display').val($(this).closest('tr').find('td:eq(1)').text());
        $('#modalFkSearchEdit').modal('hide');
    });
});
</script>
