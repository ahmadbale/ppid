{{-- berita.blade --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Berita PPID</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        @php
        $item = $beritaMenus[0] ?? null;
        @endphp
        <div class="container">
            <header>
                <h1 class="display-4 fw-bold">{{ $item['kategori'] ?? 'Berita' }}</h1>
            </header>
        </div>
    </section>

    <section class="container mt-4 py-5">
        <div id="berita-container" class="news-list">
            @include('user::partials.berita-list', ['beritaMenus' => $beritaMenus])
        </div>
    </section>

    @if ($pagination)
    <div id="pagination-container" class="Pagination mb-4">
        <ul class="pagination pagination-rounded justify-content-center gap-2">
            {{-- Previous --}}
            <li class="page-item {{ !$pagination['prev_page_url'] ? 'disabled' : '' }}">
                <a class="page-link page-nav" 
                   href="javascript:void(0);" 
                   data-page="{{ $pagination['current_page'] - 1 }}"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo; Prev</span>
                </a>
            </li>
            
            {{-- Numbered Pages --}}
            @php
                $totalPages = $pagination['total_pages'];
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
                    <a class="page-link page-nav" href="javascript:void(0);" data-page="1">1</a>
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
                        <a class="page-link page-nav" href="javascript:void(0);" data-page="{{ $i }}">{{ $i }}</a>
                    @endif
                </li>
            @endfor
            
            {{-- Last page --}}
            @if($endPage < $totalPages)
                @if($endPage < $totalPages - 1)
                    <li class="page-item d-inline d-md-none">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link page-nav" href="javascript:void(0);" data-page="{{ $totalPages }}">{{ $totalPages }}</a>
                </li>
            @endif
                
            {{-- Next --}}
            <li class="page-item {{ !$pagination['next_page_url'] ? 'disabled' : '' }}">
                <a class="page-link page-nav" 
                   href="javascript:void(0);" 
                   data-page="{{ $pagination['current_page'] + 1 }}"
                   aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                </a>
            </li>
        </ul>
    </div>
    @endif

    @include('user::layouts.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle pagination clicks
            document.querySelectorAll('.page-nav').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Don't do anything if the link is disabled
                    if (this.parentElement.classList.contains('disabled')) {
                        return;
                    }
                    
                    const page = this.getAttribute('data-page');
                    fetchBerita(page);
                });
            });
            
            function fetchBerita(page) {
                // Show loading indicator (optional)
                document.getElementById('berita-container').innerHTML = '<p class="text-center">Loading...</p>';
                
                // Create URL with query parameters
                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                
                // Fetch data with AJAX
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update the berita list
                    document.getElementById('berita-container').innerHTML = data.html;
                    
                    // Update pagination
                    updatePagination(data.pagination);
                    
                    // Update URL without reloading (optional)
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    document.getElementById('berita-container').innerHTML = 
                        '<p class="text-center">Terjadi kesalahan saat mengambil data.</p>';
                });
            }
            
            function updatePagination(pagination) {
    if (!pagination) {
        document.getElementById('pagination-container').style.display = 'none';
        return;
    }
    
    const paginationContainer = document.getElementById('pagination-container');
    paginationContainer.style.display = 'block';
    
    const totalPages = pagination.total_pages;
    const currentPage = pagination.current_page;
    
    // Determine the range of pages to show
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    // Adjust start page if we're near the end
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }
    
    let paginationHTML = `
        <ul class="pagination pagination-rounded justify-content-center gap-2">
            <li class="page-item ${!pagination.prev_page_url ? 'disabled' : ''}">
                <a class="page-link page-nav" 
                   href="javascript:void(0);" 
                   data-page="${currentPage - 1}"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo; Prev</span>
                </a>
            </li>
    `;
    
    // First page with ellipsis if needed
    if (startPage > 1) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link page-nav" href="javascript:void(0);" data-page="1">1</a>
            </li>
        `;
        if (startPage > 2) {
            paginationHTML += `
                <li class="page-item d-none d-md-inline">
                    <span class="page-link">...</span>
                </li>
            `;
        }
    }
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            paginationHTML += `
                <li class="page-item active">
                    <span class="page-link">${i}</span>
                </li>
            `;
        } else {
            const hiddenClass = (i !== startPage && i !== endPage && i !== currentPage) ? 'd-none d-md-inline' : '';
            paginationHTML += `
                <li class="page-item ${hiddenClass}">
                    <a class="page-link page-nav" href="javascript:void(0);" data-page="${i}">${i}</a>
                </li>
            `;
        }
    }
    
    // Last page with ellipsis if needed
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `
                <li class="page-item d-inline d-md-none">
                    <span class="page-link">...</span>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item">
                <a class="page-link page-nav" href="javascript:void(0);" data-page="${totalPages}">${totalPages}</a>
            </li>
        `;
    }
    
    // Next button
    paginationHTML += `
            <li class="page-item ${!pagination.next_page_url ? 'disabled' : ''}">
                <a class="page-link page-nav" 
                   href="javascript:void(0);" 
                   data-page="${currentPage + 1}"
                   aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                </a>
            </li>
        </ul>
    `;
    
    paginationContainer.innerHTML = paginationHTML;
    
    // Re-attach event listeners to the new pagination links
    document.querySelectorAll('.page-nav').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.parentElement.classList.contains('disabled')) {
                return;
            }
            
            const page = this.getAttribute('data-page');
            fetchBerita(page);
        });
    });
}
        });
    </script>
</body>
</html>