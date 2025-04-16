@extends('sisfo::layouts.template')

@section('content')
    <?php

    use Modules\Sisfo\App\Models\Log\NotifAdminModel;

    // Hitung jumlah notifikasi untuk kategori 'permohonan'
    $jumlahNotifikasiPermohonan = NotifAdminModel::where('kategori_notif_admin', 'E-Form Permohonan Informasi')->whereNull('sudah_dibaca_notif_admin')->where('isDeleted', 0)->count();
    ?>


    <div class="row">


        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('Notifikasi/NotifAdmin/notifPI') }}">
                <div class="info-box" style="transition: 0.3s;">
                    <span class="info-box-icon bg-info"><i class="fas fa-envelope text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-number">Pengajuan Permohonan Informasi</span>
                    </div>

                    @if ($jumlahNotifikasiPermohonan > 0)
                        <span
                            class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1 rounded-circle notification-badge-menu"
                            style="font-size: 0.75rem;">
                            {{ $jumlahNotifikasiPermohonan }}
                        </span>
                    @endif
                </div>
            </a>
        </div>

        {{-- <div class="col-md-3 col-sm-6 col-12">
      <div class="info-box">
        <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Bookmarks</span>
          <span class="info-box-number">410</span>
        </div>
      </div>
    </div>


    <div class="col-md-3 col-sm-6 col-12">
      <div class="info-box">
        <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Uploads</span>
          <span class="info-box-number">13,648</span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
      <div class="info-box">
        <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Likes</span>
          <span class="info-box-number">93,139</span>
        </div>
      </div>
    </div>
  </div> --}}


        <style>
            .info-box {
                transition: background-color 0.3s ease, border 0.3s ease;
                border: 1px solid transparent;
                border-radius: 0.5rem;
            }

            .info-box-icon-wrapper {
                position: relative;
                display: inline-block;
            }

            .notification-badge-menu {
                position: absolute;
                top: -5px;
                right: -5px;
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
            }
        </style>
    @endsection
