
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $detailMediaDinamis->firstItem() }} to {{ $detailMediaDinamis->lastItem() }} of {{$detailMediaDinamis->total() }} results
    </div>
</div>
<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
   
        <thead class="text-center">
        <tr>
            <th>Nomor</th>
            <th>Kategori</th>
            <th>Judul Media</th>
            <th>Tipe Media</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailMediaDinamis as $key => $item)
        <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($detailMediaDinamis->currentPage() - 1) * $detailMediaDinamis->perPage() + $key + 1 }}</td>
            <td table-data-label="Kategori" class="text-center">{{ $item->mediaDinamis->md_kategori_media ?? '-' }}</td>
            <td table-data-label="Judul Media" class="text-center">{{ $item->dm_judul_media }}</td>
            <td table-data-label="Tipe Media" class="text-center">{{ ucfirst($item->dm_type_media) }}</td>
            <td table-data-label="Aksi" class="text-center">
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/media-detail/editData/{$item->detail_media_dinamis_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/media-detail/detailData/{$item->detail_media_dinamis_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/media-detail/deleteData/{$item->detail_media_dinamis_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">
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
<div class="mt-3">
    {{ $detailMediaDinamis->appends(['search' => $search])->links() }}
</div>