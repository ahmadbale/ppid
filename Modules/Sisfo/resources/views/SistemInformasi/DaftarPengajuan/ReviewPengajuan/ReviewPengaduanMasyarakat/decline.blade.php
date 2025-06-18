<div class="modal-body">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i> Anda akan menolak review pengaduan masyarakat dari 
        <strong>{{ $pengaduanMasyarakat->pm_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-3">
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
                <th>Kronologis Kejadian</th>
                <td>{{ \Illuminate\Support\Str::limit($pengaduanMasyarakat->pm_kronologis_kejadian, 150) }}</td>
            </tr>
        </table>
    </div>

    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan review pengaduan masyarakat ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="pengaduan_id" value="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmDecline" data-id="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}" class="btn btn-danger">
        <i class="fas fa-times mr-1"></i> Tolak Review
    </button>
</div>