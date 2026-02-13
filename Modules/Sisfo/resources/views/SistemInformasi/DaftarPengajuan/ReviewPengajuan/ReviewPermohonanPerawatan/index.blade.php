@extends('sisfo::layouts.template')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Daftar Review Pengajuan Permohonan Perawatan Sarana Prasarana</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ url($daftarReviewPengajuanUrl) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            @if(count($permohonanPerawatan) > 0)
                <div class="table-responsive">
                    @include('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.data')
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i> Tidak ada data permohonan perawatan sarana prasarana yang perlu diReview.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal untuk detail dan aksi -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Permohonan Perawatan</h5>
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        $(function () {
            const baseUrl = '{{ url($reviewPPUrl) }}';

            // Fungsi universal untuk menampilkan modal update (approve/decline)
            window.showUpdateModal = function (id, actionType) {
                $('#detailModalLabel').text(actionType === 'approve' ? 'Setujui Review Permohonan Perawatan' : 'Tolak Review Permohonan Perawatan');
                $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data...</p></div>');
                $('#detailModal').modal('show');

                $.ajax({
                    url: baseUrl + '/editData/' + id + '?type=' + actionType,
                    type: 'GET',
                    success: function (response) {
                        $('#modalContent').html(response);

                        $('#btnConfirmUpdate').on('click', function () {
                            const action = $(this).data('action');
                            const ppId = $(this).data('id');

                            if (action === 'approve') {
                                processApproval(ppId);
                            } else {
                                processDecline(ppId);
                            }
                        });
                    },
                    error: function () {
                        $('#modalContent').html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
                    }
                });
            };

            // Proses approval dengan file upload
            function processApproval(id) {
                const jawaban = $('#jawaban').val().trim();
                const fileInput = $('#jawaban_file')[0];
                const file = fileInput ? fileInput.files[0] : null;

                if (!jawaban) {
                    Swal.fire('Perhatian!', 'Jawaban harus diisi', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Anda yakin sudah mereview data dan akan menyetujui permohonan perawatan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('action', 'approve');
                        formData.append('jawaban', jawaban);
                        if (file) {
                            formData.append('jawaban_file', file);
                        }

                        $.ajax({
                            url: baseUrl + '/updateData/' + id,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                $('#detailModal').modal('hide');
                                Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                            },
                            error: function (xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            }

            // Proses decline
            function processDecline(id) {
                const alasanPenolakan = $('#alasan_penolakan').val().trim();

                if (!alasanPenolakan) {
                    Swal.fire('Perhatian!', 'Alasan penolakan harus diisi', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Anda yakin akan menolak permohonan perawatan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: baseUrl + '/updateData/' + id,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'decline',
                                alasan_penolakan: alasanPenolakan
                            },
                            success: function (response) {
                                $('#detailModal').modal('hide');
                                Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                            },
                            error: function (xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            }

            // Tandai dibaca review
            window.tandaiDibacaReview = function (id, status, sudahDibaca) {
                if (status !== 'Disetujui' && status !== 'Ditolak') {
                    Swal.fire('Perhatian!', 'Anda harus menyetujui/menolak review ini terlebih dahulu', 'warning');
                    return;
                }

                if (sudahDibaca) {
                    Swal.fire('Informasi', 'Review ini sudah ditandai dibaca sebelumnya', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Tandai review ini sebagai telah dibaca?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tandai Dibaca',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: baseUrl + '/updateData/' + id,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: 'read'
                            },
                            success: function (response) {
                                Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                            },
                            error: function (xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            };

            // Hapus review
            window.hapusReview = function (id, sudahDibaca) {
                if (!sudahDibaca) {
                    Swal.fire('Perhatian!', 'Anda harus menandai review ini telah dibaca terlebih dahulu', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Hapus review ini dari daftar Review?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: baseUrl + '/deleteData/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                            },
                            error: function (xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            };

            // View image in modal
            window.viewImage = function(imageSrc, imageTitle) {
                Swal.fire({
                    title: imageTitle || 'Preview Gambar',
                    html: '<img src="' + imageSrc + '" style="max-width: 100%; max-height: 70vh; object-fit: contain;">',
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: '80%',
                    customClass: { popup: 'swal-wide' }
                });
            };
        });
    </script>

    <style>
        .swal-wide {
            max-width: 900px !important;
        }
    </style>
@endpush