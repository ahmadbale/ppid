@extends('user::layouts.mainpage')

@section('title', $pageTitle ?? 'Prosedur Layanan Informasi')

@section('content')
    <h2 class="fw-bold mt-4 mb-2 text-center text-md-center">
        {{ $judul ?? 'Daftar Informasi Publik Politeknik Negeri Malang' }}
    </h2>
    <div class="mt-4 border-top border-1 pt-3 w-75 mx-auto text-center"></div>
    <div class="flex items-center text-gray-500 text-sm mt-2 mb-3">
        <i class="bi bi-clock-fill text-warning me-2"></i>
        <span class="ml-1">
            Diperbarui pada
            @if (!empty($latestUpdateTime))
                {{ $latestUpdateTime }}
            @else
                (Belum ada pembaruan)
            @endif
        </span>
    </div>

    <div class="pdf-header shadow-sm rounded p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $pdfName }}</h5>
                <small class="text-muted">Shared By: {{ $sharedBy }}</small>
            </div>
            <div>
                <a href="{{ asset($pdfFile) }}" class="btn btn-success" download>
                    Download File
                </a>
            </div>
        </div>
    </div>

    <div class="pdf-container shadow-sm rounded mt-5 mb-5 p-3 bg-white" style="overflow: hidden">
        <embed src="{{ asset($pdfFile) }}" type="application/pdf" style="width: 100%; height: 700px; border-radius: 8px;" />
    </div>
@endsection

@push('styles')
<style>
/* Responsive for PDF card and embed */
.pdf-header {
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.pdf-header h5 {
    font-size: 1.1rem;
    word-break: break-word;
}

.pdf-container {
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

.pdf-embed {
    width: 100%;
    min-height: 400px;
    height: 70vh;
    border-radius: 8px;
    display: block;
}

@media (max-width: 600px) {
    .pdf-header {
        padding: 1rem !important;
        max-width: 100%;
    }
    .pdf-container {
        padding: 0 !important;
    }
    .pdf-embed {
        min-height: 300px;
        height: 55vw;
        border-radius: 6px;
    }
}
</style>
@endpush

