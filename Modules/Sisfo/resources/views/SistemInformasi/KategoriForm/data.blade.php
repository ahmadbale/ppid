<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $kategoriForm->firstItem() }} to {{ $kategoriForm->lastItem() }} of {{ $kategoriForm->total() }} results
    </div>
</div>
<div class="table-responsive">
<table class="table table-responsive-stack table-bordered table-striped table-hover table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>Nomor</th>
            <th>Nama Kategori Form</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategoriForm as $key => $item)
        <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($kategoriForm->currentPage() - 1) * $kategoriForm->perPage() + $key + 1 }}</td>
            <td table-data-label="Nama Kategori Form" class="text-start">{{ $item->kf_nama }}</td>
            <td table-data-label="Aksi" class="text-center">
            {{-- <td class="text-center">{{ ($kategoriForm->currentPage() - 1) * $kategoriForm->perPage() + $key + 1 }}</td>
            <td class="text-start">{{ $item->kf_nama }}</td>
            <td class="text-center"> --}}
                <button class="btn btn-sm btn-warning me-1" onclick="modalAction('{{ url("SistemInformasi/KategoriForm/editData/{$item->kategori_form_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info me-1" onclick="modalAction('{{ url("SistemInformasi/KategoriForm/detailData/{$item->kategori_form_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("SistemInformasi/KategoriForm/deleteData/{$item->kategori_form_id}") }}')">
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
    {{ $kategoriForm->appends(['search' => $search])->links() }}
</div>


{{-- <style>
    @media (max-width: 768px) {
        .table-responsive-stack tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .table-responsive-stack td,
        .table-responsive-stack th {
            display: block;
            text-align: left !important;
            width: 100% !important;
        }

        .table-responsive-stack thead {
            display: none;
        }

        .table-responsive-stack td::before {
            content: attr(table-data-label);
            font-weight: bold;
            display: block;
            margin-bottom: 0.25rem;
            color: #212529;
        }
    }
</style> --}}
