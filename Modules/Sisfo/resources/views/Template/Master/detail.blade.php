{{-- Modal Detail View --}}
@php
/**
 * Format nilai range (*2) menjadi "v1 s/d v2 [durasi]"
 */
function formatRangeValue(string $type, mixed $raw): string {
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
        $diffDays = (int) $s->diffInDays($e);
        $badge = "{$diffDays} hari";
        return '<span class="text-dark">'
             . $s->format('Y-m-d') . ' s/d ' . $e->format('Y-m-d')
             . '</span> <span class="badge badge-info ml-1">' . $badge . '</span>';
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
            $badge   = "{$days} hari"
                     . ($remHour ? " {$remHour} jam" : "")
                     . " {$remMin} menit";
        } elseif ($hours > 0) {
            $badge = "{$hours} jam {$remMin} menit";
        } else {
            $badge = "{$totalMin} menit";
        }
        return '<span class="text-dark">'
             . $s->format('Y-m-d H:i:s') . ' s/d ' . $e->format('Y-m-d H:i:s')
             . '</span> <span class="badge badge-info ml-1">' . $badge . '</span>';
    }

    if ($type === 'time2') {
        $parseTime = function(string $t): array {
            $parts = explode(':', $t);
            return [(int)($parts[0] ?? 0), (int)($parts[1] ?? 0), (int)($parts[2] ?? 0)];
        };
        [$sh, $sm, $ss] = $parseTime($start);
        [$eh, $em, $es] = $parseTime($end);
        $diffSec  = abs(($eh * 3600 + $em * 60 + $es) - ($sh * 3600 + $sm * 60 + $ss));
        $diffMin  = (int) floor($diffSec / 60);
        $hours    = (int) floor($diffMin / 60);
        $remMin   = $diffMin % 60;
        $badge    = $hours > 0
                  ? "{$hours} jam" . ($remMin ? " {$remMin} menit" : "")
                  : "{$diffMin} menit";
        $fmtStart = substr($start, 0, 5);
        $fmtEnd   = substr($end, 0, 5);
        return '<span class="text-dark">'
             . $fmtStart . ' s/d ' . $fmtEnd
             . '</span> <span class="badge badge-info ml-1">' . $badge . '</span>';
    }

    if ($type === 'year2') {
        $s     = (int) $start;
        $e     = (int) $end;
        $diff  = abs($e - $s);
        $badge = "{$diff} tahun";
        return '<span class="text-dark">'
             . $s . ' s/d ' . $e
             . '</span> <span class="badge badge-info ml-1">' . $badge . '</span>';
    }

    return e(is_string($raw) ? $raw : json_encode($raw));
}
@endphp

<div class="modal-header bg-info">
    <h5 class="modal-title text-white">
        <i class="fas fa-info-circle mr-2"></i>{{ $pageTitle ?? 'Detail Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    @foreach($fields as $field)
        <div class="row mb-3">
            <div class="col-12">
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-dark mb-1">{{ $field->wmfc_field_label }}</label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        @php
                            $columnName = $field->wmfc_column_name;
                            $rawValue   = $detailData->$columnName ?? null;
                            $value      = $rawValue ?? '-';

                            // FK (search) — tampilkan priority display
                            if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_priority_display) {
                                $priorityCol = $field->wmfc_fk_priority_display;
                                $joinedKey   = $columnName . '_' . $priorityCol;
                                $value = (isset($detailData->$joinedKey) && $detailData->$joinedKey !== null)
                                    ? e((string) $detailData->$joinedKey)
                                    : ($rawValue !== null ? e((string) $rawValue) : '-');
                            }

                            // Media
                            elseif ($field->wmfc_field_type === 'media' && !empty($rawValue)) {
                                $fileUrl  = asset('storage/' . $rawValue);
                                $fileName = basename($rawValue);
                                $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isImage  = in_array($ext, ['jpg','jpeg','png','gif','webp','svg','bmp']);
                                if ($isImage) {
                                    $value = '<div class="mb-2">
                                        <img src="' . $fileUrl . '" alt="' . e($fileName) . '"
                                             class="img-thumbnail" style="max-width:300px;max-height:300px;">
                                    </div>
                                    <a href="' . $fileUrl . '" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                    <small class="d-block mt-1 text-muted">' . e($fileName) . '</small>';
                                } else {
                                    $value = '<a href="' . $fileUrl . '" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download mr-1"></i>Download File
                                    </a>
                                    <small class="d-block mt-1 text-muted">' . e($fileName) . '</small>';
                                }
                            }

                            // Range types — decode JSON dan format dengan badge durasi
                            elseif (in_array($field->wmfc_field_type, ['date2','datetime2','time2','year2']) && !empty($rawValue)) {
                                $value = formatRangeValue($field->wmfc_field_type, $rawValue);
                            }

                            // Date single
                            elseif ($field->wmfc_field_type === 'date' && !empty($rawValue)) {
                                $value = \Carbon\Carbon::parse($rawValue)->format('d/m/Y');
                            }

                            // Datetime single
                            elseif ($field->wmfc_field_type === 'datetime' && !empty($rawValue)) {
                                $value = \Carbon\Carbon::parse($rawValue)->format('d/m/Y H:i');
                            }

                            // Time single
                            elseif ($field->wmfc_field_type === 'time' && !empty($rawValue)) {
                                $value = substr((string) $rawValue, 0, 5); // HH:MM
                            }

                            // Year single
                            elseif ($field->wmfc_field_type === 'year' && !empty($rawValue)) {
                                $value = (string) $rawValue;
                            }

                            // Textarea
                            elseif ($field->wmfc_field_type === 'textarea' && !empty($rawValue)) {
                                $value = nl2br(e((string) $rawValue));
                            }

                            // Default text — escape untuk safety
                            else {
                                if ($value !== '-') {
                                    $value = e((string) $value);
                                }
                            }
                        @endphp
                        {!! $value !!}
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Audit Trail Section --}}
    @if(isset($detailData->created_at) || isset($detailData->updated_at))
        <hr>
        <h6 class="font-weight-bold mb-3">
            <i class="fas fa-history mr-2 text-secondary"></i>Informasi Audit
        </h6>
        <div class="row">
            @if(isset($detailData->created_at) && $detailData->created_at)
                <div class="col-md-6 mb-2">
                    <small class="text-muted d-block">Dibuat pada:</small>
                    <span class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($detailData->created_at)->format('d/m/Y H:i:s') }}
                    </span>
                    @if(isset($detailData->created_by) && $detailData->created_by)
                        <small class="text-muted d-block">Oleh: {{ $detailData->created_by }}</small>
                    @endif
                </div>
            @endif

            @if(isset($detailData->updated_at) && $detailData->updated_at && $detailData->updated_at != $detailData->created_at)
                <div class="col-md-6 mb-2">
                    <small class="text-muted d-block">Terakhir diupdate:</small>
                    <span class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($detailData->updated_at)->format('d/m/Y H:i:s') }}
                    </span>
                    @if(isset($detailData->updated_by) && $detailData->updated_by)
                        <small class="text-muted d-block">Oleh: {{ $detailData->updated_by }}</small>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times mr-1"></i>Tutup
    </button>
</div>