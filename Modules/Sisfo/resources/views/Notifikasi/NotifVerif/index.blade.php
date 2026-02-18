@extends('sisfo::layouts.template')

@section('content')
    <?php
    use Modules\Sisfo\App\Models\Log\NotifVerifModel;

    // Hitung jumlah notifikasi untuk setiap kategori
    $jumlahNotifikasiPermohonanInformasi = NotifVerifModel::where('notif_verif_kategori', 'E-Form Permohonan Informasi')->whereNull('notif_verif_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiPernyataanKeberatan = NotifVerifModel::where('notif_verif_kategori', 'E-Form Pernyataan Keberatan')->whereNull('notif_verif_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiPengaduanMasyarakat = NotifVerifModel::where('notif_verif_kategori', 'E-Form Pengaduan Masyarakat')->whereNull('notif_verif_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiPermohonanPerawatan = NotifVerifModel::where('notif_verif_kategori', 'E-Form Permohonan Perawatan Sarana Prasarana')->whereNull('notif_verif_dibaca_tgl')->where('isDeleted', 0)->count();
    $jumlahNotifikasiwbs = NotifVerifModel::where('notif_verif_kategori', 'E-Form Whistle Blowing System')->whereNull('notif_verif_dibaca_tgl')->where('isDeleted', 0)->count();
    ?>

    <div class="row">
        <!-- Permohonan Informasi -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-verifikasi/detailData/1') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-info"><i class="fas fa-envelope text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">Permohonan Informasi (Verifikasi)</span>
                    </div>

                    @if ($jumlahNotifikasiPermohonanInformasi > 0)
                        <span
                            class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1 rounded-circle notification-badge-menu"
                            style="font-size: 0.75rem;">
                            {{ $jumlahNotifikasiPermohonanInformasi }}
                        </span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Pernyataan Keberatan -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-verifikasi/detailData/2') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">Pernyataan Keberatan (Verifikasi)</span>
                    </div>

                    @if ($jumlahNotifikasiPernyataanKeberatan > 0)
                        <span
                            class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1 rounded-circle notification-badge-menu"
                            style="font-size: 0.75rem;">
                            {{ $jumlahNotifikasiPernyataanKeberatan }}
                        </span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Pengaduan Masyarakat -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-verifikasi/detailData/3') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-danger"><i class="fas fa-comments text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">Pengaduan Masyarakat (Verifikasi)</span>
                    </div>

                    @if ($jumlahNotifikasiPengaduanMasyarakat > 0)
                        <span
                            class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1 rounded-circle notification-badge-menu"
                            style="font-size: 0.75rem;">
                            {{ $jumlahNotifikasiPengaduanMasyarakat }}
                        </span>
                    @endif
                </div>
            </a>
        </div>

        <!-- WBS -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-verifikasi/detailData/4') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-success"><i class="fas fa-bullhorn text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">Whistle Blowing System (Verifikasi)</span>
                    </div>

                    @if ($jumlahNotifikasiwbs > 0)
                        <span
                            class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1 rounded-circle notification-badge-menu"
                            style="font-size: 0.75rem;">
                            {{ $jumlahNotifikasiwbs }}
                        </span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Permohonan Perawatan -->
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('notifikasi-verifikasi/detailData/5') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-primary"><i class="fas fa-tools text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">Permohonan Perawatan (Verifikasi)</span>
                    </div>

                    @if ($jumlahNotifikasiPermohonanPerawatan > 0)
                        <span
                            class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1 rounded-circle notification-badge-menu"
                            style="font-size: 0.75rem;">
                            {{ $jumlahNotifikasiPermohonanPerawatan }}
                        </span>
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
        }

        .info-box {
            background-color: #ffffff;
        }

        .info-box:hover {
            background-color: #f8f9fa;
            border: 1px solid #007bff;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
