<div class="modal-body">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i> Anda akan menolak permohonan informasi dari 
        <strong>
            @if($permohonanInformasi->pi_kategori_pemohon == 'Diri Sendiri')
                {{ $permohonanInformasi->PiDiriSendiri->pi_nama_pengguna }}
            @elseif($permohonanInformasi->pi_kategori_pemohon == 'Orang Lain')
                {{ $permohonanInformasi->PiOrangLain->pi_nama_pengguna_informasi }}
            @else
                {{ $permohonanInformasi->PiOrganisasi->pi_nama_organisasi }}
            @endif
        </strong>
    </div>

    <div class="detail-section mb-3">
        <h6 class="mb-3">Detail Permohonan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th width="30%">Informasi yang Dibutuhkan</th>
                <td>{{ $permohonanInformasi->pi_informasi_yang_dibutuhkan }}</td>
            </tr>
            <tr>
                <th>Alasan Permohonan</th>
                <td>{{ $permohonanInformasi->pi_alasan_permohonan_informasi }}</td>
            </tr>
            <tr>
                <th>Sumber Informasi</th>
                <td>{{ $permohonanInformasi->pi_sumber_informasi }}</td>
            </tr>
            <tr>
                <th>Alamat Sumber Informasi</th>
                <td>{{ $permohonanInformasi->pi_alamat_sumber_informasi }}</td>
            </tr>
        </table>
    </div>

    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4" placeholder="Masukkan alasan penolakan permohonan informasi ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="permohonan_id" value="{{ $permohonanInformasi->permohonan_informasi_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmDecline" data-id="{{ $permohonanInformasi->permohonan_informasi_id }}" class="btn btn-danger">
        <i class="fas fa-times mr-1"></i> Tolak Permohonan
    </button>
</div>