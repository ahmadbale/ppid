@php
    $isApprove = $actionType === 'approve';
@endphp

<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-{{ $isApprove ? 'info-circle' : 'exclamation-triangle' }} mr-2"></i> 
        Anda akan <strong>{{ $isApprove ? 'menyetujui' : 'menolak' }}</strong> review pernyataan keberatan dari
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

    @if($isApprove)
    {{-- Form untuk jawaban (hanya tampil saat approve) --}}
    <form id="formSetujuiReview" enctype="multipart/form-data">
        <div class="form-group">
            <label for="jawaban">Jawaban Pernyataan Keberatan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jawaban" name="jawaban" rows="4" 
                placeholder="Masukkan jawaban untuk pernyataan keberatan ini..." required></textarea>
            <div class="invalid-feedback" id="jawabanError"></div>
        </div>

        <div class="form-group">
            <label for="jawaban_file">Upload File Jawaban (Opsional)</label>
            <input type="file" class="form-control-file" id="jawaban_file" name="jawaban_file" 
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <small class="form-text text-muted">
                Format: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10MB.
            </small>
            <div class="invalid-feedback" id="fileError"></div>
        </div>

        <input type="hidden" id="pernyataan_keberatan_id" value="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}">
    </form>
    @else
    {{-- Form untuk alasan penolakan (hanya tampil saat decline) --}}
    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan review pernyataan keberatan ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="pernyataan_keberatan_id" value="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}">
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
        {{ $isApprove ? 'Setujui' : 'Tolak' }} Review
    </button>
</div>
