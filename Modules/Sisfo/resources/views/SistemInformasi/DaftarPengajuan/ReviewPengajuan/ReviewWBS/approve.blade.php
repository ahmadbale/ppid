<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui review Whistle Blowing System dari
        <strong>{{ $whistleBlowingSystem->wbs_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Laporan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Jenis Laporan</th>
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
                <th>Lokasi Kejadian</th>
                <td>{{ $whistleBlowingSystem->wbs_lokasi_kejadian }}</td>
            </tr>
            <tr>
                <th>Waktu Kejadian</th>
                <td>{{ $whistleBlowingSystem->wbs_waktu_kejadian ? (new DateTime($whistleBlowingSystem->wbs_waktu_kejadian))->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Form untuk mengisi jawaban --}}
    <form id="formSetujuiReview" enctype="multipart/form-data">
        <div class="form-group">
            <label for="jawaban">Jawaban Laporan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jawaban" name="jawaban" rows="4" 
                placeholder="Masukkan jawaban untuk laporan Whistle Blowing System ini..." required></textarea>
            <div class="invalid-feedback" id="jawabanError"></div>
        </div>

        <div class="form-group">
            <label for="jawaban_file">Upload File Jawaban (Opsional)</label>
            <input type="file" class="form-control-file" id="jawaban_file" name="jawaban_file" 
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <small class="form-text text-muted">
                Format yang diizinkan: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10MB.
                <br><strong>Catatan:</strong> Jika file diupload, file akan menjadi jawaban utama dan teks di atas akan diabaikan.
            </small>
            <div class="invalid-feedback" id="fileError"></div>
        </div>

        <input type="hidden" id="wbs_id" value="{{ $whistleBlowingSystem->wbs_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $whistleBlowingSystem->wbs_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Review
    </button>
</div>