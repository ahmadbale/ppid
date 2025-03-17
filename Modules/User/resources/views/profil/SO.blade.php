@extends('user::layouts.profil-sidebar')

@section('title', 'Struktur Organisasi')

@section('content-side')
<div class="profile-card bg-white p-8 shadow-sm rounded-lg">
    <div class="m-4">
        <h2 class="fw-bold pb-2 text-center text-md-start">Struktur Organisasi</h2>
        <div class="flex items-center text-gray-500 text-sm mt-2 mb-4">
            <i class="bi bi-clock-fill text-warning me-2"></i>
            <span class="ml-1">Diperbarui pada 12 Maret 2025, 16.10 </span>
        </div>

        <!-- dokumen -->
        <p class="mt-2 text-gray-700 leading-relaxed">
            Struktur Organisasi PPID Politeknik Negeri Malang berdasarkan Keputusan Direktur Nomor
                876/UN.1.P/KPT/HUKOR/2023.</p>

        <!-- Gambar Struktur Organisasi-->
        <div class="mb-4 d-flex justify-content-center">
            <img src="{{ asset('img/struktur-organisasi.png') }}"
                 alt="Gambar struktur organisasi"
                 class="img-fluid rounded shadow">
        </div>
    </div>
</div>
@endsection
