@extends('user::layouts.right-sidebar')

@section('title', 'Profil')

@section('content-side')
    <div class="main-content bg-white p-5">
        <h2 style="font-weight: 700; padding-bottom:15px;">Profil PPID</h2>
        <div class="flex items-center text-gray-500 text-sm mt-2">
            <i class="bi bi-clock-fill text-warning"></i>Diperbarui pada 12 Maret 2025, 16.10
        </div>
        <p class="mt-4 text-gray-700 leading-relaxed">
            Dalam memberikan pelayanan Informasi publik sebagaimana diamanatkan dalam Undang-Undang Nomor 14 Tahun 2008 Tentang Keterbukaan Informasi Publik, Politeknik Negeri Malang berkomitmen untuk mendukung dan melaksanakan pelayanan Informasi Publik tersebut.
        <br><br>
            Sebagai bentuk nyata dari komitmen tersebut Politeknik Negeri Malang (Polinema) sudah membentuk Pejabat Pengelola Informasi dan Dokumentasi yang selanjutnya disebut PPID, yang bertanggungjawab di bidang penyimpanan, pendokumentasian, penyediaan, dan/atau pelayanan informasi di Badan Publik.
        <br><br>
            Informasi umum tentang Polinema dapat diakses langsung melalui website <a href="https://polinema.ac.id" class="text-blue-500 underline">https://polinema.ac.id</a>. Sedangkan informasi lain yang tidak tersedia, publik dapat mengakses di <a href="https://ppid.polinema.ac.id" class="text-blue-500 underline">ppid.polinema.ac.id</a>.
        </p>
    </div>

    <div class="main-content bg-white p-5">
        <h4 class="fw-bold text-start">Atasan Pejabat Pengelola Informasi dan Dokumentasi (PPID)</h4>
        <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>
        <div class="row">
            <!-- Kolom Foto -->
            <div class="col-md-4">
                <div class="profile-photo bg-light text-center p-5">
                    Foto formal beliau wadir 4
                </div>
            </div>
            <!-- Kolom Deskripsi -->
            <div class="col-md-8">
                <p><strong>Deskripsi singkat mengenai atasan PPID Polinema</strong></p>
                <p>
                    Prof. Dr. Nama Lengkap, M.Med.Ed., Sp.OG(K), Ph.D. sebagai atasan Pejabat
                    Pengelola Informasi dan Dokumentasi (PPID) Polinema. Beliau menjabat
                    sebagai Wakil Direktur 4 di Polinema dan memiliki berbagai pengalaman
                    akademik serta administratif.
                </p>
                <p><strong>Beberapa jabatan yang pernah diemban:</strong></p>
                <ul>
                    <li>Koordinator Program Akademik (2000 - 2005)</li>
                    <li>Wakil Dekan Bidang Akademik (2006 - 2010)</li>
                    <li>Ketua Tim Kurikulum (2011 - 2015)</li>
                    <li>Anggota Tim Pengembangan Pendidikan Polinema (2016 - Sekarang)</li>
                </ul>
                <p><strong>Penghargaan yang diterima:</strong></p>
                <ul>
                    <li>Best Lecturer Award 2018</li>
                    <li>Innovative Educator Award 2020</li>
                    <li>Academic Leadership Award 2023</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
