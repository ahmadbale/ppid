<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\detailSubMenu.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-text mr-2"></i>Detail Sub Menu
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

            <!-- Breadcrumb hierarki -->
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
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>