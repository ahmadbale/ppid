@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

<!-- Modal Pilih Level Hak Akses -->
<div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($levels as $level)
                        <a href="{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/set-menu/' . $level->hak_akses_id) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>{{ $level->hak_akses_nama }}</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>