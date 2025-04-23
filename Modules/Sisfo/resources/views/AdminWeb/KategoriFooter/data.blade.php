<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $kategoriFooter->firstItem() }} to {{ $kategoriFooter->lastItem() }} of {{ $kategoriFooter->total() }} results
    </div>
</div>

<div class="table-responsive">
<table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead class="text-center">
        <tr>
            <th>Nomor</th>
            <th>Kode Kategori</th>
            <th>Nama Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategoriFooter as $key => $item)
        <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($kategoriFooter->currentPage() - 1) * $kategoriFooter->perPage() + $key + 1 }}</td>
            <td table-data-label="Kode" class="text-center">{{ $item->kt_footer_kode }}</td>
            <td table-data-label="Nama" class="text-center">{{ $item->kt_footer_nama }}</td>
            <td table-data-label="Aksi" class="text-center">
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/kategori-footer/editData/{$item->kategori_footer_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/kategori-footer/detailData/{$item->kategori_footer_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/kategori-footer/deleteData/{$item->kategori_footer_id}") }}')">
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
    {{ $kategoriFooter->appends(['search' => $search])->links() }}
</div>
