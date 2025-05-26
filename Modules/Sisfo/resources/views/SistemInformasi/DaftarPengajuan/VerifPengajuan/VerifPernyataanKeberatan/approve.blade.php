<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui Pernyataan Keberatan dari
        <strong>
            @if($pernyataanKeberatan->pk_kategori_pemohon == 'Diri Sendiri')
                {{ $pernyataanKeberatan->PkDiriSendiri->pk_nama_pengguna }}
            @else
                {{ $pernyataanKeberatan->PkOrangLain->pk_nama_kuasa_pemohon }}
            @endif
        </strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Pengajuan Keberatan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th>Alasan Pengajuan Keberatan</th>
                <td>{{ $pernyataanKeberatan->pk_alasan_pengajuan_keberatan }}</td>
            </tr>
            <tr>
                <th>Kasus Posisi</th>
                <td>{{ $pernyataanKeberatan->pk_kasus_posisi }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $pernyataanKeberatan->pernyataan_keberatan_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Pernyataan Keberatan
    </button>
</div>