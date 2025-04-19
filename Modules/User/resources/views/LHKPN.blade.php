{{-- LHKPN.blade --}}
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
                        @php
                            $latestUpdateTime = collect($lhkpnItems)
                                ->filter(function($item) { 
                                    return !empty($item['updated_at']); 
                                })
                                ->max('updated_at');
                        @endphp
            
                        @if ($latestUpdateTime)
                            {{ $latestUpdateTime }}
                        @else
                            Belum ada pembaruan
                        @endif
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
            <div class="mt-4 border-top border-3 pt-3 w-70 mx-auto"></div>

            @if (!empty($tahunList))
                <div class="d-flex align-items-center gap-2 mt-3 mb-4">
                    <strong>Pilih tahun:</strong>
                    @foreach ($tahunList as $tahun)
                        <a href="javascript:void(0);" 
                           onclick="selectYear('{{ $tahun }}')"
                           class="btn tahun-btn {{ $tahun == $tahunDipilih ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3"
                           data-tahun="{{ $tahun }}">
                            {{ $tahun }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div id="lhkpn-content">
               @include('user::partials.lhkpn-list')
            </div>

            @if (!empty($pagination) && $pagination['last_page'] > 1 && $tahunDipilih)
<div id="pagination-container" class="Pagination mt-4 mb-4">
    <ul class="pagination pagination-rounded justify-content-center gap-2">
        {{-- Previous --}}
        <li class="page-item {{ $pagination['current_page'] <= 1 ? 'disabled' : '' }}">
            <a class="page-link page-nav" 
               href="javascript:void(0);" 
               @click="changePage({{ $pagination['current_page'] - 1 }}, {{ $pagination['current_page'] > 1 ? 'true' : 'false' }})"
               aria-label="Previous">
                <span aria-hidden="true">&laquo; Prev</span>
            </a>
        </li>
        
        {{-- Numbered Pages --}}
        @php
            $totalPages = $pagination['last_page'];
            $currentPage = $pagination['current_page'];
            
            // Determine the range of pages to show
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $startPage + 4);
            
            // Adjust start page if we're near the end
            if ($endPage - $startPage < 4) {
                $startPage = max(1, $endPage - 4);
            }
        @endphp
        
        {{-- First page --}}
        @if($startPage > 1)
            <li class="page-item">
                <a class="page-link page-nav" href="javascript:void(0);" @click="changePage(1)">1</a>
            </li>
            @if($startPage > 2)
                <li class="page-item d-none d-md-inline">
                    <span class="page-link">...</span>
                </li>
            @endif
        @endif
        
        {{-- Page numbers --}}
        @for($i = $startPage; $i <= $endPage; $i++)
            <li class="page-item {{ $i == $currentPage ? 'active' : '' }} {{ ($i != $startPage && $i != $endPage && $i != $currentPage) ? 'd-none d-md-inline' : '' }}">
                @if($i == $currentPage)
                    <span class="page-link">{{ $i }}</span>
                @else
                    <a class="page-link page-nav" href="javascript:void(0);" @click="changePage({{ $i }})">{{ $i }}</a>
                @endif
            </li>
        @endfor
        
        {{-- Last page --}}
        @if($endPage < $totalPages)
            @if($endPage < $totalPages - 1)
                <li class="page-item d-none d-md-inline">
                    <span class="page-link">...</span>
                </li>
            @endif
            <li class="page-item">
                <a class="page-link page-nav" href="javascript:void(0);" @click="changePage({{ $totalPages }})">{{ $totalPages }}</a>
            </li>
        @endif
            
        {{-- Next --}}
        <li class="page-item {{ $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' }}">
            <a class="page-link page-nav" 
               href="javascript:void(0);" 
               @click="changePage({{ $pagination['current_page'] + 1 }}, {{ $pagination['current_page'] < $pagination['last_page'] ? 'true' : 'false' }})"
               aria-label="Next">
                <span aria-hidden="true">Next &raquo;</span>
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
                changePage(pageNumber, isEnabled = true) {
                    if (!isEnabled) {
                        return;
                    }
                    
                    const baseUrl = "{{ route('lhkpn') }}";
                    const tahunParam = "{{ $tahunDipilih }}";
                    
                    // Get current detail_page parameters from URL if any
                    const currentUrl = new URL(window.location);
                    const detailPages = {};
                    
                    // Extract all detail_page parameters
                    for (const [key, value] of currentUrl.searchParams.entries()) {
                        if (key.startsWith('detail_page[')) {
                            const tahun = key.match(/detail_page\[(.*?)\]/)[1];
                            detailPages[tahun] = value;
                        }
                    }
                    
                    // Create URL with both page and tahun parameters
                    let fullUrl = `${baseUrl}?page=${pageNumber}${tahunParam ? '&tahun=' + tahunParam : ''}`;
                    
                    // Add detail_page parameters if any
                    for (const [tahun, page] of Object.entries(detailPages)) {
                        fullUrl += `&detail_page[${tahun}]=${page}`;
                    }
                    
                    // Show loading state
                    document.getElementById('lhkpn-content').innerHTML = 
                        '<div class="text-center py-4"><i class="bi bi-spinner fa-spin"></i> Memuat data...</div>';
                    
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
                        newUrl.searchParams.set('page', pageNumber);
                        if (tahunParam) {
                            newUrl.searchParams.set('tahun', tahunParam);
                        }
                        
                        // Preserve detail_page parameters
                        for (const [tahun, page] of Object.entries(detailPages)) {
                            newUrl.searchParams.set(`detail_page[${tahun}]`, page);
                        }
                        
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        document.getElementById('lhkpn-content').innerHTML = 
                            '<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>';
                    });
                }
            }
        }
        
        // Function to select a year without navigating to a new page
        function selectYear(tahun) {
            const baseUrl = "{{ route('lhkpn') }}";
            
            // Update button states immediately
            document.querySelectorAll('.tahun-btn').forEach(btn => {
                if (btn.getAttribute('data-tahun') === tahun) {
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-primary');
                } else {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                }
            });
            
            // Show loading state
            document.getElementById('lhkpn-content').innerHTML = 
                '<div class="text-center py-4"><i class="bi bi-spinner fa-spin"></i> Memuat data tahun ' + tahun + '...</div>';
            
            // Create URL with the new tahun parameter
            let fullUrl = `${baseUrl}?tahun=${tahun}`;
            
            // Get current URL parameters
            const currentUrl = new URL(window.location);
            const currentPage = currentUrl.searchParams.get('page') || 1;
            
            // Add page parameter if exists
            fullUrl += `&page=${currentPage}`;
            
            // Make ajax request
            fetch(fullUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('lhkpn-content').innerHTML = data.html;
                
                // Update pagination container if it exists
                if (data.pagination_html) {
                    document.getElementById('pagination-container').innerHTML = data.pagination_html;
                }
                
                // Update URL without page reload
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('tahun', tahun);
                newUrl.searchParams.set('page', currentPage);
                
                // Reset any detail_page parameters since we're loading a new year
                for (const [key, value] of [...newUrl.searchParams.entries()]) {
                    if (key.startsWith('detail_page[')) {
                        newUrl.searchParams.delete(key);
                    }
                }
                
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('lhkpn-content').innerHTML = 
                    '<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>';
            });
        }
        
        // Global function for loading detail pages
        function loadDetailPage(tahun, page) {
            const baseUrl = "{{ route('lhkpn') }}";
            // Get current page from URL
            const currentUrl = new URL(window.location);
            const currentPage = currentUrl.searchParams.get('page') || 1;
            const tahunParam = currentUrl.searchParams.get('tahun') || "{{ $tahunDipilih }}";
            
            // Get current URL and extract existing detail_page parameters
            const detailPages = {};
            
            // Extract all detail_page parameters
            for (const [key, value] of currentUrl.searchParams.entries()) {
                if (key.startsWith('detail_page[')) {
                    const yearKey = key.match(/detail_page\[(.*?)\]/)[1];
                    detailPages[yearKey] = value;
                }
            }
            
            // Update the detail_page for the current tahun
            detailPages[tahun] = page;
            
            // Create URL with main pagination parameters
            let fullUrl = `${baseUrl}?page=${currentPage}&tahun=${tahunParam}`;
            
            // Add all detail_page parameters
            for (const [year, pageNum] of Object.entries(detailPages)) {
                fullUrl += `&detail_page[${year}]=${pageNum}`;
            }
            
            // Show loading state
            document.getElementById('lhkpn-content').innerHTML = 
                '<div class="text-center py-4"><i class="bi bi-spinner fa-spin"></i> Memuat data...</div>';
            
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
                newUrl.searchParams.set('page', currentPage);
                newUrl.searchParams.set('tahun', tahunParam);
                
                // Update all detail_page parameters
                for (const [year, pageNum] of Object.entries(detailPages)) {
                    newUrl.searchParams.set(`detail_page[${year}]`, pageNum);
                }
                
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('lhkpn-content').innerHTML = 
                    '<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>';
            });
        }
    </script>
</body>

</html>