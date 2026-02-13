@foreach($pernyataanKeberatan as $PK)
<div class="card shadow-sm mb-4">
    <!-- Header Card dengan Status yang Lebih Menonjol -->
    <div class="card-header bg-light py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-alt mr-2"></i>Pernyataan Keberatan #{{ $PK->pernyataan_keberatan_id }}
                </h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    @switch($PK->pk_status)
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
                            <span class="badge badge-pill badge-secondary px-3 py-2">{{ $PK->pk_status }}</span>
                    @endswitch
                    
                    {{-- Update kondisi untuk Review --}}
                    @if($PK->pk_review_sudah_dibaca)
                        <span class="badge badge-pill badge-success px-3 py-2"><i class="fas fa-eye mr-1"></i> Review Sudah Dibaca</span>
                    @else
                        <span class="badge badge-pill badge-info px-3 py-2"><i class="fas fa-eye-slash mr-1"></i> Review Belum Dibaca</span>
                    @endif
                </div>
                <div class="text-muted">
                    <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($PK->created_at)->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Sistem Tab untuk Informasi Berbeda -->
        <ul class="nav nav-tabs mb-3" id="myTab-{{ $PK->pernyataan_keberatan_id }}" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab-{{ $PK->pernyataan_keberatan_id }}" data-toggle="tab" 
                   href="#general-{{ $PK->pernyataan_keberatan_id }}" role="tab" aria-controls="general" aria-selected="true">
                   <i class="fas fa-info-circle mr-1"></i> Informasi Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="requester-tab-{{ $PK->pernyataan_keberatan_id }}" data-toggle="tab" 
                   href="#requester-{{ $PK->pernyataan_keberatan_id }}" role="tab" aria-controls="requester" aria-selected="false">
                   <i class="fas fa-user mr-1"></i> Data Pemohon
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="status-tab-{{ $PK->pernyataan_keberatan_id }}" data-toggle="tab" 
                   href="#status-{{ $PK->pernyataan_keberatan_id }}" role="tab" aria-controls="status" aria-selected="false">
                   <i class="fas fa-tasks mr-1"></i> Status & Tanggal
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent-{{ $PK->pernyataan_keberatan_id }}">
            <!-- Tab Informasi Umum -->
            <div class="tab-pane fade show active" id="general-{{ $PK->pernyataan_keberatan_id }}" role="tabpanel" 
                aria-labelledby="general-tab-{{ $PK->pernyataan_keberatan_id }}">
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
                                        <td>: {{ $PK->pk_kategori_pemohon }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Kategori Aduan</td>
                                        <td>: {{ $PK->pk_kategori_aduan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Bukti Aduan</td>
                                        <td>: 
                                            @if($PK->pk_bukti_aduan)
                                                <a href="{{ asset('storage/' . $PK->pk_bukti_aduan) }}" target="_blank" 
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
                                <h6 class="m-0"><i class="fas fa-file-alt mr-1"></i> Detail Pernyataan Keberatan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Alasan Pengajuan Keberatan</td>
                                        <td>: {{ $PK->pk_alasan_pengajuan_keberatan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Kasus Posisi</td>
                                        <td>: {{ \Illuminate\Support\Str::limit($PK->pk_kasus_posisi, 100) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Data Pemohon -->
            <div class="tab-pane fade" id="requester-{{ $PK->pernyataan_keberatan_id }}" role="tabpanel" 
                aria-labelledby="requester-tab-{{ $PK->pernyataan_keberatan_id }}">
                
                @if($PK->pk_kategori_pemohon == 'Diri Sendiri' && $PK->PkDiriSendiri)
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
                                            <td>: {{ $PK->PkDiriSendiri->pk_nama_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Alamat</td>
                                            <td>: {{ $PK->PkDiriSendiri->pk_alamat_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">No HP</td>
                                            <td>: {{ $PK->PkDiriSendiri->pk_no_hp_pengguna }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="35%" class="font-weight-bold">Email</td>
                                            <td>: {{ $PK->PkDiriSendiri->pk_email_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td width="35%" class="font-weight-bold">Pekerjaan</td>
                                            <td>: {{ $PK->PkDiriSendiri->pk_pekerjaan_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">KTP</td>
                                            <td>: 
                                                <a href="{{ asset('storage/' . $PK->PkDiriSendiri->pk_upload_nik_pengguna) }}" 
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
                @elseif($PK->pk_kategori_pemohon == 'Orang Lain' && $PK->PkOrangLain)
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
                                                    <td>: {{ $PK->PkOrangLain->pk_nama_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Alamat Penginput</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_alamat_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">No HP Penginput</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_no_hp_pengguna_penginput }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Email Penginput</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_email_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Pekerjaan Penginput</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_pekerjaan_pengguna_penginput }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">KTP Penginput</td>
                                                    <td>: 
                                                        <a href="{{ asset('storage/' . $PK->PkOrangLain->pk_upload_nik_pengguna_penginput) }}" 
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
                                    <h6 class="m-0"><i class="fas fa-user-check mr-1"></i> Informasi Kuasa Pemohon</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Nama Kuasa Pemohon</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_nama_kuasa_pemohon }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Alamat Kuasa Pemohon</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_alamat_kuasa_pemohon }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">No HP Kuasa Pemohon</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_no_hp_kuasa_pemohon }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td width="35%" class="font-weight-bold">Email Kuasa Pemohon</td>
                                                    <td>: {{ $PK->PkOrangLain->pk_email_kuasa_pemohon }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">KTP Kuasa Pemohon</td>
                                                    <td>: 
                                                        <a href="{{ asset('storage/' . $PK->PkOrangLain->pk_upload_nik_kuasa_pemohon) }}" 
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
                @endif
            </div>
            
            <!-- Tab Status & Tanggal -->
            <div class="tab-pane fade" id="status-{{ $PK->pernyataan_keberatan_id }}" role="tabpanel" 
                aria-labelledby="status-tab-{{ $PK->pernyataan_keberatan_id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0"><i class="fas fa-chart-line mr-1"></i> Status Pernyataan Keberatan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="30%" class="font-weight-bold">Status</td>
                                        <td>: 
                                            @switch($PK->pk_status)
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
                                                    <span class="badge badge-secondary">{{ $PK->pk_status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Review Dibaca</td>
                                        <td>: 
                                            @if($PK->pk_review_sudah_dibaca)
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
                                        <td>: {{ $PK->created_at ? $PK->created_at->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Review Dibaca</td>
                                        <td>: {{ $PK->pk_review_tanggal_dibaca ? \Carbon\Carbon::parse($PK->pk_review_tanggal_dibaca)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Jawaban</td>
                                        <td>: {{ $PK->pk_tanggal_dijawab ? \Carbon\Carbon::parse($PK->pk_tanggal_dijawab)->format('d-m-Y H:i:s') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Tampilkan jawaban jika ada --}}
                @if($PK->pk_jawaban && in_array($PK->pk_status, ['Disetujui', 'Ditolak']))
                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-light mb-3">
                            <div class="card-header {{ $PK->pk_status == 'Disetujui' ? 'bg-success' : 'bg-danger' }} text-white">
                                <h6 class="m-0">
                                    <i class="fas {{ $PK->pk_status == 'Disetujui' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i> 
                                    {{ $PK->pk_status == 'Disetujui' ? 'Jawaban Pernyataan Keberatan' : 'Alasan Penolakan' }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($PK->pk_status == 'Disetujui' && preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $PK->pk_jawaban))
                                    {{-- Jika jawaban berupa file --}}
                                    <div class="text-center">
                                        <a href="{{ asset('storage/' . $PK->pk_jawaban) }}" target="_blank" 
                                           class="btn btn-primary">
                                            <i class="fas fa-file-download mr-1"></i> Download Dokumen Jawaban
                                        </a>
                                    </div>
                                @else
                                    {{-- Jika jawaban berupa teks --}}
                                    <p class="mb-0">{{ $PK->pk_jawaban }}</p>
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
                        @if($PK->pk_status == 'Verifikasi')
                            <button type="button" class="btn btn-success btn-sm mr-2 mb-2" onclick="showUpdateModal({{ $PK->pernyataan_keberatan_id }}, 'approve')">
                                <i class="fas fa-check"></i> Setujui Review
                            </button>
                            <button type="button" class="btn btn-danger btn-sm mr-2 mb-2" onclick="showUpdateModal({{ $PK->pernyataan_keberatan_id }}, 'decline')">
                                <i class="fas fa-times"></i> Tolak Review
                            </button>
                        @endif

                        {{-- Update kondisi untuk tandai dibaca dan hapus --}}
                        <button class="btn btn-info btn-sm mr-2 mb-2" 
                            onclick="tandaiDibacaReview({{ $PK->pernyataan_keberatan_id }}, '{{ $PK->pk_status }}', {{ $PK->pk_review_sudah_dibaca ? 'true' : 'false' }})" 
                            data-status="{{ $PK->pk_status }}" 
                            data-dibaca="{{ $PK->pk_review_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-book-reader"></i> Tandai Review Dibaca
                        </button>
                        
                        <button class="btn btn-warning btn-sm mb-2" 
                            onclick="hapusReview({{ $PK->pernyataan_keberatan_id }}, {{ $PK->pk_review_sudah_dibaca ? 'true' : 'false' }})" 
                            data-dibaca="{{ $PK->pk_review_sudah_dibaca ? 'true' : 'false' }}">
                            <i class="fas fa-trash"></i> Hapus Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach