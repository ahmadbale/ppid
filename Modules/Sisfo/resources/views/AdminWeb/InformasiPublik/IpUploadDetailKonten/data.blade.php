@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $IpUploadKontenUrl = WebMenuModel::getDynamicMenuUrl('upload-detail-konten');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $ipUploadKonten->firstItem() }} to {{ $ipUploadKonten->lastItem() }} of {{ $ipUploadKonten->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th>Nomor</th>
             <th>Kategori Konten</th>
             <th>Judul Konten</th>
             <th>Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($ipUploadKonten as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($ipUploadKonten->currentPage() - 1) * $ipUploadKonten->perPage() + $key + 1 }}</td>
             <td table-data-label="Kategori Konten" class="text-center">{{ $item->IpDinamisKonten->kd_nama_konten_dinamis }}</td>
             <td table-data-label="Judul Konten" class="text-center">{{ $item->uk_judul_konten }}</td>
             <td table-data-label="Aksi" class="text-center">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $IpUploadKontenUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($IpUploadKontenUrl . '/editData/' . $item->ip_upload_konten_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($IpUploadKontenUrl . '/detailData/' . $item->ip_upload_konten_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $IpUploadKontenUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($IpUploadKontenUrl . '/deleteData/' . $item->ip_upload_konten_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
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
     {{ $ipUploadKonten->appends(['search' => $search])->links() }}
 </div>
