@extends('sisfo::layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengajuan Permohonan</h3>
    </div>   
    <div class="card-body" style="padding-top: 10px;">
        <div class="row text-center">
            <!-- Permohonan Informasi -->
            <div class="col-md-4">
                <a href="{{ url($daftarPengajuanUrl . '/permohonan-informasi') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-info-circle fa-2x"></i>
                    <h5>Daftar Verifikasi Pengajuan Permohonan Informasi</h5>
                    @if($jumlahDaftarVerifPermohonanInformasi > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahDaftarVerifPermohonanInformasi }}</span>
                    @endif
                </a>
            </div>
            
            <!-- Pernyataan Keberatan -->
            <div class="col-md-4">
                <a href="{{ url($daftarPengajuanUrl . '/pernyataan-keberatan') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                    <h5>Daftar Verifikasi Pengajuan Pernyataan Keberatan</h5>
                    @if($jumlahDaftarVerifPernyataanKeberatan > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahDaftarVerifPernyataanKeberatan }}</span>
                    @endif
                </a>
            </div>
            
            <!-- Pengaduan Masyarakat -->
            <div class="col-md-4">
                <a href="{{ url($daftarPengajuanUrl . '/pengaduan-masyarakat') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-comments fa-2x"></i>
                    <h5>Daftar Verifikasi Pengajuan Pengaduan Masyarakat</h5>
                    @if($jumlahDaftarVerifPengaduanMasyarakat > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahDaftarVerifPengaduanMasyarakat }}</span>
                    @endif
                </a>
            </div>
            
            <!-- Whistle Blowing System -->
            <div class="col-md-4">
                <a href="{{ url($daftarPengajuanUrl . '/wbs') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-bullhorn fa-2x"></i>
                    <h5>Daftar Verifikasi Pengajuan Whistle Blowing System</h5>
                    @if($jumlahDaftarVerifWBS > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahDaftarVerifWBS }}</span>
                    @endif
                </a>
            </div>
            
            <!-- Permohonan Perawatan -->
            <div class="col-md-4">
                <a href="{{ url($daftarPengajuanUrl . '/permohonan-perawatan') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-tools fa-2x"></i>
                    <h5>Daftar Verifikasi Pengajuan Permohonan Perawatan Sarana Prasarana</h5>
                    @if($jumlahDaftarVerifPermohonanPerawatan > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahDaftarVerifPermohonanPerawatan }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-button {
        background-color: lightblue;
        border: 2px solid black;
        border-radius: 8px; 
        color: black; 
        text-decoration: none; 
        transition: background-color 0.3s, transform 0.3s; 
        position: relative; /* Untuk badge absolut dalam elemen ini */
    }

    .custom-button:hover {
        background-color: blue; 
        transform: scale(0.95);
        color: white; /* Warna ikon saat hover */
    }

    .notification-badge-menu {
        position: absolute;
        top: 5px; /* Atur posisi vertikal */
        right: 5px; /* Atur posisi horizontal */
        background-color: #dc3545; /* Warna merah */
        color: white; /* Warna teks */
        padding: 3px 8px; /* Spasi dalam */
        border-radius: 50%; /* Membulatkan badge */
        font-size: 20px; /* Ukuran font */
        font-weight: bold; /* Tebal */
    }
</style>
@endsection