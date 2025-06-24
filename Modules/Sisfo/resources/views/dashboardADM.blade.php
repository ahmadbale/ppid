@extends('sisfo::layouts.template')

@section('title', 'Dashboard Admin')

@section('content')
    <!-- Dashboard Header -->
    <div class="content-header">
        <div class="container-fluid">
            
        </div>
    </div>

    <!-- Statistics Boxes -->
    <div class="row" id="statisticBoxes">
        @foreach ($menuBoxes as $box)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-{{ $box['bg'] }} text-white shadow rounded">
                    <div class="inner">
                        <h3 class="statistic-value">{{ $box['jumlah'] }}</h3>
                        <p>Aduan <strong>{{ $box['status'] }}</strong></p>
                    </div>
                    <div class="icon">
                        <i class="{{ $box['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Filter Data</h5>
                </div>
                <div class="card-body">
                    <form class="row g-3 align-items-end" id="filterForm">
                        <!-- Dari Tanggal -->
                        <div class="col-md-4">
                            <label for="startDate" class="form-label fw-bold">Dari Tanggal:</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" value="{{ request('startDate') }}">
                        </div>

                        <!-- Sampai Tanggal -->
                        <div class="col-md-4">
                            <label for="endDate" class="form-label fw-bold">Sampai Tanggal:</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" value="{{ request('endDate') }}">
                        </div>

                        <!-- Jenis Informasi -->
                        <div class="col-md-4">
                            <label for="jenisInformasi" class="form-label fw-bold">Jenis Informasi:</label>
                            <select class="form-control" id="jenisInformasi" name="jenisInformasi">
                                <option value="">Semua</option>
                                <option value="permohonan" {{ request('jenisInformasi') == 'permohonan' ? 'selected' : '' }}>Permohonan Informasi</option>
                                <option value="wbs" {{ request('jenisInformasi') == 'wbs' ? 'selected' : '' }}>WBS</option>
                                <option value="keberatan" {{ request('jenisInformasi') == 'keberatan' ? 'selected' : '' }}>Pernyataan Keberatan</option>
                                <option value="pengaduan" {{ request('jenisInformasi') == 'pengaduan' ? 'selected' : '' }}>Pengaduan Masyarakat</option>
                                <option value="perawatan" {{ request('jenisInformasi') == 'perawatan' ? 'selected' : '' }}>Permohonan Perawatan Sarana</option>
                            </select>
                        </div>

                        <!-- Tombol Submit dan Reset -->
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-search"></i> Terapkan Filter
                            </button>
                         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <!-- Chart Section -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title text-white">
                        <i class="fas fa-chart-bar"></i> Recap Report
                        <span id="filterInfo" class="badge badge-light ml-2" style="display: none;"></span>
                    </h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-center">
                                <strong>Statistik Pengaduan Informasi</strong>
                                <small class="text-muted d-block">(grafik batang)</small>
                            </p>

                            <div class="chart">
                                <canvas id="salesChart" height="180" style="height: 180px;"></canvas>
                                <small class="text-muted text-center d-block">x=jumlah(satuan) | y=bulan</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <p class="text-center">
                                <strong>Distribusi Aduan Informasi</strong>
                            </p>
                            <div id="distributionContainer">
                                @foreach($distributionData as $item)
                                    <div class="progress-group">
                                        <span class="progress-text">{{ $item['label'] }}</span>
                                        <span class="float-right"><b>{{ $item['total'] }}</b>/{{ $item['max'] }}</span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar 
                                                @if($item['label'] == 'Permohonan Informasi') bg-primary
                                                @elseif($item['label'] == 'Pengaduan Masyarakat') bg-warning
                                                @elseif($item['label'] == 'Permohonan Sarana Prasarana') bg-success
                                                @elseif($item['label'] == 'Pernyataan Keberatan') bg-info
                                                @else bg-danger @endif" 
                                                style="width: {{ $item['percentage'] }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Requests Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary border-transparent">
                    <h3 class="card-title text-white">
                        <i class="fas fa-clock"></i> Permintaan Terbaru
                        <span id="totalFiltered" class="badge badge-light ml-2" style="display: none;"></span>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
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
                            <tbody id="permintaanTableBody">
                                @forelse($permintaanTerbaru as $item)
                                    <tr>
                                        <td table-data-label='Tanggal' class="text-center">
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                        </td>
                                        <td table-data-label='Nama Pemohon' class="text-center">
                                            {{ $item->nama ?? 'Tidak Diketahui' }}
                                        </td>
                                        <td table-data-label='Status' class="text-center">
                                            @php
                                                $badgeClass = match (strtolower($item->status)) {
                                                    'masuk' => 'warning',
                                                    'diproses' => 'info',
                                                    'disetujui' => 'success',
                                                    'ditolak' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td table-data-label='Jenis Permohonan' class="text-center">
                                            {{ $item->jenis }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-inbox"></i> Tidak ada data permintaan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Alternative Button Implementation -->
                <div class="card-footer clearfix">
                    <a href="#" class="btn btn-sm btn-info float-right" id="lihatSemuaBtn" onclick="handleLihatSemua(event)">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Chart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-chart-pie"></i> Status Pengaduan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
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
                                <li><i class="far fa-circle text-primary"></i> Masuk</li>
                                <li><i class="far fa-circle text-warning"></i> Diproses</li>
                                <li><i class="far fa-circle text-success"></i> Disetujui</li>
                                <li><i class="far fa-circle text-danger"></i> Ditolak</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-0">
                    <ul class="nav nav-pills flex-column" id="statistikFooter">
                        <li class="nav-item">
                            <div class="nav-link">
                                Total Pengaduan
                                <span class="float-right text-primary">
                                    <strong id="totalPengaduan">{{ $statistik['periode']['pengajuan_total'] ?? 0 }}</strong>
                                </span>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link">
                                Periode
                                <span class="float-right text-muted">
                                    {{ $statistik['periode']['tahun'] ?? date('Y') }}
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Lihat Semua -->
    <div class="modal fade" id="lihatSemuaModal" tabindex="-1" role="dialog" aria-labelledby="lihatSemuaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lihatSemuaModalLabel">
                        <i class="fas fa-list"></i> Semua Permintaan Data
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="semuaDataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Pemohon</th>
                                    <th>Status</th>
                                    <th>Jenis Permohonan</th>
                                </tr>
                            </thead>
                            <tbody id="semuaDataTableBody">
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Chart.js -->
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token untuk semua AJAX requests - PERBAIKAN UTAMA
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // DEBUG: Cek apakah CSRF token ada
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            console.log('CSRF Token:', csrfToken ? 'Found' : 'NOT FOUND');
            
            if (!csrfToken) {
                console.error('CSRF token not found! Make sure meta tag is present in layout.');
            }

            // DEBUG: Log data yang diterima dari server
            console.log('Chart Data from Server:', @json($chartData ?? []));
            console.log('Statistics from Server:', @json($statistik ?? []));
            console.log('Permission Data from Server:', @json($permintaanTerbaru ?? []));

            @php
                $defaultChartData = [
                    'labels' => [],
                    'datasets' => []
                ];
                
                $defaultStatistik = [
                    'periode' => [
                        'pengajuan_total' => 0,
                        'pengajuan_diterima' => 0,
                        'pengajuan_ditolak' => 0,
                        'pengajuan_diproses' => 0
                    ]
                ];
            @endphp
            
            // AMBIL DATA REAL DARI CONTROLLER
            let currentChartData = @json($chartData ?? $defaultChartData);
            let currentStatistik = @json($statistik ?? $defaultStatistik);
            
            // Set initial filters dari URL parameters
            let currentFilters = {
                startDate: '{{ request("startDate") }}',
                endDate: '{{ request("endDate") }}',
                jenisInformasi: '{{ request("jenisInformasi") }}'
            };
            
            let salesChart, pieChart;

            // Inisialisasi chart dengan data real
            initializeCharts();
            
            // Show initial filter info if there are URL parameters
            if (currentFilters.startDate || currentFilters.endDate || currentFilters.jenisInformasi) {
                showFilterInfo(currentFilters, {{ $permintaanTerbaru->count() }});
            }
            
            function initializeCharts() {
                try {
                    console.log('Initializing charts with data:', currentChartData);

                    // Sales Chart (Bar Chart) - Fix untuk Chart.js versi baru
                    const salesChartCanvas = document.getElementById('salesChart');
                    if (salesChartCanvas) {
                        const salesChartCtx = salesChartCanvas.getContext('2d');
                        
                        const salesChartData = {
                            labels: currentChartData.labels || [],
                            datasets: []
                        };

                        // Warna untuk setiap dataset
                        const colors = [
                            'rgba(60,141,188,0.9)',   // primary
                            'rgba(255,193,7,0.9)',    // warning
                            'rgba(40,167,69,0.9)',    // success
                            'rgba(23,162,184,0.9)',   // info
                            'rgba(220,53,69,0.9)'     // danger
                        ];

                        let colorIndex = 0;
                        if (currentChartData.datasets) {
                            for (const [label, data] of Object.entries(currentChartData.datasets)) {
                                salesChartData.datasets.push({
                                    label: label,
                                    backgroundColor: colors[colorIndex % colors.length],
                                    borderColor: colors[colorIndex % colors.length],
                                    data: data
                                });
                                colorIndex++;
                            }
                        }

                        console.log('Sales Chart Data:', salesChartData);

                        // Destroy existing chart if exists
                        if (salesChart) {
                            salesChart.destroy();
                        }

                        salesChart = new Chart(salesChartCtx, {
                            type: 'bar',
                            data: salesChartData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {  // PERBAIKAN: Chart.js v3+ menggunakan 'y' bukan 'yAxes'
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        console.log('Bar chart created successfully');
                    }

                    // Pie Chart - Fix untuk Chart.js versi baru
                    const pieChartCanvas = document.getElementById('pieChart');
                    if (pieChartCanvas) {
                        const pieChartCtx = pieChartCanvas.getContext('2d');
                        const periode = currentStatistik.periode || {};
                        const totalMasuk = Math.max((periode.pengajuan_total || 0) - (periode.pengajuan_diterima || 0) - (periode.pengajuan_ditolak || 0) - (periode.pengajuan_diproses || 0), 0);
                        
                        const pieData = {
                            labels: ['Masuk', 'Diproses', 'Disetujui', 'Ditolak'],
                            datasets: [{
                                data: [
                                    totalMasuk,
                                    periode.pengajuan_diproses || 0,
                                    periode.pengajuan_diterima || 0,
                                    periode.pengajuan_ditolak || 0
                                ],
                                backgroundColor: [
                                    '#007bff', // primary
                                    '#ffc107', // warning
                                    '#28a745', // success
                                    '#dc3545'  // danger
                                ]
                            }]
                        };

                        console.log('Pie Chart Data:', pieData);

                        // Destroy existing chart if exists
                        if (pieChart) {
                            pieChart.destroy();
                        }

                        pieChart = new Chart(pieChartCtx, {
                            type: 'doughnut',
                            data: pieData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {  // PERBAIKAN: Chart.js v3+ menggunakan 'plugins'
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });

                        console.log('Pie chart created successfully');
                    }
                } catch (error) {
                    console.error('Error initializing charts:', error);
                }
            }

            // Filter form handler dengan perbaikan
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    startDate: $('#startDate').val(),
                    endDate: $('#endDate').val(),
                    jenisInformasi: $('#jenisInformasi').val()
                };

                // Simpan filter saat ini
                currentFilters = formData;

                console.log('Filter form data:', formData);

                // Validasi tanggal
                if (formData.startDate && formData.endDate && formData.startDate > formData.endDate) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Validasi',
                            text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir!'
                        });
                    } else {
                        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
                    }
                    return;
                }

                // Show loading
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Memfilter Data...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }

                // AJAX request untuk filter
                $.ajax({
                    url: '{{ route("sisfo.dashboard.admin.filter") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Filter response:', response);
                        
                        if (response.success) {
                            // Update table
                            updateTable(response.data.permintaan_terbaru);
                            
                            // Update statistics
                            updateStatistics(response.data.statistik);
                            
                            // Update distribution
                            updateDistribution(response.data.distribution_data);
                            
                            // Update charts
                            currentChartData = response.data.chart_data;
                            currentStatistik = response.data.statistik;
                            initializeCharts();

                            // Show filter info
                            showFilterInfo(formData, response.data.total_filtered);

                            if (typeof Swal !== 'undefined') {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Filter Berhasil',
                                    text: `Ditemukan ${response.data.total_filtered} data`,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Terjadi kesalahan saat memfilter data'
                                });
                            } else {
                                alert(response.message || 'Terjadi kesalahan saat memfilter data');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Filter error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: `Terjadi kesalahan saat memfilter data: ${error} (Status: ${xhr.status})`
                            });
                        } else {
                            alert('Terjadi kesalahan saat memfilter data: ' + error);
                        }
                    }
                });
            });

            // Reset filter handler
            $('#resetFilter').on('click', function() {
                $('#filterForm')[0].reset();
                currentFilters = {
                    startDate: '',
                    endDate: '',
                    jenisInformasi: ''
                };
                
                // Redirect ke halaman tanpa parameter
                window.location.href = '{{ url("/dashboardADM") }}';
            });

            // Lihat Semua handler - PERBAIKAN UTAMA
            $('#lihatSemuaBtn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Lihat Semua button clicked');
                console.log('Current filters:', currentFilters);
                
                // Cek CSRF token sebelum AJAX
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                if (!csrfToken) {
                    console.error('CSRF token not found!');
                    alert('Error: CSRF token tidak ditemukan. Refresh halaman dan coba lagi.');
                    return;
                }
                
                const params = {
                    startDate: currentFilters.startDate || '',
                    endDate: currentFilters.endDate || '',
                    jenisInformasi: currentFilters.jenisInformasi || '',
                    limit: 100
                };

                console.log('AJAX params:', params);

                // Show loading
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Memuat Data...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }

                $.ajax({
                    url: '{{ route("sisfo.dashboard.admin.lihat-semua") }}',
                    method: 'GET',
                    data: params,
                    timeout: 30000, // 30 seconds timeout
                    beforeSend: function(xhr) {
                        // Pastikan CSRF token dikirim
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    },
                    success: function(response) {
                        console.log('Lihat Semua response:', response);
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                        }

                        if (response && response.success) {
                            updateModalTable(response.data);
                            $('#lihatSemuaModal').modal('show');
                        } else {
                            const errorMessage = response ? (response.message || 'Terjadi kesalahan saat memuat data') : 'Response tidak valid';
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage
                                });
                            } else {
                                alert(errorMessage);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Lihat Semua error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: `Terjadi kesalahan saat memuat data: ${error} (Status: ${xhr.status})`
                            });
                        } else {
                            alert(`Terjadi kesalahan saat memuat data: ${error}`);
                        }
                    }
                });
            });

            function updateTable(data) {
                const tbody = $('#permintaanTableBody');
                tbody.empty();

                console.log('Updating table with data:', data);

                if (!data || data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                <i class="fas fa-inbox"></i> Tidak ada data yang sesuai dengan filter.
                            </td>
                        </tr>
                    `);
                } else {
                    data.forEach(function(item) {
                        const tanggal = new Date(item.tanggal).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        const badgeClass = getBadgeClass(item.status);
                        
                        tbody.append(`
                            <tr>
                                <td table-data-label='Tanggal' class="text-center">${tanggal}</td>
                                <td table-data-label='Nama Pemohon' class="text-center">${item.nama || 'Tidak Diketahui'}</td>
                                <td table-data-label='Status' class="text-center">
                                    <span class="badge badge-${badgeClass}">
                                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                    </span>
                                </td>
                                <td table-data-label='Jenis Permohonan' class="text-center">${item.jenis}</td>
                            </tr>
                        `);
                    });
                }
            }

            function updateModalTable(data) {
                const tbody = $('#semuaDataTableBody');
                tbody.empty();

                console.log('Updating modal table with data:', data);

                if (!data || data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fas fa-inbox"></i> Tidak ada data.
                            </td>
                        </tr>
                    `);
                } else {
                    data.forEach(function(item, index) {
                        const tanggal = new Date(item.tanggal).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        const badgeClass = getBadgeClass(item.status);
                        
                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${tanggal}</td>
                                <td>${item.nama || 'Tidak Diketahui'}</td>
                                <td>
                                    <span class="badge badge-${badgeClass}">
                                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                    </span>
                                </td>
                                <td>${item.jenis}</td>
                            </tr>
                        `);
                    });
                }
            }

            function updateDistribution(distributionData) {
                const container = $('#distributionContainer');
                container.empty();

                if (!distributionData || distributionData.length === 0) {
                    container.append('<p class="text-muted text-center">Tidak ada data distribusi.</p>');
                    return;
                }

                distributionData.forEach(function(item) {
                    let bgClass = '';
                    switch(item.label) {
                        case 'Permohonan Informasi':
                            bgClass = 'bg-primary';
                            break;
                        case 'Pengaduan Masyarakat':
                            bgClass = 'bg-warning';
                            break;
                        case 'Permohonan Sarana Prasarana':
                            bgClass = 'bg-success';
                            break;
                        case 'Pernyataan Keberatan':
                            bgClass = 'bg-info';
                            break;
                        default:
                            bgClass = 'bg-danger';
                    }

                    container.append(`
                        <div class="progress-group">
                            <span class="progress-text">${item.label}</span>
                            <span class="float-right"><b>${item.total}</b>/${item.max}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar ${bgClass}" style="width: ${item.percentage}%"></div>
                            </div>
                        </div>
                    `);
                });
            }

            function updateStatistics(statistik) {
                const periode = statistik.periode || {};
                
                console.log('Updating statistics:', periode);
                
                // Update boxes
                const boxes = $('#statisticBoxes .statistic-value');
                if (boxes.length >= 4) {
                    $(boxes[0]).text(periode.pengajuan_total || 0); // Total
                    $(boxes[1]).text(periode.pengajuan_diproses || 0); // Diproses
                    $(boxes[2]).text(periode.pengajuan_diterima || 0); // Disetujui
                    $(boxes[3]).text(periode.pengajuan_ditolak || 0); // Ditolak
                }

                // Update footer
                $('#totalPengaduan').text(periode.pengajuan_total || 0);
            }

            function getBadgeClass(status) {
                switch (status.toLowerCase()) {
                    case 'masuk': return 'warning';
                    case 'diproses': return 'info';
                    case 'disetujui': return 'success';
                    case 'ditolak': return 'danger';
                    default: return 'secondary';
                }
            }

            function showFilterInfo(formData, totalFiltered) {
                let filterText = [];
                
                if (formData.startDate) {
                    filterText.push(`Dari: ${formData.startDate}`);
                }
                if (formData.endDate) {
                    filterText.push(`Sampai: ${formData.endDate}`);
                }
                if (formData.jenisInformasi) {
                    const jenisSelect = document.getElementById('jenisInformasi');
                    const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
                    const jenisText = selectedOption ? selectedOption.text : formData.jenisInformasi;
                    filterText.push(`Jenis: ${jenisText}`);
                }

                if (filterText.length > 0) {
                    $('#filterInfo').text(`Filter: ${filterText.join(', ')}`).show();
                    $('#totalFiltered').text(`${totalFiltered} data`).show();
                } else {
                    $('#filterInfo').hide();
                    $('#totalFiltered').hide();
                }
            }
        });
    </script>
@endsection
