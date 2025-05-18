<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\data.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $webMenuUrls->firstItem() ?? 0 }} to {{ $webMenuUrls->lastItem() ?? 0 }} of {{ $webMenuUrls->total() ?? 0 }}
        results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Aplikasi</th>
                <th width="20%">URL Menu</th>
                <th width="25%">Deskripsi URL Menu</th>
                <th width="30%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($webMenuUrls as $key => $item)
                <tr>
                    <td>{{ ($webMenuUrls->currentPage() - 1) * $webMenuUrls->perPage() + $key + 1 }}</td>
                    <td>{{ $item->application ? $item->application->app_nama : 'Tidak Ada' }}</td>
                    <td>{{ $item->wmu_nama }}</td>
                    <td><code>{{ $item->wmu_keterangan }}</code></td>
                    <td>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuUrlPath, 'update')
                        )
                            <button class="btn btn-sm btn-warning"
                                onclick="modalAction('{{ url($webMenuUrlPath . '/editData/' . $item->web_menu_url_id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        @endif
                        <button class="btn btn-sm btn-info"
                            onclick="modalAction('{{ url($webMenuUrlPath . '/detailData/' . $item->web_menu_url_id) }}')">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuUrlPath, 'delete')
                        )
                            <button class="btn btn-sm btn-danger"
                                onclick="modalAction('{{ url($webMenuUrlPath . '/deleteData/' . $item->web_menu_url_id) }}')">
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
                            Tidak ada data URL menu
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $webMenuUrls->appends(['search' => $search])->links() }}
</div>