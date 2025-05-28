@php
 use Modules\Sisfo\App\Models\Website\WebMenuModel;
 use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
     $uploadPSUrl = WebMenuModel::getDynamicMenuUrl('upload-penyelesaian-sengketa');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $uploadPS->firstItem() ?? 0 }} to {{ $uploadPS->lastItem() ?? 0 }} of {{ $uploadPS->total() ?? 0 }}
        results
    </div>
</div>
<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kategori Upload</th>
                <th width="15%">Tipe Data Upload</th>
                <th width="30%">List Data Upload</th>
                <th width="30%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($uploadPS as $key => $item)
                <tr>
                    <td>{{ ($uploadPS->currentPage() - 1) * $uploadPS->perPage() + $key + 1 }}</td>
                    <td>{{ $item->penyelesaianSengketa->ps_nama }}</td>
                    <td>{{ $item->kategori_upload_ps}}</td>
                    <td>
                        @if($item->kategori_upload_ps== 'link')
                            <a href="{{ $item->upload_ps }}" target="_blank" class="badge badge-info">
                                <i class="fas fa-link"></i> Lihat Link
                            </a>
                        @elseif($item->kategori_upload_ps== 'file')
                            <a href="{{ asset('storage/' . $item->upload_ps) }}" target="_blank" class="badge badge-success">
                                <i class="fas fa-file"></i> Lihat File
                            </a>
                        @else
                            {{ Str::limit($item->kategori_upload_ps, 50) }}
                        @endif
                    </td>
                    <td>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $uploadPSUrl, 'update')
                        )
                            <button class="btn btn-sm btn-warning"
                                onclick="modalAction('{{ url($uploadPSUrl . '/editData/' . $item->upload_ps_id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        @endif
                        <button class="btn btn-sm btn-info"
                            onclick="modalAction('{{ url($uploadPSUrl . '/detailData/' . $item->upload_ps_id) }}')">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                         @if(
                              Auth::user()->level->hak_akses_kode === 'SAR' ||
                              SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $uploadPSUrl, 'delete')
                         )
                              <button class="btn btn-sm btn-danger"
                                   onclick="modalAction('{{ url($uploadPSUrl . '/deleteData/' . $item->upload_ps_id) }}')">
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
        {{ $uploadPS->appends(['search' => $search])->links() }}
    </div>