<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\delete.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus URL Menu</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus URL menu dengan detail
    berikut:
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Aplikasi</th>
          <td>{{ $webMenuUrl->application ? $webMenuUrl->application->app_nama : 'Tidak Ada' }}</td>
        </tr>
        <tr>
          <th>URL Menu</th>
          <td>{{ $webMenuUrl->wmu_nama }}</td>
        </tr>
        <tr>
          <th>Deskripsi URL Menu</th>
          <td><code>{{ $webMenuUrl->wmu_keterangan }}</code></td>
        </tr>
      </table>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus URL menu ini mungkin akan
    memengaruhi menu lain yang menggunakan URL ini. Pastikan tidak ada menu lain yang masih
    menggunakan URL ini.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton"
    onclick="confirmDelete('{{ url($webMenuUrlPath . '/deleteData/' . $webMenuUrl->web_menu_url_id) }}')">
    <i class="fas fa-trash mr-1"></i> Hapus
  </button>
</div>

<script>
  function confirmDelete(url) {
    const button = $('#confirmDeleteButton');

    button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);

    $.ajax({
      url: url,
      type: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        $('#myModal').modal('hide');

        if (response.success) {
          reloadTable();

          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: response.message
          });
        }
      },
      error: function (xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
        });

        button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
      }
    });
  }
</script>