
@extends('user::layouts.regulasi-leftsidebar')

@section('title', 'Dasar Hukum Layanan Informasi Publik')

@section('content-side')
    <div class="profile-card bg-white p-8 shadow-sm rounded-lg">
        <div class="m-4">
            <h3 class="fw-bold pb-2 text-center text-md-start">
                Dasar Hukum Layanan Informasi Publik PPID Politeknik Negeri Malang
            </h3>
            <div class="flex items-center text-gray-500 text-sm mt-2 mb-4">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <span class="ml-1">Diperbarui pada {{ \Carbon\Carbon::parse($updated_at)->format('d F Y, H:i') }} </span>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Judul</th>
                            <th style="width: 130px; text-align:center;" class="fw-bold">
                                Dokumen
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dhlip as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}.</td>
                                <td>{{ $item['judul'] }}</td>
                                <td style="text-align:center;">
                                    <a href="{{ $item['dokumen'] }}" class="lihat-link" target="_blank">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection