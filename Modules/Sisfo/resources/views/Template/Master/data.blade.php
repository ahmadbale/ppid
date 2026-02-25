@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $menuUrl = $menuConfig->wmu_nama;
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
                                    $value = $row->$columnName ?? '-';
                                    
                                    // Format date
                                    if (in_array($field->wmfc_field_type, ['date', 'date2']) && $value !== '-') {
                                        $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                                    }
                                @endphp
                                {{ $value }}
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
