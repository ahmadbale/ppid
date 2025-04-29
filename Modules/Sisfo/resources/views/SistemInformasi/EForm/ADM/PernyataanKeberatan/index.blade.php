@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $pernyataanKeberatanAdminUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan-admin');
@endphp
@extends('sisfo::layouts.template')
@section('content')

    <?php
    // Di bagian awal view atau sebelum dibutuhkan
    $data = app()->call([app('Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PernyataanKeberatanController'), 'getData']);
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
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <label class="col-md-2 col-form-label font-weight-bold">Filter Statistik</label>
                <div class="col-md-4">
                    <select class="form-control" id="kode_level" name="kode_level" required>
                        <option value="">- Semua -</option>
                        {{-- @foreach ($aLevel as $item)
                        <option value="{{ $item->kode_level }}">{{ $item->kode_level }}</option>
                        @endforeach --}}
                    </select>
                    <small class="form-text text-muted">tahun/periode</small>
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    <button onclick="modalAction('{{ url('#') }}')" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-export mr-1"></i> Export Report
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6 col-12 mb-3">
                    <div class="info-box border border-masuk shadow-sm">
                        <span class="info-box-icon">
                            <i class="fas fa-inbox fa-2x text-warning"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Masuk</span>
                            <span class="info-box-number">12</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-12 mb-3">
                    <div class="info-box border border-diproses shadow-sm">
                        <span class="info-box-icon">
                            <i class="fas fa-spinner fa-2x text-primary"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Diproses</span>
                            <span class="info-box-number">8</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-12 mb-3">
                    <div class="info-box border border-selesai shadow-sm">
                        <span class="info-box-icon">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Selesai</span>
                            <span class="info-box-number">3</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-12 mb-3">
                    <div class="info-box border border-ditolak shadow-sm">
                        <span class="info-box-icon">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ditolak</span>
                            <span class="info-box-number">1</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title text-center font-weight-bold">
                            Daftar Pengaduan Keberatan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 align-items-center">
                            <!-- Kolom Pencarian -->
                            <div class="col-md-6 mb-2 mb-md-0">
                                <form id="searchForm" class="d-flex">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari nama pemohon">
                                    <button type="submit" class="btn btn-primary ml-2">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Kolom Tombol Tambah -->
                            <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                @if(
                                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $pernyataanKeberatanAdminUrl, 'create')
                                )
                                <a href="{{ url($pernyataanKeberatanAdminUrl . '/addData') }}"
                                    class="btn btn-sm btn-success d-inline-flex align-items-center">
                                    <i class="fas fa-plus mr-1"></i> Ajukan Pengaduan
                                </a>
                                @endif
                            </div>
                        </div>


                        <!-- Tabel Data -->
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
                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                onclick="modalAction('{{ url('#') }}')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Budi Setia</td>
                                        <td><span class="badge bg-primary">Diproses</span></td>
                                        <td>2025-04-13</td>
                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                onclick="modalAction('{{ url('#') }}')">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header bg-primeri">
                    <h3 class="card-title tetx-center font-weight-bold">
                        @if (isset($ketentuanPelaporan))
                            {{ $ketentuanPelaporan->kp_judul }}
                        @else
                            Mekanisme Pengajuan Keberatan
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
                <div class="card-header bg-primeri">
                    <h3 class="card-title font-weight-bold mb-0">
                        @if (isset($ketentuanPelaporan))
                            {{ $ketentuanPelaporan->kp_judul }}
                        @else
                            Ketentuan Pelaporan Keberatan
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
        </div>
    </div>
@endsection
