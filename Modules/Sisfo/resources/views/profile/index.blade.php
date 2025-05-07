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
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#biodata" data-toggle="tab">Biodata</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#editprofil" data-toggle="tab">Update Profil</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">Update Password</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="biodata">
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
                                        <hr>
                                    </div>
                                </div>
                            </div>

                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="editprofil">
                                <form class="form-horizontal" method="POST"
                                    action="{{ url('profile/update_pengguna', Auth::user()->user_id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group row">
                                        <label for="foto_profil" class="col-sm-2 col-form-label">Foto Profil</label>
                                        <div class="col-sm-10 d-flex align-items-center gap-3">
                                            @if (Auth::user()->foto_profil)
                                                <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}"
                                                    alt="Foto Profil" class="img-thumbnail" width="80" height="80">
                                            @else
                                                <img src="{{ asset('img/userr.png') }}" alt="Foto Default"
                                                    class="img-thumbnail" width="80" height="80">
                                            @endif
                                            <input type="file" class="form-control-file ml-3" name="foto_profil"
                                                accept="image/*">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nama_pengguna" class="col-sm-2 col-form-label">Nama</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="nama_pengguna"
                                                name="nama_pengguna" placeholder="Masukkan nama lengkap"
                                                value="{{ old('nama_pengguna', Auth::user()->nama_pengguna) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="alamat_pengguna" class="col-sm-2 col-form-label">Alamat</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="alamat_pengguna"
                                                name="alamat_pengguna" placeholder="Masukkan alamat"
                                                value="{{ old('alamat_pengguna', Auth::user()->alamat_pengguna) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="no_hp_pengguna" class="col-sm-2 col-form-label">Nomor HP</label>
                                        <div class="col-sm-10">
                                            <input type="tel" class="form-control" id="no_hp_pengguna"
                                                name="no_hp_pengguna" placeholder="Contoh: 081234567890"
                                                value="{{ old('no_hp_pengguna', Auth::user()->no_hp_pengguna) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email_pengguna" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="email_pengguna"
                                                name="email_pengguna" placeholder="Masukkan email"
                                                value="{{ old('email_pengguna', Auth::user()->email_pengguna) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="pekerjaan_pengguna" class="col-sm-2 col-form-label">Pekerjaan</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="pekerjaan_pengguna"
                                                name="pekerjaan_pengguna" placeholder="Masukkan pekerjaan"
                                                value="{{ old('pekerjaan_pengguna', Auth::user()->pekerjaan_pengguna) }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nik_pengguna" class="col-sm-2 col-form-label">NIK</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="nik_pengguna"
                                                name="nik_pengguna" placeholder="Masukkan NIK"
                                                value="{{ old('nik_pengguna', Auth::user()->nik_pengguna) }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="upload_nik_pengguna" class="col-sm-2 col-form-label">Foto Kartu
                                            Identitas</label>
                                        <div class="col-sm-10 d-flex align-items-center gap-3">
                                            @if (Auth::user()->upload_nik_pengguna)
                                                <img src="{{ asset('storage/' . Auth::user()->upload_nik_pengguna) }}"
                                                    alt="Foto Kartu Identitas" class="img-thumbnail" width="100"
                                                    height="100">
                                            @else
                                                <div class="d-flex flex-column align-items-center justify-content-center border rounded p-2"
                                                    style="width: 100px; height: 100px; background-color: #f8f9fa;">
                                                    <i class="far fa-id-card fa-2x text-muted"></i>
                                                    <small class="text-muted text-center mt-1">Belum ada foto</small>
                                                </div>
                                            @endif
                                            <input type="file" class="form-control-file ml-3"
                                                name="upload_nik_pengguna" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10 offset-sm-2">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="password">
                                <form class="form-horizontal" method="POST"
                                    action="{{ url('profile/update_password', Auth::user()->user_id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group row">
                                        <label for="current_password" class="col-sm-2 col-form-label">Password
                                            Lama</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" placeholder="Masukkan password lama" required
                                                autocomplete="current-password">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password" class="col-sm-2 col-form-label">Password Baru</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" placeholder="Masukkan password baru" required
                                                autocomplete="new-password">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password_confirmation" class="col-sm-2 col-form-label">Konfirmasi
                                            Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" placeholder="Ulangi password baru"
                                                required autocomplete="new-password">
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
            </section>
        @endsection
