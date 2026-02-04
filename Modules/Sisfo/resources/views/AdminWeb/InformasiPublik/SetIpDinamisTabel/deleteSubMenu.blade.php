<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\deleteSubMenu.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Konfirmasi Hapus Sub Menu</h5>
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
            <h6 class="mb-0">Detail Sub Menu yang akan dihapus</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Menu Utama:</strong>
                    <p class="text-muted">{{ $ipSubMenu->IpSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Sub Menu Utama:</strong>
                    <p class="text-muted">{{ $ipSubMenu->IpSubMenuUtama->nama_ip_smu ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Sub Menu:</strong>
                    <p class="text-muted">{{ $ipSubMenu->nama_ip_sm }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Status Dokumen:</strong>
                    <p class="text-muted">
                        @if($ipSubMenu->dokumen_ip_sm)
                            <span class="badge badge-success">Memiliki Dokumen</span>
                        @else
                            <span class="badge badge-warning">Tidak ada dokumen</span>
                        @endif
                    </p>
                </div>
            </div>

            <p class="text-center mt-3">
                Apakah Anda yakin ingin menghapus Sub Menu ini?
            </p>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
        <i class="fas fa-trash mr-1"></i> Ya, Hapus Sub Menu
    </button>
</div>

<script>
    function confirmDelete() {
        $.ajax({
            url: '{{ url($setIpDinamisTabelUrl . "/deleteData/" . $ipSubMenu->ip_sub_menu_id . "?type=submenu") }}',
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
                        text: response.message || 'Sub Menu berhasil dihapus'
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