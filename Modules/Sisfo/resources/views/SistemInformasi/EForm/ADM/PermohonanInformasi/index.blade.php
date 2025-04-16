{{-- @extends('sisfo::layouts.template')
@section('content')

<?php
// Di bagian awal view atau sebelum dibutuhkan
$data = app()->call([app('Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PermohonanInformasiController'), 'getData']);
$timeline = $data['timeline'];
$ketentuanPelaporan = $data['ketentuanPelaporan'];
?>
<!-- Flash Message -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            @if (isset($timeline))
                {{ $timeline->judul_timeline }}
            @else
                Prosedur Pengajuan Permohonan Informasi
            @endif
        </h3>
    </div>
    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        <p>Ikuti langkah-langkah berikut untuk mengajukan permohonan:</p>
        <ol style="margin-bottom: 0;">
            @if (isset($timeline) && $timeline->langkahTimeline->count() > 0)
                @foreach ($timeline->langkahTimeline as $langkah)
                    <li>{{ $langkah->langkah_timeline }}</li>
                @endforeach
            @else
                <li>Belum ada Timeline</li>
            @endif
        </ol>
    </div>

    <!-- Beri jarak dengan margin dan garis pembatas -->
    <div class="mt-4 mb-4">

    </div>

    <!-- Header dengan judul di tengah dan bold -->
    <div class="card-header text-center">
        <h3 class="card-title font-weight-bold" style="float: none; display: inline-block;">
            @if (isset($ketentuanPelaporan))
                {{ $ketentuanPelaporan->kp_judul }}
            @else
                Ketentuan Pelaporan Permohonan Informasi
            @endif
        </h3>
    </div>

    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        @if (isset($ketentuanPelaporan))
            {!! $ketentuanPelaporan->kp_konten !!}
        @else
            <p>Belum ada ketentuan pelaporan yang tersedia.</p>
        @endif
    </div>

    <div class="card-body" style="padding-top: 10px;">
        <hr class="thick-line">
        <h4><strong>Permohonan informasi terdiri atas Perorangan dan Organisasi</strong></h4>
        <hr class="thick-line">
        <div class="row text-center">
            <div class="col-md-4">
                <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PermohonanInformasi/addData') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-edit fa-2x"></i>
                    <h5>E-Form Permohonan Informasi</h5>
                </a>

            </div>
        </div>
    </div>
</div>
<style>
    .thick-line {
        border: none;
        height: 1px;
        background-color: black;
    }
    .custom-button {
        background-color: #a59c9c;
        border: 2px solid black;
        border-radius: 8px;
        color: black;
        text-decoration: none;
        transition: background-color 0.3s, transform 0.3s;
    }
    .custom-button:hover {
        background-color: #8e8585;
        transform: scale(0.95);
        color: white; /* Warna ikon saat hover */
    }
    .custom-container {
        background-color: #ffffff;
        border: 2px solid black;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }
</style>
@endsection --}}

@extends('sisfo::layouts.template')
@section('content')

    <?php
    // Di bagian awal view atau sebelum dibutuhkan
    $data = app()->call([app('Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PermohonanInformasiController'), 'getData']);
    $timeline = $data['timeline'];
    $ketentuanPelaporan = $data['ketentuanPelaporan'];
    ?>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistik -->
    <div class="card card-outline">
        <div class="card-body">
            <div class="row text-center">
                <div class="col">
                    <div class="card bg-light p-3">Masuk: 12</div>
                </div>
                <div class="col">
                    <div class="card bg-info text-white p-3">Diproses: 8</div>
                </div>
                <div class="col">
                    <div class="card bg-success text-white p-3">Selesai: 3</div>
                </div>
                <div class="col">
                    <div class="card bg-danger text-white p-3">Ditolak: 1</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline ">
        <div class="card-body">
            <h5 class="mb-3 text-bold">Daftar Permohonan Informasi</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <form id="searchForm" class="d-flex">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama pemohon">
                        <button type="submit" class="btn btn-primary ml-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PermohonanInformasi/addData') }}"
                        class="btn btn-sm btn-success d-inline-flex align-items-center">
                        <i class="fas fa-plus mr-1"></i> Ajukan Permohonan
                    </a>
                </div>
            </div>

            <div class="table-responsive" id="table-container">
                <table class="table table-responsive-stack table-bordered table-striped">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Pemohon</th>
                            <th>Status</th>
                            <th>Tanggal Pengaduan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td>1</td>
                            <td>Andi Wijaya</td>
                            <td><span class="badge bg-warning">Masuk</span></td>
                            <td>2025-04-14</td>
                            <td><a href="#" class="btn btn-sm btn-outline-info">Detail</a></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Budi Setia</td>
                            <td><span class="badge bg-primary">Diproses</span></td>
                            <td>2025-04-13</td>
                            <td><a href="#" class="btn btn-sm btn-outline-info">Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-outline">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
            <div class="card-header text-center">
                <h3 class="card-title font-weight-bold" style="float: none; display: inline-block;">
                    @if (isset($ketentuanPelaporan))
                        {{ $ketentuanPelaporan->kp_judul }}
                    @else
                        Ketentuan Pelaporan Permohonan Informasi
                    @endif
                </h3>
            </div>
            <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
                <ol style="margin-bottom: 0;">
                    @if (isset($timeline) && $timeline->langkahTimeline->count() > 0)
                        @foreach ($timeline->langkahTimeline as $langkah)
                            <li>{{ $langkah->langkah_timeline }}</li>
                        @endforeach
                    @else
                        <li>Belum ada Timeline</li>
                    @endif
                </ol>
            </div>

    </div>


    <div class="card card-outline">
        <div class="card-header text-center">
            <h3 class="card-title font-weight-bold" style="float: none; display: inline-block;">
                @if (isset($ketentuanPelaporan))
                    {{ $ketentuanPelaporan->kp_judul }}
                @else
                    Ketentuan Pelaporan Permohonan Informasi
                @endif
            </h3>
        </div>


        <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
            @if (isset($ketentuanPelaporan))
                {!! $ketentuanPelaporan->kp_konten !!}
            @else
                <p>Belum ada ketentuan pelaporan yang tersedia.</p>
            @endif
        </div>
    </div>
@endsection

{{-- -------------------------------------------------------------------------------------------------------------------- --}}


{{-- @extends('sisfo::layouts.template')

@section('content')
  <div class="card card-outline card-primary">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h3 class="card-title">{{ $page->title }}</h3>
          </div>
          <div class="col-md-6 text-right">
            <button onclick="modalAction('{{ url('adminweb/kategori-akses/addData') }}')"
                    class="btn btn-sm btn-success">
              <i class="fas fa-plus"></i> Tambah
            </button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <form id="searchForm" class="d-flex">
              <input type="text" name="search" class="form-control"
                     placeholder="Cari judul kategori"
                     value="{{ $search ?? '' }}">
              <button type="submit" class="btn btn-primary ml-2">
                <i class="fas fa-search"></i>
              </button>
            </form>
          </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive" id="table-container">
          @include('sisfo::AdminWeb.KategoriAkses.data')
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
  .pagination {
    justify-content: flex-start; /* Ubah ke kiri */
  }
</style>
@endpush

@push('js')
  <script>
    $(document).ready(function() {
      // Handle search form submission
      $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        loadKategoriAksesData(1, search);
      });

      // Handle pagination links with delegation
      $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var search = $('#searchForm input[name="search"]').val();
        loadKategoriAksesData(page, search);
      });
    });

    function loadKategoriAksesData(page, search) {
      $.ajax({
        url: '{{ url("adminweb/kategori-akses/getData") }}',
        type: 'GET',
        data: {
          page: page,
          search: search
        },
        success: function(response) {
          $('#table-container').html(response);
        },
        error: function(xhr) {
          alert('Terjadi kesalahan saat memuat data');
        }
      });
    }

    function modalAction(url) {
      $('#myModal .modal-content').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
      $('#myModal').modal('show');

      $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
          $('#myModal .modal-content').html(response);
        },
        error: function(xhr) {
          $('#myModal .modal-content').html('<div class="modal-header"><h5 class="modal-title">Error</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div></div>');
        }
      });
    }

    function reloadTable() {
      var currentPage = $('.pagination .active .page-link').text();
      currentPage = currentPage || 1;
      var search = $('#searchForm input[name="search"]').val();
      loadKategoriAksesData(currentPage, search);
    }
  </script>
@endpush --}}
