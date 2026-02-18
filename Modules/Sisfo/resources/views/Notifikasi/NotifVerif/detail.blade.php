@extends('sisfo::layouts.template')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <a href="{{ url('notifikasi-verifikasi') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <h3 class="card-title"><strong> {{ $page->title }} </strong></h3>
    </div>
    <div class="card-body">
        @if($notifikasi->isEmpty())
            <!-- Jika tidak ada notifikasi -->
            <div class="d-flex flex-column align-items-center justify-content-center"
                style="min-height: 300px; text-align: center;">
                <i class="fas fa-bell-slash" style="font-size: 5rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h4 style="color: #666;">Tidak ada notifikasi verifikasi</h4>
                <p style="color: #999;">Belum ada notifikasi masuk untuk kategori ini</p>
            </div>
        @else
            <!-- Container Notifikasi -->
            @foreach($notifikasi as $item)
                @php
                    // Ambil data form berdasarkan kategori
                    $formData = null;
                    $formUrl = '#';
                    
                    if ($item->notif_verif_kategori === 'E-Form Permohonan Informasi' && $item->t_permohonan_informasi) {
                        $formData = $item->t_permohonan_informasi;
                        $formUrl = url('daftar-review-pengajuan-permohonan-informasi/editData/' . $formData->permohonan_informasi_id);
                    } elseif ($item->notif_verif_kategori === 'E-Form Pernyataan Keberatan' && $item->t_pernyataan_keberatan) {
                        $formData = $item->t_pernyataan_keberatan;
                        $formUrl = url('daftar-review-pernyataan-keberatan/editData/' . $formData->pernyataan_keberatan_id);
                    } elseif ($item->notif_verif_kategori === 'E-Form Pengaduan Masyarakat' && $item->t_pengaduan_masyarakat) {
                        $formData = $item->t_pengaduan_masyarakat;
                        $formUrl = url('daftar-review-pengaduan-masyarakat/editData/' . $formData->pengaduan_masyarakat_id);
                    } elseif ($item->notif_verif_kategori === 'E-Form Whistle Blowing System' && $item->t_wbs) {
                        $formData = $item->t_wbs;
                        $formUrl = url('daftar-review-whistle-blowing-system/editData/' . $formData->wbs_id);
                    } elseif ($item->notif_verif_kategori === 'E-Form Permohonan Perawatan Sarana Prasarana' && $item->t_permohonan_perawatan) {
                        $formData = $item->t_permohonan_perawatan;
                        $formUrl = url('daftar-review-permohonan-perawatan-sarana-prasarana/editData/' . $formData->permohonan_perawatan_id);
                    }

                    $isDibaca = !is_null($item->notif_verif_dibaca_tgl);
                @endphp

                <div class="card mb-3 notifikasi-item {{ $isDibaca ? 'notifikasi-dibaca' : '' }}" 
                     style="border-left: 4px solid {{ $isDibaca ? '#28a745' : '#007bff' }};">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex: 1;">
                                <h5 class="card-title mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    {{ $item->notif_verif_kategori }}
                                    @if(!$isDibaca)
                                        <span class="badge badge-primary ml-2">Baru</span>
                                    @endif
                                </h5>
                                <p class="card-text mb-2">{{ $item->notif_verif_pesan }}</p>
                                <small class="text-muted">
                                    <i class="far fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                    ({{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }})
                                </small>

                                @if($isDibaca)
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-check"></i>
                                            Dibaca oleh: {{ $item->notif_verif_dibaca_oleh }} pada 
                                            {{ \Carbon\Carbon::parse($item->notif_verif_dibaca_tgl)->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex flex-column" style="gap: 0.5rem;">
                                <a href="{{ $formUrl }}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>

                                @if(!$isDibaca)
                                    <button class="btn btn-sm btn-success tandai-dibaca"
                                        data-id="{{ $item->notif_verif_id }}"
                                        data-kategori="{{ $kategori }}">
                                        <i class="fas fa-check"></i> Tandai Dibaca
                                    </button>
                                @endif

                                <button class="btn btn-sm btn-danger hapus-notifikasi"
                                    data-id="{{ $item->notif_verif_id }}"
                                    data-sudah-dibaca="{{ $item->notif_verif_dibaca_tgl }}"
                                    data-kategori="{{ $kategori }}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Tombol Aksi Massal -->
            <div class="mt-3">
                <button id="tandai-semua-dibaca" class="btn btn-success" data-kategori="{{ $kategori }}">
                    <i class="fas fa-check-double"></i> Tandai Semua Telah Dibaca
                </button>
                <button id="hapus-semua-dibaca" class="btn btn-danger ml-2" data-kategori="{{ $kategori }}">
                    <i class="fas fa-trash-alt"></i> Hapus Semua yang Telah Dibaca
                </button>
            </div>
        @endif
    </div>
</div>

<style>
    .notifikasi-item {
        transition: all 0.3s ease;
    }

    .notifikasi-item:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .notifikasi-dibaca {
        opacity: 0.7;
    }
</style>

<script>
    // Tandai Telah Dibaca
    document.querySelectorAll('.tandai-dibaca').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const kategori = this.dataset.kategori;

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menandai notifikasi ini telah dibaca?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('notifikasi-verifikasi/updateData') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action: 'tandai-dibaca',
                            kategori: kategori
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
                    });
                }
            });
        });
    });

    // Hapus Notifikasi
    document.querySelectorAll('.hapus-notifikasi').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const sudahDibaca = this.dataset.sudahDibaca;
            const kategori = this.dataset.kategori;

            if (!sudahDibaca || sudahDibaca === 'null') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Anda harus menandai notifikasi sebagai "Telah Dibaca" terlebih dahulu sebelum menghapusnya.'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menghapus notifikasi ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('notifikasi-verifikasi/deleteData') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action: 'hapus-single',
                            kategori: kategori
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
                    });
                }
            });
        });
    });

    // Tandai Semua Telah Dibaca
    document.getElementById('tandai-semua-dibaca').addEventListener('click', function() {
        const kategori = this.dataset.kategori;
        
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menandai semua notifikasi telah dibaca?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Tandai Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('notifikasi-verifikasi/updateData') }}/0`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: 'tandai-semua-dibaca',
                        kategori: kategori
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
                });
            }
        });
    });

    // Hapus Semua Notifikasi Telah Dibaca
    document.getElementById('hapus-semua-dibaca').addEventListener('click', function() {
        const kategori = this.dataset.kategori;
        
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menghapus semua notifikasi yang telah dibaca?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('notifikasi-verifikasi/deleteData') }}/0`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: 'hapus-semua-dibaca',
                        kategori: kategori
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
                });
            }
        });
    });
</script>

@endsection
