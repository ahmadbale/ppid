@php
    $isApprove = $actionType === 'approve';
@endphp

<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-{{ $isApprove ? 'info-circle' : 'exclamation-triangle' }} mr-2"></i> 
        Anda akan <strong>{{ $isApprove ? 'menyetujui' : 'menolak' }}</strong> Permohonan Perawatan dari
        <strong>{{ $permohonanPerawatan->pp_nama_pengguna }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Permohonan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th>Unit Kerja</th>
                <td>{{ $permohonanPerawatan->pp_unit_kerja }}</td>
            </tr>
            <tr>
                <th>Lokasi Perawatan</th>
                <td>{{ $permohonanPerawatan->pp_lokasi_perawatan }}</td>
            </tr>
            <tr>
                <th>Perawatan yang Diusulkan</th>
                <td>{{ $permohonanPerawatan->pp_perawatan_yang_diusulkan }}</td>
            </tr>
            <tr>
                <th>Keluhan Kerusakan</th>
                <td>{{ $permohonanPerawatan->pp_keluhan_kerusakan }}</td>
            </tr>
        </table>
    </div>

    @if(!$isApprove)
    {{-- Form untuk alasan penolakan (hanya tampil saat decline) --}}
    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan permohonan perawatan ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="permohonan_id" value="{{ $permohonanPerawatan->permohonan_perawatan_id }}">
    </form>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmUpdate" 
        data-id="{{ $permohonanPerawatan->permohonan_perawatan_id }}"
        data-action="{{ $isApprove ? 'approve' : 'decline' }}"
        class="btn btn-{{ $isApprove ? 'success' : 'danger' }}">
        <i class="fas fa-{{ $isApprove ? 'check' : 'times' }} mr-1"></i> 
        {{ $isApprove ? 'Setujui' : 'Tolak' }} Permohonan Perawatan
    </button>
</div>
