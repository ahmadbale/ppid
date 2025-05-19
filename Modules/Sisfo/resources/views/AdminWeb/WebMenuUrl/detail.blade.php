<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\detail.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <div class="card">
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">Aplikasi</th>
            <td>{{ $webMenuUrl->application ? $webMenuUrl->application->app_nama : 'Tidak Ada' }}</td>
          </tr>
          <tr>
            <th>Nama URL Menu</th>
            <td>{{ $webMenuUrl->wmu_nama }}</td>
          </tr>
          <tr>
            <th>Deskripsi URL Menu</th>
            <td>{{ $webMenuUrl->wmu_keterangan ?: '-' }}</td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($webMenuUrl->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $webMenuUrl->created_by }}</td>
          </tr>
          @if($webMenuUrl->updated_by)
            <tr>
              <th>Terakhir Diperbarui</th>
              <td>{{ date('d-m-Y H:i:s', strtotime($webMenuUrl->updated_at)) }}</td>
            </tr>
            <tr>
              <th>Diperbarui Oleh</th>
              <td>{{ $webMenuUrl->updated_by }}</td>
            </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>