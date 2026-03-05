@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $menuUrl = $menuConfig->wmu_nama;

    /**
     * Format nilai range (*2) menjadi "v1 s/d v2 [durasi]" untuk tabel index
     */
    function formatRangeIndex(string $type, mixed $raw): string {
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
                 . ' <span class="badge badge-info">' . $diff . ' hari</span>';
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
            return $s->format('Y-m-d H:i:s') . ' s/d ' . $e->format('Y-m-d H:i:s')
                 . ' <span class="badge badge-info">' . $badge . '</span>';
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
            $badge   = $hours > 0
                     ? "{$hours} jam" . ($remMin ? " {$remMin} menit" : "")
                     : "{$diffMin} menit";
            return substr($start, 0, 5) . ' s/d ' . substr($end, 0, 5)
                 . ' <span class="badge badge-info">' . $badge . '</span>';
        }

        if ($type === 'year2') {
            $s    = (int) $start;
            $e    = (int) $end;
            $diff = abs($e - $s);
            return $s . ' s/d ' . $e
                 . ' <span class="badge badge-info">' . $diff . ' tahun</span>';
        }

        return e(is_string($raw) ? $raw : json_encode($raw));
    }
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
        <thead>
            <tr>
                <th width="5%">Nomor</th>
                @foreach($fields as $field)
                    @if($field->wmfc_column_name !== $pkColumn && $field->wmfc_display_list == 1)
                        <th>{{ $field->wmfc_field_label }}</th>
                    @endif
                @endforeach
                <th width="30%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $row)
                <tr>
                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $key + 1 }}</td>
                    @foreach($fields as $field)
                        @if($field->wmfc_column_name !== $pkColumn && $field->wmfc_display_list == 1)
                            <td>
                                @php
                                    $columnName = $field->wmfc_column_name;
                                    $rawValue   = $row->$columnName ?? null;
                                    $value      = $rawValue ?? '-';

                                    // FK (search) — tampilkan priority display column
                                    if ($field->wmfc_field_type === 'search' && $field->wmfc_fk_priority_display) {
                                        $priorityCol = $field->wmfc_fk_priority_display;
                                        $joinedKey   = $columnName . '_' . $priorityCol;
                                        $value = (isset($row->$joinedKey) && $row->$joinedKey !== null)
                                            ? e((string) $row->$joinedKey)
                                            : ($rawValue !== null ? e((string) $rawValue) : '-');
                                    }

                                    // Media — tampilkan ikon + nama file
                                    elseif ($field->wmfc_field_type === 'media' && !empty($rawValue)) {
                                        $fileName = basename($rawValue);
                                        $value = '<i class="fas fa-file mr-1"></i>' . e($fileName);
                                    }

                                    // Range types — decode JSON dan format dengan badge durasi
                                    elseif (in_array($field->wmfc_field_type, ['date2','datetime2','time2','year2']) && !empty($rawValue)) {
                                        $value = formatRangeIndex($field->wmfc_field_type, $rawValue);
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
                                        $value = substr((string) $rawValue, 0, 5);
                                    }

                                    // Year single
                                    elseif ($field->wmfc_field_type === 'year' && !empty($rawValue)) {
                                        $value = (string) $rawValue;
                                    }

                                    // Textarea — strip newlines untuk tabel
                                    elseif ($field->wmfc_field_type === 'textarea' && !empty($rawValue)) {
                                        $value = e(Str::limit(strip_tags($rawValue), 80));
                                    }

                                    // Default text — escape
                                    else {
                                        if ($value !== '-') {
                                            $value = e((string) $value);
                                        }
                                    }
                                @endphp
                                {!! $value !!}
                            </td>
                        @endif
                    @endforeach
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @if(
                                Auth::user()->level->hak_akses_kode === 'SAR' ||
                                SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $menuUrl, 'update')
                            )
                                <button class="btn btn-sm btn-warning mx-1"
                                    onclick="modalAction('{{ url($menuUrl . '/editData/' . $row->$pkColumn) }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            @endif
                            <button class="btn btn-sm btn-info mx-1"
                                onclick="modalAction('{{ url($menuUrl . '/detailData/' . $row->$pkColumn) }}')">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            @if(
                                Auth::user()->level->hak_akses_kode === 'SAR' ||
                                SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $menuUrl, 'delete')
                            )
                                <button class="btn btn-sm btn-danger mx-1"
                                    onclick="modalAction('{{ url($menuUrl . '/deleteData/' . $row->$pkColumn) }}')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    @php
                        $visibleFieldsCount = $fields->where('wmfc_display_list', 1)->where('wmfc_column_name', '!=', $pkColumn)->count();
                    @endphp
                    <td colspan="{{ $visibleFieldsCount + 2 }}" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            @if(!empty($search))
                                <h5 class="text-muted">Tidak ada data yang cocok</h5>
                                <p class="text-muted mb-0">Pencarian "<strong>{{ $search }}</strong>" tidak ditemukan</p>
                            @else
                                <h5 class="text-muted">Tidak ada data</h5>
                                <p class="text-muted mb-0">Silakan tambahkan data baru dengan klik tombol <strong>Tambah</strong></p>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $data->appends(['search' => $search])->links() }}
</div>