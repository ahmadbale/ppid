<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui permohonan informasi dari <strong>{{ $pernyataanKeberatan->pk_kategori_pemohon == 'Diri Sendiri' ? $pernyataanKeberatan->PkDiriSendiri->pk_nama_pengguna : 
        $pernyataanKeberatan->pk_kategori_pemohon == 'Orang Lain' ? $pernyataanKeberatan->PkOrangLain->pk_nama_pengguna_informasi }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Permohonan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Informasi yang Dibutuhkan</th>
                <td>{{ $pernyataanKeberatan->pk_informasi_yang_dibutuhkan }}</td>
            </tr>
            <tr>
                <th>Alasan Permohonan</th>
                <td>{{ $pernyataanKeberatan->pk_alasan_pernyataan_keberatan }}</td>
            </tr>
            <tr>
                <th>Sumber Informasi</th>
                <td>{{ $pernyataanKeberatan->pk_sumber_informasi }}</td>
            </tr>
            <tr>
                <th>Alamat Sumber Informasi</th>
                <td>{{ $pernyataanKeberatan->pk_alamat_sumber_informasi }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}" class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Permohonan
    </button>
</div>