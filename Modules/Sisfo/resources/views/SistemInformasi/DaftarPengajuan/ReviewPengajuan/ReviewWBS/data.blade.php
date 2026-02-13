@foreach($whistleBlowingSystem as $WBS)
<div class="card shadow-sm mb-4">
    <!-- Header Card dengan Status yang Lebih Menonjol -->
    <div class="card-header bg-light py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bullhorn mr-2"></i>Whistle Blowing System #{{ $WBS->wbs_id }}
                </h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    @switch($WBS->wbs_status)
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
                            <span class="badge badge-pill badge-secondary px-3 py-2">{{ $WBS->wbs_status }}</span>
                    @endswitch
                    
                    {{-- Update kondisi untuk Review --}}
                    @if($WBS->wbs_review_sudah_dibaca)
                        <span class="badge badge-pill badge-success px-3 py-2"><i class="fas fa-eye mr-1"></i> Review Sudah Dibaca</span>
                    @else
                        <span class="badge badge-pill badge-info px-3 py-2"><i class="fas fa-eye-slash mr-1"></i> Review Belum Dibaca</span>
                    @endif
                </div>
                <div class="text-muted">
                    <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($WBS->created_at)->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Sistem Tab untuk Informasi Berbeda -->
        <ul class="nav nav-tabs mb-3" id="myTab-{{ $WBS->wbs_id }}" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab-{{ $WBS->wbs_id }}" data-toggle="tab" 
                   href="#general-{{ $WBS->wbs_id }}" role="tab" aria-controls="general" aria-selected="true">
                   <i class="fas fa-info-circle mr-1"></i> Informasi Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="requester-tab-{{ $WBS->wbs_id }}" data-toggle="tab" 
                   href="#requester-{{ $WBS->wbs_id }}" role="tab" aria-controls="requester" aria-selected="false">
                   <i class="fas fa-user mr-1"></i> Data Pelapor
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="detail-tab-{{ $WBS->wbs_id }}" data-toggle="tab" 
                   href="#detail-{{ $WBS->wbs_id }}" role="tab" aria-controls="detail" aria-selected="false">
                   <i class="fas fa-file-alt mr-1"></i> Detail Laporan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="status-tab-{{ $WBS->wbs_id }}" data-toggle="tab" 
                   href="#status-{{ $WBS->wbs_id }}" role="tab" aria-controls="status" aria-selected="false">
                   <i class="fas fa-tasks mr-1"></i> Status & Tanggal
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent-{{ $WBS->wbs_id }}">
            <!-- Tab Informasi Umum -->
            <div class="tab-pane fade show active" id="general-{{ $WBS->wbs_id }}" role="tabpanel" 
                aria-labelledby="general-tab-{{ $WBS->wbs_id }}">
                <div class="row">
                    <!-- Informasi Laporan -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0"><i class="fas fa-clipboard-list mr-1"></i> Informasi Laporan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Kategori Aduan</td>
                                        <td>: {{ $WBS->wbs_kategori_aduan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Jenis Laporan</td>
                                        <td>: 
                                            <span class="badge badge-info px-2 py-1">
                                                {{ $WBS->wbs_jenis_laporan }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Yang Dilaporkan</td>
                                        <td>: {{ $WBS->wbs_yang_dilaporkan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Jabatan</td>
                                        <td>: {{ $WBS->wbs_jabatan }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Waktu dan Lokasi -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0"><i class="fas fa-map-marker-alt mr-1"></i> Waktu & Lokasi</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Waktu Kejadian</td>
                                        <td>: {{ $WBS->wbs_waktu_kejadian ? \Carbon\Carbon::parse($WBS->wbs_waktu_kejadian)->format('d M Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Lokasi Kejadian</td>
                                        <td>: {{ $WBS->wbs_lokasi_kejadian }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Data Pelapor -->
            <div class="tab-pane fade" id="requester-{{ $WBS->wbs_id }}" role="tabpanel" 
                aria-labelledby="requester-tab-{{ $WBS->wbs_id }}">
                <div class="card bg-light mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0"><i class="fas fa-user mr-1"></i> Informasi Pelapor</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="35%" class="font-weight-bold">Nama</td>
                                        <td>: {{ $WBS->wbs_nama_tanpa_gelar }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">NIK</td>
                                        <td>: {{ $WBS->wbs_nik_pengguna }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Email</td>
                                        <td>: {{ $WBS->wbs_email_pengguna }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="35%" class="font-weight-bold">No HP</td>
                                        <td>: {{ $WBS->wbs_no_hp_pengguna }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Bukti Identitas</td>
                                        <td>: 
                                            @if($WBS->wbs_upload_nik_pengguna)
                                                <a href="{{ asset('storage/' . $WBS->wbs_upload_nik_pengguna) }}" target="_blank" class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fas fa-eye mr-1"></i> Lihat KTP
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
                </div>
            </div>
            
            <!-- Tab Detail Laporan -->
            <div class="tab-pane fade" id="detail-{{ $WBS->wbs_id }}" role="tabpanel" 
                aria-labelledby="detail-tab-{{ $WBS->wbs_id }}">
                <div class="card bg-light mb-3">
                    <div class="card-header bg-warning text-white">
                        <h6 class="m-0"><i class="fas fa-file-alt mr-1"></i> Detail Kronologis Laporan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="font-weight-bold">Kronologis Kejadian:</h6>
                                <div class="border p-3 bg-white rounded">
                                    {{ $WBS->wbs_kronologis_kejadian }}
                                </div>
                            </div>
                            
                            @if($WBS->wbs_catatan_tambahan)
                            <div class="col-md-12 mb-3">
                                <h6 class="font-weight-bold">Catatan Tambahan:</h6>
                                <div class="border p-3 bg-white rounded">
                                    {{ $WBS->wbs_catatan_tambahan }}
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-md-12">
                                <h6 class="font-weight-bold">Bukti Pendukung:</h6>
                                @if($WBS->wbs_bukti_pendukung)
                                    <a href="{{ asset('storage/' . $WBS->wbs_bukti_pendukung) }}" target="_blank" class="btn btn-info rounded-pill">
                                        <i class="fas fa-download mr-1"></i> Download Bukti Pendukung
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada bukti pendukung</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Status & Tanggal -->
            <div class="tab-pane fade" id="status-{{ $WBS->wbs_id }}" role="tabpanel" 
                aria-labelledby="status-tab-{{ $WBS->wbs_id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0"><i class="fas fa-chart-line mr-1"></i> Status Laporan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="30%" class="font-weight-bold">Status</td>
                                        <td>: 
                                            @switch($WBS->wbs_status)
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
                                                    <span class="badge badge-secondary">{{ $WBS->wbs_status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Review Dibaca</td>
                                        <td>: 
                                            @if($WBS->wbs_review_sudah_dibaca)
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
                                        <td>: {{ $WBS->created_at ? $WBS->created_at->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Review Dibaca</td>
                                        <td>: {{ $WBS->wbs_review_tanggal_dibaca ? \Carbon\Carbon::parse($WBS->wbs_review_tanggal_dibaca)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Jawaban</td>
                                        <td>: {{ $WBS->wbs_tanggal_dijawab ? \Carbon\Carbon::parse($WBS->wbs_tanggal_dijawab)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Tampilkan jawaban jika ada --}}
                @if($WBS->wbs_jawaban && in_array($WBS->wbs_status, ['Disetujui', 'Ditolak']))
                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-light mb-3">
                            <div class="card-header {{ $WBS->wbs_status == 'Disetujui' ? 'bg-success' : 'bg-danger' }} text-white">
                                <h6 class="m-0">
                                    <i class="fas {{ $WBS->wbs_status == 'Disetujui' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i> 
                                    {{ $WBS->wbs_status == 'Disetujui' ? 'Jawaban Laporan' : 'Alasan Penolakan' }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($WBS->wbs_status == 'Disetujui' && preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $WBS->wbs_jawaban))
                                    {{-- Jika jawaban berupa file --}}
                                    <div class="text-center">
                                        <a href="{{ asset('storage/' . $WBS->wbs_jawaban) }}" target="_blank" 
                                           class="btn btn-primary rounded-pill">
                                            <i class="fas fa-file-download mr-1"></i> Download Dokumen Jawaban
                                        </a>
                                    </div>
                                @else
                                    {{-- Jika jawaban berupa teks --}}
                                    <p class="mb-0">{{ $WBS->wbs_jawaban }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tombol Aksi - Update untuk Review -->
        <div class="action-container mt-4 pt-3 border-top">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap">
                        {{-- Ganti kondisi dari 'Masuk' ke 'Verifikasi' untuk Review --}}
                        @if($WBS->wbs_status == 'Verifikasi')
                            <button type="button" class="btn btn-success btn-sm mr-2 mb-2" onclick="showUpdateModal({{ $WBS->wbs_id }}, 'approve')">
                                <i class="fas fa-check"></i> Setujui Review
                            </button>
                            <button type="button" class="btn btn-danger btn-sm mr-2 mb-2" onclick="showUpdateModal({{ $WBS->wbs_id }}, 'decline')">
                                <i class="fas fa-times"></i> Tolak Review
                            </button>
                        @endif

                        {{-- Update kondisi untuk tandai dibaca dan hapus --}}
                        <button class="btn btn-info btn-sm mr-2 mb-2" 
                            onclick="tandaiDibacaReview({{ $WBS->wbs_id }}, '{{ $WBS->wbs_status }}', {{ $WBS->wbs_review_sudah_dibaca ? 'true' : 'false' }})" 
                            data-status="{{ $WBS->wbs_status }}" 
                            data-dibaca="{{ $WBS->wbs_review_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-book-reader"></i> Tandai Review Dibaca
                        </button>
                        
                        <button class="btn btn-warning btn-sm mb-2" 
                            onclick="hapusReview({{ $WBS->wbs_id }}, {{ $WBS->wbs_review_sudah_dibaca ? 'true' : 'false' }})" 
                            data-dibaca="{{ $WBS->wbs_review_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-trash"></i> Hapus Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach