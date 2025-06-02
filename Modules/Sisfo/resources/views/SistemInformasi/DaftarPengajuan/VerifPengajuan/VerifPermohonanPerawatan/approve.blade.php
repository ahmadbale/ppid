{{-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPermohonanPerawatan\approve.blade.php --}}
<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui Permohonan Perawatan dari
        <strong>{{ $permohonanPerawatan->pp_nama_pengguna }}</strong>
    </div>

    <div class="detail-section mb-4">
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
            <tr>
                <th>Keluhan Kerusakan</th>
                <td>{{ $permohonanPerawatan->pp_keluhan_kerusakan }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $permohonanPerawatan->permohonan_perawatan_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Permohonan Perawatan
    </button>
</div>