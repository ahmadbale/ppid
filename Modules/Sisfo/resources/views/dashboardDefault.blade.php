<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\dashboardDefault.blade.php -->
@extends('sisfo::layouts.template')

@section('content')
    <div class="card">
        <h5 class="card-header font-weight-bold">Dashboard {{ Auth::user()->getRoleName() }}</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="info-message alert-info">
                        <h5><i class="fas fa-info-circle"></i> Selamat Datang!</h5>
                        <p>Anda login sebagai <strong>{{ Auth::user()->getRoleName() }}</strong></p>
                        <p>Dashboard khusus untuk level Anda sedang dalam pengembangan.</p>
                    </div>
                </div>
            </div>
            
            <!-- Tampilkan informasi level saat ini -->
            <div class="row">
                <div class="col-lg-6 col-12 mb-4">
                    <div class="small-box bg-info text-white shadow rounded d-flex flex-column justify-content-between" style="min-height: 120px; padding: 1.5rem;">
                        <div class="inner">
                            <h4 class="font-weight-bold">Level Aktif</h4>
                            <p class="mb-0">{{ Auth::user()->getRoleName() }}</p>
                            <small>Kode: {{ Auth::user()->getRole() }}</small>
                        </div>
                        <div class="icon mt-auto text-end">
                            <i class="fas fa-user-tag fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-12 mb-4">
                    <div class="small-box bg-success text-white shadow rounded d-flex flex-column justify-content-between" style="min-height: 120px; padding: 1.5rem;">
                        <div class="inner">
                            <h4 class="font-weight-bold">Profil Pengguna</h4>
                            <p class="mb-0">{{ Auth::user()->nama_pengguna }}</p>
                            <small>{{ Auth::user()->email_pengguna }}</small>
                        </div>
                        <div class="icon mt-auto text-end">
                            <i class="fas fa-user fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tampilkan informasi level lain yang dimiliki -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0 font-weight-bold">
                                <i class="fas fa-user-friends text-primary"></i> Level Lain yang Anda Punya
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(Auth::user()->getAvailableRoles()->count() > 0)
                                <div class="row">
                                    @foreach(Auth::user()->getAvailableRoles() as $role)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #007bff !important;">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="bg-primary bg-gradient rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                            <i class="fas fa-user-shield text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1 font-weight-bold text-dark">{{ $role->hak_akses_nama }}</h6>
                                                        <span class="badge badge-light text-muted px-2 py-1">
                                                            <i class="fas fa-code text-primary"></i> {{ $role->hak_akses_kode }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <!-- Informasi tambahan -->
                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <small class="text-muted mb-0">
                                            <strong>Total Level:</strong> {{ Auth::user()->getAvailableRoles()->count() + 1 }} level (termasuk level aktif saat ini)
                                        </small>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fas fa-user-times fa-2x text-muted"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-muted font-weight-bold mb-2">Tidak Ada Level Lain</h5>
                                    <p class="text-muted mb-0">Anda hanya memiliki satu level akses saat ini.</p>
                                    <small class="text-muted">Hubungi administrator jika memerlukan akses tambahan.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection