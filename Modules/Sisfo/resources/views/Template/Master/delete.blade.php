{{-- Modal Delete Confirmation --}}
@php
/**
 * Format nilai range (*2) untuk modal delete
 * (sama seperti formatRangeValue di detail.blade.php)
 */
function formatRangeDelete(string $type, mixed $raw): string {
    if (empty($raw) || $raw === '-') return '-';
    $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
    if (!is_array($decoded) || !isset($decoded['start'], $decoded['end'])) {
        return is_string($raw) ? e($raw) : '-';
    }
    $start = $decoded['start'];
    $end   = $decoded['end'];

    if ($type === 'date2') {
        $s = \Carbon\Carbon::parse($start);
        $e = \Carbon\Carbon::parse($end);
        $diff = (int) $s->diffInDays($e);
        return $s->format('Y-m-d') . ' s/d ' . $e->format('Y-m-d')
             . ' (' . $diff . ' hari)';
    }
    if ($type === 'datetime2') {
        $s = \Carbon\Carbon::parse($start);
        $e = \Carbon\Carbon::parse($end);
        $totalMin = (int) $s->diffInMinutes($e);
        $hours    = (int) floor($totalMin / 60);
        $remMin   = $totalMin % 60;
        if ($hours >= 24) {
            $days    = (int) floor($hours / 24);
            $remHour = $hours % 24;
            $dur     = "{$days} hari" . ($remHour ? " {$remHour} jam" : "") . " {$remMin} menit";
        } elseif ($hours > 0) {
            $dur = "{$hours} jam {$remMin} menit";
        } else {
            $dur = "{$totalMin} menit";
        }
        return $s->format('Y-m-d H:i:s') . ' s/d ' . $e->format('Y-m-d H:i:s')
             . ' (' . $dur . ')';
    }
    if ($type === 'time2') {
        $parseTime = function(string $t): array {
            $parts = explode(':', $t);
            return [(int)($parts[0] ?? 0), (int)($parts[1] ?? 0), (int)($parts[2] ?? 0)];
        };
        [$sh, $sm, $ss] = $parseTime($start);
        [$eh, $em, $es] = $parseTime($end);
        $diffSec = abs(($eh * 3600 + $em * 60 + $es) - ($sh * 3600 + $sm * 60 + $ss));
        $diffMin = (int) floor($diffSec / 60);
        $hours   = (int) floor($diffMin / 60);
        $remMin  = $diffMin % 60;
        $dur     = $hours > 0
                 ? "{$hours} jam {$remMin} menit"
                 : "{$diffMin} menit";
        return substr($start, 0, 5) . ' s/d ' . substr($end, 0, 5) . ' (' . $dur . ')';
    }
    if ($type === 'year2') {
        $s    = (int) $start;
        $e    = (int) $end;
        $diff = abs($e - $s);
        return "{$s} s/d {$e} ({$diff} tahun)";
    }
    return e(is_string($raw) ? $raw : json_encode($raw));
}
@endphp
<div class="modal-header bg-danger">
    <h5 class="modal-title text-white">
        <i class="fas fa-trash-alt mr-2"></i>{{ $pageTitle ?? 'Hapus Data' }}
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
            <div class="card-body p-2">
                @foreach($fields as $field)
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <strong>{{ $field->wmfc_field_label }}:</strong>
                            <span class="ml-2">
                                @php
                                    $columnName = $field->wmfc_column_name;
                                    $rawValue   = $detailData->$columnName ?? null;
                                    $value      = '-';

                                    if ($rawValue === null || $rawValue === '') {
                                        $value = '-';
                                    }
                                    // FK (search) — tampilkan priority display column
                                    elseif ($field->wmfc_field_type === 'search' && $field->wmfc_fk_priority_display) {
                                        $priorityCol = $field->wmfc_fk_priority_display;
                                        $joinedKey   = $columnName . '_' . $priorityCol;
                                        $value = (isset($detailData->$joinedKey) && $detailData->$joinedKey !== null)
                                            ? e((string) $detailData->$joinedKey)
                                            : e((string) $rawValue);
                                    }
                                    // Media — tampilkan nama file saja
                                    elseif ($field->wmfc_field_type === 'media') {
                                        $value = e(basename((string) $rawValue));
                                    }
                                    // Range types
                                    elseif (in_array($field->wmfc_field_type, ['date2','datetime2','time2','year2'])) {
                                        $value = formatRangeDelete($field->wmfc_field_type, $rawValue);
                                    }
                                    // Date single
                                    elseif ($field->wmfc_field_type === 'date') {
                                        $value = \Carbon\Carbon::parse($rawValue)->format('Y-m-d');
                                    }
                                    // Datetime single
                                    elseif ($field->wmfc_field_type === 'datetime') {
                                        $value = \Carbon\Carbon::parse($rawValue)->format('Y-m-d H:i:s');
                                    }
                                    // Time single
                                    elseif ($field->wmfc_field_type === 'time') {
                                        $value = substr((string) $rawValue, 0, 5);
                                    }
                                    // Year
                                    elseif ($field->wmfc_field_type === 'year') {
                                        $value = (string) $rawValue;
                                    }
                                    // Textarea — potong jika panjang
                                    elseif ($field->wmfc_field_type === 'textarea') {
                                        $value = e(\Illuminate\Support\Str::limit(strip_tags((string) $rawValue), 100));
                                    }
                                    // Default
                                    else {
                                        $value = e((string) $rawValue);
                                    }
                                @endphp
                                {!! $value !!}
                            </span>
                        </div>
                    </div>
                @endforeach
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
