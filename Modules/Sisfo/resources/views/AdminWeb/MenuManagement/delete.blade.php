<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\delete.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus <strong id="menuNameToDelete"></strong>? Tindakan ini tidak dapat
                    dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Hapus Menu - gunakan event delegation untuk mencegah binding ganda
$(document).off('click', '.delete-menu').on('click', '.delete-menu', function() {
    let menuId = $(this).data('id');
    let menuName = $(this).data('name');
    let hakAksesKode = $(this).data('level-kode');

    // Jika level menu adalah SAR dan pengguna bukan SAR, tolak
    if (hakAksesKode === 'SAR' && '{{ Auth::user()->level->hak_akses_kode }}' !== 'SAR') {
        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat menghapus menu SAR');
        return false;
    }

    $('#confirmDelete').data('id', menuId);
    $('#menuNameToDelete').text(menuName);
    $('#deleteConfirmModal').modal('show');
});

// Event listener untuk konfirmasi hapus
$('#confirmDelete').off('click').on('click', function() {
    let menuId = $(this).data('id');

    if (menuId) {
        $.ajax({
            url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/deleteData/${menuId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
                $('#deleteConfirmModal').modal('hide');
            },
            error: function(xhr) {
                toastr.error('Error deleting menu');
                console.error(xhr.responseText);
                $('#deleteConfirmModal').modal('hide');
            }
        });
    }
});
</script>
@endpush