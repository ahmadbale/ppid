@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $getIpDinamisTabelInformasiSetiapSaatUrl = WebMenuModel::getDynamicMenuUrl('get-informasi-publik-informasi-setiap-saat');
@endphp
@extends('sisfo::layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>{{ $page->title }}
                </h3>
            </div>
            <div class="col-md-4 text-right">
                <button class="btn btn-outline-primary" onclick="reloadTable()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        @if($kategori)
            <!-- Header Informasi -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="info-message alert-info border-left-primary">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x text-primary mr-3"></i>
                            <div>
                                <h4 class="alert-heading mb-2">{{ $kategori->ip_judul }}</h4>
                                @if($kategori->ip_deskripsi)
                                    <p class="mb-0">{{ $kategori->ip_deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Update dan Total -->
            <div class="row mb-3">
                <div class="col-md-4">
                    @if($lastUpdate)
                        <div class="info-message alert-warning py-2">
                            <i class="fas fa-clock mr-1"></i>
                            <small>Diperbaharui pada {{ date('d F Y, H:i', strtotime($lastUpdate)) }}</small>
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="info-message alert-info py-2">
                        <i class="fas fa-list mr-1"></i>
                        <small>Total: <strong>{{ $totalData ?? 0 }}</strong> data tersedia</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-message alert-success py-2">
                        <i class="fas fa-file-alt mr-1"></i>
                        <small>Dokumen: <strong>{{ $totalDokumen ?? 0 }}</strong> dari {{ $totalData ?? 0 }}</small>
                    </div>
                </div>
            </div>

            <!-- Filter Pencarian -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form id="searchForm" class="form-inline">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="{{ $search }}" 
                                   placeholder="Cari informasi publik..." style="width: 350px;">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i> Cari
                                </button>
                                @if(!empty($search))
                                    <button type="button" class="btn btn-outline-danger" onclick="clearSearch()">
                                        <i class="fas fa-times mr-1"></i> Reset
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
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

            <!-- Container untuk data -->
            <div id="table-container">
                @include('sisfo::AdminWeb.InformasiPublik.GetIPDinamisTabel.SetiapSaat.data')
            </div>
        @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                    <h4 class="text-warning">Kategori Tidak Ditemukan</h4>
                    <p class="text-muted">
                        Kategori "Informasi Setiap Saat" belum tersedia dalam sistem.
                        <br>Silakan hubungi administrator untuk menambahkan kategori ini.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk menampilkan dokumen -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Content akan dimuat via AJAX -->
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.empty-state {
    padding: 2rem;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table thead th {
    background-color: #30373d;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d7ff;
    color: #0c5460;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}

.alert-success {
    background-color: #d1f2eb;
    border-color: #a3e4d7;
    color: #0f5132;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
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

/* Modal styles */
.modal-xl {
    max-width: 90% !important;
}

.document-viewer {
    background: #f8f9fa;
}

.pdf-container {
    background: white;
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95% !important;
        margin: 10px;
    }
    
    .document-actions {
        flex-direction: column;
        gap: 5px;
    }
    
    .document-actions .btn {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    var getIpDinamisTabelInformasiSetiapSaatUrl = '{{ $getIpDinamisTabelInformasiSetiapSaatUrl }}';
    
    // Handle view document button click
    $(document).on('click', '.view-document-setiap-saat', function() {
        const type = $(this).data('type');
        const id = $(this).data('id');
        const title = $(this).data('title');
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        // Show loading state
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...').prop('disabled', true);
        
        // Load document modal
        $.ajax({
            url: getIpDinamisTabelInformasiSetiapSaatUrl + '/detailData/' + id + '?type=' + type,
            type: 'GET',
            success: function(response) {
                $('#documentModal .modal-content').html(response);
                $('#documentModal').modal('show');
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat membuka dokumen.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuka Dokumen',
                    text: errorMessage
                });
            },
            complete: function() {
                // Restore button state
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });

    // Handle modal close
    $('#documentModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').empty();
    });

    // Handle search form submission
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        loadInformasiSetiapSaatData(search);
    });

    // Clear search function
    window.clearSearch = function() {
        $('#searchForm input[name="search"]').val('');
        loadInformasiSetiapSaatData('');
    }

    // Function to load data
    function loadInformasiSetiapSaatData(search) {
        var params = {};
        if (search && search.trim() !== '') {
            params.search = search.trim();
        }
        
        $.ajax({
            url: getIpDinamisTabelInformasiSetiapSaatUrl + '/getData',
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

    // Reload function
    window.reloadTable = function() {
        var search = $('#searchForm input[name="search"]').val();
        loadInformasiSetiapSaatData(search);
    }

    // Auto focus pada input pencarian jika ada parameter search
    @if($search)
        $('input[name="search"]').focus();
    @endif

    // Submit form saat Enter ditekan
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut();
    }, 5000);

    console.log('Informasi Setiap Saat page loaded successfully');
});
</script>
@endpush
