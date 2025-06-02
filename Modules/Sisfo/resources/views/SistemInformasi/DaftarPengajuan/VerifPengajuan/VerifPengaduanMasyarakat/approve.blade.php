{{-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPengaduanMasyarakat\approve.blade.php --}}
<div class="modal-body">
    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> Anda akan menyetujui Pengaduan Masyarakat dari
        <strong>{{ $pengaduanMasyarakat->pm_nama_tanpa_gelar }}</strong>
    </div>

    <div class="detail-section mb-4">
        <h6 class="mb-3">Detail Pengaduan</h6>
        <table class="table table-bordered table-sm">
            <tr>
                <th>Jenis Laporan</th>
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
                <th>Waktu Kejadian</th>
                <td>{{ $pengaduanMasyarakat->pm_waktu_kejadian ? (new DateTime($pengaduanMasyarakat->pm_waktu_kejadian))->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi Kejadian</th>
                <td>{{ $pengaduanMasyarakat->pm_lokasi_kejadian }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" id="btnConfirmApprove" data-id="{{ $pengaduanMasyarakat->pengaduan_masyarakat_id }}"
        class="btn btn-success">
        <i class="fas fa-check mr-1"></i> Setujui Pengaduan Masyarakat
    </button>
</div>
