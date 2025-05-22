@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $liDinamisUrl = WebMenuModel::getDynamicMenuUrl('layanan-informasi-Dinamis');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $liDinamis->firstItem() }} to {{ $liDinamis->lastItem() }} of {{ $liDinamis->total() }}
        results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Kode</th>
            <th width="45%">Nama</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($liDinamis as $key => $item)
                <tr>
                    <td>{{ ($liDinamis->currentPage() - 1) * $liDinamis->perPage() + $key + 1 }}</td>
                    <td>{{ $item->li_dinamis_kode }}</td>
                    <td>{{ $item->li_dinamis_nama }}</td>
                    <td>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $liDinamisUrl, 'update')
                        )
                            <button class="btn btn-sm btn-warning"
                                onclick="modalAction('{{ url($liDinamisUrl . '/editData/' . $item->li_dinamis_id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        @endif
                        <button class="btn btn-sm btn-info"
                            onclick="modalAction('{{ url($liDinamisUrl . '/detailData/' . $item->li_dinamis_id) }}')">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $liDinamisUrl, 'delete')
                        )
                            <button class="btn btn-sm btn-danger"
                                onclick="modalAction('{{ url($liDinamisUrl . '/deleteData/' . $item->li_dinamis_id) }}')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        @endif
                    </td>
                </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">
                    @if(!empty($search))
                        Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                    @else
                        Tidak ada data
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">
    {{ $liDinamis->appends(['search' => $search])->links() }}
</div>