@extends('sisfo::layouts.template')
@section('content')

<?php
// Di bagian awal view atau sebelum dibutuhkan
$data = app()->call([app('Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\WBSController'), 'getData']);
$timeline = $data['timeline'];
$ketentuanPelaporan = $data['ketentuanPelaporan'];
?>
<!-- Flash Message -->
{{-- @if(session('success'))
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
            @if(isset($timeline))
                {{ $timeline->judul_timeline }}
            @else
                Prosedur Pengajuan Whistle Blowing System
            @endif
        </h3>
    </div>
    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        <p>Ikuti langkah-langkah berikut untuk mengajukan Whistle Blowing System:</p>
        <ol style="margin-bottom: 0;">
            @if(isset($timeline) && $timeline->langkahTimeline->count() > 0)
                @foreach($timeline->langkahTimeline as $langkah)
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
            @if(isset($ketentuanPelaporan))
                {{ $ketentuanPelaporan->kp_judul }}
            @else
                Ketentuan Pelaporan Whistle Blowing System
            @endif
        </h3>
    </div>

    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        @if(isset($ketentuanPelaporan))
            {!! $ketentuanPelaporan->kp_konten !!}
        @else
            <p>Belum ada ketentuan pelaporan yang tersedia.</p>
        @endif
    </div>

    <div class="card-body" style="padding-top: 10px;">
        <hr class="thick-line">
        <h4><strong>Silakan laporkan Whistle Blowing System Anda melalui form berikut</strong></h4>
        <hr class="thick-line">
        <div class="row text-center">
            <div class="col-md-4">
                <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/WBS/addData') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-edit fa-2x"></i>
                    <h5>E-Form Whistle Blowing System</h5>
                </a>
                <div class="custom-container p-3">
                    <p>Silakan Mengklik Button Diatas Untuk Melakukan Pengisian Form Whistle Blowing System</p>
                </div>
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
            <h5 class="mb-3 text-bold">Jumlah Pelaporan WBS</h5>
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
            <h5 class="mb-3 text-bold">Daftar Pelaporan WBS</h5>
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
                    <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/WBS/addData') }}"
                        class="btn btn-sm btn-success d-inline-flex align-items-center">
                        <i class="fas fa-plus mr-1"></i> Ajukan Pengaduan
                    </a>
                </div>
            </div>

            <div class="table-responsive" id="table-container">
                <table class="table table-responsive-stack table-bordered table-striped">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Pengadu</th>
                            <th>Status</th>
                            <th>Tanggal Pengaduan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td>1</td>
                            <td>Surya Kurniawan</td>
                            <td><span class="badge bg-warning">Masuk</span></td>
                            <td>2025-04-14</td>
                            <td><a href="#" class="btn btn-sm btn-outline-info">Detail</a></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Nurul</td>
                            <td><span class="badge bg-primary">Diproses</span></td>
                            <td>2025-04-13</td>
                            <td><a href="#" class="btn btn-sm btn-outline-info">Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title tetx-center font-weight-bold">
                @if (isset($ketentuanPelaporan))
                    {{ $ketentuanPelaporan->kp_judul }}
                @else
                    Mekanisme Permohonan Informasi
                @endif
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body px-3 py-2">
            @if (isset($timeline) && $timeline->langkahTimeline->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach ($timeline->langkahTimeline as $index => $langkah)
                        <li class="list-group-item d-flex align-items-center border-bottom">
                            <span class="badge bg-primary text-white rounded-circle me-3 mr-2"
                                style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                {{ $index + 1 }}
                            </span>
                            <span class="flex-grow-1">{{ $langkah->langkah_timeline }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted text-center mb-0">Belum ada Timeline Mekanisme</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title font-weight-bold mb-0">
                @if (isset($ketentuanPelaporan))
                    {{ $ketentuanPelaporan->kp_judul }}
                @else
                    Ketentuan Pelaporan Permohonan Informasi
                @endif
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body px-3 py-2" x>
            @if (isset($ketentuanPelaporan))
                {!! $ketentuanPelaporan->kp_konten !!}
            @else
                <p class="text-muted text-center mb-0">Belum ada ketentuan pelaporan yang tersedia.</p>
            @endif
        </div>
    </div>

@endsection

