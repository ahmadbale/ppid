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
        <div class="lhkpn-section" x-data="lhkpnHandler()">
            <h2 class="fw-bold pb-2 text-center text-md-start">Laporan Harta Kekayaan Penyelenggara Negara</h2>
            <div class="mt-4 border-top border-1 pt-3 w-65 "></div>
            <div class="flex items-center text-gray-500 text-sm mt-2 mb-5">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <span class="ml-1">
                    Diperbarui pada
                    @if (!empty($lhkpnItems))
                        {{ \Carbon\Carbon::parse($lhkpnItems[0]['updated_at'] ?? null)->translatedFormat('d F Y, H:i') }}
                    @else
                        Belum ada pembaruan
                    @endif
                </span>
            </div>

            @if (!empty($lhkpnItems))
            <div class="mb-4">
                {!! $lhkpnItems[0]['deskripsi'] ?? '' !!}
            </div>
        @endif

            <h5 class="fw-bold text-start mt-5">Dokumen LHKPN</h5>
            <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>

            @if (!empty($tahunList))
                <div class="d-flex align-items-center gap-2 mt-3 mb-4">
                    <strong>Pilih tahun:</strong>
                    @foreach ($tahunList as $tahun)
                        <a href="{{ route('LHKPN', ['tahun' => $tahun]) }}"
                            class="btn {{ $tahun == $tahunDipilih ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                            {{ $tahun }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if (!$tahunDipilih)
                <p class="text-info">Silakan pilih tahun untuk melihat data.</p>
            @endif

            <div id="lhkpn-content">
                @include('user::partials.lhkpn-list')
            </div>

            @if (!empty($pagination) && $pagination['last_page'] > 1 && $tahunDipilih)
                <div class="pagination_rounded mt-4">
                    <ul>
                        <li>
                            <a href="javascript:void(0)" 
                               @click="changePage('{{ $pagination['prev_page_url'] }}')" 
                               class="prev" 
                               :class="{ 'disabled': '{{ $pagination['prev_page_url'] }}' === 'null' }">
                                <i class="fa fa-angle-left" aria-hidden="true"></i> Prev
                            </a>
                        </li>
                        
                        @php
                            $startPage = max(1, $pagination['current_page'] - 2);
                            $endPage = min($pagination['last_page'], $pagination['current_page'] + 2);
                        @endphp
                        
                        @if ($startPage > 1)
                            <li><a href="javascript:void(0)" @click="changePage('?page=1')">1</a></li>
                            @if ($startPage > 2)
                                <li class="visible-xs"><a href="javascript:void(0)">...</a></li>
                            @endif
                        @endif
                        
                        @for ($i = $startPage; $i <= $endPage; $i++)
                            <li class="{{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                <a href="javascript:void(0)" @click="changePage('?page={{ $i }}')">{{ $i }}</a>
                            </li>
                        @endfor
                        
                        @if ($endPage < $pagination['last_page'])
                            @if ($endPage < $pagination['last_page'] - 1)
                                <li class="visible-xs"><a href="javascript:void(0)">...</a></li>
                            @endif
                            <li>
                                <a href="javascript:void(0)" @click="changePage('?page={{ $pagination['last_page'] }}')">
                                    {{ $pagination['last_page'] }}
                                </a>
                            </li>
                        @endif
                        
                        <li>
                            <a href="javascript:void(0)" 
                               @click="changePage('{{ $pagination['next_page_url'] }}')" 
                               class="next"
                               :class="{ 'disabled': '{{ $pagination['next_page_url'] }}' === 'null' }">
                                Next <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @include('user::layouts.footer')

    <script>
        function lhkpnHandler() {
            return {
                changePage(url) {
                    if (!url || url === 'null') {
                        return;
                    }
                    
                    const baseUrl = "{{ route('LHKPN') }}";
                    const tahunParam = "{{ $tahunDipilih ? '&tahun=' . $tahunDipilih : '' }}";
                    
                    // Handle both full URLs and query strings
                    const fullUrl = url.startsWith('?') 
                        ? `${baseUrl}${url}${tahunParam}` 
                        : url + (tahunParam ? tahunParam : '');
                    
                    // Show loading state
                    document.getElementById('lhkpn-content').innerHTML = 
                        '<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div>';
                    
                    fetch(fullUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('lhkpn-content').innerHTML = data.html;
                        
                        // Update URL without page reload
                        const newUrl = new URL(window.location);
                        const pageMatch = url.match(/page=(\d+)/);
                        if (pageMatch && pageMatch[1]) {
                            newUrl.searchParams.set('page', pageMatch[1]);
                            window.history.pushState({}, '', newUrl);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        document.getElementById('lhkpn-content').innerHTML = 
                            '<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>';
                    });
                }
            }
        }
    </script>
</body>

</html>