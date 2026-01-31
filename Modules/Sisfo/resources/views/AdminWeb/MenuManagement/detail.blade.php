<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\detail.blade.php -->
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
@endphp

<!-- Detail Menu Modal -->
<div class="modal fade" id="detailMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Detail Menu Aplikasi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="35%">Nama Menu</th>
                        <td><span id="detail_menu_nama"></span></td>
                    </tr>
                    <tr>
                        <th>URL Menu</th>
                        <td><span id="detail_menu_url"></span></td>
                    </tr>
                    <tr>
                        <th>Jenis Menu</th>
                        <td><span id="detail_jenis_menu"></span></td>
                    </tr>
                    <tr>
                        <th>Kategori Menu</th>
                        <td><span id="detail_parent_menu"></span></td>
                    </tr>
                    <tr>
                        <th>Urutan Menu</th>
                        <td><span id="detail_urutan_menu"></span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span id="detail_status_menu"></span></td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td><span id="detail_created_by"></span></td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td><span id="detail_created_at"></span></td>
                    </tr>
                    <tr>
                        <th>Diperbarui Oleh</th>
                        <td><span id="detail_updated_by"></span></td>
                    </tr>
                    <tr>
                        <th>Tanggal Diperbarui</th>
                        <td><span id="detail_updated_at"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Detail Menu - gunakan event delegation untuk mencegah binding ganda
$(document).off('click', '.detail-menu').on('click', '.detail-menu', function() {
    let menuId = $(this).data('id');

    if (!menuId) {
        console.error("Data ID tidak ditemukan.");
        return;
    }

    $.ajax({
        url: "/{{ WebMenuModel::getDynamicMenuUrl('menu-management') }}/detailData/" + menuId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let menu = response.menu;

                // Isi modal dengan data dari server
                $('#detail_menu_nama').text(menu.wm_menu_nama || '-');
                $('#detail_menu_url').text(menu.wm_menu_url || '-');
                $('#detail_jenis_menu').text(menu.jenis_menu_nama || '-'); 
                $('#detail_parent_menu').text(
                    menu.wm_parent_id ?
                        `Anak dari Menu ${menu.parent_menu_nama || '-'}` :
                        'Menu Induk'
                );
                $('#detail_urutan_menu').text(menu.wm_urutan_menu || '-');
                $('#detail_status_menu').html(
                    `<span class="badge ${menu.wm_status_menu === 'aktif' ? 'badge-success' : 'badge-danger'}">
                        ${menu.wm_status_menu}
                    </span>`
                );
                $('#detail_created_by').text(menu.created_by || '-');
                $('#detail_created_at').text(menu.created_at || '-');
                $('#detail_updated_by').text(menu.updated_by || '-');
                $('#detail_updated_at').text(menu.updated_at || '-');

                // Tampilkan modal setelah data terisi
                $('#detailMenuModal').modal('show');
            } else {
                console.error("Gagal mendapatkan data:", response.message);
                toastr.error("Gagal memuat detail menu: " + response.message);
            }
        },
        error: function(xhr) {
            console.error("AJAX Error:", xhr.responseText);
            toastr.error("Terjadi kesalahan saat mengambil data menu.");
        }
    });
});
</script>
@endpush