<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Informasi Publik</title>
    @vite(['resources/css/app.css'])
</head>

<body>
@include('user::layouts.header')
@include('user::layouts.navbar')
    <div class="container">
        <h2 class="fw-bold mt-4 mb-2 text-center text-md-center">Daftar Informasi Dikecualikan Politeknik Negeri Malang</h2>
        <div class="mt-4 border-top border-1 pt-3 w-75 mx-auto text-center"></div>
        <div class="flex items-center text-gray-500 text-sm mt-2 mb-3">
            <i class="bi bi-clock-fill text-warning me-2"></i>
            <span class="ml-1">
                Diperbarui pada
                @if (!empty($lhkpnItems))
                    @php
                        $latestUpdateTime = collect($lhkpnItems)
                            ->filter(function($item) {
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
        <div class="pdf-header shadow-sm rounded p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{$pdfName}}</h5>
                    <small class="text-muted">Shared By: {{$sharedBy}}</small>
                </div>
                <div>
                    <a href=".pdf" class="btn btn-success" download>
                        Download File
                    </a>
                </div>
            </div>
        </div>

        <div class="pdf-container shadow-sm rounded mt-5 mb-5 p-3 bg-white" style="overflow: hidden">
            <embed src="{{ asset($pdfFile) }}" type="application/pdf" style="width: 100%; height: 700px; border-radius: 8px;" />
        </div>
    </div>
    @include('user::layouts.footer');
</body>
</html>
