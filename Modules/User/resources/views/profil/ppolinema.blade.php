<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="background-color: #f8f9fa">
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <div class="pengantar-section">
        <h3 class="title-section">Profil Politeknik Negeri Malang</h3>
        <div class="mt-4 border-top border-1  mb-4 w-65 mx-auto"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="text-center">
                    <p>
                        Polinema adalah institusi pendidikan vokasi di Malang,
                        kota terbesar kedua di Jawa Timur dengan udara sejuk dan
                        akses transportasi yang mudah. Polinema terus berkembang
                        sebagai institusi unggul dengan sistem pendidikan inovatif,
                        keterampilan kompetitif, serta mendukung penelitian terapan
                        dan pengabdian masyarakat. Dengan manajemen berbasis good
                        governance, Polinema menciptakan atmosfer akademik kondusif
                        untuk meningkatkan kualitas SDM, pengajaran, serta jiwa
                        wirausaha.
                        {{-- {{ $pengantar['content'] }} --}}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sambutan Direktur -->
    <section class="sambutan-section text-center">
        <div class="container">
            <h3 class="fw-bold m-4">Sambutan Direktur</h3>
            <div class="row sambutan-content">

                <div class="col-12 col-md-6 d-flex justify-content-center">
                    <img src="{{ asset('img/direktur-polinema-bendera.webp') }}" alt="gambar-pengantar" class="img">
                </div>
                <div class="col-12 col-md-6 sambutan-text m-2">
                    <p>
                        Selamat datang di laman Pejabat Pengelola Informasi dan Dokumentasi
                        (PPID) Politeknik Negeri Malang (Polinema). Sebagai institusi
                        pendidikan vokasi yang berkomitmen mencetak lulusan berkualitas dan
                        berdaya saing global, Polinema senantiasa mengedepankan prinsip
                        transparansi dan keterbukaan informasi publik.
                        <br><br>
                        Dalam era digital saat ini, akses terhadap informasi yang akurat dan
                        terpercaya menjadi kebutuhan utama masyarakat. Oleh karena itu,
                        melalui PPID, Polinema berupaya menyediakan layanan informasi yang
                        cepat, tepat, dan mudah diakses oleh seluruh pemangku kepentingan.
                        Kami memastikan bahwa setiap informasi publik yang tersedia dapat
                        diakses dengan baik, kecuali yang dikecualikan sesuai dengan
                        ketentuan peraturan perundang-undangan yang berlaku.
                        <br><br>
                        Kami berharap kehadiran PPID Polinema dapat menjadi jembatan
                        komunikasi yang efektif antara institusi dan masyarakat, serta
                        mendukung tata kelola yang transparan dan akuntabel. Kami juga
                        mengajak seluruh sivitas akademika dan masyarakat untuk memanfaatkan
                        layanan ini dengan sebaik-baiknya demi terwujudnya keterbukaan
                        informasi yang berkualitas.
                        <br><br>
                        [Nama Direktur]
                        Direktur Politeknik Negeri Malang
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Visi & Misi -->
    <section class="visi-misi my-5">
        <div class="row flex-column flex-md-row">
        <div class="visi col-12 col-md-6 p-4 text-center text-md-start">
            <h4 class="fw-bold text-center m-4">Visi</h4>
            <p>Menjadi lembaga pendidikan tinggi vokasi yang unggul dalam persaingan global.</p>
        </div>
        <div class="misi col-12 col-md-6 p-4">
            <h4 class="fw-bold text-center m-4">Misi</h4>
            <ol>
                <li>Menyelenggarakan dan mengembangkan pendidikan vokasi yang berkualitas dan inovatif.</li>
                <li>Menyelenggarakan penelitian terapan yang bermanfaat.</li>
                <li>Menyelenggarakan pengabdian kepada masyarakat yang bermanfaat bagi kesejahteraan.</li>
                <li>Menyelenggarakan sistem pengelolaan pendidikan yang berbasis tata kelola yang baik.</li>
                <li>Mengembangkan kerja sama dengan berbagai pihak dalam maupun luar negeri.</li>
            </ol>
        </div>
        </div>
    </section>
    @include('user::layouts.footer')
</body>

</html>
