@extends('sisfo::layouts.template')

@section('content')
    {{-- <div class="card-body">
        <div class="row">
            @foreach ($menuBoxes as $box)
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $box['bg'] }} text-white shadow rounded">
                        <div class="inner">
                            <h4 class="font-weight-bold">{{ $box['title'] }}</h4>
                        </div>
                        <div class="icon">
                            <i class="{{ $box['icon'] }} fa-3x"></i>
                        </div>
                        <a href="{{ $box['url'] }}" class="small-box-footer text-white">
                            Akses Menu <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div> --}}

    <div class="card-body">
        <div class="row">
            @foreach ($menuBoxes as $box)
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $box['bg'] }} text-white shadow rounded">
                        <div class="inner">
                            <h3>{{ $box['jumlah'] }}</h3>
                            <p>Aduan <strong>{{ $box['status'] }}</strong></p>
                        </div>
                        <div class="icon">
                            <i class="{{ $box['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- <div class="card-body">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>150</h3>
                    <p>Aduan <strong>Masuk</strong></p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>53<sup style="font-size: 30px"></sup></h3>

                    <p>Aduan <strong>Diproses</strong></p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>44</h3>

                    <p>Aduan <strong>Disetujui</strong></p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>65</h3>

                    <p>Aduan <strong>Ditolak</strong></p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="card">
        filter
        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-3 align-items-end">
                    <!-- Dari Tanggal -->
                    <div class="col-md-4">
                        <label for="startDate" class="form-label fw-bold">Dari Tanggal:</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" placeholder="dd-mm-yyyy">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="col-md-4">
                        <label for="endDate" class="form-label fw-bold">Sampai Tanggal:</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" placeholder="dd-mm-yyyy">
                    </div>

                    <!-- Jenis Informasi -->
                    <div class="col-md-4">
                        <label for="jenisInformasi" class="form-label fw-bold">Jenis Informasi:</label>
                        <select class="form-select" id="jenisInformasi" name="jenisInformasi">
                            <option value="">Semua</option>
                            <option value="permohonan">Permohonan Informasi</option>
                            <option value="wbs">WBS</option>
                            <option value="keberatan">Pernyataan Keberatan</option>
                            <option value="pengaduan">Pengaduan Masyarakat</option>
                            <option value="perawatan">Permohonan Perawatan Sarana</option>
                        </select>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primeri">
                        <h5 class="card-title">Recap Report</h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="text-center">
                                    <strong>Statistik Pengaduan Informasi</strong>
                                    <small class="text-muted d-block">(grafik batang)</small>
                                </p>

                                <div class="chart">
                                    <!-- Sales Chart Canvas -->
                                    <canvas id="salesChart" height="180" style="height: 180px;"></canvas>
                                    <small class="text-muted text-center d-block">x=jumlah(satuan) | y=bulan</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="text-center">
                                    <strong>Distribusi Aduan Informasi</strong>
                                </p>
                                <div class="progress-group">
                                    <span class="progress-text">Permohonan Informasi</span>
                                    <span class="float-right"><b>80</b>/200</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: 40%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Pengaduan Masyarakat</span>
                                    <span class="float-right"><b>50</b>/200</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" style="width: 25%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Permohonan Sarana Prasarana</span>
                                    <span class="float-right"><b>35</b>/200</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" style="width: 17.5%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Pernyataan Keberatan</span>
                                    <span class="float-right"><b>20</b>/200</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-info" style="width: 10%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Whistle Blowing</span>
                                    <span class="float-right"><b>15</b>/200</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-danger" style="width: 7.5%"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primeri border-transparent">
                        <h3 class="card-title">Permintaan Terbaru</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-responsive-stack align-middle m-0 text-center">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Pemohon</th>
                                        <th>Status</th>
                                        <th>Jenis Permohonan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permintaanTerbaru as $item)
                                        <tr>
                                            <td table-data-label='Tanggal' class="text-center">
                                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                                            <td table-data-label='Nama Pemohon' class="text-center">{{ $item['nama'] }}
                                            </td>
                                            <td table-data-label='Status' class="text-center">
                                                @php
                                                    $badgeClass = match (strtolower($item['status'])) {
                                                        'masuk' => 'warning',
                                                        'diproses' => 'info',
                                                        'disetujui' => 'success',
                                                        'ditolak' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span
                                                    class="badge badge-{{ $badgeClass }}">{{ ucfirst($item['status']) }}</span>
                                            </td>
                                            <td table-data-label='Jenis Permohonan' class="text-center">
                                                {{ $item['jenis'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data permintaan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="#" class="btn btn-sm btn-info float-right">Lihat Semua</a>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primeri">
                        <h3 class="card-title">Browser Usage</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="pieChart" height="150"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-danger"></i> Chrome</li>
                                    <li><i class="far fa-circle text-success"></i> IE</li>
                                    <li><i class="far fa-circle text-warning"></i> FireFox</li>
                                    <li><i class="far fa-circle text-info"></i> Safari</li>
                                    <li><i class="far fa-circle text-primary"></i> Opera</li>
                                    <li><i class="far fa-circle text-secondary"></i> Navigator</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    United States of America
                                    <span class="float-right text-danger">
                                        <i class="fas fa-arrow-down text-sm"></i>
                                        12%
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    India
                                    <span class="float-right text-success">
                                        <i class="fas fa-arrow-up text-sm"></i> 4%
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    China
                                    <span class="float-right text-warning">
                                        <i class="fas fa-arrow-left text-sm"></i> 0%
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const pie_chart_options = {
            series: [700, 500, 400, 600, 300, 100],
            chart: {
                type: 'donut',
            },
            labels: ['Chrome', 'Edge', 'FireFox', 'Safari', 'Opera', 'IE'],
            dataLabels: {
                enabled: false,
            },
            colors: ['#0d6efd', '#20c997', '#ffc107', '#d63384', '#6f42c1', '#adb5bd'],
        };

        const pie_chart = new ApexCharts(document.querySelector('#pie-chart'), pie_chart_options);
        pie_chart.render();
    </script>
@endsection
