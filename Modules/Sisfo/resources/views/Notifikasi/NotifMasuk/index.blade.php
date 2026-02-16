@extends('sisfo::layouts.template')

@section('content')
    <?php
    use Modules\Sisfo\App\Models\Log\NotifMasukModel;

    // Hitung jumlah notifikasi untuk setiap kategori
    $jumlahNotifikasiPermohonanInformasi = NotifMasukModel::where('notif_masuk_kategori', 'E-Form Permohonan Informasi')->whereNull('notif_masuk_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiPernyataanKeberatan = NotifMasukModel::where('notif_masuk_kategori', 'E-Form Pernyataan Keberatan')->whereNull('notif_masuk_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiPengaduanMasyarakat = NotifMasukModel::where('notif_masuk_kategori', 'E-Form Pengaduan Masyarakat')->whereNull('notif_masuk_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiPermohonanPerawatan = NotifMasukModel::where('notif_masuk_kategori', 'E-Form Permohonan Perawatan Sarana Prasarana')->whereNull('notif_masuk_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiwbs = NotifMasukModel::where('notif_masuk_kategori', 'E-Form Whistle Blowing System')->whereNull('notif_masuk_dibaca_tgl')->where('isDeleted', 0)->count();
    ?>

    <div class="row">
        <!-- Permohonan Informasi -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-masuk/detailData/PI') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-info"><i class="fas fa-envelope text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Permohonan Informasi</span>
                        <span class="info-box-number">{{ $jumlahNotifikasiPermohonanInformasi }} Notifikasi Baru</span>
                    </div>
                    @if ($jumlahNotifikasiPermohonanInformasi > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahNotifikasiPermohonanInformasi }}</span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Pernyataan Keberatan -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-masuk/detailData/PK') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pernyataan Keberatan</span>
                        <span class="info-box-number">{{ $jumlahNotifikasiPernyataanKeberatan }} Notifikasi Baru</span>
                    </div>
                    @if ($jumlahNotifikasiPernyataanKeberatan > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahNotifikasiPernyataanKeberatan }}</span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Pengaduan Masyarakat -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-masuk/detailData/PM') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-danger"><i class="fas fa-comments text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pengaduan Masyarakat</span>
                        <span class="info-box-number">{{ $jumlahNotifikasiPengaduanMasyarakat }} Notifikasi Baru</span>
                    </div>
                    @if ($jumlahNotifikasiPengaduanMasyarakat > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahNotifikasiPengaduanMasyarakat }}</span>
                    @endif
                </div>
            </a>
        </div>

        <!-- WBS -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-masuk/detailData/WBS') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-success"><i class="fas fa-bullhorn text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Whistle Blowing System</span>
                        <span class="info-box-number">{{ $jumlahNotifikasiwbs }} Notifikasi Baru</span>
                    </div>
                    @if ($jumlahNotifikasiwbs > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahNotifikasiwbs }}</span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Permohonan Perawatan -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-masuk/detailData/PP') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-primary"><i class="fas fa-tools text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Permohonan Perawatan</span>
                        <span class="info-box-number">{{ $jumlahNotifikasiPermohonanPerawatan }} Notifikasi Baru</span>
                    </div>
                    @if ($jumlahNotifikasiPermohonanPerawatan > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahNotifikasiPermohonanPerawatan }}</span>
                    @endif
                </div>
            </a>
        </div>
    </div>

    <style>
        .info-box {
            transition: background-color 0.3s ease, border 0.3s ease;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            position: relative;
        }

        .info-box-icon-wrapper {
            position: relative;
            display: inline-block;
        }

        .notification-badge-menu {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            padding: 6px;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: bold;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
        }

        .info-box {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .info-box:hover {
            background-color: #f0f9ff;
            border: 1px solid #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
