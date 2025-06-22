<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\IpDinamisTabel\data.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $ipDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('kategori-informasi-publik-dinamis-tabel');
@endphp

@if($ipDinamisTabel->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Nama Submenu</th>
                    <th width="40%">Judul</th>
                    <th width="15%">Tanggal Dibuat</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ipDinamisTabel as $index => $item)
                    <tr>
                        <td class="text-center">{{ $ipDinamisTabel->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-folder text-primary me-2"></i>
                                <span class="font-weight-bold">{{ $item->ip_nama_submenu }}</span>
                            </div>
                        </td>
                        <td>{{ $item->ip_judul }}</td>
                        <td>
                            <small class="text-muted">
                                {{ date('d/m/Y H:i', strtotime($item->created_at)) }}
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $ipDinamisTabelUrl, 'update'))
                                    <button class="btn btn-warning" onclick="modalAction('{{ url($ipDinamisTabelUrl . '/editData/' . $item->ip_dinamis_tabel_id) }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                
                                <button class="btn btn-info" onclick="modalAction('{{ url($ipDinamisTabelUrl . '/detailData/' . $item->ip_dinamis_tabel_id) }}')" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $ipDinamisTabelUrl, 'delete'))
                                    <button class="btn btn-danger" onclick="modalAction('{{ url($ipDinamisTabelUrl . '/deleteData/' . $item->ip_dinamis_tabel_id) }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Menampilkan {{ $ipDinamisTabel->firstItem() }} - {{ $ipDinamisTabel->lastItem() }} 
            dari {{ $ipDinamisTabel->total() }} data
        </div>
        <div>
            {{ $ipDinamisTabel->links() }}
        </div>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-folder-open"></i>
        <h5 class="mt-3">
            @if(!empty($search))
                Tidak ditemukan data yang sesuai dengan pencarian "{{ $search }}"
            @else
                Belum ada data Kategori Informasi Publik Dinamis Tabel
            @endif
        </h5>
        <p class="text-muted">
            @if(!empty($search))
                Coba gunakan kata kunci yang berbeda atau kosongkan pencarian.
            @else
                Silakan tambahkan kategori baru untuk memulai.
            @endif
        </p>
        @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $ipDinamisTabelUrl, 'create'))
            <button class="btn btn-primary mt-3" onclick="modalAction('{{ url($ipDinamisTabelUrl . '/addData') }}')">
                <i class="fas fa-plus me-2"></i>Tambah Kategori Baru
            </button>
        @endif
    </div>
@endif