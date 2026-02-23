{{-- Modal Create Form --}}
<div class="modal-header bg-primary">
    <h5 class="modal-title text-white">
        <i class="fas fa-plus-circle mr-2"></i>Tambah {{ $pageTitle ?? 'Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreate" action="{{ url($menuConfig->wmu_nama . '/createData') }}" method="POST">
    @csrf
    <div class="modal-body">
        @foreach($formFields as $field)
            <div class="row mb-2">
                <div class="col-12">
                    @include('sisfo::components.form-field-type', [
                        'field' => $field,
                        'value' => $field['value'] ?? null,
                        'mode' => 'create'
                    ])
                </div>
            </div>
        @endforeach
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

{{-- Modal FK Search --}}
<div class="modal fade" id="modalFkSearch" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <input type="text" class="form-control" id="searchFkInput" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableFkSearch">
                        <thead>
                            <tr id="fkTableHeaders"></tr>
                        </thead>
                        <tbody id="fkTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ==========================================
    // BUTTON SUBMIT - Event Handler
    // ==========================================
    $(document).off('click', '#btnSubmit').on('click', '#btnSubmit', function(e) {
        e.preventDefault();
        
        const form = $('#formCreate');
        const submitBtn = $(this);
        const originalText = submitBtn.html();
        
        if (submitBtn.prop('disabled')) {
            return false;
        }
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        // Disable button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');
        
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
                            text: response.message || 'Data berhasil ditambahkan',
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
                        $('#error-' + field).html(messages[0]);
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
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server'
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
        const createBtn = $('#btnSubmit');
        if (createBtn.length) {
            createBtn.prop('disabled', false);
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
    // FK SEARCH - Foreign Key Functionality
    // ==========================================
    let currentFkField = null;
    let fkSearchData = [];
    
    $(document).on('click', '.btn-search-fk', function() {
        const fieldName = $(this).data('field');
        const fkTable = $(this).data('fk-table');
        const displayColumns = $(this).data('display-columns').split(',');
        
        currentFkField = fieldName;
        
        // Load FK data
        $.ajax({
            url: '{{ url($menuConfig->wmu_nama) }}/getFkData',
            data: { 
                table: fkTable,
                columns: displayColumns 
            },
            success: function(response) {
                fkSearchData = response.data;
                renderFkTable(response.data, displayColumns, response.pkColumn);
                $('#modalFkSearch').modal('show');
            }
        });
    });
    
    function renderFkTable(data, columns, pkColumn) {
        // Build headers
        let headers = '<th width="50">No</th>';
        columns.forEach(col => {
            headers += '<th>' + col.toUpperCase() + '</th>';
        });
        headers += '<th width="80" class="text-center">Aksi</th>';
        $('#fkTableHeaders').html(headers);
        
        // Build rows
        let rows = '';
        data.forEach((row, index) => {
            rows += '<tr>';
            rows += '<td>' + (index + 1) + '</td>';
            columns.forEach(col => {
                rows += '<td>' + (row[col] || '-') + '</td>';
            });
            rows += '<td class="text-center">';
            rows += '<button type="button" class="btn btn-sm btn-success btn-select-fk" data-id="' + row[pkColumn] + '">';
            rows += '<i class="fas fa-check"></i>';
            rows += '</button></td>';
            rows += '</tr>';
        });
        $('#fkTableBody').html(rows);
    }
    
    // Search FK
    $('#searchFkInput').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#tableFkSearch tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
        });
    });
    
    // Select FK
    $(document).on('click', '.btn-select-fk', function() {
        const selectedId = $(this).data('id');
        $('#' + currentFkField).val(selectedId);
        $('#' + currentFkField + '_display').val($(this).closest('tr').find('td:eq(1)').text());
        $('#modalFkSearch').modal('hide');
    });
});
</script>
