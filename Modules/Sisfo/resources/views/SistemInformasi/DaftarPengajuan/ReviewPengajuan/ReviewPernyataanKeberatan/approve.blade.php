<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui review pernyataan keberatan dari
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

    {{-- Form untuk mengisi jawaban --}}
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
                Format yang diizinkan: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10MB.
                <br><strong>Catatan:</strong> Jika file diupload, file akan menjadi jawaban utama dan teks di atas akan diabaikan.
            </small>
            <div class="invalid-feedback" id="fileError"></div>
        </div>

        <input type="hidden" id="pernyataan_keberatan_id" value="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Review
    </button>
</div>