<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\delete.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Menu Global</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus menu global dengan detail
    berikut:
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Nama Default Menu</th>
          <td>{{ $webMenuGlobal->wmg_nama_default }}</td>
        </tr>
        <tr>
          <th>URL Menu</th>
          <td>
            @if($webMenuGlobal->fk_web_menu_url)
                <strong>Aplikasi:</strong> {{ $webMenuGlobal->WebMenuUrl->application->app_nama }}<br>
                <strong>Nama URL:</strong> {{ $webMenuGlobal->WebMenuUrl->wmu_nama }}
            @else
                <span class="badge badge-info">Group Menu</span>
            @endif
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus menu global ini mungkin akan
    memengaruhi menu website yang menggunakannya. Pastikan tidak ada menu website yang masih
    menggunakan menu global ini.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton"
    onclick="confirmDelete('{{ url($webMenuGlobalPath . '/deleteData/' . $webMenuGlobal->web_menu_global_id) }}')">
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

        // Cek pesan error dalam respons terlepas dari status success
        if (response.message && response.message.includes('sedang digunakan')) {
          // Jika pesan mengandung "sedang digunakan", selalu tampilkan warning
          Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: response.message
          });
        } else if (response.success) {
          // Jika sukses dan bukan pesan "sedang digunakan"
          reloadTable();
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message
          });
        } else {
          // Untuk error lainnya
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
      },
      complete: function() {
        button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
      }
    });
  }
</script>