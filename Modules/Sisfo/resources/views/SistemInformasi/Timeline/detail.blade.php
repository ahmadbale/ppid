<!-- views/SistemInformasi/Timeline/detail.blade.php -->

<div class="modal-header">
  <h5 class="modal-title">{{ $title }}</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Kategori Form</th>
          <td>{{ $timeline->TimelineKategoriForm->kf_nama ?? 'Tidak ada' }}</td>
        </tr>
        <tr>
          <th>Judul Timeline</th>
          <td>{{ $timeline->judul_timeline }}</td>
        </tr>
        @if($timeline->timeline_file)
        <tr>
          <th>File Timeline</th>
          <td>
            <a href="{{ Storage::url($timeline->timeline_file) }}" target="_blank" class="btn btn-sm btn-primary">
              <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
            </a>
            <small class="ml-2 text-muted">{{ $timeline->timeline_file }}</small>
          </td>
        </tr>
        @endif
        <tr>
          <th>Tanggal Dibuat</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($timeline->created_at)) }}</td>
        </tr>
        <tr>
          <th>Dibuat Oleh</th>
          <td>{{ $timeline->created_by }}</td>
        </tr>
        @if($timeline->updated_by)
        <tr>
          <th>Terakhir Diperbarui</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($timeline->updated_at)) }}</td>
        </tr>
        <tr>
          <th>Diperbarui Oleh</th>
          <td>{{ $timeline->updated_by }}</td>
        </tr>
        @endif
      </table>
    </div>
  </div>
  
  <div class="card mt-3">
    <div class="card-header">
      <h5 class="card-title">Langkah-langkah Timeline</h5>
    </div>
    <div class="card-body">
      @if($timeline->langkahTimeline->count() > 0)
        <ol class="pl-3">
          @foreach($timeline->langkahTimeline as $langkah)
            <li class="mb-2">{{ $langkah->langkah_timeline }}</li>
          @endforeach
        </ol>
      @else
        <div class="alert alert-info">
          Tidak ada langkah timeline yang tersedia.
        </div>
      @endif
    </div>
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>