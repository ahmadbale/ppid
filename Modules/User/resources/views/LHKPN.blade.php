<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LHKPN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <div class="container">

        <div class="lhkpn-section">
            <h2 class="fw-bold pb-2 text-center text-md-start">Laporan Harta Kekayaan Penyelenggara Negara</h2>
            <div class="mt-4 border-top border-1 pt-3 w-65 "></div>
            <div class="flex items-center text-gray-500 text-sm mt-2 mb-5">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <span class="ml-1">
                    Diperbarui pada
                    {{ $updated_at ? \Carbon\Carbon::parse($updated_at)->translatedFormat('d F Y, H:i') : 'Belum ada pembaruan' }}
                </span>
            </div>

            @if (!empty($lhkpnList))
                @foreach ($lhkpnList as $item)
                    <div class="mb-4">
                        @foreach (explode("\n", $item['deskripsi']) as $deskripsiItem)
                            @if (!empty(trim($deskripsiItem)))
                                <p>{{ $deskripsiItem }}</p>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            @endif

            <h5 class="fw-bold text-start mt-5">Dokumen LHKPN</h5>
            <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>


            {{-- @if (!empty($tahunList)) --}}
                <div class="d-flex align-items-center gap-2">
                    <strong>Pilih tahun:</strong>
                    @foreach ($tahunList as $tahun)
                        <a href="{{ route('LHKPN', ['tahun' => $tahun]) }}"
                            class="btn {{ $tahun == $tahunDipilih ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                            {{ $tahun }}
                        </a>
                    @endforeach
                </div>
            {{-- @endif --}}

            @if (!$tahunDipilih)
                <p class="text-info">Silakan pilih tahun untuk melihat data.</p>
            @endif

            @if ($tahunDipilih)
                <p class="text-gray-500"><i>Menampilkan data LHKPN tahun {{ $tahunDipilih }}</i></p>

                <ol class="lhkpn-list">
                    @if (!empty($lhkpnList))
                        @foreach ($lhkpnList as $item)
                            @if (!empty($item['details']))
                                @foreach ($item['details'] as $detail)
                                    <li>
                                        <span class="lhkpn-text">{{ $detail['nama_karyawan'] }}</span>
                                        <a href="{{ $detail['file'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    @else
                        <p class="text-warning">Tidak ada data untuk tahun {{ $tahunDipilih }}.</p>
                    @endif
                </ol>
            @endif

            <div class="pagination_rounded">
                <ul>
                    <li>
                        <a href="#" class="prev">
                            <i class="fa fa-angle-left" aria-hidden="true"></i> Prev
                        </a>
                    </li>
                    <li><a href="#">1</a></li>
                    <li class="hidden-xs"><a href="#">2</a></li>
                    <li class="hidden-xs"><a href="#">3</a></li>
                    <li class="hidden-xs"><a href="#">4</a></li>
                    <li class="hidden-xs"><a href="#">5</a></li>
                    <li class="visible-xs"><a href="#">...</a></li>
                    <li><a href="#">6</a></li>
                    <li>
                        <a href="#" class="next">
                            Next <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    @include('user::layouts.footer')
</body>

</html>
