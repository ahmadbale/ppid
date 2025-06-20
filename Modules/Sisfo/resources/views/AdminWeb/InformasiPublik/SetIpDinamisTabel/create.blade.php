@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Tambah Set Informasi Publik Dinamis Tabel</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formCreateSetIpDinamisTabel" action="{{ url($setIpDinamisTabelUrl . '/createData') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="alert alert-info">
      <i class="fas fa-info-circle mr-2"></i>
      <strong>Petunjuk:</strong> Silakan isi informasi dasar terlebih dahulu, kemudian tentukan struktur menu bertingkat sesuai kebutuhan.
    </div>

    <div class="form-group">
      <label for="fk_t_ip_dinamis_tabel">Kategori Informasi Publik Dinamis Tabel <span class="text-danger">*</span></label>
      <select class="form-control" id="fk_t_ip_dinamis_tabel" name="fk_t_ip_dinamis_tabel">
        <option value="">-- Pilih Kategori --</option>
        @foreach($ipDinamisTabel as $kategori)
          <option value="{{ $kategori->ip_dinamis_tabel_id }}">{{ $kategori->ip_nama_submenu }} - {{ $kategori->ip_judul }}</option>
        @endforeach
      </select>
      <div class="invalid-feedback" id="fk_t_ip_dinamis_tabel_error"></div>
    </div>

    <div class="form-group">
      <label for="jumlah_menu_utama">Jumlah Menu Utama <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="jumlah_menu_utama" name="jumlah_menu_utama"
        min="1" max="20" placeholder="Masukkan jumlah menu utama (1-20)">
      <div class="invalid-feedback" id="jumlah_menu_utama_error"></div>
      <small class="form-text text-muted">Minimal 1, maksimal 20 menu utama</small>
    </div>

    <div id="menu_utama_container">
      <!-- Input menu utama akan muncul di sini secara dinamis -->
    </div>
  </form>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-success" id="btnSubmitForm">
    <i class="fas fa-save mr-1"></i> Simpan
  </button>
</div>

<script>
  let fileCounter = 0;

  function generateMenuUtamaFields() {
    const jumlahMenuUtama = parseInt($('#jumlah_menu_utama').val());
    const container = $('#menu_utama_container');

    if (isNaN(jumlahMenuUtama) || jumlahMenuUtama < 1 || jumlahMenuUtama > 20) {
      container.empty();
      return;
    }

    container.empty();
    fileCounter = 0;

    for (let i = 1; i <= jumlahMenuUtama; i++) {
      const menuUtamaCard = `
        <div class="card border-primary mb-3" id="menu_utama_card_${i}">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
              <i class="fas fa-folder-open mr-2"></i>Menu Utama ${i}
            </h6>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="nama_menu_utama_${i}">Nama Menu Utama ${i} <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_menu_utama_${i}" 
                name="nama_menu_utama_${i}" maxlength="255" placeholder="Masukkan nama menu utama">
              <div class="invalid-feedback" id="nama_menu_utama_${i}_error"></div>
            </div>

            <div class="form-group">
              <label>Apakah menu ini akan memiliki Sub Menu Utama? <span class="text-danger">*</span></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="ada_sub_menu_utama_${i}" 
                  id="ada_sub_menu_utama_${i}_tidak" value="tidak" onchange="toggleSubMenuUtama(${i})">
                <label class="form-check-label" for="ada_sub_menu_utama_${i}_tidak">
                  Tidak (Upload dokumen langsung)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="ada_sub_menu_utama_${i}" 
                  id="ada_sub_menu_utama_${i}_ya" value="ya" onchange="toggleSubMenuUtama(${i})">
                <label class="form-check-label" for="ada_sub_menu_utama_${i}_ya">
                  Ya (Buat Sub Menu Utama)
                </label>
              </div>
              <div class="invalid-feedback" id="ada_sub_menu_utama_${i}_error"></div>
            </div>

            <div id="dokumen_menu_utama_${i}_container" style="display: none;">
              <div class="form-group">
                <label for="dokumen_menu_utama_${i}">Dokumen Menu Utama ${i} <span class="text-danger">*</span></label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="dokumen_menu_utama_${i}" 
                    name="dokumen_menu_utama_${i}" accept=".pdf">
                  <label class="custom-file-label" for="dokumen_menu_utama_${i}">Pilih file PDF (maks. 5 MB)</label>
                </div>
                <div class="invalid-feedback" id="dokumen_menu_utama_${i}_error"></div>
              </div>
            </div>

            <div id="sub_menu_utama_${i}_container" style="display: none;">
              <div class="form-group">
                <label for="jumlah_sub_menu_utama_${i}">Jumlah Sub Menu Utama ${i} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="jumlah_sub_menu_utama_${i}" 
                  name="jumlah_sub_menu_utama_${i}" min="1" max="20" 
                  placeholder="Masukkan jumlah sub menu utama" onchange="generateSubMenuUtamaFields(${i})">
                <div class="invalid-feedback" id="jumlah_sub_menu_utama_${i}_error"></div>
              </div>
              <div id="sub_menu_utama_fields_${i}"></div>
            </div>
          </div>
        </div>
      `;
      container.append(menuUtamaCard);
    }
  }

  function toggleSubMenuUtama(menuIndex) {
    const adaSubMenuUtama = $(`input[name="ada_sub_menu_utama_${menuIndex}"]:checked`).val();
    const dokumenContainer = $(`#dokumen_menu_utama_${menuIndex}_container`);
    const subMenuContainer = $(`#sub_menu_utama_${menuIndex}_container`);

    if (adaSubMenuUtama === 'tidak') {
      dokumenContainer.show();
      subMenuContainer.hide();
      // Clear sub menu utama fields
      $(`#jumlah_sub_menu_utama_${menuIndex}`).val('');
      $(`#sub_menu_utama_fields_${menuIndex}`).empty();
    } else if (adaSubMenuUtama === 'ya') {
      dokumenContainer.hide();
      subMenuContainer.show();
      // Clear dokumen field
      $(`#dokumen_menu_utama_${menuIndex}`).val('');
      $(`label[for="dokumen_menu_utama_${menuIndex}"]`).text('Pilih file PDF (maks. 5 MB)');
    }
  }

  function generateSubMenuUtamaFields(menuIndex) {
    const jumlahSubMenuUtama = parseInt($(`#jumlah_sub_menu_utama_${menuIndex}`).val());
    const container = $(`#sub_menu_utama_fields_${menuIndex}`);

    if (isNaN(jumlahSubMenuUtama) || jumlahSubMenuUtama < 1 || jumlahSubMenuUtama > 20) {
      container.empty();
      return;
    }

    container.empty();

    for (let j = 1; j <= jumlahSubMenuUtama; j++) {
      const subMenuUtamaCard = `
        <div class="card border-success mb-2" id="sub_menu_utama_card_${menuIndex}_${j}">
          <div class="card-header bg-success text-white">
            <h6 class="mb-0">
              <i class="fas fa-folder mr-2"></i>Sub Menu Utama ${menuIndex}.${j}
            </h6>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="nama_sub_menu_utama_${menuIndex}_${j}">Nama Sub Menu Utama ${menuIndex}.${j} <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_sub_menu_utama_${menuIndex}_${j}" 
                name="nama_sub_menu_utama_${menuIndex}_${j}" maxlength="255" placeholder="Masukkan nama sub menu utama">
              <div class="invalid-feedback" id="nama_sub_menu_utama_${menuIndex}_${j}_error"></div>
            </div>

            <div class="form-group">
              <label>Apakah menu ini akan memiliki Sub Menu? <span class="text-danger">*</span></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="ada_sub_menu_${menuIndex}_${j}" 
                  id="ada_sub_menu_${menuIndex}_${j}_tidak" value="tidak" onchange="toggleSubMenu(${menuIndex}, ${j})">
                <label class="form-check-label" for="ada_sub_menu_${menuIndex}_${j}_tidak">
                  Tidak (Upload dokumen langsung)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="ada_sub_menu_${menuIndex}_${j}" 
                  id="ada_sub_menu_${menuIndex}_${j}_ya" value="ya" onchange="toggleSubMenu(${menuIndex}, ${j})">
                <label class="form-check-label" for="ada_sub_menu_${menuIndex}_${j}_ya">
                  Ya (Buat Sub Menu)
                </label>
              </div>
              <div class="invalid-feedback" id="ada_sub_menu_${menuIndex}_${j}_error"></div>
            </div>

            <div id="dokumen_sub_menu_utama_${menuIndex}_${j}_container" style="display: none;">
              <div class="form-group">
                <label for="dokumen_sub_menu_utama_${menuIndex}_${j}">Dokumen Sub Menu Utama ${menuIndex}.${j} <span class="text-danger">*</span></label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="dokumen_sub_menu_utama_${menuIndex}_${j}" 
                    name="dokumen_sub_menu_utama_${menuIndex}_${j}" accept=".pdf">
                  <label class="custom-file-label" for="dokumen_sub_menu_utama_${menuIndex}_${j}">Pilih file PDF (maks. 5 MB)</label>
                </div>
                <div class="invalid-feedback" id="dokumen_sub_menu_utama_${menuIndex}_${j}_error"></div>
              </div>
            </div>

            <div id="sub_menu_${menuIndex}_${j}_container" style="display: none;">
              <div class="form-group">
                <label for="jumlah_sub_menu_${menuIndex}_${j}">Jumlah Sub Menu ${menuIndex}.${j} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="jumlah_sub_menu_${menuIndex}_${j}" 
                  name="jumlah_sub_menu_${menuIndex}_${j}" min="1" max="20" 
                  placeholder="Masukkan jumlah sub menu" onchange="generateSubMenuFields(${menuIndex}, ${j})">
                <div class="invalid-feedback" id="jumlah_sub_menu_${menuIndex}_${j}_error"></div>
              </div>
              <div id="sub_menu_fields_${menuIndex}_${j}"></div>
            </div>
          </div>
        </div>
      `;
      container.append(subMenuUtamaCard);
    }
  }

  function toggleSubMenu(menuIndex, subMenuIndex) {
    const adaSubMenu = $(`input[name="ada_sub_menu_${menuIndex}_${subMenuIndex}"]:checked`).val();
    const dokumenContainer = $(`#dokumen_sub_menu_utama_${menuIndex}_${subMenuIndex}_container`);
    const subMenuContainer = $(`#sub_menu_${menuIndex}_${subMenuIndex}_container`);

    if (adaSubMenu === 'tidak') {
      dokumenContainer.show();
      subMenuContainer.hide();
      // Clear sub menu fields
      $(`#jumlah_sub_menu_${menuIndex}_${subMenuIndex}`).val('');
      $(`#sub_menu_fields_${menuIndex}_${subMenuIndex}`).empty();
    } else if (adaSubMenu === 'ya') {
      dokumenContainer.hide();
      subMenuContainer.show();
      // Clear dokumen field
      $(`#dokumen_sub_menu_utama_${menuIndex}_${subMenuIndex}`).val('');
      $(`label[for="dokumen_sub_menu_utama_${menuIndex}_${subMenuIndex}"]`).text('Pilih file PDF (maks. 5 MB)');
    }
  }

  function generateSubMenuFields(menuIndex, subMenuIndex) {
    const jumlahSubMenu = parseInt($(`#jumlah_sub_menu_${menuIndex}_${subMenuIndex}`).val());
    const container = $(`#sub_menu_fields_${menuIndex}_${subMenuIndex}`);

    if (isNaN(jumlahSubMenu) || jumlahSubMenu < 1 || jumlahSubMenu > 20) {
      container.empty();
      return;
    }

    container.empty();

    for (let k = 1; k <= jumlahSubMenu; k++) {
      const subMenuCard = `
        <div class="card border-warning mb-2">
          <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">
              <i class="fas fa-file mr-2"></i>Sub Menu ${menuIndex}.${subMenuIndex}.${k}
            </h6>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="nama_sub_menu_${menuIndex}_${subMenuIndex}_${k}">Nama Sub Menu ${menuIndex}.${subMenuIndex}.${k} <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_sub_menu_${menuIndex}_${subMenuIndex}_${k}" 
                name="nama_sub_menu_${menuIndex}_${subMenuIndex}_${k}" maxlength="255" placeholder="Masukkan nama sub menu">
              <div class="invalid-feedback" id="nama_sub_menu_${menuIndex}_${subMenuIndex}_${k}_error"></div>
            </div>

            <div class="form-group">
              <label for="dokumen_sub_menu_${menuIndex}_${subMenuIndex}_${k}">Dokumen Sub Menu ${menuIndex}.${subMenuIndex}.${k} <span class="text-danger">*</span></label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="dokumen_sub_menu_${menuIndex}_${subMenuIndex}_${k}" 
                  name="dokumen_sub_menu_${menuIndex}_${subMenuIndex}_${k}" accept=".pdf">
                <label class="custom-file-label" for="dokumen_sub_menu_${menuIndex}_${subMenuIndex}_${k}">Pilih file PDF (maks. 5 MB)</label>
              </div>
              <div class="invalid-feedback" id="dokumen_sub_menu_${menuIndex}_${subMenuIndex}_${k}_error"></div>
            </div>
          </div>
        </div>
      `;
      container.append(subMenuCard);
    }
  }

  $(document).ready(function () {
    // Handle custom file input
    $(document).on('change', '.custom-file-input', function() {
      var fileName = $(this).val().split('\\').pop();
      $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file PDF (maks. 5 MB)');
    });

    // Handle input event pada field jumlah menu utama
    $('#jumlah_menu_utama').on('input', function () {
      if ($(this).val() !== '') {
        generateMenuUtamaFields();
      } else {
        $('#menu_utama_container').empty();
      }
    });

    // Remove error ketika input berubah
    $(document).on('input change', 'input, select, textarea', function() {
      $(this).removeClass('is-invalid');
      const errorId = `#${$(this).attr('id')}_error`;
      $(errorId).html('');
    });

    // Handle submit form
    $('#btnSubmitForm').on('click', function() {
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').html('');
      
      const form = $('#formCreateSetIpDinamisTabel');
      const formData = new FormData(form[0]);
      const button = $(this);
      
      button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
      
      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            $('#myModal').modal('hide');
            reloadTable();
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message || 'Set Informasi Publik Dinamis Tabel berhasil dibuat'
            });
          } else {
            if (response.errors) {
              $.each(response.errors, function(key, value) {
                $(`#${key}`).addClass('is-invalid');
                $(`#${key}_error`).html(value[0]);
              });
              
              Swal.fire({
                icon: 'error', 
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali input Anda'
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.message || 'Terjadi kesalahan saat menyimpan data'
              });
            }
          }
        },
        error: function(xhr) {
          console.error('Ajax Error:', xhr);
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
          });
        },
        complete: function() {
          button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
        }
      });
    });
  });
</script>

<style>
  .card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .card-header h6 {
    font-weight: 600;
  }
  
  .form-check {
    margin-bottom: 0.5rem;
  }
  
  .custom-file-label.selected {
    color: #495057;
  }
  
  .alert-info {
    border-left: 4px solid #17a2b8;
  }
</style>