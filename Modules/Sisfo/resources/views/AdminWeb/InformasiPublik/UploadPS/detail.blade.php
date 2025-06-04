<div class="modal-header">
    <h5 class="modal-title">{{ $tittle }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Kategori Upload Penyelesaian Sengketa</th>
                    <td>{{ $uploadPS->penyelesaianSengketa->ps_nama }}</td>
                </tr>
                <tr>
                    <th width="200">Tipe Data Upload</th>
                    <td>{{ $uploadPS->kategori_upload_ps }}</td>
                </tr>
                <tr>
                    <th width="200">List Data Upload</th>
                    <td>
                        @if($uploadPS->kategori_upload_ps == 'link')
                            <a href="{{ $uploadPS->upload_ps }}" target="_blank" class="badge badge-info">
                                <i class="fas fa-link"></i> Lihat Link
                            </a>
                        @elseif($uploadPS->kategori_upload_ps == 'file')
                            <a href="{{ asset('storage/' . $uploadPS->upload_ps) }}" target="_blank" class="badge badge-success">
                                <i class="fas fa-file"></i> Lihat File PDF
                            </a>
                        @else
                            {{ Str::limit($uploadPS->kategori_upload_ps, 50) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ date('d-m-Y H:i:s', strtotime($uploadPS->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $uploadPS->created_by }}</td>
                </tr>
                @if($uploadPS->updated_by)
                <tr>
                    <th>Terakhir Diperbarui</th>
                    <td>{{ date('d-m-Y H:i:s', strtotime($uploadPS->updated_at)) }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Oleh</th>
                    <td>{{ $uploadPS->updated_by }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>     
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>   
</div>