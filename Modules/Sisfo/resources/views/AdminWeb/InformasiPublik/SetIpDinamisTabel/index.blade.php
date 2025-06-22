<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\SetIpDinamisTabel\index.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  use Illuminate\Support\Facades\Storage;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
@extends('sisfo::layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h3 class="card-title">{{ $page->title }}</h3>
        </div>
        <div class="col-md-6 text-right">
          @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $setIpDinamisTabelUrl, 'create'))
            <button class="btn btn-success" onclick="modalAction('{{ url($setIpDinamisTabelUrl . '/addData') }}')">
              <i class="fas fa-plus"></i> Tambah Set Informasi Publik
            </button>
          @endif
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4">
          <form id="searchForm" class="form-inline">
            <div class="input-group">
              <input type="text" class="form-control" name="search" value="{{ $search }}" 
                     placeholder="Cari nama menu atau kategori..." style="width: 250px;">
              <div class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary">
                  <i class="fas fa-search"></i>
                </button>
                @if(!empty($search))
                  <button type="button" class="btn btn-outline-danger" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                  </button>
                @endif
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-4">
          <div class="form-group mb-0">
            <select class="form-control" id="kategoriFilter" onchange="filterByKategori(this.value)">
              <option value="">Semua Kategori</option>
              @foreach($ipDinamisTabel as $kategoriItem)
                <option value="{{ $kategoriItem->ip_dinamis_tabel_id }}" 
                  {{ (string)$kategoriItem->ip_dinamis_tabel_id === (string)$kategori ? 'selected' : '' }}>
                  {{ $kategoriItem->ip_nama_submenu }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="btn-group">
            <button class="btn btn-outline-secondary" onclick="expandAll()">
              <i class="fas fa-expand"></i> Buka Semua
            </button>
            <button class="btn btn-outline-secondary" onclick="collapseAll()">
              <i class="fas fa-compress"></i> Tutup Semua
            </button>
            <button class="btn btn-outline-primary" onclick="reloadTable()">
              <i class="fas fa-sync"></i> Refresh
            </button>
          </div>
        </div>
      </div>

      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      <div id="menu-container">
        @include('sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.data')
      </div>
    </div>
  </div>

  <!-- Modal for CRUD operations -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <!-- Modal content will be loaded here -->
      </div>
    </div>
  </div>
@endsection

@push('css')
  <style>
    /* Tree View Styling */
    .menu-tree-container {
      padding: 1rem 0;
    }
    
    .menu-item {
      transition: all 0.3s ease;
      margin-bottom: 0.75rem;
    }
    
    .menu-item:hover {
      transform: translateY(-2px);
    }
    
    .card {
      border-radius: 8px;
      transition: all 0.2s ease;
      border: 1px solid #dee2e6;
    }
    
    .card:hover {
      box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .border-left-primary {
      border-left: 4px solid #007bff !important;
    }
    
    .border-left-success {
      border-left: 4px solid #28a745 !important;
    }
    
    .border-left-info {
      border-left: 4px solid #17a2b8 !important;
    }
    
    .toggle-submenu {
      cursor: pointer; 
      width: 24px;
      height: 24px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 3px;
      border: none;
      background: transparent;
      padding: 0;
      transition: all 0.2s ease;
    }

    .toggle-submenu:hover {
      background-color: #e9ecef;
    }
    
    .toggle-submenu.collapsed i {
      transform: rotate(-90deg);
    }
    
    .submenu-container {
      border-left: 2px dashed #dee2e6;
      padding-left: 1rem;
      display: none; /* Default hidden */
      margin-top: 0.5rem;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    
    .badge-sm {
      font-size: 0.7em;
      padding: 0.25em 0.5em;
    }
    
    /* Button Groups */
    .btn-group-sm > .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
    /* Search highlight */
    .search-highlight {
      background-color: #fff3cd;
      padding: 0 2px;
      border-radius: 2px;
      font-weight: bold;
    }
    
    /* Loading animation */
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(0,0,0,.3);
      border-radius: 50%;
      border-top-color: #007bff;
      animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #6c757d;
    }
    
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .btn-group {
        display: flex;
        flex-wrap: wrap;
      }
      
      .btn-group .btn {
        margin-bottom: 0.25rem;
      }
      
      .input-group {
        width: 100% !important;
      }
      
      .col-md-4 {
        margin-bottom: 1rem;
      }
    }
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function () {
      var setIpDinamisTabelUrl = '{{ $setIpDinamisTabelUrl }}';
      var currentKategori = '{{ $kategori ?? '' }}';
      
      // Set kategori filter value if exists
      if (currentKategori && currentKategori !== '') {
        $('#kategoriFilter').val(currentKategori);
      }
      
      // Toggle submenu dengan delegasi event
      $(document).off('click', '.toggle-submenu').on('click', '.toggle-submenu', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var targetId = $(this).data('target');
        var submenuContainer = $('#' + targetId);
        var icon = $(this).find('i');
        
        if (submenuContainer.length) {
          submenuContainer.slideToggle(300, function() {
            if ($(this).is(':visible')) {
              icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            } else {
              icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            }
          });
        }
      });

      // Handle search form submission
      $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        var kategori = $('#kategoriFilter').val();
        loadSetIpDinamisTabelData(search, kategori);
      });

      // Function untuk expand all submenus
      window.expandAll = function() {
        $('.submenu-container').slideDown(300);
        $('.toggle-submenu i').removeClass('fa-chevron-right').addClass('fa-chevron-down');
      }

      // Function untuk collapse all submenus
      window.collapseAll = function() {
        $('.submenu-container').slideUp(300);
        $('.toggle-submenu i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
      }

      // Filter by kategori
      window.filterByKategori = function(kategori) {
        var search = $('#searchForm input[name="search"]').val();
        loadSetIpDinamisTabelData(search, kategori);
      }

      // Clear search
      window.clearSearch = function() {
        $('#searchForm input[name="search"]').val('');
        var kategori = $('#kategoriFilter').val();
        loadSetIpDinamisTabelData('', kategori);
      }

      // Function to load data
      function loadSetIpDinamisTabelData(search, kategori) {
        var params = {};
        if (search && typeof search === 'string' && search.trim() !== '') {
          params.search = search.trim();
        }
        if (kategori && typeof kategori === 'string' && kategori.trim() !== '') {
          params.kategori = kategori.trim();
        }
        
        $.ajax({
          url: setIpDinamisTabelUrl + '/getData',
          type: 'GET',
          data: params,
          beforeSend: function() {
            $('#menu-container').html(`
              <div class="text-center py-5">
                <div class="loading-spinner"></div>
                <p class="mt-3 text-muted">Memuat data...</p>
              </div>
            `);
          },
          success: function(response) {
            $('#menu-container').html(response);
            
            // Auto expand if there's a search
            if (search && search.trim().length > 0) {
              setTimeout(function() {
                expandAll();
              }, 300);
            }
          },
          error: function(xhr) {
            $('#menu-container').html(`
              <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Terjadi kesalahan saat memuat data. Silakan coba lagi.
              </div>
            `);
            console.error('Error loading data:', xhr.responseText);
          }
        });
      }

      // Modal action functions
      window.modalAction = function(action) {
        $('#myModal .modal-content').html(`
          <div class="text-center p-5">
            <div class="loading-spinner"></div>
            <p class="mt-3 text-muted">Memuat...</p>
          </div>
        `);
        $('#myModal').modal('show');

        $.ajax({
          url: action,
          type: 'GET',
          success: function(response) {
            $('#myModal .modal-content').html(response);
          },
          error: function(xhr) {
            $('#myModal .modal-content').html(`
              <div class="modal-header">
                <h5 class="modal-title">Error</h5>
                <button type="button" class="close" data-dismiss="modal">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="alert alert-danger">
                  <i class="fas fa-exclamation-triangle mr-2"></i>
                  Terjadi kesalahan saat memuat data. Silakan coba lagi.
                </div>
              </div>
            `);
            console.error('Error loading modal:', xhr.responseText);
          }
        });
      }

      // Functions untuk Sub Menu Utama
      window.editSubMenuUtama = function(id) {
        modalAction(setIpDinamisTabelUrl + '/editSubMenuUtama/' + id);
      }

      window.detailSubMenuUtama = function(id) {
        modalAction(setIpDinamisTabelUrl + '/detailSubMenuUtama/' + id);
      }

      window.deleteSubMenuUtama = function(id) {
        modalAction(setIpDinamisTabelUrl + '/deleteSubMenuUtama/' + id);
      }

      // Functions untuk Sub Menu
      window.editSubMenu = function(id) {
        modalAction(setIpDinamisTabelUrl + '/editSubMenu/' + id);
      }

      window.detailSubMenu = function(id) {
        modalAction(setIpDinamisTabelUrl + '/detailSubMenu/' + id);
      }

      window.deleteSubMenu = function(id) {
        modalAction(setIpDinamisTabelUrl + '/deleteSubMenu/' + id);
      }

      // Reload function
      window.reloadTable = function() {
        var search = $('#searchForm input[name="search"]').val();
        var kategori = $('#kategoriFilter').val();
        loadSetIpDinamisTabelData(search, kategori);
      }

      // Auto-dismiss alerts after 5 seconds
      setTimeout(function() {
        $('.alert-dismissible').fadeOut();
      }, 5000);

      console.log('Set IP Dinamis Tabel page loaded successfully');
    });
  </script>
@endpush