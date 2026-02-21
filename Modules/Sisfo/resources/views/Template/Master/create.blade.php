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
        <div class="row">
            @foreach($formFields as $field)
                <div class="col-md-{{ $field['type'] === 'textarea' ? '12' : '6' }}">
                    @include('sisfo::components.form-field-type', [
                        'field' => $field,
                        'value' => $field['value'] ?? null,
                        'mode' => 'create'
                    ])
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Batal
        </button>
        <button type="submit" class="btn btn-primary" id="btnSubmit">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation & submission
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#btnSubmit');
        const originalText = submitBtn.html();
        
        // Disable button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#modalAction').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Data berhasil disimpan',
                    timer: 2000
                });
                if (typeof window.reloadTable === 'function') {
                    window.reloadTable();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = '<ul class="text-left mb-0">';
                    $.each(errors, function(key, value) {
                        errorMsg += '<li>' + value[0] + '</li>';
                        // Highlight error fields
                        $('[name="' + key + '"]').addClass('is-invalid');
                    });
                    errorMsg += '</ul>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errorMsg
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            }
        });
    });
    
    // Remove invalid class on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // FK Search functionality
    let currentFkField = null;
    let fkSearchData = [];
    
    $('.btn-search-fk').on('click', function() {
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
@endpush
