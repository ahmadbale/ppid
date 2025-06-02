{{-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPermohonanPerawatan\decline.blade.php --}}
<div class="modal-body">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i> Anda akan menolak Permohonan Perawatan dari
        <strong>{{ $permohonanPerawatan->pp_nama_pengguna }}</strong>
    </div>

    <div class="detail-section mb-3">
        <h6 class="mb-3">Detail Permohonan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th>Unit Kerja</th>
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
        </table>
    </div>

    <form id="formTolakModal">
        <div class="form-group">
            <label for="alasan_penolakan_modal">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasan_penolakan_modal" name="alasan_penolakan" rows="4"
                placeholder="Masukkan alasan penolakan permohonan perawatan ini..." required></textarea>
            <div class="invalid-feedback" id="alasanErrorModal"></div>
        </div>
        <input type="hidden" id="permohonan_id" value="{{ $permohonanPerawatan->permohonan_perawatan_id }}">
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmDecline" data-id="{{ $permohonanPerawatan->permohonan_perawatan_id }}"
        class="btn btn-danger">
        <i class="fas fa-times mr-1"></i> Tolak Permohonan Perawatan
    </button>
</div>