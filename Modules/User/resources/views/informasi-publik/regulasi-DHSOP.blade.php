@extends('user::layouts.regulasi-leftsidebar')

@section('title', 'Dasar Hukum SOP')

@section('content-side')
    <div class="profile-card bg-white p-8 shadow-sm rounded-lg">
        <div class="m-4">
            <h3 class="fw-bold pb-2 text-center text-md-start">
                Dasar Hukum Standar Operating Procedure (SOP) PPID Politeknik Negeri Malang
            </h3>
            <div class="flex items-center text-gray-500 text-sm mt-2 mb-4">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <span class="ml-1">Diperbarui pada 12 Maret 2025, 16.10 </span>
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
                        @foreach($sopList as $i => $sop)
                            <tr>
                                <td>{{ $i + 1 }}.</td>
                                <td>{{ $sop['judul'] }}</td>
                                <td style="text-align:center;">
                                    <a href="{{ $sop['link'] }}" class="lihat-link">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
