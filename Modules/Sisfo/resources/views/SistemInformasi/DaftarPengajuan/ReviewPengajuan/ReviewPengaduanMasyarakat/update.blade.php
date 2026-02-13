@php
    $isApprove = $actionType === 'approve';
@endphp

<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-{{ $isApprove ? 'info-circle' : 'exclamation-triangle' }} mr-2"></i> 
        Anda akan <strong>{{ $isApprove ? 'menyetujui' : 'menolak' }}</strong> review pengaduan masyarakat dari
        <strong>{{ $pengaduanMasyarakat->pm_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Pengaduan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Jenis Laporan</th>
                <td>{{ $pengaduanMasyarakat->pm_jenis_laporan }}</td>
            </tr>
            <tr>
                <th>Yang Dilaporkan</th>
                <td>{{ $pengaduanMasyarakat->pm_yang_dilaporkan }}</td>
            </tr>
            <tr>
                <th>Jabatan</th>
                <td>{{ $pengaduanMasyarakat->pm_jabatan }}</td>
            </tr>
            <tr>
                <th>Lokasi Kejadian</th>
                <td>{{ $pengaduanMasyarakat->pm_lokasi_kejadian }}</td>
            </tr>
            <tr>
                <th>Waktu Kejadian</th>
                <td>{{ $pengaduanMasyarakat->pm_waktu_kejadian ? (new DateTime($pengaduanMasyarakat->pm_waktu_kejadian))->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </table>
    </div>

    @if($isApprove)
    {{-- Form untuk jawaban (hanya tampil saat approve) --}}
    <form id="formSetujuiReview" enctype="multipart/form-data">
        <div class="form-group">
            <label for="jawaban">Jawaban Pengaduan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jawaban" name="jawaban" rows="4" 
                placeholder="Masukkan jawaban untuk pengaduan masyarakat ini..." required></textarea>
            <div class="invalid-feedback" id="jawabanError"></div>
        </div>

        <div class="form-group">
            <label for="jawaban_file">Upload File Jawaban (Opsional)</label>
            <input type="file" class="form-control-file" id="jawaban_file" name="jawaban_file" 
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <small class="form-text text-muted">
                Format yang diperbolehkan: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10 MB.
            </small>
            <div class="invalid-feedback" id="fileError"></div>
        </div>

        <input type="hidden" id="pengaduan_id" value="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}">
    </form>
    @else
    {{-- Form untuk alasan penolakan (hanya tampil saat decline) --}}
    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan review pengaduan masyarakat ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="pengaduan_id" value="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}">
    </form>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmUpdate" 
        data-id="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}"
        data-action="{{ $isApprove ? 'approve' : 'decline' }}"
        class="btn btn-{{ $isApprove ? 'success' : 'danger' }}">
        <i class="fas fa-{{ $isApprove ? 'check' : 'times' }} mr-1"></i> 
        {{ $isApprove ? 'Setujui' : 'Tolak' }} Review
    </button>
</div>
