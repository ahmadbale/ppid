@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $liUploadUrl = WebMenuModel::getDynamicMenuUrl('layanan-informasi-upload');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Layanan Informasi Upload</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus layanan informasi upload dengan detail
    berikut:
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Kategori Layanan Informasi Dinamis</th>
          <td>{{ $liUpload->LiDinamis->li_dinamis_nama }}</td>
        </tr>
        <tr>
          <th width="200">Tipe Upload</th>
          <td>{{ $liUpload->lid_upload_type == 'file' ? 'File PDF' : 'Link' }}</td>
        </tr>
        <tr>
          <th width="200">List Data Upload</th>
          <td>
            @if($liUpload->lid_upload_type == 'link')
                <a href="{{ $liUpload->lid_upload_value }}" target="_blank" class="badge badge-info">
                    <i class="fas fa-link"></i> Buka Link
                </a>
            @elseif($liUpload->lid_upload_type == 'file')
                <a href="{{ asset('storage/' . $liUpload->lid_upload_value) }}" target="_blank" class="badge badge-success">
                    <i class="fas fa-file-pdf"></i> Lihat PDF
                </a>
            @endif
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan dan link Maupun file PDF yang terupload juga akan ikut terhapus.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton"
    onclick="confirmDelete('{{ url( $liUploadUrl . '/deleteData/' . $liUpload->lid_upload_id) }}')">
    <i class="fas fa-trash mr-1"></i> Hapus
  </button>
</div>

<script>
  function confirmDelete(url) {
    const button = $('#confirmDeleteButton');

    button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);

    $.ajax({
      url: url,
      type: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        $('#myModal').modal('hide');

        if (response.success) {
          reloadTable();

          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: response.message
          });
        }
      },
      error: function (xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
        });

        button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
      }
    });
  }
</script>