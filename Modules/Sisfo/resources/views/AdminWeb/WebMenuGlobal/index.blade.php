<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\index.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
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
          @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $webMenuGlobalPath, 'create'))
            <button class="btn btn-success" onclick="modalAction('{{ url($webMenuGlobalPath . '/addData') }}')">
              <i class="fas fa-plus"></i> Tambah Menu Global
            </button>
          @endif
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <form id="searchForm" class="form-inline">
            <div class="input-group">
              <input type="text" class="form-control" name="search" value="{{ $search }}" 
                     placeholder="Cari nama menu, kategori, atau URL..." style="width: 300px;">
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
        <div class="col-md-6 text-right">
          <div class="btn-group">
            <button class="btn btn-outline-info btn-sm" onclick="filterByKategori('')">
              <i class="fas fa-list"></i> Semua
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="filterByKategori('Group Menu')">
              <i class="fas fa-folder"></i> Group Menu
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="filterByKategori('Menu Biasa')">
              <i class="fas fa-file"></i> Menu Biasa
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="filterByKategori('Sub Menu')">
              <i class="fas fa-sitemap"></i> Sub Menu
            </button>
          </div>
        </div>
      </div>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div id="menu-container">
        @include('sisfo::AdminWeb.WebMenuGlobal.data')
      </div>
    </div>
  </div>

  <!-- Modal for CRUD operations -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
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
    }
    
    .menu-item:hover {
      transform: translateY(-2px);
    }
    
    .card {
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    
    .card:hover {
      box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .border-left-primary {
      border-left: 4px solid #007bff !important;
    }
    
    .border-left-info {
      border-left: 4px solid #17a2b8 !important;
    }
    
    .border-left-warning {
      border-left: 4px solid #ffc107 !important;
    }
    
    .toggle-submenu {
      cursor: pointer; 
      width: 24px;
      height: 24px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 3px;
    }

    .toggle-submenu:hover {
      background-color: #97d2c9;
    }
    
    .toggle-submenu.collapsed i {
      transform: rotate(-90deg);
    }
    
    .submenu-container {
      border-left: 2px dashed #dee2e6;
      padding-left: 1rem;
      display: none; /* Default hidden */
      margin-top: 0.5rem;
    }
    
    .badge-sm {
      font-size: 0.7em;
    }
    
    /* Button Groups */
    .btn-group-sm > .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
    /* Animation for expand/collapse */
    .submenu-container {
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    
    /* Search highlight */
    .search-highlight {
      background-color: yellow;
      padding: 0 2px;
    }
    
    /* Category filter buttons */
    .btn-group .btn.active {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function () {
      var webMenuGlobalPath = '{{ $webMenuGlobalPath }}';
      
      // Perbaikan: Toggle submenu dengan delegasi event yang lebih kuat
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
        } else {
          console.warn('Submenu container dengan ID ' + targetId + ' tidak ditemukan.');
        }
      });

      // Handle search form submission
      $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        loadWebMenuGlobalData(search);
      });

      // Perbaikan: Function untuk expand all submenus
      window.expandAll = function() {
        $('.submenu-container').slideDown(300);
        $('.toggle-submenu i').removeClass('fa-chevron-right').addClass('fa-chevron-down');
      }

      // Perbaikan: Function untuk collapse all submenus
      window.collapseAll = function() {
        $('.submenu-container').slideUp(300);
        $('.toggle-submenu i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
      }

      // Filter by kategori
      window.filterByKategori = function(kategori) {
        // Update active button
        $('.btn-group .btn').removeClass('active');
        if (kategori === '') {
          $('.btn-group .btn:first').addClass('active');
        } else {
          $('.btn-group .btn').each(function() {
            if ($(this).text().trim().includes(kategori)) {
              $(this).addClass('active');
            }
          });
        }
        
        loadWebMenuGlobalData($('#searchForm input[name="search"]').val(), kategori);
      }

      // Clear search
      window.clearSearch = function() {
        $('#searchForm input[name="search"]').val('');
        loadWebMenuGlobalData('');
      }

      // Perbaikan: Function to load data
      function loadWebMenuGlobalData(search, kategori) {
        var params = {};
        if (search && typeof search === 'string') params.search = search;
        if (kategori && typeof kategori === 'string') params.kategori = kategori;
        
        $.ajax({
          url: webMenuGlobalPath + '/getData',
          type: 'GET',
          data: params,
          beforeSend: function() {
            $('#menu-container').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data...</p></div>');
          },
          success: function(response) {
            $('#menu-container').html(response);
            
            // Auto expand if there's a search
            if (search && search.length > 0) {
              setTimeout(function() {
                expandAll();
              }, 300);
            }
          },
          error: function(xhr) {
            $('#menu-container').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
            console.error('Error loading data:', xhr.responseText);
          }
        });
      }

      // Modal action function
      window.modalAction = function(action) {
        $('#myModal .modal-content').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat...</p></div>');
        $('#myModal').modal('show');

        $.ajax({
          url: action,
          type: 'GET',
          success: function(response) {
            $('#myModal .modal-content').html(response);
          },
          error: function(xhr) {
            $('#myModal .modal-content').html('<div class="modal-header"><h5 class="modal-title">Error</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><div class="alert alert-danger">Terjadi kesalahan saat memuat data</div></div>');
            console.error('Error loading modal:', xhr.responseText);
          }
        });
      }

      // Reload function
      window.reloadTable = function() {
        var search = $('#searchForm input[name="search"]').val();
        loadWebMenuGlobalData(search);
      }
      
      // Perbaikan: Log saat halaman dimuat untuk keperluan debugging
      console.log('Menu Global page loaded');
      
      // Perbaikan: Jalankan initial load
      loadWebMenuGlobalData($('#searchForm input[name="search"]').val());
    });
  </script>
@endpush