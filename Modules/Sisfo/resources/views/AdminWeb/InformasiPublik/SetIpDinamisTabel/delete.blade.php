@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');

  // Detect type: menu, submenu_utama, or submenu
  $type = 'menu';
  $title = 'Konfirmasi Hapus Set Informasi Publik Dinamis Tabel';
  $deleteUrl = '';
  $itemName = '';
  $canDelete = true;
  
  if (isset($ipSubMenu)) {
      $type = 'submenu';
      $title = 'Konfirmasi Hapus Sub Menu';
      $deleteUrl = url($setIpDinamisTabelUrl . "/deleteData/" . $ipSubMenu->ip_sub_menu_id . "?type=submenu");
      $itemName = $ipSubMenu->nama_ip_sm;
      $canDelete = true;
  } elseif (isset($ipSubMenuUtama)) {
      $type = 'submenu_utama';
      $title = 'Konfirmasi Hapus Sub Menu Utama';
      $deleteUrl = url($setIpDinamisTabelUrl . "/deleteData/" . $ipSubMenuUtama->ip_sub_menu_utama_id . "?type=submenu_utama");
      $itemName = $ipSubMenuUtama->nama_ip_smu;
      $canDelete = $ipSubMenuUtama->IpSubMenu->count() == 0;
  } else {
      $deleteUrl = url($setIpDinamisTabelUrl . "/deleteData/" . $ipMenuUtama->ip_menu_utama_id);
      $itemName = $ipMenuUtama->nama_ip_mu;
      
      // Untuk menu utama, hitung total
      $totalSubMenu = 0;
      if($ipMenuUtama->IpSubMenuUtama) {
        foreach($ipMenuUtama->IpSubMenuUtama as $smu) {
          if($smu->IpSubMenu) {
            $totalSubMenu += $smu->IpSubMenu->count();
          }
        }
      }
      
      $totalDokumen = 0;
      if($ipMenuUtama->dokumen_ip_mu) $totalDokumen++;
      if($ipMenuUtama->IpSubMenuUtama) {
        foreach($ipMenuUtama->IpSubMenuUtama as $smu) {
          if($smu->dokumen_ip_smu) $totalDokumen++;
          if($smu->IpSubMenu) {
            foreach($smu->IpSubMenu as $sm) {
              if($sm->dokumen_ip_sm) $totalDokumen++;
            }
          }
        }
      }
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
    {{-- SUB MENU DELETE --}}
    <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
    </div>

    <div class="card border-danger">
      <div class="card-header bg-danger text-white">
        <h6 class="mb-0">Detail Sub Menu yang akan dihapus</h6>
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
            <strong>Sub Menu:</strong>
            <p class="text-muted">{{ $ipSubMenu->nama_ip_sm }}</p>
          </div>
          <div class="col-md-6">
            <strong>Status Dokumen:</strong>
            <p class="text-muted">
              @if($ipSubMenu->dokumen_ip_sm)
                <span class="badge badge-success">Memiliki Dokumen</span>
              @else
                <span class="badge badge-warning">Tidak ada dokumen</span>
              @endif
            </p>
          </div>
        </div>

        <p class="text-center mt-3">
          Apakah Anda yakin ingin menghapus Sub Menu ini?
        </p>
      </div>
    </div>

  @elseif($type == 'submenu_utama')
    {{-- SUB MENU UTAMA DELETE --}}
    <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
    </div>

    <div class="card border-danger">
      <div class="card-header bg-danger text-white">
        <h6 class="mb-0">Detail Sub Menu Utama yang akan dihapus</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <strong>Menu Utama:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
            <strong>Sub Menu Utama:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->nama_ip_smu }}</p>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <strong>Jumlah Sub Menu:</strong>
            <p class="text-muted">{{ $ipSubMenuUtama->IpSubMenu->count() }} Sub Menu</p>
          </div>
          <div class="col-md-6">
            <strong>Status Dokumen:</strong>
            <p class="text-muted">
              @if($ipSubMenuUtama->dokumen_ip_smu)
                <span class="badge badge-success">Memiliki Dokumen</span>
              @else
                <span class="badge badge-warning">Tidak ada dokumen</span>
              @endif
            </p>
          </div>
        </div>

        @if($ipSubMenuUtama->IpSubMenu->count() > 0)
          <div class="alert alert-danger mt-3">
            <i class="fas fa-times-circle mr-2"></i>
            <strong>Sub Menu Utama ini tidak dapat dihapus</strong> karena masih memiliki {{ $ipSubMenuUtama->IpSubMenu->count() }} Sub Menu.
            <br><small>Silakan hapus semua Sub Menu terlebih dahulu.</small>
          </div>
        @else
          <p class="text-center mt-3">
            Apakah Anda yakin ingin menghapus Sub Menu Utama ini?
          </p>
        @endif
      </div>
    </div>

  @else
    {{-- MENU UTAMA DELETE --}}
  <div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle mr-2"></i> 
    <strong>Peringatan!</strong> Menghapus Set Informasi Publik Dinamis Tabel ini akan menghapus:
    <ul class="mb-0 mt-2">
      <li>Menu Utama: <strong>{{ $ipMenuUtama->nama_ip_mu }}</strong></li>
      @if($ipMenuUtama->IpSubMenuUtama && $ipMenuUtama->IpSubMenuUtama->count() > 0)
        <li>{{ $ipMenuUtama->IpSubMenuUtama->count() }} Sub Menu Utama beserta seluruh isinya</li>
        @if($totalSubMenu > 0)
          <li>{{ $totalSubMenu }} Sub Menu</li>
        @endif
      @endif
      @if($totalDokumen > 0)
        <li>{{ $totalDokumen }} Dokumen PDF yang terkait</li>
      @endif
    </ul>
    <div class="mt-2">
      <strong>Aksi ini tidak dapat dibatalkan!</strong>
    </div>
  </div>
  
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="card-title mb-0">
        <i class="fas fa-info-circle mr-2"></i>Detail yang akan dihapus
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

  @if($ipMenuUtama->IpSubMenuUtama && $ipMenuUtama->IpSubMenuUtama->count() > 0)
  <div class="card mt-3">
    <div class="card-header bg-warning text-dark">
      <h5 class="card-title mb-0">
        <i class="fas fa-sitemap mr-2"></i>Struktur Menu yang akan dihapus
      </h5>
    </div>
    <div class="card-body">
      <div class="hierarchy-preview">
        <!-- Menu Utama -->
        <div class="d-flex align-items-center p-2 mb-2 border rounded bg-light">
          <i class="fas fa-folder-open text-primary mr-2"></i>
          <span class="font-weight-bold text-primary">{{ $ipMenuUtama->nama_ip_mu }}</span>
          @if($ipMenuUtama->dokumen_ip_mu)
            <i class="fas fa-file-pdf text-danger ml-2" title="Memiliki dokumen"></i>
          @endif
        </div>

        @foreach($ipMenuUtama->IpSubMenuUtama as $index => $subMenuUtama)
        <!-- Sub Menu Utama -->
        <div class="ml-3">
          <div class="d-flex align-items-center p-2 mb-2 border rounded bg-light">
            <i class="fas fa-folder text-success mr-2"></i>
            <span class="font-weight-medium text-success">{{ $subMenuUtama->nama_ip_smu }}</span>
            @if($subMenuUtama->dokumen_ip_smu)
              <i class="fas fa-file-pdf text-danger ml-2" title="Memiliki dokumen"></i>
            @endif
            @if($subMenuUtama->IpSubMenu && $subMenuUtama->IpSubMenu->count() > 0)
              <span class="badge badge-info ml-2">{{ $subMenuUtama->IpSubMenu->count() }} sub menu</span>
            @endif
          </div>

          @if($subMenuUtama->IpSubMenu && $subMenuUtama->IpSubMenu->count() > 0)
          <!-- Sub Menu -->
          @foreach($subMenuUtama->IpSubMenu as $subMenu)
          <div class="ml-3 mb-1">
            <div class="d-flex align-items-center p-2 border rounded bg-white">
              <i class="fas fa-file text-warning mr-2"></i>
              <span class="text-warning">{{ $subMenu->nama_ip_sm }}</span>
              @if($subMenu->dokumen_ip_sm)
                <i class="fas fa-file-pdf text-danger ml-2" title="Memiliki dokumen"></i>
              @endif
            </div>
          </div>
          @endforeach
          @endif
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @else
  <!-- Tampilkan pesan jika tidak ada sub menu -->
  <div class="card mt-3">
    <div class="card-header bg-info text-white">
      <h5 class="card-title mb-0">
        <i class="fas fa-info-circle mr-2"></i>Informasi Menu
      </h5>
    </div>
    <div class="card-body">
      <div class="text-center py-3">
        <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
        <h5 class="text-info">Menu Utama Tanpa Sub Menu</h5>
        <p class="text-muted">
          Menu utama ini tidak memiliki sub menu dan hanya berisi dokumen langsung.
        </p>
        @if($ipMenuUtama->dokumen_ip_mu)
          <div class="mt-3">
            <span class="badge badge-success">
              <i class="fas fa-file-pdf mr-1"></i>
              Memiliki Dokumen PDF
            </span>
          </div>
        @endif
      </div>
    </div>
  </div>
  @endif

  <!-- Statistik yang akan dihapus -->
  <div class="card mt-3 border-danger">
    <div class="card-header bg-danger text-white">
      <h5 class="card-title mb-0">
        <i class="fas fa-trash mr-2"></i>Ringkasan Penghapusan
      </h5>
    </div>
    <div class="card-body">
      <div class="row text-center">
        <div class="col-md-3">
          <div class="border-right">
            <h4 class="text-primary mb-0">1</h4>
            <small class="text-muted">Menu Utama</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="border-right">
            <h4 class="text-success mb-0">{{ $ipMenuUtama->IpSubMenuUtama ? $ipMenuUtama->IpSubMenuUtama->count() : 0 }}</h4>
            <small class="text-muted">Sub Menu Utama</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="border-right">
            <h4 class="text-warning mb-0">{{ $totalSubMenu }}</h4>
            <small class="text-muted">Sub Menu</small>
          </div>
        </div>
        <div class="col-md-3">
          <h4 class="text-danger mb-0">{{ $totalDokumen }}</h4>
          <small class="text-muted">Dokumen PDF</small>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Catatan:</strong> Pastikan Anda telah membackup dokumen-dokumen penting sebelum melanjutkan penghapusan.
  </div>
  @endif
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  @if($canDelete)
    @if($type == 'menu')
      <button type="button" class="btn btn-danger" id="confirmDeleteButton" onclick="confirmDelete('{{ $deleteUrl }}')">
        <i class="fas fa-trash mr-1"></i> Ya, Hapus Semua
      </button>
    @else
      <button type="button" class="btn btn-danger" id="confirmDeleteButton" onclick="confirmDelete('{{ $deleteUrl }}')">
        <i class="fas fa-trash mr-1"></i> Ya, Hapus {{ $type == 'submenu' ? 'Sub Menu' : 'Sub Menu Utama' }}
      </button>
    @endif
  @endif
</div>

<script>
  function confirmDelete(url) {
    @if($type == 'menu')
      Swal.fire({
        title: 'Konfirmasi Final',
        text: 'Apakah Anda benar-benar yakin ingin menghapus Set Informasi Publik Dinamis Tabel ini beserta seluruh isinya?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          executeDelete(url);
        }
      });
    @else
      executeDelete(url);
    @endif
  }
  
  function executeDelete(url) {
    $.ajax({
      url: url,
      type: 'DELETE',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response.success) {
          $('#myModal').modal('hide');
          reloadTable();
          
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message || 'Data berhasil dihapus'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: response.message || 'Terjadi kesalahan saat menghapus data'
          });
        }
      },
      error: function(xhr) {
        let errorMessage = 'Terjadi kesalahan saat menghapus data';
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: errorMessage
        });
      }
    });
  }
</script>

<style>
  .hierarchy-preview {
    max-height: 300px;
    overflow-y: auto;
  }
  
  .border-right:last-child {
    border-right: none !important;
  }
</style>