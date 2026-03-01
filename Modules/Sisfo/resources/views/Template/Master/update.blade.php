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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-info py-2">
                <h5 class="modal-title text-white">
                    <i class="fas fa-search mr-2"></i>Pilih Data
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body px-3 py-3">
                {{-- Search Input --}}
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" class="form-control" id="searchFkInputEdit" placeholder="Ketik untuk mencari...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-clear-search-fk-edit" type="button" title="Hapus pencarian" style="display:none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                {{-- Info Bar --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted" id="fkDataInfoEdit">Menampilkan 0 data</small>
                </div>
                {{-- Table --}}
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-hover table-striped mb-0" id="tableFkSearchEdit">
                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr id="fkTableHeadersEdit"></tr>
                        </thead>
                        <tbody id="fkTableBodyEdit"></tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <small class="text-muted" id="fkPaginationInfoEdit">Halaman 0 dari 0</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="fkPaginationEdit"></ul>
                    </nav>
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
        
        // Use FormData for file upload support
        const formData = new FormData(form[0]);
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
    let fkSearchDataEdit = [];
    let fkFilteredDataEdit = [];
    let fkCurrentPageEdit = 1;
    const FK_PER_PAGE_EDIT = 5;
    let fkColumnsEdit = [];
    let fkHeadersEdit = [];
    let fkPkColumnEdit = '';
    let fkPriorityColEdit = '';
    
    $(document).on('click', '.btn-search-fk', function() {
        const fieldName = $(this).data('column');
        const fkTable = $(this).data('fk-table');
        const rawDisplay = $(this).data('fk-display');
        const rawLabels = $(this).data('fk-labels');
        const priorityCol = $(this).data('fk-priority') || '';
        const displayColumns = Array.isArray(rawDisplay) ? rawDisplay : JSON.parse(rawDisplay);
        const labelColumns = Array.isArray(rawLabels) ? rawLabels : (rawLabels ? JSON.parse(rawLabels) : []);
        
        currentFkFieldEdit = fieldName;
        window._currentFkPriorityColEdit = priorityCol;
        
        // Reset
        fkCurrentPageEdit = 1;
        $('#searchFkInputEdit').val('');
        $('.btn-clear-search-fk-edit').hide();
        
        $.ajax({
            url: '{{ url($menuConfig->wmu_nama) }}/getFkData',
            data: { 
                table: fkTable,
                columns: displayColumns,
                labels: labelColumns
            },
            beforeSend: function() {
                $('#fkTableBodyEdit').html('<tr><td colspan="20" class="text-center py-3"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
                $('#fkPaginationEdit').empty();
                $('#fkPaginationInfoEdit').text('');
                $('#fkDataInfoEdit').text('Memuat...');
                $('#modalFkSearchEdit').modal('show');
            },
            success: function(response) {
                fkSearchDataEdit = response.data;
                fkFilteredDataEdit = [...fkSearchDataEdit];
                fkColumnsEdit = displayColumns;
                fkHeadersEdit = response.headers || [];
                fkPkColumnEdit = response.pkColumn;
                fkPriorityColEdit = priorityCol;
                fkCurrentPageEdit = 1;
                renderFkTablePaginatedEdit();
            }
        });
    });
    
    function renderFkTablePaginatedEdit() {
        // Build headers
        let headerHtml = '<th class="text-center" style="width:45px;">No</th>';
        fkColumnsEdit.forEach((col, i) => {
            const label = (fkHeadersEdit[i] && fkHeadersEdit[i] !== 'default') ? fkHeadersEdit[i] : col.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            headerHtml += '<th>' + label + '</th>';
        });
        headerHtml += '<th class="text-center" style="width:60px;">Aksi</th>';
        $('#fkTableHeadersEdit').html(headerHtml);
        
        // Pagination calc
        const totalItems = fkFilteredDataEdit.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / FK_PER_PAGE_EDIT));
        if (fkCurrentPageEdit > totalPages) fkCurrentPageEdit = totalPages;
        const startIdx = (fkCurrentPageEdit - 1) * FK_PER_PAGE_EDIT;
        const endIdx = Math.min(startIdx + FK_PER_PAGE_EDIT, totalItems);
        const pageData = fkFilteredDataEdit.slice(startIdx, endIdx);
        
        // Build rows
        let rows = '';
        if (pageData.length === 0) {
            rows = '<tr><td colspan="' + (fkColumnsEdit.length + 2) + '" class="text-center py-4 text-muted">' +
                   '<i class="fas fa-inbox fa-2x d-block mb-2"></i>Tidak ada data ditemukan</td></tr>';
        } else {
            pageData.forEach((row, index) => {
                rows += '<tr class="fk-row-selectable-edit" style="cursor:pointer;">';
                rows += '<td class="text-center text-muted">' + (startIdx + index + 1) + '</td>';
                fkColumnsEdit.forEach(col => {
                    rows += '<td>' + (row[col] !== null && row[col] !== undefined ? row[col] : '<span class="text-muted">-</span>') + '</td>';
                });
                const displayVal = (fkPriorityColEdit && row[fkPriorityColEdit] !== undefined) ? row[fkPriorityColEdit] : (row[fkColumnsEdit[0]] || '');
                rows += '<td class="text-center">';
                rows += '<button type="button" class="btn btn-sm btn-success btn-select-fk-edit rounded-circle" style="width:30px;height:30px;padding:0;" data-id="' + row[fkPkColumnEdit] + '" data-display="' + $('<div>').text(String(displayVal)).html() + '" title="Pilih">';
                rows += '<i class="fas fa-check" style="font-size:12px;"></i>';
                rows += '</button></td>';
                rows += '</tr>';
            });
        }
        $('#fkTableBodyEdit').html(rows);
        
        // Info text
        if (totalItems > 0) {
            $('#fkDataInfoEdit').text('Menampilkan ' + (startIdx + 1) + '-' + endIdx + ' dari ' + totalItems + ' data');
            $('#fkPaginationInfoEdit').text('Halaman ' + fkCurrentPageEdit + ' dari ' + totalPages);
        } else {
            $('#fkDataInfoEdit').text('0 data ditemukan');
            $('#fkPaginationInfoEdit').text('');
        }
        
        // Build pagination
        let pagHtml = '';
        if (totalPages > 1) {
            pagHtml += '<li class="page-item ' + (fkCurrentPageEdit === 1 ? 'disabled' : '') + '">';
            pagHtml += '<a class="page-link" href="#" data-fk-page-edit="' + (fkCurrentPageEdit - 1) + '"><i class="fas fa-chevron-left" style="font-size:10px;"></i></a></li>';
            
            let startPage = Math.max(1, fkCurrentPageEdit - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
            
            for (let p = startPage; p <= endPage; p++) {
                pagHtml += '<li class="page-item ' + (p === fkCurrentPageEdit ? 'active' : '') + '">';
                pagHtml += '<a class="page-link" href="#" data-fk-page-edit="' + p + '">' + p + '</a></li>';
            }
            
            pagHtml += '<li class="page-item ' + (fkCurrentPageEdit === totalPages ? 'disabled' : '') + '">';
            pagHtml += '<a class="page-link" href="#" data-fk-page-edit="' + (fkCurrentPageEdit + 1) + '"><i class="fas fa-chevron-right" style="font-size:10px;"></i></a></li>';
        }
        $('#fkPaginationEdit').html(pagHtml);
    }
    
    // Pagination click
    $(document).on('click', '#fkPaginationEdit .page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('fk-page-edit'));
        if (page && page !== fkCurrentPageEdit) {
            fkCurrentPageEdit = page;
            renderFkTablePaginatedEdit();
        }
    });
    
    // Search FK with debounce
    let fkSearchTimeoutEdit = null;
    $('#searchFkInputEdit').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        $('.btn-clear-search-fk-edit').toggle(searchTerm.length > 0);
        
        clearTimeout(fkSearchTimeoutEdit);
        fkSearchTimeoutEdit = setTimeout(function() {
            if (searchTerm === '') {
                fkFilteredDataEdit = [...fkSearchDataEdit];
            } else {
                fkFilteredDataEdit = fkSearchDataEdit.filter(row => {
                    return fkColumnsEdit.some(col => {
                        const val = row[col];
                        return val !== null && val !== undefined && String(val).toLowerCase().includes(searchTerm);
                    });
                });
            }
            fkCurrentPageEdit = 1;
            renderFkTablePaginatedEdit();
        }, 200);
    });
    
    // Clear search button
    $(document).on('click', '.btn-clear-search-fk-edit', function() {
        $('#searchFkInputEdit').val('').trigger('keyup').focus();
    });
    
    // Select FK (click button)
    $(document).on('click', '.btn-select-fk-edit', function(e) {
        e.stopPropagation();
        const selectedId = $(this).data('id');
        const displayVal = $(this).data('display') || $(this).closest('tr').find('td:eq(1)').text();
        $('#' + currentFkFieldEdit).val(selectedId);
        $('#' + currentFkFieldEdit + '_display').val(displayVal);
        $('#modalFkSearchEdit').modal('hide');
    });
    
    // Click on row to select
    $(document).on('click', '.fk-row-selectable-edit', function() {
        $(this).find('.btn-select-fk-edit').click();
    });
});
</script>
