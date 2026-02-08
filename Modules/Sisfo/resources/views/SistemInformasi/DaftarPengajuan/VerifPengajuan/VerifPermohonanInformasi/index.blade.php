@extends('sisfo::layouts.template')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Daftar Verifikasi Pengajuan Permohonan Informasi</h3>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ url($daftarPengajuanUrl) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        @if(count($permohonanInformasi) > 0)
            <div class="table-responsive">
                @include('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.VerifPermohonanInformasi.data')
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> Tidak ada data permohonan informasi yang perlu diverifikasi.
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk detail dan aksi -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Permohonan Informasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .badge-status {
        font-size: 0.9em;
        font-weight: 500;
        padding: 5px 10px;
    }
    .permohonan-card {
        margin-bottom: 20px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .permohonan-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .permohonan-card .card-title {
        margin-bottom: 0;
        font-size: 1.1rem;
    }
    .detail-section {
        background-color: #f9f9f9;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .detail-section h6 {
        border-bottom: 1px solid #ddd;
        padding-bottom: 8px;
        margin-bottom: 15px;
        color: #555;
    }
    .btn-action {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .btn-action i {
        margin-right: 5px;
    }
    .action-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 15px;
    }
</style>
@endpush

