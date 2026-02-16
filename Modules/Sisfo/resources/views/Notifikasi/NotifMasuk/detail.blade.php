@extends('sisfo::layouts.template')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <a href="{{ url('notifikasi-masuk') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
        <h3 class="card-title"><strong> {{ $page->title }} </strong></h3>
    </div>
    <div class="card-body">
        @if($notifikasi->isEmpty())
            <!-- Jika tidak ada notifikasi -->
            <div class="d-flex flex-column align-items-center justify-content-center"
                style="height: 200px; background-color: #fff3cd; border: 1px solid #856404; border-radius: 10px;">
                <span style="font-size: 50px;">📭</span>
                <p style="margin: 0; font-weight: bold; font-size: 18px; text-align: center;">Tidak ada Notifikasi
                    Baru</p>
            </div>
        @else
            <!-- Container Notifikasi -->
            @foreach($notifikasi as $item)
                <div class="p-3 mb-3 notifikasi-item {{ $item->notif_masuk_dibaca_tgl ? 'notifikasi-dibaca' : '' }}"
                    style="border: 1px solid #ddd; border-radius: 10px; background-color: {{ $item->notif_masuk_dibaca_tgl ? '#f5f5f5' : '#fff' }};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1;">
                            <p style="margin: 0; font-weight: bold;">{{ $item->notif_masuk_pesan }}</p>
                            <small class="text-muted">
                                <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}
                            </small>
                            @if($item->notif_masuk_dibaca_oleh)
                                <br>
                                <small class="text-success">
                                    <i class="fa fa-check-circle"></i> Dibaca oleh: {{ $item->notif_masuk_dibaca_oleh }}
                                </small>
                            @endif
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <button class="btn btn-danger btn-sm hapus-notifikasi" data-id="{{ $item->notif_masuk_id }}"
                                data-sudah-dibaca="{{ $item->notif_masuk_dibaca_tgl }}" 
                                data-kategori="{{ $kategori }}"
                                style="width: 132px;">
                                <i class="fa fa-trash"></i> Hapus Notifikasi
                            </button>

                            @if(!$item->notif_masuk_dibaca_tgl)
                                <button class="btn btn-secondary btn-sm mt-2 tandai-dibaca" 
                                    data-id="{{ $item->notif_masuk_id }}"
                                    data-kategori="{{ $kategori }}">
                                    <i class="fa fa-check"></i> Tandai Telah Dibaca
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Tombol Aksi Massal -->
            <div class="mt-3">
                <button id="tandai-semua-dibaca" class="btn btn-secondary" data-kategori="{{ $kategori }}">
                    <i class="fa fa-check-double"></i> Tandai Semua Telah Dibaca
                </button>
                <button id="hapus-semua-dibaca" class="btn btn-danger" data-kategori="{{ $kategori }}">
                    <i class="fa fa-trash-alt"></i> Hapus Semua yang Telah Dibaca
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
                    fetch(`{{ url('notifikasi-masuk/updateData') }}/${id}`, {
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
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
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
                        Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
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
                    title: 'Tidak Dapat Dihapus',
                    text: 'Notifikasi harus ditandai telah dibaca terlebih dahulu sebelum dapat dihapus.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
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
                    fetch(`{{ url('notifikasi-masuk/deleteData') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            action: 'hapus',
                            kategori: kategori
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
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
                        Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
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
                fetch(`{{ url('notifikasi-masuk/updateData') }}/0`, {
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
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Informasi', data.message, 'info');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
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
                fetch(`{{ url('notifikasi-masuk/deleteData') }}/0`, {
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
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Informasi', data.message, 'info');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                });
            }
        });
    });
</script>

@endsection
