<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuUrl\delete.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
<div class="modal-header bg-danger text-white">
  <h5 class="modal-title"><i class="fas fa-trash-alt mr-2"></i> Konfirmasi Hapus URL Menu</h5>
  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <style>
    .badge-pk {
      background-color: #007bff;
      color: white;
      font-size: 0.7rem;
      padding: 0.25rem 0.5rem;
    }
    
    .badge-fk {
      background-color: #28a745;
      color: white;
      font-size: 0.7rem;
      padding: 0.25rem 0.5rem;
    }
  </style>

  <div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle mr-2"></i> 
    <strong>Peringatan!</strong> Apakah Anda yakin ingin menghapus URL menu dengan detail berikut?
  </div>

  <!-- Informasi Umum -->
  <div class="card mb-3">
    <div class="card-header bg-light">
      <strong><i class="fas fa-info-circle mr-1"></i> Informasi URL Menu</strong>
    </div>
    <div class="card-body">
      <table class="table table-borderless mb-0">
        <tr>
          <th width="200">Aplikasi</th>
          <td>{{ $webMenuUrl->application ? $webMenuUrl->application->app_nama : 'Tidak Ada' }}</td>
        </tr>
        <tr>
          <th>Kategori Menu</th>
          <td>
            @if($webMenuUrl->wmu_kategori_menu === 'master')
              <span class="badge badge-success">Master</span>
            @elseif($webMenuUrl->wmu_kategori_menu === 'custom')
              <span class="badge badge-info">Custom</span>
            @elseif($webMenuUrl->wmu_kategori_menu === 'pengajuan')
              <span class="badge badge-warning">Pengajuan</span>
            @else
              <span class="badge badge-secondary">{{ ucfirst($webMenuUrl->wmu_kategori_menu) }}</span>
            @endif
          </td>
        </tr>
        <tr>
          <th>URL Menu</th>
          <td><code>{{ $webMenuUrl->wmu_nama }}</code></td>
        </tr>
        <tr>
          <th>Deskripsi</th>
          <td>{{ $webMenuUrl->wmu_keterangan ?: '-' }}</td>
        </tr>
        @if($webMenuUrl->wmu_kategori_menu === 'master')
        <tr>
          <th>Tabel Akses</th>
          <td><code>{{ $webMenuUrl->wmu_akses_tabel }}</code></td>
        </tr>
        @endif
        @if($webMenuUrl->controller_name)
        <tr>
          <th>Controller</th>
          <td><code>{{ $webMenuUrl->controller_name }}</code></td>
        </tr>
        @endif
      </table>
    </div>
  </div>

  <!-- Field Configs yang akan terhapus (jika Master) -->
  @if($webMenuUrl->wmu_kategori_menu === 'master' && $webMenuUrl->fieldConfigs && count($webMenuUrl->fieldConfigs) > 0)
  <div class="card mb-3">
    <div class="card-header bg-warning text-dark">
      <strong><i class="fas fa-exclamation-circle mr-1"></i> Data yang Akan Terhapus</strong>
    </div>
    <div class="card-body">
      <p class="mb-2"><strong>{{ count($webMenuUrl->fieldConfigs) }} Field Configuration</strong> akan ikut terhapus:</p>
      <ul class="mb-0">
        @foreach($webMenuUrl->fieldConfigs as $field)
        <li>
          <strong>{{ $field->wmfc_column_name }}</strong>
          @if($field->wmfc_is_primary_key)
            <span class="badge badge-pk ml-1">PK</span>
          @endif
          @if($field->wmfc_fk_table)
            <span class="badge badge-fk ml-1">FK</span>
          @endif
          <small class="text-muted">({{ $field->wmfc_field_label }})</small>
        </li>
        @endforeach
      </ul>
    </div>
  </div>
  @endif

  <div class="alert alert-warning">
    <i class="fas fa-info-circle mr-2"></i> 
    <strong>Perhatian:</strong> Menghapus URL menu ini mungkin akan memengaruhi:
    <ul class="mb-0 mt-2">
      <li>Menu global yang menggunakan URL ini</li>
      <li>Hak akses yang terkait dengan menu ini</li>
      <li>Konfigurasi field (untuk menu Master)</li>
    </ul>
    Pastikan tidak ada menu lain yang masih menggunakan URL ini.
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

        // Cek pesan error dalam respons terlepas dari status success
        if (response.message && response.message.includes('sedang digunakan')) {
          // Hapus duplikasi dalam pesan jika ada
          let cleanMessage = response.message;
          
          // Jika pesan duplikat, potong bagian duplikat
          const colonPos = cleanMessage.indexOf(':');
          if (colonPos > -1 && cleanMessage.substring(colonPos + 2) === cleanMessage.substring(0, colonPos)) {
            cleanMessage = cleanMessage.substring(0, colonPos);
          }
          
          // Jika pesan mengandung "sedang digunakan", selalu tampilkan warning
          Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: cleanMessage
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
            text: response.message || 'Gagal menghapus URL menu'
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