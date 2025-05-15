@foreach($permohonanInformasi as $permohonan)
<div class="permohonan-card card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">
            <span class="mr-2">Permohonan #{{ $permohonan->permohonan_informasi_id }}</span>

            @switch($permohonan->pi_status)
                @case('Masuk')
                    <span class="badge badge-primary badge-status">Masuk</span>
                    @break
                @case('Verifikasi')
                    <span class="badge badge-warning badge-status">Verifikasi</span>
                    @break
                @case('Disetujui')
                    <span class="badge badge-success badge-status">Disetujui</span>
                    @break
                @case('Ditolak')
                    <span class="badge badge-danger badge-status">Ditolak</span>
                    @break
                @default
                    <span class="badge badge-secondary badge-status">{{ $permohonan->pi_status }}</span>
            @endswitch
            
            @if($permohonan->pi_sudah_dibaca)
                <span class="badge badge-success badge-status">Sudah Dibaca</span>
            @else
                <span class="badge badge-info badge-status">Belum Ditandai Dibaca</span>
            @endif
        </h5>
        <small>{{ \Carbon\Carbon::parse($permohonan->created_at)->format('d M Y H:i') }}</small>
    </div>
    <div class="card-body">
        <!-- Informasi Umum -->
        <div class="row">
            <div class="col-md-6">
                <div class="detail-section">
                    <h6>Informasi Permohonan</h6>
                    <div class="form-group row">
                        <label class="col-sm-4">Kategori Pemohon</label>
                        <div class="col-sm-8">: {{ $permohonan->pi_kategori_pemohon }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4">Kategori Aduan</label>
                        <div class="col-sm-8">: {{ $permohonan->pi_kategori_aduan }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4">Bukti Aduan</label>
                        <div class="col-sm-8">: 
                            @if($permohonan->pi_bukti_aduan)
                                <a href="{{ asset('storage/' . $permohonan->pi_bukti_aduan) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-download"></i> Lihat Dokumen
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-section">
                    <h6>Detail Permohonan</h6>
                    <div class="form-group row">
                        <label class="col-sm-4">Informasi yang Dibutuhkan</label>
                        <div class="col-sm-8">: {{ $permohonan->pi_informasi_yang_dibutuhkan }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4">Alasan Permohonan</label>
                        <div class="col-sm-8">: {{ $permohonan->pi_alasan_permohonan_informasi }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4">Alamat Sumber</label>
                        <div class="col-sm-8">: {{ $permohonan->pi_alamat_sumber_informasi }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Spesifik berdasarkan Kategori Pemohon -->
        <div class="mt-3">
            @if($permohonan->pi_kategori_pemohon == 'Diri Sendiri' && $permohonan->PiDiriSendiri)
                <div class="detail-section">
                    <h6>Informasi Pemohon (Diri Sendiri)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Nama</label>
                                <div class="col-sm-8">: {{ $permohonan->PiDiriSendiri->pi_nama_pengguna }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">Alamat</label>
                                <div class="col-sm-8">: {{ $permohonan->PiDiriSendiri->pi_alamat_pengguna }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">No HP</label>
                                <div class="col-sm-8">: {{ $permohonan->PiDiriSendiri->pi_no_hp_pengguna }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Email</label>
                                <div class="col-sm-8">: {{ $permohonan->PiDiriSendiri->pi_email_pengguna }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">KTP</label>
                                <div class="col-sm-8">: 
                                    <a href="{{ asset('storage/' . $permohonan->PiDiriSendiri->pi_upload_nik_pengguna) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-id-card"></i> Lihat KTP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($permohonan->pi_kategori_pemohon == 'Orang Lain' && $permohonan->PiOrangLain)
                <div class="detail-section">
                    <h6>Informasi Penginput</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Nama</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_nama_pengguna_penginput }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">Alamat</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_alamat_pengguna_penginput }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">No HP</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_no_hp_pengguna_penginput }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Email</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_email_pengguna_penginput }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">KTP</label>
                                <div class="col-sm-8">: 
                                    <a href="{{ asset('storage/' . $permohonan->PiOrangLain->pi_upload_nik_pengguna_penginput) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-id-card"></i> Lihat KTP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h6>Informasi Penerima Informasi</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Nama</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_nama_pengguna_informasi }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">Alamat</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_alamat_pengguna_informasi }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">No HP</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_no_hp_pengguna_informasi }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Email</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrangLain->pi_email_pengguna_informasi }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">KTP</label>
                                <div class="col-sm-8">: 
                                    <a href="{{ asset('storage/' . $permohonan->PiOrangLain->pi_upload_nik_pengguna_informasi) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-id-card"></i> Lihat KTP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($permohonan->pi_kategori_pemohon == 'Organisasi' && $permohonan->PiOrganisasi)
                <div class="detail-section">
                    <h6>Informasi Organisasi</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Nama Organisasi</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrganisasi->pi_nama_organisasi }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">No Telepon</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrganisasi->pi_no_telp_organisasi }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">Email/Media Sosial</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrganisasi->pi_email_atau_medsos_organisasi }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4">Nama Narahubung</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrganisasi->pi_nama_narahubung }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">No Telepon Narahubung</label>
                                <div class="col-sm-8">: {{ $permohonan->PiOrganisasi->pi_no_telp_narahubung }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">Identitas Narahubung</label>
                                <div class="col-sm-8">: 
                                    <a href="{{ asset('storage/' . $permohonan->PiOrganisasi->pi_identitas_narahubung) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-id-card"></i> Lihat Identitas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Informasi Status dan Tanggal -->
        <div class="row">
            <div class="col-md-6">
                <div class="detail-section">
                    <h6>Status Permohonan</h6>
                    <div class="form-group row">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">: 
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
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4">Dibaca</label>
                        <div class="col-sm-8">: 
                            @if($permohonan->pi_sudah_dibaca)
                                <span class="badge badge-success">Sudah Dibaca</span>
                            @else
                                <span class="badge badge-info">Belum Ditandai Dibaca</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-section">
                    <h6>Tanggal</h6>
                    <div class="form-group row">
                        <label class="col-sm-4">Tanggal Dibuat</label>
                        <div class="col-sm-8">: {{ $permohonan->created_at ? $permohonan->created_at->format('d-m-Y H:i:s') : '-' }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4">Tanggal Dibaca</label>
                        <div class="col-sm-8">: {{ $permohonan->pi_tanggal_dibaca ? \Carbon\Carbon::parse($permohonan->pi_tanggal_dibaca)->format('d-m-Y H:i:s') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="action-container">
            @if($permohonan->pi_status == 'Masuk')
                <button type="button" class="btn btn-success btn-action" onclick="showApproveModal({{ $permohonan->permohonan_informasi_id }})">
                    <i class="fas fa-check"></i> Setujui
                </button>
                <button type="button" class="btn btn-danger btn-action" onclick="showDeclineModal({{ $permohonan->permohonan_informasi_id }})">
                    <i class="fas fa-times"></i> Tolak
                </button>
            @endif

            <button class="btn btn-info btn-action" onclick="tandaiDibaca({{ $permohonan->permohonan_informasi_id }}, '{{ $permohonan->pi_status }}', {{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }})" 
                data-status="{{ $permohonan->pi_status }}" data-dibaca="{{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }}">
                <i class="fas fa-book-reader"></i> Tandai Dibaca
            </button>
            
            <button class="btn btn-warning btn-action" onclick="hapusPermohonan({{ $permohonan->permohonan_informasi_id }}, {{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }})" 
                data-dibaca="{{ $permohonan->pi_sudah_dibaca ? 'true' : 'false' }}">
                <i class="fas fa-trash"></i> Tandai Hapus
            </button>
        </div>
    </div>
</div>
@endforeach