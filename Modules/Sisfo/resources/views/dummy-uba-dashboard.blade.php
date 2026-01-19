<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard UBA - PPID Polinema</title>
    
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f4f6f9;
        }
        
        .page-header {
            background: linear-gradient(135deg, #0E1F43 0%, #1a3a5c 100%);
            color: white;
            padding: 25px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .page-header h1 {
            font-family: 'K2D', sans-serif;
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
        }
        
        .page-header .logo-text {
            color: #FFC030;
        }
        
        .page-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        .container-fluid {
            padding: 0 30px 30px 30px;
        }
        
        .small-box {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: relative;
            display: block;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .small-box .inner {
            padding: 10px;
            position: relative;
            z-index: 10;
        }
        
        .small-box .inner h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        
        .small-box .inner p {
            font-size: 1.1rem;
            margin: 0;
        }
        
        .small-box .icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 80px;
            opacity: 0.25;
            z-index: 5;
        }
        
        .bg-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .bg-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }
        
        .bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529 !important;
        }
        
        .bg-warning .inner h3,
        .bg-warning .inner p {
            color: #212529 !important;
        }
        
        .bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .bg-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .bg-dark {
            background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            color: white;
            font-weight: 600;
            padding: 15px 20px;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        #tabelLogUBA {
            font-size: 0.9rem;
            width: 100%;
        }
        
        #tabelLogUBA thead th {
            color: white;
            vertical-align: middle;
            text-align: center;
            background-color: #343a40;
            border-color: #454d55;
            font-weight: 600;
        }
        
        #tabelLogUBA tbody td {
            vertical-align: middle;
            padding: 12px 8px;
        }
        
        .badge-durasi {
            font-size: 0.85rem;
            padding: 5px 10px;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .btn {
            border-radius: 5px;
        }
        
        .form-control {
            border-radius: 5px;
        }
        
        .card-tools {
            display: flex;
            gap: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .chart-container-tall {
            position: relative;
            height: 400px;
        }
    </style>
</head>
<body>
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1><i class="fas fa-chart-line"></i> Dashboard <span class="logo-text">User Behavior Analytics (UBA)</span></h1>
            <p><i class="fas fa-info-circle"></i> Monitoring aktivitas dan perilaku pengguna secara real-time</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3><i class="fas fa-filter"></i> Filter Data</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterTipeLog"><i class="fas fa-list"></i> Tipe Log</label>
                                    <select class="form-control" id="filterTipeLog">
                                        <option value="uba" selected>Log Analisis Perilaku (UBA)</option>
                                        <option value="transaksi">Log Transaksi</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterTanggalMulai"><i class="fas fa-calendar-alt"></i> Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="filterTanggalMulai" value="2025-01-13">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterTanggalAkhir"><i class="fas fa-calendar-check"></i> Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="filterTanggalAkhir" value="2025-01-19">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary" id="btnTerapkanFilter">
                                    <i class="fas fa-search"></i> Terapkan Filter
                                </button>
                                <button class="btn btn-secondary" id="btnResetFilter">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4" id="statisticsSection">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="totalSesi">245</h3>
                        <p>Total Sesi Login</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="rataRataDurasi">2.5<sup style="font-size: 20px">jam</sup></h3>
                        <p>Rata-rata Durasi Sesi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="userAktif">45</h3>
                        <p>User Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="jamPadat">14:00</h3>
                        <p>Jam Login Terpadat</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visualisasi Grafik -->
        <div class="row mb-4" id="grafikSection">
            <div class="col-lg-8 col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3><i class="fas fa-chart-area"></i> Tren Aktivitas Login Harian</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartLoginHarian"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-12">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3><i class="fas fa-chart-pie"></i> Distribusi Perangkat</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartPerangkat"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4" id="grafikSection2">
            <div class="col-lg-6 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3><i class="fas fa-chart-bar"></i> Top 10 User Teraktif</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-tall">
                            <canvas id="chartTopUsers"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 col-12">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3><i class="fas fa-clock"></i> Pola Login Per Jam</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-tall">
                            <canvas id="chartPolaJam"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Log UBA -->
        <div class="row" id="tabelUBASection">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3><i class="fas fa-table"></i> Detail Log Aktivitas Pengguna</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-success" id="btnExportExcel">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <button class="btn btn-sm btn-danger" id="btnExportPDF">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabelLogUBA" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pengguna</th>
                                        <th>Level</th>
                                        <th>Login</th>
                                        <th>Logout</th>
                                        <th>Durasi</th>
                                        <th>Browser</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                        <th>OS</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelLogBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
};

const dummyDataUBA = [
    {nama:"Gelby Firmansyah",level:"Super Administrator",login:"2025-01-19 08:15:23",logout:"2025-01-19 17:30:45",durasi:"9h 15m",browser:"Chrome 120",ip:"192.168.1.101",device:"Desktop",os:"Windows 11"},
    {nama:"Adelia Shaharani",level:"Administrator",login:"2025-01-19 09:00:12",logout:"2025-01-19 16:45:30",durasi:"7h 45m",browser:"Firefox 121",ip:"192.168.1.102",device:"Laptop",os:"Windows 10"},
    {nama:"Budi Santoso",level:"Verifikator",login:"2025-01-19 08:30:00",logout:"2025-01-19 15:20:15",durasi:"6h 50m",browser:"Edge 120",ip:"192.168.1.103",device:"Desktop",os:"Windows 11"},
    {nama:"Siti Nurhaliza",level:"Pimpinan",login:"2025-01-19 10:15:45",logout:"2025-01-19 14:30:20",durasi:"4h 15m",browser:"Safari 17",ip:"192.168.1.104",device:"MacBook",os:"macOS Sonoma"},
    {nama:"Ahmad Hidayat",level:"Administrator",login:"2025-01-19 07:45:00",logout:"2025-01-19 18:15:30",durasi:"10h 30m",browser:"Chrome 120",ip:"192.168.1.105",device:"Desktop",os:"Ubuntu 22.04"},
    {nama:"Dewi Lestari",level:"Verifikator",login:"2025-01-18 08:00:00",logout:"2025-01-18 17:00:00",durasi:"9h 0m",browser:"Chrome 120",ip:"192.168.1.106",device:"Laptop",os:"Windows 11"},
    {nama:"Eko Prasetyo",level:"Administrator",login:"2025-01-18 09:30:00",logout:"2025-01-18 16:30:00",durasi:"7h 0m",browser:"Firefox 121",ip:"192.168.1.107",device:"Desktop",os:"Windows 10"},
    {nama:"Fitri Handayani",level:"Pimpinan",login:"2025-01-17 10:00:00",logout:"2025-01-17 15:45:00",durasi:"5h 45m",browser:"Edge 120",ip:"192.168.1.108",device:"Laptop",os:"Windows 11"},
    {nama:"Hendra Wijaya",level:"Verifikator",login:"2025-01-17 08:15:00",logout:"2025-01-17 17:15:00",durasi:"9h 0m",browser:"Chrome 120",ip:"192.168.1.109",device:"Desktop",os:"Windows 11"},
    {nama:"Indah Permata",level:"Administrator",login:"2025-01-16 07:30:00",logout:"2025-01-16 16:30:00",durasi:"9h 0m",browser:"Safari 17",ip:"192.168.1.110",device:"MacBook",os:"macOS Sonoma"},
    {nama:"Joko Susilo",level:"Verifikator",login:"2025-01-16 09:00:00",logout:"2025-01-16 17:30:00",durasi:"8h 30m",browser:"Chrome 120",ip:"192.168.1.111",device:"Laptop",os:"Windows 10"},
    {nama:"Kartika Sari",level:"Pimpinan",login:"2025-01-15 10:30:00",logout:"2025-01-15 14:00:00",durasi:"3h 30m",browser:"Firefox 121",ip:"192.168.1.112",device:"Desktop",os:"Windows 11"},
    {nama:"Linda Maharani",level:"Administrator",login:"2025-01-15 08:00:00",logout:"2025-01-15 17:00:00",durasi:"9h 0m",browser:"Edge 120",ip:"192.168.1.113",device:"Laptop",os:"Windows 11"},
    {nama:"Muhammad Rizki",level:"Verifikator",login:"2025-01-14 07:45:00",logout:"2025-01-14 16:45:00",durasi:"9h 0m",browser:"Chrome 120",ip:"192.168.1.114",device:"Desktop",os:"Ubuntu 22.04"},
    {nama:"Nur Azizah",level:"Administrator",login:"2025-01-14 09:15:00",logout:"2025-01-14 17:15:00",durasi:"8h 0m",browser:"Safari 17",ip:"192.168.1.115",device:"MacBook",os:"macOS Sonoma"}
];

function muatDataTabel() {
    const tbody = document.getElementById('tabelLogBody');
    tbody.innerHTML = '';
    
    dummyDataUBA.forEach((data, index) => {
        const browserIcon = data.browser.toLowerCase().includes('chrome') ? 'chrome' : 
                           data.browser.toLowerCase().includes('firefox') ? 'firefox' : 
                           data.browser.toLowerCase().includes('safari') ? 'safari' : 'edge';
        const deviceIcon = data.device === 'Desktop' ? 'desktop' : 'laptop';
        
        tbody.innerHTML += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td><strong>${data.nama}</strong></td>
                <td><span class="badge badge-primary">${data.level}</span></td>
                <td><i class="fas fa-sign-in-alt text-success"></i> ${data.login}</td>
                <td><i class="fas fa-sign-out-alt text-danger"></i> ${data.logout}</td>
                <td><span class="badge badge-info badge-durasi">${data.durasi}</span></td>
                <td><i class="fab fa-${browserIcon}"></i> ${data.browser}</td>
                <td><code>${data.ip}</code></td>
                <td><i class="fas fa-${deviceIcon}"></i> ${data.device}</td>
                <td>${data.os}</td>
            </tr>
        `;
    });
    
    if ($.fn.DataTable.isDataTable('#tabelLogUBA')) {
        $('#tabelLogUBA').DataTable().destroy();
    }
    
    $('#tabelLogUBA').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
}

function createChartLoginHarian() {
    new Chart(document.getElementById('chartLoginHarian'), {
        type: 'line',
        data: {
            labels: ['13 Jan','14 Jan','15 Jan','16 Jan','17 Jan','18 Jan','19 Jan'],
            datasets: [{
                label: 'Jumlah Login',
                data: [32,45,38,52,41,48,55],
                borderColor: 'rgb(75,192,192)',
                backgroundColor: 'rgba(75,192,192,0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {display: true, position: 'top'},
                title: {display: true, text: 'Aktivitas Login 7 Hari Terakhir'}
            },
            scales: {y: {beginAtZero: true, ticks: {stepSize: 10}}}
        }
    });
}

function createChartPerangkat() {
    new Chart(document.getElementById('chartPerangkat'), {
        type: 'doughnut',
        data: {
            labels: ['Desktop','Laptop','MacBook','Tablet'],
            datasets: [{
                data: [120,85,30,10],
                backgroundColor: ['rgba(255,99,132,0.8)','rgba(54,162,235,0.8)','rgba(255,206,86,0.8)','rgba(75,192,192,0.8)'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {legend: {display: true, position: 'bottom'}}
        }
    });
}

function createChartTopUsers() {
    new Chart(document.getElementById('chartTopUsers'), {
        type: 'bar',
        data: {
            labels: ['Gelby F.','Ahmad H.','Dewi L.','Adelia S.','Linda M.','Eko P.','Hendra W.','Joko S.','Budi S.','Nur A.'],
            datasets: [{
                label: 'Total Jam Online',
                data: [45,42,38,35,33,30,28,26,24,22],
                backgroundColor: 'rgba(54,162,235,0.8)',
                borderColor: 'rgba(54,162,235,1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {display: false},
                title: {display: true, text: 'User dengan Waktu Online Terbanyak'}
            },
            scales: {x: {beginAtZero: true, ticks: {callback: v => v + ' jam'}}}
        }
    });
}

function createChartPolaJam() {
    new Chart(document.getElementById('chartPolaJam'), {
        type: 'bar',
        data: {
            labels: ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'],
            datasets: [{
                label: 'Jumlah Login',
                data: [15,32,28,22,18,12,25,42,38,30,20,8],
                backgroundColor: 'rgba(255,159,64,0.8)',
                borderColor: 'rgba(255,159,64,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {display: false},
                title: {display: true, text: 'Distribusi Login Berdasarkan Jam'}
            },
            scales: {y: {beginAtZero: true, ticks: {stepSize: 10}}}
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    muatDataTabel();
    createChartLoginHarian();
    createChartPerangkat();
    createChartTopUsers();
    createChartPolaJam();
    
    document.getElementById('btnTerapkanFilter').addEventListener('click', function() {
        const tipeLog = document.getElementById('filterTipeLog').value;
        if (tipeLog === 'uba') {
            document.getElementById('statisticsSection').style.display = 'flex';
            document.getElementById('grafikSection').style.display = 'flex';
            document.getElementById('grafikSection2').style.display = 'flex';
            document.getElementById('tabelUBASection').style.display = 'block';
            toastr.success('Filter diterapkan untuk Log Analisis Perilaku (UBA)');
        } else {
            document.getElementById('statisticsSection').style.display = 'none';
            document.getElementById('grafikSection').style.display = 'none';
            document.getElementById('grafikSection2').style.display = 'none';
            document.getElementById('tabelUBASection').style.display = 'none';
            toastr.info('Filter Log Transaksi - Fitur dalam pengembangan');
        }
    });
    
    document.getElementById('btnResetFilter').addEventListener('click', function() {
        document.getElementById('filterTipeLog').value = 'uba';
        document.getElementById('filterTanggalMulai').value = '2025-01-13';
        document.getElementById('filterTanggalAkhir').value = '2025-01-19';
        document.getElementById('statisticsSection').style.display = 'flex';
        document.getElementById('grafikSection').style.display = 'flex';
        document.getElementById('grafikSection2').style.display = 'flex';
        document.getElementById('tabelUBASection').style.display = 'block';
        toastr.success('Filter direset ke default');
    });
    
    document.getElementById('btnExportExcel').addEventListener('click', function() {
        toastr.success('Export ke Excel sedang diproses...');
    });
    
    document.getElementById('btnExportPDF').addEventListener('click', function() {
        toastr.success('Export ke PDF sedang diproses...');
    });
});
</script>
</body>
</html>
