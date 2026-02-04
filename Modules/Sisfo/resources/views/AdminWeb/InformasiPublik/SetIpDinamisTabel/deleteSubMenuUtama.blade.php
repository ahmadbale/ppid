<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\deleteSubMenuUtama.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Konfirmasi Hapus Sub Menu Utama</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
    </div>

    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h6 class="mb-0">Detail Sub Menu Utama yang akan dihapus</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Menu Utama:</strong>
                    <p class="text-muted">{{ $ipSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Sub Menu Utama:</strong>
                    <p class="text-muted">{{ $ipSubMenuUtama->nama_ip_smu }}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Jumlah Sub Menu:</strong>
                    <p class="text-muted">{{ $ipSubMenuUtama->IpSubMenu->count() }} Sub Menu</p>
                </div>
                <div class="col-md-6">
                    <strong>Status Dokumen:</strong>
                    <p class="text-muted">
                        @if($ipSubMenuUtama->dokumen_ip_smu)
                            <span class="badge badge-success">Memiliki Dokumen</span>
                        @else
                            <span class="badge badge-warning">Tidak ada dokumen</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($ipSubMenuUtama->IpSubMenu->count() > 0)
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>Sub Menu Utama ini tidak dapat dihapus</strong> karena masih memiliki {{ $ipSubMenuUtama->IpSubMenu->count() }} Sub Menu.
                    <br><small>Silakan hapus semua Sub Menu terlebih dahulu.</small>
                </div>
            @else
                <p class="text-center mt-3">
                    Apakah Anda yakin ingin menghapus Sub Menu Utama ini?
                </p>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    @if($ipSubMenuUtama->IpSubMenu->count() == 0)
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
            <i class="fas fa-trash mr-1"></i> Ya, Hapus Sub Menu Utama
        </button>
    @endif
</div>

<script>
    function confirmDelete() {
        $.ajax({
            url: '{{ url($setIpDinamisTabelUrl . "/deleteData/" . $ipSubMenuUtama->ip_sub_menu_utama_id . "?type=submenu_utama") }}',
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#myModal').modal('hide');
                    reloadTable();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Sub Menu Utama berhasil dihapus'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat menghapus data'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menghapus data'
                });
            }
        });
    }
</script>