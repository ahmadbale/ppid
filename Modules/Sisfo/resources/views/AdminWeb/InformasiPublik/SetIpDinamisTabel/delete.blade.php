<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\delete.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Set Informasi Publik Dinamis Tabel</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">    
  <div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle mr-2"></i> 
    <strong>Peringatan!</strong> Menghapus Set Informasi Publik Dinamis Tabel ini akan menghapus:
    <ul class="mb-0 mt-2">
      <li>Menu Utama: <strong>{{ $ipMenuUtama->nama_ip_mu }}</strong></li>
      @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
        <li>{{ $ipMenuUtama->IpSubMenuUtama->count() }} Sub Menu Utama beserta seluruh isinya</li>
        @php
          $totalSubMenu = 0;
          foreach($ipMenuUtama->IpSubMenuUtama as $smu) {
            $totalSubMenu += $smu->IpSubMenu->count();
          }
        @endphp
        @if($totalSubMenu > 0)
          <li>{{ $totalSubMenu }} Sub Menu</li>
        @endif
      @endif
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

  @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
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
            @if($subMenuUtama->IpSubMenu->count() > 0)
              <span class="badge badge-info ml-2">{{ $subMenuUtama->IpSubMenu->count() }} sub menu</span>
            @endif
          </div>

          @if($subMenuUtama->IpSubMenu->count() > 0)
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
            <h4 class="text-success mb-0">{{ $ipMenuUtama->IpSubMenuUtama->count() }}</h4>
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
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
    onclick="confirmDelete('{{ url($setIpDinamisTabelUrl . '/deleteData/' . $ipMenuUtama->ip_menu_utama_id) }}')">
    <i class="fas fa-trash mr-1"></i> Ya, Hapus Semua
  </button>
</div>

<script>
  function confirmDelete(url) {
    const button = $('#confirmDeleteButton');
    
    // Konfirmasi tambahan dengan SweetAlert
    Swal.fire({
      title: 'Konfirmasi Final',
      text: 'Apakah Anda benar-benar yakin ingin menghapus Set Informasi Publik Dinamis Tabel ini beserta seluruh isinya?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Proceed with deletion
        button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);
        
        $.ajax({
          url: url,
          type: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            $('#myModal').modal('hide');
            
            if (response.success) {
              reloadTable();
              
              Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus',
                text: response.message || 'Set Informasi Publik Dinamis Tabel berhasil dihapus',
                timer: 3000,
                showConfirmButton: false
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: response.message || 'Terjadi kesalahan saat menghapus data'
              });
            }
          },
          error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Gagal Menghapus',
              text: errorMessage
            });
            
            button.html('<i class="fas fa-trash mr-1"></i> Ya, Hapus Semua').prop('disabled', false);
          }
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