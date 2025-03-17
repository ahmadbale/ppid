@extends('user::layouts.profil-sidebar')

@section('title', 'Tugas dan Fungsi')

@section('content-side')
<div class="profile-card bg-white p-8 shadow-sm rounded-lg">
    <div class="m-4">
        <h2 class="fw-bold padding-bottom:15px;">Tugas dan Fungsi</h2>
        <div class="flex items-center text-gray-500 text-sm mt-2 mb-4">
            <i class="bi bi-clock-fill text-warning me-2"></i>
            <span class="ml-1">Diperbarui pada 12 Maret 2025, 16.10 </span>
        </div>

        <!-- Tugas PPID -->
        <h6 class="fw-bold">PPID Politeknik Negeri Malang bertugas:</h6>
        <p class="mt-2 text-gray-700 leading-relaxed">
            Merencanakan, mengorganisasikan, melaksanakan, mengawasi, dan mengevaluasi pelaksanaan pelayanan serta pengelolaan informasi dan dokumentasi di lingkungan Polinema. Dalam menjalankan tugasnya, PPID didukung oleh PPID Pelaksana yang berada di setiap unit kerja.
        </p>

        <!-- Fungsi PPID -->
        <h6 class="fw-bold">Pejabat Pengelola Informasi dan Dokumentasi berfungsi:</h6>
        <ul class="list-disc ml-6 text-gray-700 mt-2 leading-relaxed">
            <li>Penyediaan, penyimpanan, pendokumentasian, dan pengamanan informasi;</li>
            <li>Pelayanan informasi;</li>
            <li>Pelayanan Informasi Publik yang cepat, tepat, dan sederhana;</li>
            <li>Penetapan prosedur operasional penyebarluasan Informasi Publik;</li>
            <li>Pengujian konsekuensi;</li>
            <li>Pengklasifikasian informasi dan/atau pengubahannya;</li>
            <li>Penetapan Informasi Publik yang dikecualikan yang telah habis jangka waktu pengecualiannya sebagai Informasi Publik yang dapat diakses;</li>
            <li>Penetapan pertimbangan tertulis atas setiap kebijakan yang diambil untuk memenuhi hak setiap orang atas Informasi Publik;</li>
            <li>Menyelesaikan sengketa Informasi Publik unit organisasi atau unit kerja yang bersangkutan;</li>
            <li>Melakukan evaluasi terhadap kinerja PPID.</li>
        </ul>
    </div>
</div>
@endsection
