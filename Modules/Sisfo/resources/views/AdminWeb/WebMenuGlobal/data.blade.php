<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\data.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $webMenuGlobals->firstItem() ?? 0 }} to {{ $webMenuGlobals->lastItem() ?? 0 }} of {{ $webMenuGlobals->total() ?? 0 }}
        results
    </div>
</div>

<div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Default</th>
                <th width="15%">Kategori</th>
                <th width="20%">Menu URL</th>
                <th width="15%">Parent/Urutan</th>
                <th width="10%">Status</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($webMenuGlobals as $key => $item)
                <tr>
                    <td>{{ ($webMenuGlobals->currentPage() - 1) * $webMenuGlobals->perPage() + $key + 1 }}</td>
                    <td>{{ $item->wmg_nama_default }}</td>
                    <td>
                        @php
                            $badgeClass = [
                                'Menu Biasa' => 'badge-primary',
                                'Group Menu' => 'badge-success',
                                'Sub Menu' => 'badge-info'
                            ][$item->wmg_kategori_menu] ?? 'badge-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $item->wmg_kategori_menu }}</span>
                    </td>
                    <td>
                        @if($item->fk_web_menu_url)
                            <strong>{{ $item->WebMenuUrl->application->app_nama }}</strong><br>
                            <small>{{ $item->WebMenuUrl->wmu_nama }}</small>
                        @else
                            <span class="badge badge-warning">Group Menu</span>
                        @endif
                    </td>
                    <td>
                        @if($item->wmg_parent_id)
                            <small class="text-muted">
                                Parent: {{ $item->parentMenu->wmg_nama_default ?? 'N/A' }}<br>
                            </small>
                        @endif
                        <span class="badge badge-light">Urutan: {{ $item->wmg_urutan_menu ?? 'Auto' }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $item->wmg_status_menu === 'aktif' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($item->wmg_status_menu) }}
                        </span>
                    </td>
                    <td>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'update')
                        )
                            <button class="btn btn-sm btn-warning"
                                onclick="modalAction('{{ url($webMenuGlobalPath . '/editData/' . $item->web_menu_global_id) }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                        <button class="btn btn-sm btn-info"
                            onclick="modalAction('{{ url($webMenuGlobalPath . '/detailData/' . $item->web_menu_global_id) }}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'delete')
                        )
                            <button class="btn btn-sm btn-danger"
                                onclick="modalAction('{{ url($webMenuGlobalPath . '/deleteData/' . $item->web_menu_global_id) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        @if(!empty($search))
                            Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                        @else
                            Tidak ada data menu global
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $webMenuGlobals->appends(['search' => $search])->links() }}
</div>