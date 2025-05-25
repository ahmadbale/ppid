@php 
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
$uploadPSUrl = WebMenuModel::getDynamicMenuUrl('upload-penyelesaian-sengketa');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Hapus Upload Penyelesaian Sengketa</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="alert alert-danger mt-3">
        <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus upload penyelesaian sengketa dengan detail berikut:
    </div>

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
            </table>
        </div>
    </div>

    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan dan link atau file yang terupload juga akan ikut terhapus.
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-danger" id="confirmDeleteButton"
        onclick="confirmDelete('{{ url($uploadPSUrl . '/deleteData/' . $uploadPS->upload_ps_id) }}')">
        <i class="fas fa-trash mr-1"></i> Hapus
    </button>
</div>

<script>
function confirmDelete(url) {
    const button = $('#confirmDeleteButton');
    button.html('<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...').prop('disabled', true);

    $.ajax({
        url: url,
        type: 'DELETE',
      headers: {
            'X-CSRF-TOKEEN': $('meta[name="csrf-token"]').attr('content')
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

