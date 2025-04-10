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
                                        <span class="me-2">{{ $loop->parent->index * 5 + $loop->iteration }}.</span>
                                        <span class="lhkpn-text">{{ $detail['nama_karyawan'] }}</span>
                                    </div>
                                    <a href="{{ $detail['file'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye-fill me-1"></i> Lihat
                                    </a>
                                </li>
                            @endforeach
                            
                            @if ($item['has_more'])
                                <li class="text-center py-3 text-muted">
                                    <span>
                                        Menampilkan {{ count($item['details']) }} dari {{ $item['total_karyawan'] }} data
                                    </span>
                                </li>
                            @endif
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
@else
    <div class="alert alert-info mt-3">
        <p class="mb-0">Silakan pilih tahun terlebih dahulu untuk melihat data LHKPN.</p>
    </div>
@endif