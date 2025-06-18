<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui review pengaduan masyarakat dari
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

    {{-- Form untuk mengisi jawaban --}}
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
                Format yang diizinkan: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10MB.
                <br><strong>Catatan:</strong> Jika file diupload, file akan menjadi jawaban utama dan teks di atas akan diabaikan.
            </small>
            <div class="invalid-feedback" id="fileError"></div>
        </div>

        <input type="hidden" id="pengaduan_id" value="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Review
    </button>
</div>