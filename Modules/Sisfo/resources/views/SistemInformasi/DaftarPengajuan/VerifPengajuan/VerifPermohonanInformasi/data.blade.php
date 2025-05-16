@foreach($permohonanInformasi as $permohonan)
<div class="card shadow-sm mb-4">
    <!-- Header Card dengan Status yang Lebih Menonjol -->
    <div class="card-header bg-light py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-alt mr-2"></i>Permohonan #{{ $permohonan->permohonan_informasi_id }}
                </h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    @switch($permohonan->pi_status)
                        @case('Masuk')
                            <span class="badge badge-pill badge-primary px-3 py-2"><i class="fas fa-inbox mr-1"></i> Masuk</span>
                            @break
                        @case('Verifikasi')
                            <span class="badge badge-pill badge-warning px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Verifikasi</span>
                            @break
                        @case('Disetujui')
                            <span class="badge badge-pill badge-success px-3 py-2"><i class="fas fa-check-double mr-1"></i> Disetujui</span>
                            @break
                        @case('Ditolak')
                            <span class="badge badge-pill badge-danger px-3 py-2"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                            @break
                        @default
                            <span class="badge badge-pill badge-secondary px-3 py-2">{{ $permohonan->pi_status }}</span>
                    @endswitch
                    
                    @if($permohonan->pi_sudah_dibaca)
                        <span class="badge badge-pill badge-success px-3 py-2"><i class="fas fa-eye mr-1"></i> Sudah Dibaca</span>
                    @else
                        <span class="badge badge-pill badge-info px-3 py-2"><i class="fas fa-eye-slash mr-1"></i> Belum Dibaca</span>
                    @endif
                </div>
                <div class="text-muted">
                    <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($permohonan->created_at)->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Sistem Tab untuk Informasi Berbeda -->
        <ul class="nav nav-tabs mb-3" id="myTab-{{ $permohonan->permohonan_informasi_id }}" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab-{{ $permohonan->permohonan_informasi_id }}" data-toggle="tab" 
                   href="#general-{{ $permohonan->permohonan_informasi_id }}" role="tab" aria-controls="general" aria-selected="true">
                   <i class="fas fa-info-circle mr-1"></i> Informasi Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="requester-tab-{{ $permohonan->permohonan_informasi_id }}" data-toggle="tab" 
                   href="#requester-{{ $permohonan->permohonan_informasi_id }}" role="tab" aria-controls="requester" aria-selected="false">
                   <i class="fas fa-user mr-1"></i> Data Pemohon
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="status-tab-{{ $permohonan->permohonan_informasi_id }}" data-toggle="tab" 
                   href="#status-{{ $permohonan->permohonan_informasi_id }}" role="tab" aria-controls="status" aria-selected="false">
                   <i class="fas fa-tasks mr-1"></i> Status & Tanggal
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent-{{ $permohonan->permohonan_informasi_id }}">
            <!-- Tab Informasi Umum -->
            <div class="tab-pane fade show active" id="general-{{ $permohonan->permohonan_informasi_id }}" role="tabpanel" 
                aria-labelledby="general-tab-{{ $permohonan->permohonan_informasi_id }}">
                <div class="row">
                    <!-- Informasi Permohonan -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0"><i class="fas fa-clipboard-list mr-1"></i> Informasi Permohonan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Kategori Pemohon</td>
                                        <td>: {{ $permohonan->pi_kategori_pemohon }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Kategori Aduan</td>
                                        <td>: {{ $permohonan->pi_kategori_aduan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Bukti Aduan</td>
                                        <td>: 
                                            @if($permohonan->pi_bukti_aduan)
                                                <a href="{{ asset('storage/' . $permohonan->pi_bukti_aduan) }}" target="_blank" 
                                                   class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fas fa-file-download"></i> Lihat Dokumen
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Permohonan -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0"><i class="fas fa-file-alt mr-1"></i> Detail Permohonan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Informasi yang Dibutuhkan</td>
                                        <td>: {{ $permohonan->pi_informasi_yang_dibutuhkan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Alasan Permohonan</td>
                                        <td>: {{ $permohonan->pi_alasan_permohonan_informasi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Alamat Sumber</td>
                                        <td>: {{ $permohonan->pi_alamat_sumber_informasi }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Data Pemohon -->
            <div class="tab-pane fade" id="requester-{{ $permohonan->permohonan_informasi_id }}" role="tabpanel" 
                aria-labelledby="requester-tab-{{ $permohonan->permohonan_informasi_id }}">
                
                @if($permohonan->pi_kategori_pemohon == 'Diri Sendiri' && $permohonan->PiDiriSendiri)
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0"><i class="fas fa-user mr-1"></i> Informasi Pemohon (Diri Sendiri)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="35%" class="font-weight-bold">Nama</td>
                                            <td>: {{ $permohonan->PiDiriSendiri->pi_nama_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Alamat</td>
                                            <td>: {{ $permohonan->PiDiriSendiri->pi_alamat_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">No HP</td>
                                            <td>: {{ $permohonan->PiDiriSendiri->pi_no_hp_pengguna }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="35%" class="font-weight-bold">Email</td>
                                            <td>: {{ $permohonan->PiDiriSendiri->pi_email_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">KTP</td>
                                            <td>: 
                                                <a href="{{ asset('storage/' . $permohonan->PiDiriSendiri->pi_upload_nik_pengguna) }}" 
                                                   target="_blank" class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fas fa-id-card"></i> Lihat KTP
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($permohonan->pi_kategori_pemohon == 'Orang Lain' && $permohonan->PiOrangLain)
                    <!-- Kode untuk Orang Lain (disingkat untuk menjaga panjang kode) -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="m-0"><i class="fas fa-user-edit mr-1"></i> Informasi Penginput</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Nama Penginput</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_nama_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Alamat Penginput</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_alamat_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">No HP Penginput</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_no_hp_pengguna_penginput }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Email Penginput</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_email_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">KTP Penginput</td>
                                                    <td>: 
                                                        <a href="{{ asset('storage/' . $permohonan->PiOrangLain->pi_upload_nik_pengguna_penginput) }}" 
                                                           target="_blank" class="btn btn-sm btn-info rounded-pill">
                                                            <i class="fas fa-id-card"></i> Lihat KTP
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header bg-info text-white">
                                    <h6 class="m-0"><i class="fas fa-user-check mr-1"></i> Informasi Penerima Informasi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Nama Penerima</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_nama_pengguna_informasi }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Alamat Penerima</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_alamat_pengguna_informasi }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">No HP Penerima</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_no_hp_pengguna_informasi }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Email Penerima</td>
                                                    <td>: {{ $permohonan->PiOrangLain->pi_email_pengguna_informasi }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">KTP Penerima</td>
                                                    <td>: 
                                                        <a href="{{ asset('storage/' . $permohonan->PiOrangLain->pi_upload_nik_pengguna_informasi) }}" 
                                                           target="_blank" class="btn btn-sm btn-info rounded-pill">
                                                            <i class="fas fa-id-card"></i> Lihat KTP
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @elseif($permohonan->pi_kategori_pemohon == 'Organisasi' && $permohonan->PiOrganisasi)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="m-0"><i class="fas fa-building mr-1"></i> Informasi Organisasi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Nama Organisasi</td>
                                                    <td>: {{ $permohonan->PiOrganisasi->pi_nama_organisasi }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">No. Telp Organisasi</td>
                                                    <td>: {{ $permohonan->PiOrganisasi->pi_no_telp_organisasi }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Email/Medsos Organisasi</td>
                                                    <td>: {{ $permohonan->PiOrganisasi->pi_email_atau_medsos_organisasi }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header bg-info text-white">
                                    <h6 class="m-0"><i class="fas fa-user-tie mr-1"></i> Informasi Narahubung</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Nama Narahubung</td>
                                                    <td>: {{ $permohonan->PiOrganisasi->pi_nama_narahubung }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">No. Telp Narahubung</td>
                                                    <td>: {{ $permohonan->PiOrganisasi->pi_no_telp_narahubung }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Identitas Narahubung</td>
                                                    <td>: 
                                                        <a href="{{ asset('storage/' . $permohonan->PiOrganisasi->pi_identitas_narahubung) }}" 
                                                           target="_blank" class="btn btn-sm btn-info rounded-pill">
                                                            <i class="fas fa-id-card"></i> Lihat Identitas
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Tab Status & Tanggal -->
            <div class="tab-pane fade" id="status-{{ $permohonan->permohonan_informasi_id }}" role="tabpanel" 
                aria-labelledby="status-tab-{{ $permohonan->permohonan_informasi_id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0"><i class="fas fa-chart-line mr-1"></i> Status Permohonan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="30%" class="font-weight-bold">Status</td>
                                        <td>: 
                                            @switch($permohonan->pi_status)
                                                @case('Masuk')
                                                    <span class="badge badge-primary">Masuk</span>
                                                    @break
                                                @case('Verifikasi')
                                                    <span class="badge badge-warning">Verifikasi</span>
                                                    @break
                                                @case('Disetujui')
                                                    <span class="badge badge-success">Disetujui</span>
                                                    @break
                                                @case('Ditolak')
                                                    <span class="badge badge-danger">Ditolak</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $permohonan->pi_status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Dibaca</td>
                                        <td>: 
                                            @if($permohonan->pi_sudah_dibaca)
                                                <span class="badge badge-success">Sudah Dibaca</span>
                                            @else
                                                <span class="badge badge-info">Belum Ditandai Dibaca</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0"><i class="fas fa-calendar-alt mr-1"></i> Tanggal</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Tanggal Dibuat</td>
                                        <td>: {{ $permohonan->created_at ? $permohonan->created_at->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Dibaca</td>
                                        <td>: {{ $permohonan->pi_tanggal_dibaca ? \Carbon\Carbon::parse($permohonan->pi_tanggal_dibaca)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="action-container mt-4 pt-3 border-top">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap">
                        @if($permohonan->pi_status == 'Masuk')
                            <button type="button" class="btn btn-success btn-sm mr-2 mb-2" onclick="showApproveModal({{ $permohonan->permohonan_informasi_id }})">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button type="button" class="btn btn-danger btn-sm mr-2 mb-2" onclick="showDeclineModal({{ $permohonan->permohonan_informasi_id }})">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        @endif

                        <button class="btn btn-info btn-sm mr-2 mb-2" onclick="tandaiDibaca({{ $permohonan->permohonan_informasi_id }}, '{{ $permohonan->pi_status }}', {{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }})" 
                            data-status="{{ $permohonan->pi_status }}" data-dibaca="{{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-book-reader"></i> Tandai Dibaca
                        </button>
                        
                        <button class="btn btn-warning btn-sm mb-2" onclick="hapusPermohonan({{ $permohonan->permohonan_informasi_id }}, {{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }})" 
                            data-dibaca="{{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach