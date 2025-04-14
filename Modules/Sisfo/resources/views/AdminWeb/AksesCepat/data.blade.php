<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $aksesCepat->firstItem() }} to {{ $aksesCepat->lastItem() }} of {{ $aksesCepat->total() }} results
    </div>
</div>

<div class="table-responsive">
<table class="table table-responsive-stack table-bordered table-striped table-hover table-sm align-middle">
    <thead class="text-center">
        <tr>
            <th>Nomor</th>
            <th>Judul</th>
            <th>Icon Statis</th>
            <th>Icon Animasi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($aksesCepat as $key => $item)
        <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($aksesCepat->currentPage() - 1) * $aksesCepat->perPage() + $key + 1 }}</td>

            <td table-data-label="Judul" class="text-start">{{ $item->ac_judul }}</td>
            <td table-data-label="Icon Animasi" class="text-center">
                @if($item->ac_static_icon)
                    <img src="{{ asset('storage/akses_cepat_static_icons/' . $item->ac_static_icon) }}"
                         alt="Static Icon" class="img-thumbnail" style="max-height: 50px;">
                @else
                 -
                @endif
            </td>
            <td table-data-label="Icon Animasi" class="text-center">
                @if($item->ac_animation_icon)
                    <img src="{{ asset('storage/akses_cepat_animation_icons/' . $item->ac_animation_icon) }}"
                         alt="Animation Icon" class="img-thumbnail" style="max-height: 50px;">
                @else
                -
                @endif
            </td>
            <td table-data-label="Aksi" class="text-center">
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/akses-cepat/editData/{$item->akses_cepat_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/akses-cepat/detailData/{$item->akses_cepat_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/akses-cepat/deleteData/{$item->akses_cepat_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">
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
    {{ $aksesCepat->appends(['search' => $search])->links() }}
</div>

@push('css')
<style>
    .img-thumbnail {
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }
</style>
@endpush
