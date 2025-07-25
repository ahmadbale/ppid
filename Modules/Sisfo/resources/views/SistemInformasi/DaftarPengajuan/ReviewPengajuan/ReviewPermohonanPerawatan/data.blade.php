@foreach($permohonanPerawatan as $PP)
<div class="card shadow-sm mb-4">
    <!-- Header Card dengan Status yang Lebih Menonjol -->
    <div class="card-header bg-light py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tools mr-2"></i>Permohonan Perawatan #{{ $PP->permohonan_perawatan_id }}
                </h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    @switch($PP->pp_status)
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
                            <span class="badge badge-pill badge-secondary px-3 py-2">{{ $PP->pp_status }}</span>
                    @endswitch
                    
                    {{-- Update kondisi untuk Review --}}
                    @if($PP->pp_review_sudah_dibaca)
                        <span class="badge badge-pill badge-success px-3 py-2"><i class="fas fa-eye mr-1"></i> Review Sudah Dibaca</span>
                    @else
                        <span class="badge badge-pill badge-info px-3 py-2"><i class="fas fa-eye-slash mr-1"></i> Review Belum Dibaca</span>
                    @endif
                </div>
                <div class="text-muted">
                    <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($PP->created_at)->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Sistem Tab untuk Informasi Berbeda -->
        <ul class="nav nav-tabs mb-3" id="myTab-{{ $PP->permohonan_perawatan_id }}" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab-{{ $PP->permohonan_perawatan_id }}" data-toggle="tab" 
                   href="#general-{{ $PP->permohonan_perawatan_id }}" role="tab" aria-controls="general" aria-selected="true">
                   <i class="fas fa-info-circle mr-1"></i> Informasi Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="requester-tab-{{ $PP->permohonan_perawatan_id }}" data-toggle="tab" 
                   href="#requester-{{ $PP->permohonan_perawatan_id }}" role="tab" aria-controls="requester" aria-selected="false">
                   <i class="fas fa-user mr-1"></i> Data Pengusul
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="detail-tab-{{ $PP->permohonan_perawatan_id }}" data-toggle="tab" 
                   href="#detail-{{ $PP->permohonan_perawatan_id }}" role="tab" aria-controls="detail" aria-selected="false">
                   <i class="fas fa-file-alt mr-1"></i> Detail Permohonan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="status-tab-{{ $PP->permohonan_perawatan_id }}" data-toggle="tab" 
                   href="#status-{{ $PP->permohonan_perawatan_id }}" role="tab" aria-controls="status" aria-selected="false">
                   <i class="fas fa-tasks mr-1"></i> Status & Tanggal
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent-{{ $PP->permohonan_perawatan_id }}">
            <!-- Tab Informasi Umum -->
            <div class="tab-pane fade show active" id="general-{{ $PP->permohonan_perawatan_id }}" role="tabpanel" 
                aria-labelledby="general-tab-{{ $PP->permohonan_perawatan_id }}">
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
                                        <td width="40%" class="font-weight-bold">Kategori Aduan</td>
                                        <td>: {{ $PP->pp_kategori_aduan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Unit Kerja</td>
                                        <td>: {{ $PP->pp_unit_kerja }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Lokasi Perawatan</td>
                                        <td>: {{ $PP->pp_lokasi_perawatan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Bukti Aduan</td>
                                        <td>: 
                                            @if($PP->pp_bukti_aduan)
                                                <a href="{{ asset('storage/' . $PP->pp_bukti_aduan) }}" target="_blank" class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fas fa-eye mr-1"></i> Lihat Dokumen
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
                    
                    <!-- File Bukti -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0"><i class="fas fa-file-image mr-1"></i> File Bukti</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Foto Kondisi</td>
                                        <td>: 
                                            @if($PP->pp_foto_kondisi)
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="viewImage('{{ asset('storage/' . $PP->pp_foto_kondisi) }}', 'Foto Kondisi Permohonan #{{ $PP->permohonan_perawatan_id }}')">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </button>
                                                    <a href="{{ asset('storage/' . $PP->pp_foto_kondisi) }}" download class="btn btn-sm btn-success">
                                                        <i class="fas fa-download mr-1"></i> Download
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Data Pengusul -->
            <div class="tab-pane fade" id="requester-{{ $PP->permohonan_perawatan_id }}" role="tabpanel" 
                aria-labelledby="requester-tab-{{ $PP->permohonan_perawatan_id }}">
                <div class="card bg-light mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0"><i class="fas fa-user mr-1"></i> Informasi Pengusul</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="35%" class="font-weight-bold">Nama</td>
                                        <td>: {{ $PP->pp_nama_pengguna }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Email</td>
                                        <td>: {{ $PP->pp_email_pengguna }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="35%" class="font-weight-bold">No HP</td>
                                        <td>: {{ $PP->pp_no_hp_pengguna }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Unit Kerja</td>
                                        <td>: {{ $PP->pp_unit_kerja }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Detail Permohonan -->
            <div class="tab-pane fade" id="detail-{{ $PP->permohonan_perawatan_id }}" role="tabpanel" 
                aria-labelledby="detail-tab-{{ $PP->permohonan_perawatan_id }}">
                <div class="card bg-light mb-3">
                    <div class="card-header bg-warning text-white">
                        <h6 class="m-0"><i class="fas fa-file-alt mr-1"></i> Detail Permohonan Perawatan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="font-weight-bold">Perawatan yang Diusulkan:</h6>
                                <div class="border p-3 bg-white rounded">
                                    {{ $PP->pp_perawatan_yang_diusulkan }}
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <h6 class="font-weight-bold">Keluhan Kerusakan:</h6>
                                <div class="border p-3 bg-white rounded">
                                    {{ $PP->pp_keluhan_kerusakan }}
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <h6 class="font-weight-bold">Lokasi Perawatan:</h6>
                                <div class="border p-3 bg-white rounded">
                                    {{ $PP->pp_lokasi_perawatan }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Status & Tanggal -->
            <div class="tab-pane fade" id="status-{{ $PP->permohonan_perawatan_id }}" role="tabpanel" 
                aria-labelledby="status-tab-{{ $PP->permohonan_perawatan_id }}">
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
                                            @switch($PP->pp_status)
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
                                                    <span class="badge badge-secondary">{{ $PP->pp_status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Review Dibaca</td>
                                        <td>: 
                                            @if($PP->pp_review_sudah_dibaca)
                                                <span class="badge badge-success">Sudah Dibaca</span>
                                            @else
                                                <span class="badge badge-info">Belum Ditandai Dibaca</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Review oleh</td>
                                        <td>: {{ $PP->pp_dijawab ?? '-' }}</td>
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
                                        <td>: {{ $PP->created_at ? $PP->created_at->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Review Dibaca</td>
                                        <td>: {{ $PP->pp_review_tanggal_dibaca ? \Carbon\Carbon::parse($PP->pp_review_tanggal_dibaca)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Jawaban</td>
                                        <td>: {{ $PP->pp_tanggal_dijawab ? \Carbon\Carbon::parse($PP->pp_tanggal_dijawab)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Tampilkan jawaban jika ada --}}
                @if($PP->pp_jawaban && in_array($PP->pp_status, ['Disetujui', 'Ditolak']))
                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-light mb-3">
                            <div class="card-header {{ $PP->pp_status == 'Disetujui' ? 'bg-success' : 'bg-danger' }} text-white">
                                <h6 class="m-0">
                                    <i class="fas {{ $PP->pp_status == 'Disetujui' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i> 
                                    {{ $PP->pp_status == 'Disetujui' ? 'Jawaban Permohonan' : 'Alasan Penolakan' }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($PP->pp_status == 'Disetujui' && preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $PP->pp_jawaban))
                                    {{-- Jika jawaban berupa file --}}
                                    <div class="text-center">
                                        <a href="{{ asset('storage/' . $PP->pp_jawaban) }}" target="_blank" 
                                           class="btn btn-primary rounded-pill">
                                            <i class="fas fa-file-download mr-1"></i> Download Dokumen Jawaban
                                        </a>
                                    </div>
                                @else
                                    {{-- Jika jawaban berupa teks --}}
                                    <p class="mb-0">{{ $PP->pp_jawaban }}</p>
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
                        @if($PP->pp_status == 'Verifikasi')
                            <button type="button" class="btn btn-success btn-sm mr-2 mb-2" onclick="showApproveModal({{ $PP->permohonan_perawatan_id }})">
                                <i class="fas fa-check"></i> Setujui Review
                            </button>
                            <button type="button" class="btn btn-danger btn-sm mr-2 mb-2" onclick="showDeclineModal({{ $PP->permohonan_perawatan_id }})">
                                <i class="fas fa-times"></i> Tolak Review
                            </button>
                        @endif

                        {{-- Update kondisi untuk tandai dibaca dan hapus --}}
                        <button class="btn btn-info btn-sm mr-2 mb-2" 
                            onclick="tandaiDibacaReview({{ $PP->permohonan_perawatan_id }}, '{{ $PP->pp_status }}', {{ $PP->pp_review_sudah_dibaca ? 'true' : 'false' }})" 
                            data-status="{{ $PP->pp_status }}" 
                            data-dibaca="{{ $PP->pp_review_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-book-reader"></i> Tandai Review Dibaca
                        </button>
                        
                        <button class="btn btn-warning btn-sm mb-2" 
                            onclick="hapusReview({{ $PP->permohonan_perawatan_id }}, {{ $PP->pp_review_sudah_dibaca ? 'true' : 'false' }})" 
                            data-dibaca="{{ $PP->pp_review_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-trash"></i> Hapus Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach