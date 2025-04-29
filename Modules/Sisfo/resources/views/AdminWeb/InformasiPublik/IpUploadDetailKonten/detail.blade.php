
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detail Upload Konten</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">ID Upload Konten</th>
                    <td>{{ $ipUploadKonten->ip_upload_konten_id }}</td>
                </tr>
                <tr>
                    <th>Kategori Konten Dinamis</th>
                    <td>{{ $ipUploadKonten->IpDinamisKonten->kd_nama_konten_dinamis }}</td>
                </tr>
                <tr>
                    <th>Judul Konten</th>
                    <td>{{ $ipUploadKonten->uk_judul_konten }}</td>
                </tr>
                <tr>
                    <th>Dokumen Konten</th>
                    <td>
                        @if($ipUploadKonten->uk_dokumen_konten)
                            <a href="{{ Storage::url($ipUploadKonten->uk_dokumen_konten) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                            </a>
                        @else
                            Tidak ada dokumen
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $ipUploadKonten->created_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $ipUploadKonten->created_at ? date('d-m-Y H:i:s', strtotime($ipUploadKonten->created_at)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Oleh</th>
                    <td>{{ $ipUploadKonten->updated_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Terakhir Diperbarui</th>
                    <td>{{ $ipUploadKonten->updated_at ? date('d-m-Y H:i:s', strtotime($ipUploadKonten->updated_at)) : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
