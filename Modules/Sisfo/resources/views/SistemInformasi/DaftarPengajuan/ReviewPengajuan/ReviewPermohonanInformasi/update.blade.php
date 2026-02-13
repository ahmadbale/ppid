@php
    $isApprove = $actionType === 'approve';
@endphp

<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-{{ $isApprove ? 'info-circle' : 'exclamation-triangle' }} mr-2"></i> 
        Anda akan <strong>{{ $isApprove ? 'menyetujui' : 'menolak' }}</strong> review permohonan informasi dari
        <strong>
            @if($permohonanInformasi->pi_kategori_pemohon == 'Diri Sendiri')
                {{ $permohonanInformasi->PiDiriSendiri->pi_nama_pengguna }}
            @elseif($permohonanInformasi->pi_kategori_pemohon == 'Orang Lain')
                {{ $permohonanInformasi->PiOrangLain->pi_nama_pengguna_informasi }}
            @else
                {{ $permohonanInformasi->PiOrganisasi->pi_nama_organisasi }}
            @endif
        </strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Permohonan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Informasi yang Dibutuhkan</th>
                <td>{{ $permohonanInformasi->pi_informasi_yang_dibutuhkan }}</td>
            </tr>
            <tr>
                <th>Alasan Permohonan</th>
                <td>{{ $permohonanInformasi->pi_alasan_permohonan_informasi }}</td>
            </tr>
            <tr>
                <th>Sumber Informasi</th>
                <td>{{ $permohonanInformasi->pi_sumber_informasi }}</td>
            </tr>
            <tr>
                <th>Alamat Sumber Informasi</th>
                <td>{{ $permohonanInformasi->pi_alamat_sumber_informasi }}</td>
            </tr>
        </table>
    </div>

    @if($isApprove)
    {{-- Form untuk jawaban (hanya tampil saat approve) --}}
    <form id="formSetujuiReview" enctype="multipart/form-data">
        <div class="form-group">
            <label for="jawaban">Jawaban Permohonan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jawaban" name="jawaban" rows="4" 
                placeholder="Masukkan jawaban untuk permohonan informasi ini..." required></textarea>
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

        <input type="hidden" id="permohonan_id" value="{{ $permohonanInformasi->permohonan_informasi_id }}">
    </form>
    @else
    {{-- Form untuk alasan penolakan (hanya tampil saat decline) --}}
    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan permohonan informasi ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="permohonan_id" value="{{ $permohonanInformasi->permohonan_informasi_id }}">
    </form>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmUpdate" 
        data-id="{{ $permohonanInformasi->permohonan_informasi_id }}"
        data-action="{{ $isApprove ? 'approve' : 'decline' }}"
        class="btn btn-{{ $isApprove ? 'success' : 'danger' }}">
        <i class="fas fa-{{ $isApprove ? 'check' : 'times' }} mr-1"></i> 
        {{ $isApprove ? 'Setujui' : 'Tolak' }} Review
    </button>
</div>
