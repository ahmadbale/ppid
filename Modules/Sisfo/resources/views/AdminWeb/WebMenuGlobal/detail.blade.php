<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\detail.blade.php -->
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
            <th width="200">Nama Default Menu</th>
            <td>{{ $webMenuGlobal->wmg_nama_default }}</td>
          </tr>
          <tr>
            <th>URL Menu</th>
            <td>
              @if($webMenuGlobal->fk_web_menu_url)
                  <strong>Aplikasi:</strong> {{ $webMenuGlobal->WebMenuUrl->application->app_nama }}<br>
                  <strong>Nama URL:</strong> {{ $webMenuGlobal->WebMenuUrl->wmu_nama }}<br>
                  @if($webMenuGlobal->WebMenuUrl->wmu_keterangan)
                      <strong>Keterangan:</strong> {{ $webMenuGlobal->WebMenuUrl->wmu_keterangan }}
                  @endif
              @else
                  <span class="badge badge-info">Group Menu</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($webMenuGlobal->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $webMenuGlobal->created_by }}</td>
          </tr>
          @if($webMenuGlobal->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($webMenuGlobal->updated_at)) }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $webMenuGlobal->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>