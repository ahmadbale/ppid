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
        // Base URL untuk AJAX - Gunakan sub-menu URL langsung dari database
        const baseUrl = '{{ url(\Modules\Sisfo\App\Models\Website\WebMenuModel::getDynamicMenuUrl("daftar-verifikasi-pengajuan-permohonan-informasi")) }}';
        
        // Fungsi untuk menampilkan modal update (approve/decline)
        window.showUpdateModal = function(id, type) {
            const isApprove = type === 'approve';
            const title = isApprove ? 'Setujui Permohonan' : 'Tolak Permohonan';
            
            $('#detailModalLabel').text(title);
            $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data...</p></div>');
            $('#detailModal').modal('show');
            
            $.ajax({
                url: baseUrl + '/editData/' + id,
                type: 'GET',
                data: { 
                    type: type 
                },
                success: function(response) {
                    $('#modalContent').html(response);
                    
                    // Bind event ke tombol konfirmasi
                    $('#btnConfirmUpdate').off('click').on('click', function() {
                        const action = $(this).data('action');
                        const dataId = $(this).data('id');
                        
                        if (action === 'approve') {
                            processApproval(dataId);
                        } else if (action === 'decline') {
                            processDecline(dataId);
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Error loading modal:', xhr);
                    let errorMsg = 'Gagal memuat data. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#modalContent').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        };
        
        // Fungsi untuk memproses persetujuan
        function processApproval(id) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: 'Apakah Anda yakin ingin menyetujui permohonan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Setujui',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/updateData/' + id,
                        type: 'POST',
                        data: {
                            action: 'approve',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#detailModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Permohonan berhasil disetujui',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error approving:', xhr);
                            const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyetujui permohonan';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        }
        
        // Fungsi untuk memproses penolakan
        function processDecline(id) {
            const alasanPenolakan = $('#alasan_penolakan_modal').val();
            
            // Validasi alasan penolakan
            if (!alasanPenolakan || alasanPenolakan.trim() === '') {
                $('#alasan_penolakan_modal').addClass('is-invalid');
                $('#alasanErrorModal').text('Alasan penolakan harus diisi');
                return;
            }
            
            $('#alasan_penolakan_modal').removeClass('is-invalid');
            
            Swal.fire({
                title: 'Konfirmasi Penolakan',
                text: 'Apakah Anda yakin ingin menolak permohonan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Tolak',
                cancelButtonText: '<i class="fas fa-arrow-left mr-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/updateData/' + id,
                        type: 'POST',
                        data: {
                            action: 'decline',
                            alasan_penolakan: alasanPenolakan,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#detailModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Permohonan berhasil ditolak',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error declining:', xhr);
                            const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menolak permohonan';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        }
        
        // Fungsi untuk tandai dibaca
        window.tandaiDibaca = function(id, status, sudahDibaca) {
            // Validasi: permohonan harus sudah diverifikasi (disetujui/ditolak)
            if (status !== 'Verifikasi' && status !== 'Ditolak') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Ditandai',
                    text: 'Permohonan harus sudah diverifikasi (disetujui/ditolak) terlebih dahulu sebelum bisa ditandai dibaca.'
                });
                return;
            }
            
            // Cek apakah sudah ditandai dibaca
            if (sudahDibaca) {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: 'Permohonan ini sudah ditandai sebagai dibaca.'
                });
                return;
            }
            
            Swal.fire({
                title: 'Tandai Sudah Dibaca',
                text: 'Apakah Anda yakin sudah membaca permohonan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-eye mr-1"></i> Ya, Tandai Dibaca',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/updateData/' + id,
                        type: 'POST',
                        data: {
                            action: 'read',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Permohonan berhasil ditandai dibaca',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error marking as read:', xhr);
                            const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        };
        
        // Fungsi untuk hapus permohonan
        window.hapusPermohonan = function(id, sudahDibaca) {
            // Validasi: permohonan harus sudah dibaca
            if (!sudahDibaca) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Dihapus',
                    text: 'Permohonan harus sudah ditandai dibaca terlebih dahulu sebelum bisa dihapus dari daftar verifikasi.'
                });
                return;
            }
            
            Swal.fire({
                title: 'Hapus Permohonan',
                text: 'Apakah Anda yakin ingin menghapus permohonan ini dari daftar verifikasi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/deleteData/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Permohonan berhasil dihapus dari daftar verifikasi',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error deleting:', xhr);
                            const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        };
    });
</script>
@endpush

