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
            <th>Kategori Menu</th>
            <td>
              @php
                  $badgeClass = [
                      'Menu Biasa' => 'badge-primary',
                      'Group Menu' => 'badge-success',  
                      'Sub Menu' => 'badge-info'
                  ][$webMenuGlobal->wmg_kategori_menu] ?? 'badge-secondary';
              @endphp
              <span class="badge {{ $badgeClass }}">{{ $webMenuGlobal->wmg_kategori_menu }}</span>
            </td>
          </tr>
          @if($webMenuGlobal->wmg_parent_id)
          <tr>
            <th>Menu Induk</th>
            <td>{{ $webMenuGlobal->parentMenu->wmg_nama_default ?? 'N/A' }}</td>
          </tr>
          @endif
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
                  <span class="badge badge-warning">Group Menu (Tanpa URL)</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Icon Menu</th>
            <td>
              @if($webMenuGlobal->wmg_icon)
                <i class="fas {{ $webMenuGlobal->wmg_icon }} fa-2x mr-2"></i>
                <code>{{ $webMenuGlobal->wmg_icon }}</code>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Tipe Menu</th>
            <td>
              <span class="badge badge-{{ $webMenuGlobal->wmg_type === 'general' ? 'primary' : 'warning' }}">
                {{ ucfirst($webMenuGlobal->wmg_type) }}
              </span>
              <br>
              <small class="text-muted">
                @if($webMenuGlobal->wmg_type === 'general')
                  Muncul di sidebar (operasional) & header (user)
                @else
                  Hanya muncul di header halaman dengan sidebar
                @endif
              </small>
            </td>
          </tr>
          <tr>
            <th>Indikator Notifikasi</th>
            <td>
              @if($webMenuGlobal->wmg_badge_method)
                <span class="badge badge-success">
                  <i class="fas fa-bell"></i> Ya
                </span>
                <br><small class="text-muted">Method: {{ $webMenuGlobal->wmg_badge_method }}</small>
              @else
                <span class="badge badge-secondary">
                  <i class="fas fa-bell-slash"></i> Tidak
                </span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Urutan Menu</th>
            <td>{{ $webMenuGlobal->wmg_urutan_menu ?? 'Auto' }}</td>
          </tr>
          <tr>
            <th>Status Menu</th>
            <td>
              <span class="badge {{ $webMenuGlobal->wmg_status_menu === 'aktif' ? 'badge-success' : 'badge-danger' }}">
                {{ ucfirst($webMenuGlobal->wmg_status_menu) }}
              </span>
            </td>
          </tr>
          @if($webMenuGlobal->children->count() > 0)
          <tr>
            <th>Sub Menu</th>
            <td>
              <ul class="list-unstyled mb-0">
                @foreach($webMenuGlobal->children->sortBy('wmg_urutan_menu') as $child)
                <li>
                  <i class="fas fa-angle-right text-muted"></i> 
                  {{ $child->wmg_nama_default }}
                  <span class="badge badge-sm {{ $child->wmg_status_menu === 'aktif' ? 'badge-success' : 'badge-danger' }}">
                    {{ $child->wmg_status_menu }}
                  </span>
                </li>
                @endforeach
              </ul>
            </td>
          </tr>
          @endif
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