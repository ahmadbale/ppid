@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $detailFooterUrl = WebMenuModel::getDynamicMenuUrl('detail-footer');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $footer->firstItem() }} to {{ $footer->lastItem() }} of {{ $footer->total() }} results
    </div>
</div>
<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="30%">Kategori Footer</th>
            <th width="40%">Judul Footer</th>
            <th width="25%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($footer as $key => $item)
        <tr>
            <td>{{ ($footer->currentPage() - 1) * $footer->perPage() + $key + 1 }}</td>
             <td>{{ $item->kategoriFooter->kt_footer_nama ?? 'Tidak Ada' }}</td>
            <td>{{ $item->f_judul_footer }}</td>
            <td class="text-center">
    <div class="btn-group" role="group">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailFooterUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning mx-1"
                        onclick="modalAction('{{ url($detailFooterUrl . '/editData/' . $item->footer_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info mx-1"
                    onclick="modalAction('{{ url($detailFooterUrl . '/detailData/' . $item->footer_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailFooterUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger mx-1"
                        onclick="modalAction('{{ url($detailFooterUrl . '/deleteData/' . $item->footer_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
            </td>
            </div>
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
    {{ $footer->appends(['search' => $search])->links() }}
</div>