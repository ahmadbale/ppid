@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $IpdinamisKontenUrl  = WebMenuModel::getDynamicMenuUrl('dinamis-konten');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $ipDinamisKonten->firstItem() }} to {{ $ipDinamisKonten->lastItem() }} of {{ $ipDinamisKonten->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th>Nomor</th>
             <th>Nama Konten Dinamis</th>
             <th>Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($ipDinamisKonten as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($ipDinamisKonten->currentPage() - 1) * $ipDinamisKonten->perPage() + $key + 1 }}</td>
             <td table-data-label="Nama Konten Dinamis" class="text-center">{{ $item->kd_nama_konten_dinamis }}</td>
             <td table-data-label="Aksi" class="text-center">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $IpdinamisKontenUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($IpdinamisKontenUrl . '/editData/' . $item->ip_dinamis_konten_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($IpdinamisKontenUrl . '/detailData/' . $item->ip_dinamis_konten_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $IpdinamisKontenUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($IpdinamisKontenUrl . '/deleteData/' . $item->ip_dinamis_konten_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
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
     {{ $ipDinamisKonten->appends(['search' => $search])->links() }}
 </div>