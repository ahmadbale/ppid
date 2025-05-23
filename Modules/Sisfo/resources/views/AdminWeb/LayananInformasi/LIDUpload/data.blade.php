@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $liUploadUrl = WebMenuModel::getDynamicMenuUrl('layanan-informasi-upload');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $liUpload->firstItem() ?? 0 }} to {{ $liUpload->lastItem() ?? 0 }} of {{ $liUpload->total() ?? 0 }}
        results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Kategori Layanan Informasi Dinamis</th>
            <th width="15%">Tipe Data Upload</th>
            <th width="30%">List Data Upload</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($liUpload as $key => $item)
                <tr>
                    <td>{{ ($liUpload->currentPage() - 1) * $liUpload->perPage() + $key + 1 }}</td>
                    <td>{{ $item->LiDinamis->li_dinamis_nama }}</td>
                    <td>{{ $item->lid_upload_type }}</td>
                    <td>
                        @if($item->lid_upload_type == 'link')
                            <a href="{{ $item->lid_upload_value }}" target="_blank" class="badge badge-info">
                                <i class="fas fa-link"></i> Lihat Link
                            </a>
                        @elseif($item->lid_upload_type == 'file')
                            <a href="{{ asset('storage/' . $item->lid_upload_value) }}" target="_blank" class="badge badge-success">
                                <i class="fas fa-file"></i> Lihat File
                            </a>
                        @else
                            {{ Str::limit($item->lid_upload_value, 50) }}
                        @endif
                    </td>
                    <td>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $liUploadUrl, 'update')
                        )
                            <button class="btn btn-sm btn-warning"
                                onclick="modalAction('{{ url($liUploadUrl . '/editData/' . $item->lid_upload_id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        @endif
                        <button class="btn btn-sm btn-info"
                            onclick="modalAction('{{ url($liUploadUrl . '/detailData/' . $item->lid_upload_id) }}')">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $liUploadUrl, 'delete')
                        )
                            <button class="btn btn-sm btn-danger"
                                onclick="modalAction('{{ url($liUploadUrl . '/deleteData/' . $item->lid_upload_id) }}')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        @endif
                    </td>
                </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
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
    {{ $liUpload->appends(['search' => $search])->links() }}
</div>