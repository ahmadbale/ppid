@extends('sisfo::layouts.template')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Daftar Verifikasi Pengajuan Permohonan Informasi</h3>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ url($daftarPengajuanUrl) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        @if(count($permohonanInformasi) > 0)
            <div class="table-responsive">
                @include('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.data')
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> Tidak ada data permohonan informasi yang perlu diverifikasi.
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk detail dan aksi -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Permohonan Informasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .badge-status {
        font-size: 0.9em;
        font-weight: 500;
        padding: 5px 10px;
    }
    .permohonan-card {
        margin-bottom: 20px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .permohonan-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .permohonan-card .card-title {
        margin-bottom: 0;
        font-size: 1.1rem;
    }
    .detail-section {
        background-color: #f9f9f9;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .detail-section h6 {
        border-bottom: 1px solid #ddd;
        padding-bottom: 8px;
        margin-bottom: 15px;
        color: #555;
    }
    .btn-action {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .btn-action i {
        margin-right: 5px;
    }
    .action-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 15px;
    }
</style>
@endpush

@push('js')
<script>
    $(function() {
        // Fungsi untuk menampilkan modal approve
        window.showApproveModal = function(id) {
            // Reset modal content
            $('#detailModalLabel').text('Persetujuan Permohonan');
            $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data...</p></div>');
            
            // Tampilkan modal
            $('#detailModal').modal('show');
            
            // Ambil konten modal via AJAX
            $.ajax({
                url: '{{ url("$daftarPengajuanUrl/permohonan-informasi/approve-modal") }}/' + id,
                type: 'GET',
                success: function(response) {
                    $('#modalContent').html(response);
                    
                    // Event handler untuk tombol konfirmasi approve
                    $('#btnConfirmApprove').on('click', function() {
                        const permohonanId = $(this).data('id');
                        
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: 'Anda yakin sudah mereview data pengguna dan akan melakukan penyetujuan pengajuan ini?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Setujui',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tampilkan loading
                                Swal.fire({
                                    title: 'Memproses...',
                                    text: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Kirim permintaan AJAX
                                $.ajax({
                                    url: '{{ url("$daftarPengajuanUrl/permohonan-informasi/setujuiPermohonan") }}/' + permohonanId,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            $('#detailModal').modal('hide');
                                            Swal.fire({
                                                title: 'Berhasil!',
                                                text: response.message,
                                                icon: 'success'
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Perhatian!',
                                                text: response.message,
                                                icon: 'warning'
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server',
                                            icon: 'error'
                                        });
                                    }
                                });
                            }
                        });
                    });
                },
                error: function(xhr) {
                    $('#modalContent').html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
                }
            });
        };
        
        // Fungsi untuk menampilkan modal decline
        window.showDeclineModal = function(id) {
            // Reset modal content
            $('#detailModalLabel').text('Penolakan Permohonan');
            $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data...</p></div>');
            
            // Tampilkan modal
            $('#detailModal').modal('show');
            
            // Ambil konten modal via AJAX
            $.ajax({
                url: '{{ url("$daftarPengajuanUrl/permohonan-informasi/decline-modal") }}/' + id,
                type: 'GET',
                success: function(response) {
                    $('#modalContent').html(response);
                    
                    // Event handler untuk tombol konfirmasi decline
                    $('#btnConfirmDecline').on('click', function() {
                        // Reset error
                        $('#alasan_penolakan_modal').removeClass('is-invalid');
                        $('#alasanErrorModal').html('');
                        
                        // Ambil nilai alasan penolakan
                        const alasanPenolakan = $('#alasan_penolakan_modal').val().trim();
                        const permohonanId = $(this).data('id');
                        
                        if (!alasanPenolakan) {
                            $('#alasan_penolakan_modal').addClass('is-invalid');
                            $('#alasanErrorModal').html('Alasan penolakan harus diisi untuk menolak pengajuan');
                            return;
                        }
                        
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: 'Jika anda yakin telah melakukan review dan akan menolak pengajuan ini dengan alasan yang telah diisi?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Tolak',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tampilkan loading
                                Swal.fire({
                                    title: 'Memproses...',
                                    text: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Kirim permintaan AJAX
                                $.ajax({
                                    url: '{{ url("$daftarPengajuanUrl/permohonan-informasi/tolakPermohonan") }}/' + permohonanId,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        alasan_penolakan: alasanPenolakan
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            $('#detailModal').modal('hide');
                                            Swal.fire({
                                                title: 'Berhasil!',
                                                text: response.message,
                                                icon: 'success'
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Perhatian!',
                                                text: response.message,
                                                icon: 'warning'
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server',
                                            icon: 'error'
                                        });
                                    }
                                });
                            }
                        });
                    });
                },
                error: function(xhr) {
                    $('#modalContent').html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
                }
            });
        };
        
        // PERBAIKAN: Fungsi untuk tandai dibaca dengan validasi awal
        window.tandaiDibaca = function(id, status, sudahDibaca) {
            // Validasi awal: permohonan harus sudah disetujui atau ditolak
            if (status !== 'Verifikasi' && status !== 'Ditolak') {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Anda harus menyetujui/menolak permohonan ini terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }
            
            // Jika sudah dibaca, tampilkan peringatan
            if (sudahDibaca) {
                Swal.fire({
                    title: 'Informasi',
                    text: 'Permohonan ini sudah ditandai dibaca sebelumnya',
                    icon: 'info'
                });
                return;
            }
            
            // Jika validasi sukses, baru tampilkan konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menandai pengajuan permohonan informasi ini sebagai telah dibaca?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tandai Dibaca',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim permintaan AJAX
                    $.ajax({
                        url: '{{ url("$daftarPengajuanUrl/permohonan-informasi/tandaiDibaca") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success'
                                }).then(() => {
                                    // Reload halaman setelah aksi berhasil
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Perhatian!',
                                    text: response.message,
                                    icon: 'warning'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        };

        // PERBAIKAN: Fungsi untuk hapus permohonan dengan validasi awal
        window.hapusPermohonan = function(id, sudahDibaca) {
            // Validasi awal: permohonan harus sudah ditandai dibaca
            if (!sudahDibaca) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Anda harus menandai pengajuan ini telah dibaca terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }
            
            // Jika validasi sukses, baru tampilkan konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus pengajuan ini dari daftar verifikasi?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim permintaan AJAX
                    $.ajax({
                        url: '{{ url("$daftarPengajuanUrl/permohonan-informasi/hapusPermohonan") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success'
                                }).then(() => {
                                    // Reload halaman setelah aksi berhasil
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Perhatian!',
                                    text: response.message,
                                    icon: 'warning'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        };
    });
</script>
@endpush