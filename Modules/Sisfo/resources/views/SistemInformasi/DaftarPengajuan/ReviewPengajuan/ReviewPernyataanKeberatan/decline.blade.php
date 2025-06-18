<div class="modal-body">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i> Anda akan menolak review pernyataan keberatan dari 
        <strong>
            @if($pernyataanKeberatan->pk_kategori_pemohon == 'Diri Sendiri')
                {{ $pernyataanKeberatan->PkDiriSendiri->pk_nama_pengguna }}
            @else
                {{ $pernyataanKeberatan->PkOrangLain->pk_nama_kuasa_pemohon }}
            @endif
        </strong>
    </div>

    <div class="detail-section mb-3">
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

    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4" 
                placeholder="Masukkan alasan penolakan review pernyataan keberatan ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="pernyataan_keberatan_id" value="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmDecline" data-id="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}" class="btn btn-danger">
        <i class="fas fa-times mr-1"></i> Tolak Review
    </button>
</div>