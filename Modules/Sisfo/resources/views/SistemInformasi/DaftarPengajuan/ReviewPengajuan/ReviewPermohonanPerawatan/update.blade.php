<div class="modal-header">
    <h5 class="modal-title">
        @if($actionType == 'approve')
            Setujui Review Permohonan Perawatan
        @else
            Tolak Review Permohonan Perawatan
        @endif
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tr>
            <td><strong>Nama Pengguna</strong></td>
            <td>{{ $permohonanPerawatan->pp_nama_pengguna ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Unit Kerja</strong></td>
            <td>{{ $permohonanPerawatan->pp_unit_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi Perawatan</strong></td>
            <td>{{ $permohonanPerawatan->pp_lokasi_perawatan ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Perawatan yang Diusulkan</strong></td>
            <td>{{ $permohonanPerawatan->pp_perawatan_yang_diusulkan ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Keluhan/Kerusakan</strong></td>
            <td>{{ $permohonanPerawatan->pp_keluhan_kerusakan ?? '-' }}</td>
        </tr>
    </table>

    @if($actionType == 'approve')
        <div class="mb-3">
            <label for="jawaban" class="form-label">Jawaban <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jawaban" name="jawaban" rows="4" 
                      placeholder="Masukkan jawaban untuk permohonan perawatan ini"></textarea>
        </div>
        <div class="mb-3">
            <label for="jawaban_file" class="form-label">File Jawaban (Optional)</label>
            <input type="file" class="form-control" id="jawaban_file" name="jawaban_file" 
                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <small class="form-text text-muted">Format: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)</small>
        </div>
    @else
        <div class="mb-3">
            <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" 
                      placeholder="Masukkan alasan penolakan permohonan perawatan ini"></textarea>
        </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnConfirmUpdate"
            data-action="{{ $actionType }}" 
            data-id="{{ $permohonanPerawatan->permohonan_perawatan_id }}">
        @if($actionType == 'approve')
            Setujui
        @else
            Tolak
        @endif
    </button>
</div>
