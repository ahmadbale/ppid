<div class="modal-body">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i> Anda akan menolak review Whistle Blowing System dari 
        <strong>{{ $whistleBlowingSystem->wbs_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-3">
        <h6 class="mb-3">Detail Laporan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Jenis Laporan</th>
                <td>{{ $whistleBlowingSystem->wbs_jenis_laporan }}</td>
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
                <th>Kronologis Kejadian</th>
                <td>{{ \Illuminate\Support\Str::limit($whistleBlowingSystem->wbs_kronologis_kejadian, 150) }}</td>
            </tr>
        </table>
    </div>

    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan review Whistle Blowing System ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="wbs_id" value="{{ $whistleBlowingSystem->wbs_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmDecline" data-id="{{ $whistleBlowingSystem->wbs_id }}" class="btn btn-danger">
        <i class="fas fa-times mr-1"></i> Tolak Review
    </button>
</div>