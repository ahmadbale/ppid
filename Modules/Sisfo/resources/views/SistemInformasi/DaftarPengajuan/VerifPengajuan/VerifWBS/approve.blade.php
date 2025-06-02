<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui Whistle Blowing System dari
        <strong>{{ $whistleBlowingSystem->wbs_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Laporan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th>Jenis Laporan</th>
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
                <th>Waktu Kejadian</th>
                <td>{{ $whistleBlowingSystem->wbs_waktu_kejadian ? (new DateTime($whistleBlowingSystem->wbs_waktu_kejadian))->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi Kejadian</th>
                <td>{{ $whistleBlowingSystem->wbs_lokasi_kejadian }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $whistleBlowingSystem->wbs_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Whistle Blowing System
    </button>
</div>