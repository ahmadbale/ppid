{{-- lhkpn-list.blade --}}
@if ($tahunDipilih)
    <p class="text-gray-500 mt-3 mb-3"><i>Menampilkan data LHKPN tahun {{ $tahunDipilih }}</i></p>

    <div class="card">
        <div class="card-body p-0">
            @if (!empty($lhkpnItems))
                <ol class="lhkpn-list m-0 p-0 list-unstyled">
                    @foreach ($lhkpnItems as $item)
                        @if ($item['tahun'] == $tahunDipilih && !empty($item['details']))
                            @foreach ($item['details'] as $detail)
                                <li class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">
                                            {{ (($item['detail_pagination']['current_page'] - 1) * $item['detail_pagination']['per_page']) + $loop->iteration }}.
                                        </span>
                                        <span class="lhkpn-text">{{ $detail['nama_karyawan'] }}</span>
                                    </div>
                                    <a href="{{ $detail['file'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye-fill me-1"></i> Lihat
                                    </a>
                                </li>
                            @endforeach
                            <div class="text-center mt-2 mb-2">
                                <small class="text-muted">
                                    Halaman {{ $item['detail_pagination']['current_page'] }} dari {{ $item['detail_pagination']['total_pages'] }}
                                </small>
                            </div>
                        @endif
                    @endforeach
                    
                    @if (count($lhkpnItems) == 0 || !array_filter($lhkpnItems, function($item) use ($tahunDipilih) { return $item['tahun'] == $tahunDipilih; }))
                        <li class="text-center py-4">
                            <span class="text-warning">Tidak ada data untuk tahun {{ $tahunDipilih }}.</span>
                        </li>
                    @endif
                </ol>
            @else
                <div class="text-center py-4">
                    <span class="text-warning">Tidak ada data untuk tahun {{ $tahunDipilih }}.</span>
                </div>
            @endif
        </div>
    </div>

    @foreach ($lhkpnItems as $item)
        @if ($item['tahun'] == $tahunDipilih && isset($item['detail_pagination']))
            <div class="d-flex justify-content-center mt-3">
                <div class="pagination-buttons">
                    @if ($item['detail_pagination']['current_page'] > 1)
                        <a href="javascript:void(0);" 
                           class="btn btn-sm btn-outline-secondary me-2"
                           onclick="loadDetailPage('{{ $item['tahun'] }}', {{ $item['detail_pagination']['current_page'] - 1 }})">
                            <i class="bi bi-chevron-left"></i> Sebelumnya
                        </a>
                    @endif
                    
                    @if ($item['detail_pagination']['current_page'] < $item['detail_pagination']['total_pages'])
                        <a href="javascript:void(0);" 
                           class="btn btn-sm btn-outline-primary"
                           onclick="loadDetailPage('{{ $item['tahun'] }}', {{ $item['detail_pagination']['current_page'] + 1 }})">
                            Selanjutnya <i class="bi bi-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            
        @endif
    @endforeach
@else
    <div class="alert alert-info mt-3">
        <p class="mb-0">Silakan pilih tahun terlebih dahulu untuk melihat data LHKPN.</p>
    </div>
@endif