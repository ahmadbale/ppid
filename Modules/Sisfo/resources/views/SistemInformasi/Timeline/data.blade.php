<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $timeline->firstItem() }} to {{ $timeline->lastItem() }} of {{ $timeline->total() }} results
    </div>
</div>

<div class="table-responsive">
<table class="table table-responsive-stack table-bordered table-striped table-hover table-sm">
    <thead class="text-center">
        <tr>
            <th>Nomor</th>
            <th>Kategori Timeline</th>
            <th>Judul Timeline</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($timeline as $key => $item)
        <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($timeline->currentPage() - 1) * $timeline->perPage() + $key + 1 }}</td>
            <td table-data-label="Kategori Timeline" class="text-start">{{ $item->TimelineKategoriForm->kf_nama ?? '-' }}</td>
            <td table-data-label="Judul Timeline" class="text-start">{{ $item->judul_timeline }}</td>
            <td table-data-label="Aksi" class="text-center">

                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("SistemInformasi/Timeline/editData/{$item->timeline_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("SistemInformasi/Timeline/detailData/{$item->timeline_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("SistemInformasi/Timeline/deleteData/{$item->timeline_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
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
    {{ $timeline->appends(['search' => $search])->links() }}
</div>
