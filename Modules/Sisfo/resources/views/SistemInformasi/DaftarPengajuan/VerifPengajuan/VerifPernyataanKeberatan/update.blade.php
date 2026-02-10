@php
    $isApprove = $actionType === 'approve';
@endphp

<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-{{ $isApprove ? 'info-circle' : 'exclamation-triangle' }} mr-2"></i> 
        Anda akan <strong>{{ $isApprove ? 'menyetujui' : 'menolak' }}</strong> pernyataan keberatan dari
        <strong>
            @if($pernyataanKeberatan->pk_kategori_pemohon == 'Diri Sendiri')
                {{ $pernyataanKeberatan->PkDiriSendiri->pk_nama_pengguna }}
            @else
                {{ $pernyataanKeberatan->PkOrangLain->pk_nama_kuasa_pemohon }}
            @endif
        </strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Pernyataan Keberatan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Alasan Pengajuan Keberatan</th>
                <td>{{ $pernyataanKeberatan->pk_alasan_pengajuan_keberatan }}</td>
            </tr>
            <tr>
                <th>Kasus Posisi</th>
                <td>{{ $pernyataanKeberatan->pk_kasus_posisi }}</td>
            </tr>
        </table>
    </div>

    @if(!$isApprove)
    {{-- Form untuk alasan penolakan (hanya tampil saat decline) --}}
    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4" 
                placeholder="Masukkan alasan penolakan pernyataan keberatan ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="permohonan_id" value="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}">
    </form>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmUpdate" 
        data-id="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}"
        data-action="{{ $isApprove ? 'approve' : 'decline' }}"
        class="btn btn-{{ $isApprove ? 'success' : 'danger' }}">
        <i class="fas fa-{{ $isApprove ? 'check' : 'times' }} mr-1"></i> 
        {{ $isApprove ? 'Setujui' : 'Tolak' }} Pernyataan Keberatan
    </button>
</div>
