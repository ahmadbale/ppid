<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $footer->firstItem() }} to {{ $footer->lastItem() }} of {{ $footer->total() }} results
    </div>
</div>

<div class="table-responsive">
<table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead class="text-center">
        <tr>
            <th width=10% >Nomor</th>
            <th width=20%>Kode Footer</th>
            <th width=40%>Judul Footer</th>
            <th width=35%>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($footer as $key => $item)
        <tr>

            <td table-data-label="Nomor" class="text-center">{{ ($footer->currentPage() - 1) * $footer->perPage() + $key + 1 }}</td>
            <td table-data-label="Kode Footer" class="text-center">{{ $item->kategoriFooter->kt_footer_kode ?? 'Tidak Ada' }}</td>
            <td table-data-label="Judul Footer" class="text-center">{{ $item->f_judul_footer }}</td>
            <td table-data-label="Aksi" class="text-center">
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/footer/editData/{$item->footer_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/footer/detailData/{$item->footer_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/footer/deleteData/{$item->footer_id}") }}')">
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
    {{ $footer->appends(['search' => $search])->links() }}
</div>
