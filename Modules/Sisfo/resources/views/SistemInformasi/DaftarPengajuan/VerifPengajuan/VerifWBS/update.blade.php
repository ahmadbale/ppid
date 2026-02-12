@php
    $isApprove = $actionType === 'approve';
@endphp

<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-{{ $isApprove ? 'info-circle' : 'exclamation-triangle' }} mr-2"></i> 
        Anda akan <strong>{{ $isApprove ? 'menyetujui' : 'menolak' }}</strong> Whistle Blowing System dari
        <strong>{{ $whistleBlowingSystem->wbs_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Laporan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th>Jenis Laporan</th>
                <td>
                    <span class="badge badge-info px-2 py-1">
                        {{ $whistleBlowingSystem->wbs_jenis_laporan }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Yang Dilaporkan</th>
                <td>{{ $whistleBlowingSystem->wbs_yang_dilaporkan }}</td>
            </tr>
            <tr>
                <th>Jabatan</th>
                <td>{{ $whistleBlowingSystem->wbs_jabatan }}</td>
            </tr>
            <tr>
                <th>Waktu Kejadian</th>
                <td>{{ $whistleBlowingSystem->wbs_waktu_kejadian ? (new DateTime($whistleBlowingSystem->wbs_waktu_kejadian))->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi Kejadian</th>
                <td>{{ $whistleBlowingSystem->wbs_lokasi_kejadian }}</td>
            </tr>
        </table>
    </div>

    @if(!$isApprove)
    {{-- Form untuk alasan penolakan (hanya tampil saat decline) --}}
    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan Whistle Blowing System ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="permohonan_id" value="{{ $whistleBlowingSystem->wbs_id }}">
    </form>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmUpdate" 
        data-id="{{ $whistleBlowingSystem->wbs_id }}"
        data-action="{{ $isApprove ? 'approve' : 'decline' }}"
        class="btn btn-{{ $isApprove ? 'success' : 'danger' }}">
        <i class="fas fa-{{ $isApprove ? 'check' : 'times' }} mr-1"></i> 
        {{ $isApprove ? 'Setujui' : 'Tolak' }} Whistle Blowing System
    </button>
</div>