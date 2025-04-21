<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $pengumumanDinamis->firstItem() }} to {{ $pengumumanDinamis->lastItem() }} of {{ $pengumumanDinamis->total() }} results
    </div>
</div>

<div class="table-responsive">
<table class="table table-responsive-stack table-bordered table-striped table-hover table-sm">
    <thead class="text-center">
        <tr>
            <th width="5%">Nomor</th>
            <th width="55%">Nama Submenu Pengumuman</th>
            <th width="40%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pengumumanDinamis as $key => $item)
        <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($pengumumanDinamis->currentPage() - 1) * $pengumumanDinamis->perPage() + $key + 1 }}</td>
            <td table-data-label="Nama Submenu" class="text-start">{{ $item->pd_nama_submenu }}</td>
            <td table-data-label="Aksi" class="text-center">
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("AdminWeb/PengumumanDinamis/editData/{$item->pengumuman_dinamis_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("AdminWeb/PengumumanDinamis/detailData/{$item->pengumuman_dinamis_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("AdminWeb/PengumumanDinamis/deleteData/{$item->pengumuman_dinamis_id}") }}')">
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
</div>

<div class="mt-3">
    {{ $pengumumanDinamis->appends(['search' => $search])->links() }}
</div>
