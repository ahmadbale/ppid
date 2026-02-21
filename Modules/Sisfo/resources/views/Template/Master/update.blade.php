{{-- Modal Update Form --}}
<div class="modal-header bg-warning">
    <h5 class="modal-title text-white">
        <i class="fas fa-edit mr-2"></i>Edit {{ $pageTitle ?? 'Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formUpdate" action="{{ url($menuConfig->wmu_nama . '/updateData/' . $existingData->$pkColumn) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="modal-body">
        <div class="row">
            @foreach($formFields as $field)
                <div class="col-md-{{ $field['type'] === 'textarea' ? '12' : '6' }}">
                    @include('sisfo::components.form-field-type', [
                        'field' => $field,
                        'value' => $field['value'],
                        'mode' => 'update'
                    ])
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Batal
        </button>
        <button type="submit" class="btn btn-warning" id="btnUpdate">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation & submission
    $('#formUpdate').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#btnUpdate');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Updating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#modalAction').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Data berhasil diupdate',
                    timer: 2000
                });
                if (typeof window.reloadTable === 'function') {
                    window.reloadTable();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = '<ul class="text-left mb-0">';
                    $.each(errors, function(key, value) {
                        errorMsg += '<li>' + value[0] + '</li>';
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
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupdate data'
                    });
                }
            }
        });
    });
    
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // FK Search functionality for edit
    let currentFkFieldEdit = null;
    
    $('.btn-search-fk').on('click', function() {
        const fieldName = $(this).data('field');
        const fkTable = $(this).data('fk-table');
        const displayColumns = $(this).data('display-columns').split(',');
        
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
@endpush
