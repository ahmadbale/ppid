<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\IpDinamisTabel\index.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $ipDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('kategori-informasi-publik-dinamis-tabel');
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
          @if(Auth::user()->level->hak_akses_kode === 'SAR' || SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $ipDinamisTabelUrl, 'create'))
            <button class="btn btn-success" onclick="modalAction('{{ url($ipDinamisTabelUrl . '/addData') }}')">
              <i class="fas fa-plus"></i> Tambah Kategori Baru
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
                     placeholder="Cari nama submenu atau judul..." style="width: 300px;">
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
          <button class="btn btn-outline-primary" onclick="reloadTable()">
            <i class="fas fa-sync"></i> Refresh
          </button>
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

      <div id="table-container">
        @include('sisfo::AdminWeb.InformasiPublik.IpDinamisTabel.data')
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
    .table-responsive {
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .table th {
      background-color: #f8f9fa;
      border-top: none;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }
    
    .table td {
      vertical-align: middle;
    }
    
    .btn-group-sm > .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
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
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function () {
      var ipDinamisTabelUrl = '{{ $ipDinamisTabelUrl }}';
      
      // Handle search form submission
      $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        loadIpDinamisTabelData(search);
      });

      // Clear search
      window.clearSearch = function() {
        $('#searchForm input[name="search"]').val('');
        loadIpDinamisTabelData('');
      }

      // Function to load data
      function loadIpDinamisTabelData(search) {
        var params = {};
        if (search && search.trim() !== '') {
          params.search = search.trim();
        }
        
        $.ajax({
          url: ipDinamisTabelUrl + '/getData',
          type: 'GET',
          data: params,
          beforeSend: function() {
            $('#table-container').html(`
              <div class="text-center py-5">
                <div class="loading-spinner"></div>
                <p class="mt-3 text-muted">Memuat data...</p>
              </div>
            `);
          },
          success: function(response) {
            $('#table-container').html(response);
          },
          error: function(xhr) {
            $('#table-container').html(`
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

      // Reload function
      window.reloadTable = function() {
        var search = $('#searchForm input[name="search"]').val();
        loadIpDinamisTabelData(search);
      }

      // Auto-dismiss alerts after 5 seconds
      setTimeout(function() {
        $('.alert-dismissible').fadeOut();
      }, 5000);

      console.log('IP Dinamis Tabel page loaded successfully');
    });
  </script>
@endpush