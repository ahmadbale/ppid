@extends('sisfo::layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img src="{{ Auth::user()->foto_profil ? asset('storage/' . Auth::user()->foto_profil) : asset('img/userr.png') }}"
                                class="img-circle elevation-2 mb-2" alt="User Image"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        </div>

                        <h3 class="profile-username text-center">
                            {{ Auth::user()->nama_pengguna }}
                        </h3>

                        <p class="text-muted text-center">
                            {{ Auth::user()->level->hak_akses_nama }}
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>No Hp</b> <a class="float-right">{{ Auth::user()->no_hp_pengguna }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Email</b> <a class="float-right">{{ Auth::user()->email_pengguna }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>NIK</b> <a class="float-right">{{ Auth::user()->nik_pengguna }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link {{ session('error_type') != 'update_profil' && session('error_type') != 'update_password' ? 'active' : '' }}"
                                    href="#biodata" data-toggle="tab">Biodata</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ session('error_type') == 'update_profil' ? 'active' : '' }}"
                                    href="#editprofil" data-toggle="tab">Update Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ session('error_type') == 'update_password' ? 'active' : '' }}"
                                    href="#password" data-toggle="tab">Update Password</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab Biodata -->
                            <div class="{{ session('error_type') != 'update_profil' && session('error_type') != 'update_password' ? 'active' : '' }} tab-pane"
                                id="biodata">
                                <div class="card-body">
                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat</strong>
                                    <p class="text-muted">{{ Auth::user()->alamat_pengguna }}</p>
                                    <hr>
                                    <strong><i class="fas fa-briefcase mr-1"></i> Pekerjaan</strong>
                                    <p class="text-muted">{{ Auth::user()->pekerjaan_pengguna }}</p>
                                    <hr>
                                    <strong><i class="far fa-image mr-1"></i> Foto Kartu Identitas</strong>
                                    <div class="mt-2">
                                        @if (
                                            !empty(Auth::user()->upload_nik_pengguna) &&
                                                file_exists(public_path('storage/' . Auth::user()->upload_nik_pengguna)))
                                            <p>
                                                <img src="{{ asset('storage/' . Auth::user()->upload_nik_pengguna) }}"
                                                    alt="Foto Kartu Identitas" width="200" class="img-thumbnail">
                                            </p>
                                        @else
                                            <div class="text-muted">
                                                <i class="far fa-id-card fa-4x"></i>
                                                <p class="mt-2">Belum ada foto Kartu Identitas</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Update Profil -->
                            <div class="{{ session('error_type') == 'update_profil' ? 'active' : '' }} tab-pane"
                                id="editprofil">
                                <form class="form-horizontal" method="POST" enctype="multipart/form-data"
                                    action="{{ url('profile/update_pengguna', Auth::user()->user_id) }}">
                                    @csrf
                                    @method('PUT')

                                    <!-- Foto Profil -->
                                    <div class="form-group row">
                                        <label for="foto_profil" class="col-sm-2 col-form-label">Foto Profil</label>
                                        <div class="col-sm-10">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                @if (Auth::user()->foto_profil)
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}"
                                                            alt="Foto Profil" class="img-thumbnail" width="80"
                                                            height="80">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm position-absolute"
                                                            style="top: -5px; right: -5px; padding: 2px 6px;"
                                                            onclick="showDeleteModal('foto_profil', {{ Auth::user()->user_id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <img src="{{ asset('img/userr.png') }}" alt="Foto Default"
                                                        class="img-thumbnail" width="80" height="80">
                                                @endif
                                            </div>
                                            <input type="file"
                                                class="form-control-file @error('foto_profil') is-invalid @enderror"
                                                name="foto_profil" accept="image/*">
                                            @error('foto_profil')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Form fields lainnya tetap sama -->
                                    <div class="form-group row">
                                        <label for="nama_pengguna" class="col-sm-2 col-form-label">Nama</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('nama_pengguna') is-invalid @enderror"
                                                id="nama_pengguna" name="nama_pengguna"
                                                placeholder="Masukkan nama lengkap"
                                                value="{{ old('nama_pengguna', Auth::user()->nama_pengguna) }}">
                                            @error('nama_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="alamat_pengguna" class="col-sm-2 col-form-label">Alamat</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('alamat_pengguna') is-invalid @enderror"
                                                id="alamat_pengguna" name="alamat_pengguna" placeholder="Masukkan alamat"
                                                value="{{ old('alamat_pengguna', Auth::user()->alamat_pengguna) }}">
                                            @error('alamat_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="no_hp_pengguna" class="col-sm-2 col-form-label">Nomor HP</label>
                                        <div class="col-sm-10">
                                            <input type="tel"
                                                class="form-control @error('no_hp_pengguna') is-invalid @enderror"
                                                id="no_hp_pengguna" name="no_hp_pengguna"
                                                placeholder="Contoh: 081234567890"
                                                value="{{ old('no_hp_pengguna', Auth::user()->no_hp_pengguna) }}">
                                            @error('no_hp_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email_pengguna" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="email"
                                                class="form-control @error('email_pengguna') is-invalid @enderror"
                                                id="email_pengguna" name="email_pengguna" placeholder="Masukkan email"
                                                value="{{ old('email_pengguna', Auth::user()->email_pengguna) }}">
                                            @error('email_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="pekerjaan_pengguna" class="col-sm-2 col-form-label">Pekerjaan</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('pekerjaan_pengguna') is-invalid @enderror"
                                                id="pekerjaan_pengguna" name="pekerjaan_pengguna"
                                                placeholder="Masukkan pekerjaan"
                                                value="{{ old('pekerjaan_pengguna', Auth::user()->pekerjaan_pengguna) }}">
                                            @error('pekerjaan_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nik_pengguna" class="col-sm-2 col-form-label">NIK</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('nik_pengguna') is-invalid @enderror"
                                                id="nik_pengguna" name="nik_pengguna" placeholder="Masukkan NIK"
                                                value="{{ old('nik_pengguna', Auth::user()->nik_pengguna) }}">
                                            @error('nik_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Foto KTP -->
                                    <div class="form-group row">
                                        <label for="upload_nik_pengguna" class="col-sm-2 col-form-label">Foto Kartu
                                            Identitas</label>
                                        <div class="col-sm-10">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                @if (Auth::user()->upload_nik_pengguna)
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . Auth::user()->upload_nik_pengguna) }}"
                                                            alt="Foto Kartu Identitas" class="img-thumbnail"
                                                            width="100" height="100">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm position-absolute"
                                                            style="top: -5px; right: -5px; padding: 2px 6px;"
                                                            onclick="showDeleteModal('foto_ktp', {{ Auth::user()->user_id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="d-flex flex-column align-items-center justify-content-center border rounded p-2"
                                                        style="width: 100px; height: 100px; background-color: #f8f9fa;">
                                                        <i class="far fa-id-card fa-2x text-muted"></i>
                                                        <small class="text-muted text-center mt-1">Belum ada foto</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file"
                                                class="form-control-file @error('upload_nik_pengguna') is-invalid @enderror"
                                                name="upload_nik_pengguna" accept="image/*">
                                            @error('upload_nik_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-10 offset-sm-2">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab Update Password -->
                            <div class="{{ session('error_type') == 'update_password' ? 'active' : '' }} tab-pane"
                                id="password">
                                <form class="form-horizontal" method="POST"
                                    action="{{ url('profile/update_password', Auth::user()->user_id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group row">
                                        <label for="current_password" class="col-sm-2 col-form-label">Password
                                            Lama</label>
                                        <div class="col-sm-10">
                                            <input type="password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                id="current_password" name="current_password"
                                                placeholder="Masukkan password lama" required
                                                autocomplete="current-password">
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password" class="col-sm-2 col-form-label">Password Baru</label>
                                        <div class="col-sm-10">
                                            <input type="password"
                                                class="form-control @error('new_password') is-invalid @enderror"
                                                id="new_password" name="new_password"
                                                placeholder="Masukkan password baru" required autocomplete="new-password">
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password_confirmation" class="col-sm-2 col-form-label">Konfirmasi
                                            Password</label>
                                        <div class="col-sm-10">
                                            <input type="password"
                                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                                id="new_password_confirmation" name="new_password_confirmation"
                                                placeholder="Ulangi password baru" required autocomplete="new-password">
                                            @error('new_password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-10 offset-sm-2">
                                            <button type="submit" class="btn btn-warning">Ubah Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="opacity: 0.8;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center px-5 py-4">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-light"
                            style="width: 80px; height: 80px; background-color: #fff3cd;">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3 font-weight-bold text-dark" id="deleteConfirmModalLabel">
                        Konfirmasi Penghapusan
                    </h4>
                    <p class="text-muted mb-4" id="deleteMessage" style="font-size: 1.1rem; line-height: 1.6;">
                        Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light btn-lg px-4 mr-3" data-dismiss="modal"
                            style="border: 1px solid #ddd; font-weight: 500;">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="button" class="btn btn-danger btn-lg px-4" id="confirmDeleteBtn"
                            style="font-weight: 500;">
                            <i class="fas fa-trash mr-2"></i>Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Loading -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <h5 class="mb-2 font-weight-bold text-dark">Memproses</h5>
                    <p class="mb-0 text-muted">Sedang menghapus foto, mohon tunggu...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center px-5 py-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width: 80px; height: 80px; background-color: #d4edda;">
                            <i class="fas fa-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="mb-3 font-weight-bold text-success">Berhasil!</h4>
                    <p class="text-muted mb-4" id="successMessage" style="font-size: 1.1rem; line-height: 1.6;">
                        Foto berhasil dihapus dari sistem.
                    </p>
                    <button type="button" class="btn btn-success btn-lg px-4" onclick="location.reload()"
                        style="font-weight: 500;">
                        <i class="fas fa-check mr-2"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center px-5 py-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width: 80px; height: 80px; background-color: #f8d7da;">
                            <i class="fas fa-times text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="mb-3 font-weight-bold text-danger">Gagal!</h4>
                    <p class="text-muted mb-4" id="errorMessage" style="font-size: 1.1rem; line-height: 1.6;">
                        Terjadi kesalahan saat memproses permintaan Anda.
                    </p>
                    <button type="button" class="btn btn-danger btn-lg px-4" data-dismiss="modal"
                        style="font-weight: 500;">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Modal Konfirmasi - PERBAIKAN -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variabel untuk menyimpan info hapus
            let currentDeleteType = '';
            let currentUserId = '';

            // Fungsi untuk menampilkan modal konfirmasi
            window.showDeleteModal = function(type, userId) {
                currentDeleteType = type;
                currentUserId = userId;

                // Pastikan jQuery tersedia
                if (typeof $ === 'undefined') {
                    console.error('jQuery tidak tersedia. Menggunakan vanilla JavaScript');

                    // Menggunakan vanilla JavaScript
                    const modal = document.getElementById('deleteConfirmModal');
                    const title = document.getElementById('deleteConfirmModalLabel');
                    const message = document.getElementById('deleteMessage');

                    if (type === 'foto_profil') {
                        title.textContent = 'Hapus Foto Profil';
                        message.textContent =
                            'Apakah Anda yakin ingin menghapus foto profil ini? Foto yang dihapus tidak dapat dikembalikan.';
                    } else if (type === 'foto_ktp') {
                        title.textContent = 'Hapus Foto Kartu Identitas';
                        message.textContent =
                            'Apakah Anda yakin ingin menghapus foto kartu identitas ini? Foto yang dihapus tidak dapat dikembalikan.';
                    }

                    // Tampilkan modal - membutuhkan Bootstrap JS
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                } else {
                    // Menggunakan jQuery jika tersedia
                    const modal = $('#deleteConfirmModal');
                    const title = modal.find('#deleteConfirmModalLabel');
                    const message = modal.find('#deleteMessage');

                    if (type === 'foto_profil') {
                        title.text('Hapus Foto Profil');
                        message.text(
                            'Apakah Anda yakin ingin menghapus foto profil ini? Foto yang dihapus tidak dapat dikembalikan.'
                            );
                    } else if (type === 'foto_ktp') {
                        title.text('Hapus Foto Kartu Identitas');
                        message.text(
                            'Apakah Anda yakin ingin menghapus foto kartu identitas ini? Foto yang dihapus tidak dapat dikembalikan.'
                            );
                    }

                    modal.modal('show');
                }
            };

            // Event listener untuk tombol konfirmasi hapus
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                // Sembunyikan modal konfirmasi
                if (typeof $ !== 'undefined') {
                    $('#deleteConfirmModal').modal('hide');

                    // Tampilkan loading modal dengan delay
                    setTimeout(() => {
                        $('#loadingModal').modal('show');
                    }, 300);
                } else {
                    const confirmModal = document.getElementById('deleteConfirmModal');
                    bootstrap.Modal.getInstance(confirmModal).hide();

                    // Tampilkan loading modal
                    setTimeout(() => {
                        const loadingModal = document.getElementById('loadingModal');
                        bootstrap.Modal.getOrCreateInstance(loadingModal).show();
                    }, 300);
                }

                // Tentukan endpoint berdasarkan tipe hapus
                let endpoint = '';
                if (currentDeleteType === 'foto_profil') {
                    endpoint = `{{ url('profile/delete_foto_profil') }}/${currentUserId}`;
                } else if (currentDeleteType === 'foto_ktp') {
                    endpoint = `{{ url('profile/delete_foto_ktp') }}/${currentUserId}`;
                }

                console.log('Endpoint:', endpoint);

                // Lakukan request hapus
                fetch(endpoint, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Sembunyikan loading modal
                        if (typeof $ !== 'undefined') {
                            $('#loadingModal').modal('hide');
                        } else {
                            const loadingModal = document.getElementById('loadingModal');
                            bootstrap.Modal.getInstance(loadingModal).hide();
                        }

                        console.log('Response data:', data);

                        // Tampilkan pesan berhasil/gagal
                        setTimeout(() => {
                            if (data.success) {
                                if (typeof $ !== 'undefined') {
                                    $('#successMessage').text(data.message ||
                                        'Foto berhasil dihapus dari sistem.');
                                    $('#successModal').modal('show');
                                } else {
                                    const successModal = document.getElementById(
                                    'successModal');
                                    document.getElementById('successMessage').textContent = data
                                        .message || 'Foto berhasil dihapus dari sistem.';
                                    bootstrap.Modal.getOrCreateInstance(successModal).show();
                                }
                            } else {
                                if (typeof $ !== 'undefined') {
                                    $('#errorMessage').text(data.message ||
                                        'Terjadi kesalahan saat menghapus foto.');
                                    $('#errorModal').modal('show');
                                } else {
                                    const errorModal = document.getElementById('errorModal');
                                    document.getElementById('errorMessage').textContent = data
                                        .message || 'Terjadi kesalahan saat menghapus foto.';
                                    bootstrap.Modal.getOrCreateInstance(errorModal).show();
                                }
                            }
                        }, 300);
                    })
                    .catch(error => {
                        // Sembunyikan loading modal
                        if (typeof $ !== 'undefined') {
                            $('#loadingModal').modal('hide');
                        } else {
                            const loadingModal = document.getElementById('loadingModal');
                            if (bootstrap.Modal.getInstance(loadingModal)) {
                                bootstrap.Modal.getInstance(loadingModal).hide();
                            }
                        }

                        console.error('Error:', error);

                        // Tampilkan pesan error
                        setTimeout(() => {
                            if (typeof $ !== 'undefined') {
                                $('#errorMessage').text(
                                    'Terjadi kesalahan jaringan. Pastikan koneksi internet Anda stabil.'
                                    );
                                $('#errorModal').modal('show');
                            } else {
                                const errorModal = document.getElementById('errorModal');
                                document.getElementById('errorMessage').textContent =
                                    'Terjadi kesalahan jaringan. Pastikan koneksi internet Anda stabil.';
                                bootstrap.Modal.getOrCreateInstance(errorModal).show();
                            }
                        }, 300);
                    });
            });

            // Handler sukses reload
            document.querySelector('button[onclick="location.reload()"]').addEventListener('click', function() {
                window.location.reload();
            });

            // Auto hide alerts after 7 seconds
            if (typeof $ !== 'undefined') {
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 7000);
            } else {
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 7000);
            }

            // Modal animations - jQuery version
            if (typeof $ !== 'undefined') {
                $('.modal').on('show.bs.modal', function() {
                    $(this).find('.modal-content').removeClass('modal-slide-out').addClass(
                    'modal-slide-in');
                });

                $('.modal').on('hide.bs.modal', function() {
                    $(this).find('.modal-content').removeClass('modal-slide-in').addClass(
                    'modal-slide-out');
                });
            }
        });
    </script>

    <!-- CSS untuk Styling Modal yang Lebih Profesional -->
    <style>
        /* Modal Enhancements */
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: none;
            overflow: hidden;
        }

        .modal-lg {
            max-width: 450px;
        }

        .modal-sm .modal-content {
            max-width: 320px;
            margin: 0 auto;
        }

        /* Button Styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border: none;
            padding: 10px 20px;
        }

        .btn-lg {
            padding: 12px 30px;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-light {
            background-color: #f8f9fa;
            color: #495057;
        }

        .btn-light:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        /* Spinner Enhancement */
        .spinner-border {
            border-width: 3px;
        }

        /* Modal Animations */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes modalSlideOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            to {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
        }

        .modal-slide-in {
            animation: modalSlideIn 0.3s ease-out;
        }

        .modal-slide-out {
            animation: modalSlideOut 0.2s ease-in;
        }

        /* Icon Containers */
        .rounded-circle {
            border: 3px solid transparent;
        }

        /* Typography Improvements */
        .modal-title {
            font-size: 1.25rem;
            color: #2c3e50;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Close Button Enhancement */
        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            z-index: 1051;
            color: #6c757d;
            font-size: 1.5rem;
            font-weight: 300;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .close:hover {
            opacity: 1;
            color: #495057;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .modal-lg {
                max-width: 90%;
            }

            .modal-body {
                padding: 2rem 1.5rem !important;
            }

            .btn-lg {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        /* Alert Improvements */
        .alert {
            border-radius: 8px;
            border: none;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
@endsection
