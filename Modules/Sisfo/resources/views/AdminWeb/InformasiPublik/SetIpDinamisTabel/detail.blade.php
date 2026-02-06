@php
  use Illuminate\Support\Facades\Storage;
  
  // Detect type: menu, submenu_utama, or submenu
  $type = 'menu';
  $cardColor = 'primary';
  $iconClass = 'fa-info-circle';
  $cardTitle = 'Informasi Dasar';
  
  if (isset($ipSubMenu)) {
      $type = 'submenu';
      $cardColor = 'info';
      $iconClass = 'fa-file-text';
      $cardTitle = 'Detail Sub Menu';
  } elseif (isset($ipSubMenuUtama)) {
      $type = 'submenu_utama';
      $cardColor = 'success';
      $iconClass = 'fa-folder-open';
      $cardTitle = 'Detail Sub Menu Utama';
  }
@endphp

<div class="modal-header">
  <h5 class="modal-title">{{ $title }}</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  @if($type == 'submenu')
    {{-- SUB MENU DETAIL --}}
    <div class="card border-{{ $cardColor }}">
      <div class="card-header bg-{{ $cardColor }} text-white">
        <h6 class="mb-0">
          <i class="fas {{ $iconClass }} mr-2"></i>{{ $cardTitle }}
        </h6>
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
            <strong>Nama Sub Menu:</strong>
            <p class="text-muted">{{ $ipSubMenu->nama_ip_sm }}</p>
          </div>
          <div class="col-md-6">
            <strong>Level:</strong>
            <p class="text-muted">
              <span class="badge badge-info">Level 3 (Sub Menu)</span>
            </p>
          </div>
        </div>

        @if($ipSubMenu->dokumen_ip_sm)
          <div class="row">
            <div class="col-md-12">
              <strong>Dokumen:</strong>
              <div class="mt-2">
                <a href="{{ Storage::url($ipSubMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-primary">
                  <i class="fas fa-file-pdf"></i> Lihat Dokumen PDF
                </a>
              </div>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-md-12">
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Sub Menu ini belum memiliki dokumen
              </div>
            </div>
          </div>
        @endif

        <div class="row mt-3">
          <div class="col-md-6">
            <strong>Dibuat:</strong>
            <p class="text-muted">{{ $ipSubMenu->created_at ? $ipSubMenu->created_at->format('d/m/Y H:i:s') : '-' }}</p>
          </div>
          <div class="col-md-6">
            <strong>Diperbarui:</strong>
            <p class="text-muted">{{ $ipSubMenu->updated_at ? $ipSubMenu->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-light mt-3">
      <div class="card-header">
        <h6 class="mb-0">
          <i class="fas fa-sitemap mr-2"></i>Hierarki Menu
        </h6>
      </div>
      <div class="card-body">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
              <i class="fas fa-folder text-primary"></i>
              {{ $ipSubMenu->IpSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }}
            </li>
            <li class="breadcrumb-item">
              <i class="fas fa-folder text-success"></i>
              {{ $ipSubMenu->IpSubMenuUtama->nama_ip_smu ?? 'N/A' }}
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <i class="fas fa-file text-info"></i>
              {{ $ipSubMenu->nama_ip_sm }}
            </li>
          </ol>
        </nav>
      </div>
    </div>

  @elseif($type == 'submenu_utama')
    {{-- SUB MENU UTAMA DETAIL --}}
    <div class="card border-{{ $cardColor }}">
      <div class="card-header bg-{{ $cardColor }} text-white">
        <h6 class="mb-0">
          <i class="fas {{ $iconClass }} mr-2"></i>{{ $cardTitle }}
        </h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <strong>Menu Utama:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <strong>Kategori:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->IpMenuUtama->IpDinamisTabel->ip_nama_submenu ?? 'N/A' }}</p>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <strong>Nama Sub Menu Utama:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->nama_ip_smu }}</p>
          </div>
          <div class="col-md-6">
            <strong>Jumlah Sub Menu:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->IpSubMenu->count() }} Sub Menu</p>
          </div>
        </div>

        @if($ipSubMenuUtama->dokumen_ip_smu)
          <div class="row">
            <div class="col-md-12">
              <strong>Dokumen:</strong>
              <div class="mt-2">
                <a href="{{ Storage::url($ipSubMenuUtama->dokumen_ip_smu) }}" target="_blank" class="btn btn-primary">
                  <i class="fas fa-file-pdf"></i> Lihat Dokumen PDF
                </a>
              </div>
            </div>
          </div>
        @endif

        <div class="row mt-3">
          <div class="col-md-6">
            <strong>Dibuat:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->created_at ? $ipSubMenuUtama->created_at->format('d/m/Y H:i:s') : '-' }}</p>
          </div>
          <div class="col-md-6">
            <strong>Diperbarui:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->updated_at ? $ipSubMenuUtama->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
          </div>
        </div>
      </div>
    </div>

    @if($ipSubMenuUtama->IpSubMenu->count() > 0)
      <div class="card border-info mt-3">
        <div class="card-header bg-info text-white">
          <h6 class="mb-0">
            <i class="fas fa-file mr-2"></i>Daftar Sub Menu ({{ $ipSubMenuUtama->IpSubMenu->count() }})
          </h6>
        </div>
        <div class="card-body">
          @foreach($ipSubMenuUtama->IpSubMenu as $index => $subMenu)
            <div class="card border-secondary mb-2">
              <div class="card-body p-2">
                <div class="row align-items-center">
                  <div class="col-md-6">
                    <strong>{{ $index + 1 }}. {{ $subMenu->nama_ip_sm }}</strong>
                  </div>
                  <div class="col-md-3">
                    @if($subMenu->dokumen_ip_sm)
                      <span class="badge badge-success">Memiliki Dokumen</span>
                    @else
                      <span class="badge badge-warning">Tidak ada dokumen</span>
                    @endif
                  </div>
                  <div class="col-md-3 text-right">
                    @if($subMenu->dokumen_ip_sm)
                      <a href="{{ Storage::url($subMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-file-pdf"></i> PDF
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

  @else
    {{-- MENU UTAMA DETAIL --}}
    <div class="card">
      <div class="card-header bg-{{ $cardColor }} text-white">
        <h5 class="card-title mb-0">
          <i class="fas {{ $iconClass }} mr-2"></i>{{ $cardTitle }}
        </h5>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">Kategori Tabel Dinamis</th>
            <td>{{ $ipMenuUtama->IpDinamisTabel->ip_nama_submenu ?? 'Tidak ada' }}</td>
          </tr>
          <tr>
            <th>Judul Tabel Dinamis</th>
            <td>{{ $ipMenuUtama->IpDinamisTabel->ip_judul ?? 'Tidak ada' }}</td>
          </tr>
          <tr>
            <th>Nama Menu Utama</th>
            <td>{{ $ipMenuUtama->nama_ip_mu }}</td>
          </tr>
          @if($ipMenuUtama->dokumen_ip_mu)
          <tr>
            <th>Dokumen Menu Utama</th>
            <td>
              <a href="{{ Storage::url($ipMenuUtama->dokumen_ip_mu) }}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
              </a>
              <small class="ml-2 text-muted">{{ basename($ipMenuUtama->dokumen_ip_mu) }}</small>
            </td>
          </tr>
          @endif
          <tr>
            <th>Status Menu</th>
            <td>
              @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
                <span class="badge badge-info">Memiliki {{ $ipMenuUtama->IpSubMenuUtama->count() }} Sub Menu Utama</span>
              @else
                <span class="badge badge-success">Menu Tunggal (Memiliki Dokumen)</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ $ipMenuUtama->created_at ? $ipMenuUtama->created_at->format('d-m-Y H:i:s') : '-' }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $ipMenuUtama->created_by ?? '-' }}</td>
          </tr>
          @if($ipMenuUtama->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $ipMenuUtama->updated_at ? $ipMenuUtama->updated_at->format('d-m-Y H:i:s') : '-' }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $ipMenuUtama->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  @endif

  @if($type == 'menu')
    {{-- STATISTIK HANYA UNTUK MENU UTAMA --}}
    @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
    <div class="card mt-3">
      <div class="card-header bg-success text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-sitemap mr-2"></i>Struktur Menu Hierarki
        </h5>
      </div>
      <div class="card-body">
        <div class="hierarchy-structure">
          <!-- Menu Utama -->
          <div class="menu-level-0 mb-3">
            <div class="d-flex align-items-center p-3 border rounded bg-light">
              <i class="fas fa-folder-open fa-2x text-primary mr-3"></i>
              <div>
                <h6 class="mb-1 text-primary">{{ $ipMenuUtama->nama_ip_mu }}</h6>
                <small class="text-muted">Menu Utama</small>
              </div>
            </div>
          </div>

          @foreach($ipMenuUtama->IpSubMenuUtama as $index => $subMenuUtama)
          <!-- Sub Menu Utama -->
          <div class="menu-level-1 ml-4 mb-3">
            <div class="d-flex align-items-center p-3 border rounded bg-light">
              <i class="fas fa-folder fa-2x text-success mr-3"></i>
              <div class="flex-grow-1">
                <h6 class="mb-1 text-success">{{ $subMenuUtama->nama_ip_smu }}</h6>
                <small class="text-muted">Sub Menu Utama {{ $index + 1 }}</small>
                @if($subMenuUtama->dokumen_ip_smu)
                  <div class="mt-2">
                    <a href="{{ Storage::url($subMenuUtama->dokumen_ip_smu) }}" target="_blank" class="btn btn-sm btn-primary">
                      <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                    </a>
                  </div>
                @endif
              </div>
              <div class="text-right">
                @if($subMenuUtama->IpSubMenu->count() > 0)
                  <span class="badge badge-info">{{ $subMenuUtama->IpSubMenu->count() }} Sub Menu</span>
                @else
                  <span class="badge badge-success">Memiliki Dokumen</span>
                @endif
              </div>
            </div>

            @if($subMenuUtama->IpSubMenu->count() > 0)
            <!-- Sub Menu -->
            <div class="sub-menu-container mt-2">
              @foreach($subMenuUtama->IpSubMenu as $subIndex => $subMenu)
              <div class="menu-level-2 ml-4 mb-2">
                <div class="d-flex align-items-center p-2 border rounded bg-white">
                  <i class="fas fa-file fa-lg text-warning mr-3"></i>
                  <div class="flex-grow-1">
                    <span class="text-warning font-weight-medium">{{ $subMenu->nama_ip_sm }}</span>
                    <small class="d-block text-muted">Sub Menu {{ $index + 1 }}.{{ $subIndex + 1 }}</small>
                  </div>
                  <div>
                    @if($subMenu->dokumen_ip_sm)
                      <a href="{{ Storage::url($subMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-file-pdf"></i>
                      </a>
                    @else
                      <span class="badge badge-warning">Tidak ada dokumen</span>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
            @endif
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    <!-- Statistik -->
    <div class="card mt-3">
      <div class="card-header bg-info text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-chart-bar mr-2"></i>Statistik Menu
        </h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="text-center">
              <h3 class="text-primary">{{ $ipMenuUtama->IpSubMenuUtama->count() }}</h3>
              <small class="text-muted">Sub Menu Utama</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="text-center">
              @php
                $totalSubMenu = 0;
                foreach($ipMenuUtama->IpSubMenuUtama as $smu) {
                  $totalSubMenu += $smu->IpSubMenu->count();
                }
              @endphp
              <h3 class="text-success">{{ $totalSubMenu }}</h3>
              <small class="text-muted">Sub Menu</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="text-center">
              @php
                $totalDokumen = 0;
                if($ipMenuUtama->dokumen_ip_mu) $totalDokumen++;
                foreach($ipMenuUtama->IpSubMenuUtama as $smu) {
                  if($smu->dokumen_ip_smu) $totalDokumen++;
                  foreach($smu->IpSubMenu as $sm) {
                    if($sm->dokumen_ip_sm) $totalDokumen++;
                  }
                }
              @endphp
              <h3 class="text-warning">{{ $totalDokumen }}</h3>
              <small class="text-muted">Total Dokumen</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<style>
  .hierarchy-structure {
    position: relative;
  }
  
  .menu-level-1::before {
    content: '';
    position: absolute;
    left: 2rem;
    top: -1rem;
    bottom: 50%;
    width: 2px;
    background: #28a745;
  }
  
  .menu-level-2::before {
    content: '';
    position: absolute;
    left: 6rem;
    top: -0.5rem;
    bottom: 50%;
    width: 2px;
    background: #ffc107;
  }
  
  .menu-level-1::after {
    content: '';
    position: absolute;
    left: 2rem;
    top: 50%;
    width: 1rem;
    height: 2px;
    background: #28a745;
  }
  
  .menu-level-2::after {
    content: '';
    position: absolute;
    left: 6rem;
    top: 50%;
    width: 1rem;
    height: 2px;
    background: #ffc107;
  }
  
  .font-weight-medium {
    font-weight: 500;
  }
</style>