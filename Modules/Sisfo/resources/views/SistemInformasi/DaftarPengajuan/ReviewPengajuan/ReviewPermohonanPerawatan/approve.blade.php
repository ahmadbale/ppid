<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui review permohonan perawatan dari
        <strong>{{ $permohonanPerawatan->pp_nama_pengguna }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Permohonan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Unit Kerja</th>
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
                <td>{{ \Illuminate\Support\Str::limit($permohonanPerawatan->pp_keluhan_kerusakan, 150) }}</td>
            </tr>
        </table>
    </div>

    {{-- Form untuk mengisi jawaban --}}
    <form id="formSetujuiReview" enctype="multipart/form-data">
        <div class="form-group">
            <label for="jawaban">Jawaban Permohonan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jawaban" name="jawaban" rows="4" 
                placeholder="Masukkan jawaban untuk permohonan perawatan ini..." required></textarea>
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

        <input type="hidden" id="permohonan_id" value="{{ $permohonanPerawatan->permohonan_perawatan_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $permohonanPerawatan->permohonan_perawatan_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Review
    </button>
</div>