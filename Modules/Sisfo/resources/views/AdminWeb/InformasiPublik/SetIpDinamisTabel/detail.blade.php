<div class="modal-header">
  <h5 class="modal-title">{{ $title }}</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="card-title mb-0">
        <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
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
          <td>{{ date('d-m-Y H:i:s', strtotime($ipMenuUtama->created_at)) }}</td>
        </tr>
        <tr>
          <th>Dibuat Oleh</th>
          <td>{{ $ipMenuUtama->created_by }}</td>
        </tr>
        @if($ipMenuUtama->updated_by)
        <tr>
          <th>Terakhir Diperbarui</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($ipMenuUtama->updated_at)) }}</td>
        </tr>
        <tr>
          <th>Diperbarui Oleh</th>
          <td>{{ $ipMenuUtama->updated_by }}</td>
        </tr>
        @endif
      </table>
    </div>
  </div>

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
</style>