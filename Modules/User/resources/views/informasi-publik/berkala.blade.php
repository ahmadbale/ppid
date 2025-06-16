<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Berkala</title>
    @vite(['resources/css/app.css'])

</head>

<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')
    <div class="spacer" style="height: 80px;"></div> 
    <div class="container">
        <h2 class="fw-bold mt-4 mb-2 text-center text-md-center">Informasi Publik yang Wajib Diumumkan Berkala</h2>
        <div class="mt-4 border-top border-1 pt-3 w-75 mx-auto"></div>
        <div class="mt-2 mb-4 text-gray-600" style="max-width: 1000px; margin: 0 auto;">
            Informasi berkala adalah informasi yang wajib diumumkan secara rutin oleh badan publik tanpa perlu diminta,
            sebagai bentuk transparansi atas kegiatan dan kebijakan publik. Ketentuan ini diatur dalam Undang-Undang No.
            14 Tahun 2008 tentang Keterbukaan Informasi Publik.
        </div>

        <div class="info-bar mb-3"
            style="max-width:1000px;margin:auto;display:flex;justify-content:space-between;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            <div class="flex items-center text-gray-500 text-sm">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <span class="ml-1">
                    Diperbarui pada
                    @if (!empty($updated_item))
                        @php
                            $latestUpdateTime = collect($updated_item)
                                ->filter(function ($item) {
                                    return !empty($item['updated_at']);
                                })
                                ->max('updated_at');
                        @endphp

                        @if ($latestUpdateTime)
                            {{ $latestUpdateTime }}
                        @else
                            (Belum ada pembaruan)
                        @endif
                    @else
                        (Belum ada pembaruan)
                    @endif
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-end" style="min-width:200px;">
                <label for="cari" class="me-2 mb-0">Cari :</label>
                <input type="text" id="cari" name="cari" class="border rounded px-3 py-1 w-48"
                    style="border:1.5px solid #bbb;max-width:250px;" placeholder="">
            </div>
        </div>

        <div class="card shadow-sm mb-4 custom-table-responsive"
            style="max-width:1000px; margin:auto; border-radius:16px; border:1.5px solid #e9e9e9;">
            <div class="card-body p-0">
                <table class="table table-stack mb-0" style="border-radius:16px 16px 0 0; overflow:hidden;">
                    <thead>
                        <tr style="background:#233e73; color: #fff;">
                            <th style="width: 50px;">No.</th>
                            <th class="fw-bold">Nama Informasi Berkala</th>
                            <th style="width: 140px;">Tanggal Informasi</th>
                            <th style="width: 120px;">Dokumen</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($informasiBerkala as $no => $info)
                            <tr>
                                <td data-label="No.">{{ $no + 1 }}.</td>
                                <td data-label="Nama Informasi">{{ $info['nama'] }}</td>
                                <td data-label="Tanggal Informasi">{{ $info['tanggal'] }}</td>
                                <td data-label="Dokumen">
                                    <a href="{{ $info['dokumen_url'] }}"
                                        class="lihat-link-table btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center mt-2 mb-2 text-muted" style="font-size:0.98rem;">
                    Halaman 1 dari 1
                </div>
                <div class="text-center pb-3 pe-3">
                    <button class="btn btn-outline-primary btn-sm" disabled>
                        Selanjutnya <span class="ms-1">&gt;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('user::layouts.footer');
</body>

</html>
