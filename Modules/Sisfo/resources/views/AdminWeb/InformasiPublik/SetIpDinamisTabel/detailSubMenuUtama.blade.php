<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\detailSubMenuUtama.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-folder-open mr-2"></i>Detail Sub Menu Utama
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
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>