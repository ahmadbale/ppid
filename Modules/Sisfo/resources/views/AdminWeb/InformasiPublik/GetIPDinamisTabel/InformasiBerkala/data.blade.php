@if(isset($kategori) && $kategori && $menuUtamaList->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white">
                <tr>
                    <th width="50">No.</th>
                    <th>Nama Informasi Berkala</th>
                    <th width="150">Tanggal Informasi</th>
                    <th width="120">Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                
                @foreach($menuUtamaList as $menuUtama)
                    <!-- Menu Utama - Tampilkan semua, baik yang ada dokumen maupun tidak -->
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            <strong>{{ $menuUtama->nama_ip_mu }}</strong>
                        </td>
                        <td class="text-center">
                            @if($menuUtama->dokumen_ip_mu)
                                {{ date('d M Y', strtotime($menuUtama->updated_at ?: $menuUtama->created_at)) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($menuUtama->dokumen_ip_mu)
                                <button type="button" 
                                        class="btn btn-sm btn-success view-document-berkala" 
                                        data-type="menu-utama" 
                                        data-id="{{ $menuUtama->ip_menu_utama_id }}"
                                        data-title="{{ $menuUtama->nama_ip_mu }}">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </button>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times mr-1"></i> Tidak Ada
                                </span>
                            @endif
                        </td>
                    </tr>

                    @foreach($menuUtama->IpSubMenuUtama as $subMenuUtama)
                        <!-- Sub Menu Utama - Tampilkan semua, baik yang ada dokumen maupun tidak -->
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>
                                <div class="ml-3">
                                    <i class="fas fa-angle-right text-muted mr-2"></i>
                                    {{ $subMenuUtama->nama_ip_smu }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($subMenuUtama->dokumen_ip_smu)
                                    {{ date('d M Y', strtotime($subMenuUtama->updated_at ?: $subMenuUtama->created_at)) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subMenuUtama->dokumen_ip_smu)
                                    <button type="button" 
                                            class="btn btn-sm btn-success view-document-berkala" 
                                            data-type="sub-menu-utama" 
                                            data-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}"
                                            data-title="{{ $subMenuUtama->nama_ip_smu }}">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </button>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times mr-1"></i> Tidak Ada
                                    </span>
                                @endif
                            </td>
                        </tr>

                        @foreach($subMenuUtama->IpSubMenu as $subMenu)
                            <!-- Sub Menu - Tampilkan semua, baik yang ada dokumen maupun tidak -->
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>
                                    <div class="ml-5">
                                        <i class="fas fa-angle-double-right text-muted mr-2"></i>
                                        {{ $subMenu->nama_ip_sm }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($subMenu->dokumen_ip_sm)
                                        {{ date('d M Y', strtotime($subMenu->updated_at ?: $subMenu->created_at)) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($subMenu->dokumen_ip_sm)
                                        <button type="button" 
                                                class="btn btn-sm btn-success view-document-berkala" 
                                                data-type="sub-menu" 
                                                data-id="{{ $subMenu->ip_sub_menu_id }}"
                                                data-title="{{ $subMenu->nama_ip_sm }}">
                                            <i class="fas fa-eye mr-1"></i> Lihat
                                        </button>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times mr-1"></i> Tidak Ada
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

@else
    <div class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Tidak Ada Data</h4>
            <p class="text-muted">
                @if(!empty($search))
                    Tidak ditemukan informasi berkala yang sesuai dengan pencarian "{{ $search }}"
                @else
                    Belum ada informasi berkala yang tersedia
                @endif
            </p>
            @if(!empty($search))
                <button onclick="clearSearch()" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Semua Data
                </button>
            @endif
        </div>
    </div>
@endif
