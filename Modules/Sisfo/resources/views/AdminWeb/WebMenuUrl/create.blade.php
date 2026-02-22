<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\create.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah URL Menu Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <form id="formCreateWebMenuUrl" action="{{ url($webMenuUrlPath . '/createData') }}" method="POST">
      @csrf
      
      <style>
        /* ✅ Modal 80% width dengan jarak dari sidebar */
        .modal-dialog {
          max-width: 80% !important;
          margin: 1.75rem auto !important;
          margin-left: calc(250px + 4rem) !important; /* Sidebar 250px + jarak 2rem */
          margin-right: 2rem !important;
        }
        
        /* Style untuk disabled inputs - warna pink/merah muda */
        input:disabled, select:disabled, textarea:disabled {
          background-color: #ffe6f0 !important;
          cursor: not-allowed;
          opacity: 0.8;
        }
        
        .custom-control-input:disabled ~ .custom-control-label {
          color: #ff69b4;
          cursor: not-allowed;
        }
        
        .custom-control-input:disabled ~ .custom-control-label::before {
          background-color: #ffe6f0;
          border-color: #ff69b4;
        }
        
        /* FK Display Columns Selector */
        .fk-display-cols {
          margin-top: 5px;
          padding: 5px;
          border: 1px solid #dee2e6;
          border-radius: 4px;
          background-color: #f8f9fa;
        }
        
        .fk-display-cols label {
          display: block;
          margin-bottom: 3px;
          font-size: 0.875rem;
        }
      </style>
  
      <div class="form-group">
        <label for="fk_m_application">Aplikasi <span class="text-danger">*</span></label>
        <select class="form-control" id="fk_m_application" name="web_menu_url[fk_m_application]">
          <option value="">Pilih Aplikasi</option>
          @foreach($applications as $app)
            <option value="{{ $app->application_id }}">{{ $app->app_nama }}</option>
          @endforeach
        </select>
        <div class="invalid-feedback" id="fk_m_application_error"></div>
      </div>

      <div class="form-group">
        <label>Kategori Menu <span class="text-danger">*</span></label>
        <div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="kategori_custom" name="web_menu_url[wmu_kategori_menu]" 
                   class="custom-control-input" value="custom" checked>
            <label class="custom-control-label" for="kategori_custom">Custom (Manual Ngoding)</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="kategori_master" name="web_menu_url[wmu_kategori_menu]" 
                   class="custom-control-input" value="master">
            <label class="custom-control-label" for="kategori_master">Master (Template Master)</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="kategori_pengajuan" name="web_menu_url[wmu_kategori_menu]" 
                   class="custom-control-input" value="pengajuan">
            <label class="custom-control-label" for="kategori_pengajuan">Pengajuan (Coming Soon)</label>
          </div>
        </div>
        <small class="text-muted">Pilih kategori menu sesuai kebutuhan</small>
      </div>

      <div class="form-group">
        <label for="wmu_nama">URL Menu <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="wmu_nama" name="web_menu_url[wmu_nama]" maxlength="255">
        <small class="text-muted">Contoh: management-aplikasi, menu-footer, berita</small>
        <div class="invalid-feedback" id="wmu_nama_error"></div>
      </div>

      <!-- Section untuk Custom -->
      <div id="section-custom" style="display:none;">
        <div class="form-group">
          <label for="module_type">Module Type <span class="text-danger">*</span></label>
          <select class="form-control" id="module_type" name="web_menu_url[module_type]">
            <option value="">Pilih Module Type</option>
            <option value="sisfo">Sisfo</option>
            <option value="user">User</option>
          </select>
          <div class="invalid-feedback" id="module_type_error"></div>
        </div>

        <div class="form-group">
          <label for="controller_name">Controller Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="controller_name" name="web_menu_url[controller_name]" maxlength="255">
          <small class="text-muted">Contoh: AdminWeb\Footer\FooterController</small>
          <div class="invalid-feedback" id="controller_name_error"></div>
        </div>
      </div>

      <!-- Section untuk Master -->
      <div id="section-master" style="display:none;">
        <div class="form-group">
          <label for="wmu_akses_tabel">Nama Tabel Database <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="text" class="form-control" id="wmu_akses_tabel" name="web_menu_url[wmu_akses_tabel]" maxlength="100">
            <div class="input-group-append">
              <button type="button" class="btn btn-primary" id="btnCekTabel">
                <i class="fas fa-search"></i> Cek Tabel
              </button>
            </div>
          </div>
          <small class="text-muted">Contoh: m_kategori, m_footer, web_banner</small>
          <div class="invalid-feedback" id="wmu_akses_tabel_error"></div>
          <div class="valid-feedback" id="wmu_akses_tabel_success"></div>
        </div>

        <div id="field-configurator" style="display:none;">
          <hr>
          <h6 class="font-weight-bold">Konfigurasi Field</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-sm" id="tableFieldConfig">
              <thead class="thead-light">
                <tr>
                  <th width="3%">No</th>
                  <th width="18%">Nama Kolom</th>
                  <th width="15%">Label Field</th>
                  <th width="12%">Tipe Input</th>
                  <th width="15%">Kriteria <small class="text-muted">(opsional)</small></th>
                  <th width="17%">Validasi <small class="text-muted">(opsional)</small></th>
                  <th width="10%">FK Config</th>
                  <th width="10%">Visible</th>
                </tr>
              </thead>
              <tbody id="fieldConfigBody">
                <!-- Field rows akan di-generate oleh JavaScript -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Section untuk Pengajuan -->
      <div id="section-pengajuan" style="display:none;">
        <div class="alert alert-info">
          <i class="fas fa-info-circle mr-2"></i>
          <strong>Informasi:</strong> Fitur kategori menu "Pengajuan" masih dalam tahap pengembangan. 
          Silahkan gunakan kategori "Custom" atau "Master" terlebih dahulu.
        </div>
      </div>
      
      <div class="form-group">
        <label for="wmu_keterangan">Deskripsi URL Menu</label>
        <textarea class="form-control" id="wmu_keterangan" name="web_menu_url[wmu_keterangan]" rows="3"></textarea>
        <div class="invalid-feedback" id="wmu_keterangan_error"></div>
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
    $(document).ready(function () {
      let fieldConfigs = [];
      let fieldCounter = 0;

      // Handle kategori menu change
      $('input[name="web_menu_url[wmu_kategori_menu]"]').on('change', function() {
        const kategori = $(this).val();
        
        // Hide all sections
        $('#section-custom, #section-master, #section-pengajuan').hide();
        
        // Show selected section
        if (kategori === 'custom') {
          $('#section-custom').show();
        } else if (kategori === 'master') {
          $('#section-master').show();
        } else if (kategori === 'pengajuan') {
          $('#section-pengajuan').show();
        }
      });

      // Trigger change pada load untuk show section custom (default)
      $('input[name="web_menu_url[wmu_kategori_menu]"]:checked').trigger('change');

      // Handle Cek Tabel button
      $('#btnCekTabel').on('click', function() {
        const tableName = $('#wmu_akses_tabel').val().trim();
        
        if (!tableName) {
          Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Nama tabel tidak boleh kosong'
          });
          return;
        }

        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Mengecek...').prop('disabled', true);

        $.ajax({
          url: '{{ url($webMenuUrlPath . "/createData") }}',
          type: 'POST',
          data: {
            action: 'validateTable',
            table_name: tableName,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            // ✅ CASE 1: Tabel valid, belum terdaftar
            if (response.success && !response.isDuplicate) {
              $('#wmu_akses_tabel').removeClass('is-invalid').addClass('is-valid');
              $('#wmu_akses_tabel_success').html('<i class="fas fa-check-circle"></i> ' + response.message).show();
              $('#wmu_akses_tabel_error').hide();
              $('#btnSubmitForm').prop('disabled', false); // Enable submit

              // Auto generate fields
              autoGenerateFields(tableName);
            } 
            // ✅ CASE 2: Tabel sudah terdaftar, ada perubahan struktur
            else if (response.success && response.isDuplicate && response.hasChanges) {
              $('#wmu_akses_tabel').removeClass('is-invalid').addClass('is-valid');
              
              // Build changes detail HTML
              let changesHtml = buildChangesDetailHtml(response.changes);
              
              Swal.fire({
                icon: 'warning',
                title: 'Tabel Sudah Terdaftar - Ada Perubahan',
                html: response.message + '<br><br><button id="btnShowChanges" class="btn btn-info btn-sm mt-2"><i class="fas fa-list"></i> Lihat Detail Perubahan</button>',
                showConfirmButton: true,
                confirmButtonText: 'OK, Lanjutkan Konfigurasi Ulang',
                didOpen: () => {
                  $('#btnShowChanges').on('click', function() {
                    Swal.fire({
                      icon: 'info',
                      title: 'Detail Perubahan Struktur Tabel',
                      html: changesHtml,
                      width: '600px',
                      confirmButtonText: 'Tutup'
                    });
                  });
                }
              });

              $('#wmu_akses_tabel_success').html('<i class="fas fa-exclamation-triangle text-warning"></i> ' + response.message).show();
              $('#wmu_akses_tabel_error').hide();
              $('#btnSubmitForm').prop('disabled', false); // ✅ ALLOW update
              
              // Store existing menu ID untuk soft delete nanti
              $('#formCreateWebMenuUrl').data('existingMenuId', response.existingMenuId);
              $('#formCreateWebMenuUrl').data('isUpdate', true);

              // Auto generate fields baru
              autoGenerateFields(tableName);
            }
            // ❌ CASE 3: Tabel sudah terdaftar, TIDAK ada perubahan
            else if (!response.success && response.isDuplicate && !response.hasChanges) {
              $('#wmu_akses_tabel').removeClass('is-valid').addClass('is-invalid');
              $('#wmu_akses_tabel_error').html(response.message).show();
              $('#wmu_akses_tabel_success').hide();
              $('#field-configurator').hide();
              $('#btnSubmitForm').prop('disabled', true); // ❌ BLOCK submit
              
              Swal.fire({
                icon: 'error',
                title: 'Tabel Sudah Terdaftar',
                html: response.message,
                showConfirmButton: true,
              });
            }
            // ❌ CASE 4: Error lainnya (missing common fields, dll)
            else {
              $('#wmu_akses_tabel').removeClass('is-valid').addClass('is-invalid');
              $('#wmu_akses_tabel_error').html(response.message).show();
              $('#wmu_akses_tabel_success').hide();
              $('#field-configurator').hide();
              $('#btnSubmitForm').prop('disabled', true);
            }
          },
          error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengecek tabel';
            $('#wmu_akses_tabel').removeClass('is-valid').addClass('is-invalid');
            $('#wmu_akses_tabel_error').html(errorMsg).show();
            $('#wmu_akses_tabel_success').hide();
            $('#field-configurator').hide();
            $('#btnSubmitForm').prop('disabled', true);
          },
          complete: function() {
            btn.html('<i class="fas fa-search"></i> Cek Tabel').prop('disabled', false);
          }
        });
      });

      // ✅ Function untuk build HTML detail perubahan
      function buildChangesDetailHtml(changes) {
        let html = '<div style="text-align: left;">';
        
        // Added columns
        if (changes.added && changes.added.length > 0) {
          html += '<h6 class="text-success"><i class="fas fa-plus-circle"></i> Kolom Baru Ditambahkan (' + changes.added.length + '):</h6>';
          html += '<ul>';
          changes.added.forEach(col => {
            html += `<li><strong>${col.column_name}</strong> - ${col.data_type}${col.is_primary_key ? ' (PK)' : ''}</li>`;
          });
          html += '</ul><hr>';
        }
        
        // Modified columns
        if (changes.modified && changes.modified.length > 0) {
          html += '<h6 class="text-warning"><i class="fas fa-edit"></i> Kolom yang Berubah (' + changes.modified.length + '):</h6>';
          html += '<ul>';
          changes.modified.forEach(col => {
            html += `<li><strong>${col.column_name}</strong>: <span class="text-danger">${col.old_type}</span> → <span class="text-success">${col.new_type}</span></li>`;
          });
          html += '</ul><hr>';
        }
        
        // Removed columns
        if (changes.removed && changes.removed.length > 0) {
          html += '<h6 class="text-danger"><i class="fas fa-minus-circle"></i> Kolom yang Dihapus (' + changes.removed.length + '):</h6>';
          html += '<ul>';
          changes.removed.forEach(col => {
            html += `<li><strong>${col.column_name}</strong> - ${col.data_type}</li>`;
          });
          html += '</ul>';
        }
        
        html += '</div>';
        return html;
      }

      // Auto generate fields dari tabel
      function autoGenerateFields(tableName) {
        $.ajax({
          url: '{{ url($webMenuUrlPath . "/createData") }}',
          type: 'POST',
          data: {
            action: 'autoGenerateFields',
            table_name: tableName,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success && response.data) {
              fieldConfigs = response.data;
              renderFieldConfigs();
              $('#field-configurator').slideDown();
              
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: response.message,
                timer: 2000
              });
            }
          },
          error: function(xhr) {
            console.error('Error generating fields:', xhr);
          }
        });
      }

      // Render field configs ke table
      function renderFieldConfigs() {
        const tbody = $('#fieldConfigBody');
        tbody.empty();

        fieldConfigs.forEach((field, index) => {
          const row = createFieldRow(field, index);
          tbody.append(row);
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize field type validations
        initializeFieldTypeValidations();
      }

      // Create field row HTML
      function createFieldRow(field, index) {
        const isFk = field.wmfc_is_foreign_key == 1 || field.wmfc_field_type === 'search';
        const isPk = field.wmfc_is_primary_key == 1;
        const isAutoInc = field.wmfc_is_auto_increment == 1;
        const maxLength = field.wmfc_max_length || null;
        
        // Determine disable states
        const isPkAutoInc = isPk && isAutoInc; // PK + Auto Increment
        const isPkNonAutoInc = isPk && !isAutoInc; // PK tapi bukan Auto Increment (misal: FK yang jadi PK)
        
        // Parse JSON strings
        const criteria = field.wmfc_criteria ? (typeof field.wmfc_criteria === 'string' ? JSON.parse(field.wmfc_criteria) : field.wmfc_criteria) : {};
        const validation = field.wmfc_validation ? (typeof field.wmfc_validation === 'string' ? JSON.parse(field.wmfc_validation) : field.wmfc_validation) : {};
        
        // Build field type options based on available types
        let typeOptionsHtml = '';
        const typeLabels = {
          text: 'Text',
          textarea: 'Textarea',
          number: 'Number',
          date: 'Date',
          date2: 'Date Range',
          dropdown: 'Dropdown',
          radio: 'Radio',
          search: 'Search (FK)'
        };
        
        // ✅ FIX: Jika FK, HANYA tampilkan Search (FK) tanpa opsi lain
        let fieldTypeOptions;
        if (isFk) {
          fieldTypeOptions = ['search']; // FK hanya boleh Search
        } else {
          fieldTypeOptions = field.wmfc_field_type_options || ['text', 'textarea', 'number', 'date', 'date2', 'dropdown', 'radio'];
        }
        
        fieldTypeOptions.forEach(type => {
          typeOptionsHtml += `<option value="${type}" ${field.wmfc_field_type === type ? 'selected' : ''}>${typeLabels[type]}</option>`;
        });
        
        // Check unique criteria - auto untuk PK (baik auto-inc maupun non auto-inc)
        const isUniqueCriteria = criteria.unique === true || isPk;
        
        // FK Config - Build checkbox untuk kolom yang ditampilkan
        let fkDisplayColsHtml = '';
        let fkTooltipDisplay = ''; // Untuk tooltip - hanya kolom tercentang
        if (isFk && field.wmfc_fk_table) {
          const fkDisplayCols = field.wmfc_fk_display_columns ? 
            (typeof field.wmfc_fk_display_columns === 'string' ? JSON.parse(field.wmfc_fk_display_columns) : field.wmfc_fk_display_columns) : [];
          
          // Build FK display columns checkboxes (pindah ke kolom Type Input)
          if (fkDisplayCols.length > 0) {
            fkDisplayColsHtml = '<div class="fk-display-cols mt-2"><small class="text-muted d-block mb-1">Kolom yang ditampilkan:</small>';
            fkDisplayCols.forEach(col => {
              // Extract column name dari object atau gunakan langsung jika string
              const colName = typeof col === 'object' ? col.column_name : col;
              const colLabel = typeof col === 'object' ? (col.suggested_label || colName) : colName;
              
              fkDisplayColsHtml += `
                <label class="mb-0">
                  <input type="checkbox" class="fk-display-checkbox" 
                         name="field_configs[${index}][fk_display_cols][]" 
                         value="${colName}" 
                         data-index="${index}"
                         checked class="mr-1">
                  ${colLabel}
                </label>`;
            });
            fkDisplayColsHtml += '</div>';
            
            // Initial tooltip display (semua kolom tercentang di awal)
            const displayNames = fkDisplayCols.map(col => typeof col === 'object' ? col.column_name : col);
            fkTooltipDisplay = displayNames.join(', ');
          }
        }
        
        // FK Config content (pindah ke kolom FK Config - hanya badge + tooltip)
        let fkConfigHtml = '';
        if (isFk && field.wmfc_fk_table) {
          fkConfigHtml = `
            <span class="badge badge-success">Ada</span>
            <button type="button" class="badge badge-info border-0 fk-detail-tooltip" 
                    data-toggle="tooltip" 
                    data-html="true"
                    data-index="${index}"
                    title="<strong>Tabel:</strong> ${field.wmfc_fk_table}<br><strong>PK:</strong> ${field.wmfc_fk_pk_column}<br><strong>Display:</strong> ${fkTooltipDisplay}">
              Detail
            </button>
            <input type="hidden" name="field_configs[${index}][wmfc_fk_table]" value="${field.wmfc_fk_table || ''}">
            <input type="hidden" name="field_configs[${index}][wmfc_fk_pk_column]" value="${field.wmfc_fk_pk_column || ''}">
            <input type="hidden" name="field_configs[${index}][wmfc_fk_display_columns]" value='${field.wmfc_fk_display_columns || '[]'}'>
          `;
        } else {
          fkConfigHtml = '<span class="badge badge-secondary">Tidak Ada</span>';
        }
        
        return `
          <tr data-index="${index}" data-field-type="${field.wmfc_field_type}">
            <td class="text-center">${index + 1}</td>
            <td>
              <strong>${field.wmfc_column_name}</strong>
              <br><small class="text-muted">${field.wmfc_column_type || ''}</small>
              <input type="hidden" name="field_configs[${index}][wmfc_column_name]" value="${field.wmfc_column_name}">
              <input type="hidden" name="field_configs[${index}][wmfc_column_type]" value="${field.wmfc_column_type || ''}">
              <input type="hidden" name="field_configs[${index}][wmfc_max_length]" value="${maxLength || ''}">
              <input type="hidden" name="field_configs[${index}][wmfc_order]" value="${field.wmfc_order}">
              <input type="hidden" name="field_configs[${index}][wmfc_is_primary_key]" value="${field.wmfc_is_primary_key}">
              <input type="hidden" name="field_configs[${index}][wmfc_is_auto_increment]" value="${field.wmfc_is_auto_increment}">
            </td>
            <td>
              <input type="text" class="form-control form-control-sm" 
                     name="field_configs[${index}][wmfc_field_label]" 
                     value="${field.wmfc_field_label}" ${isPkAutoInc ? 'disabled' : ''}>
            </td>
            <td>
              <select class="form-control form-control-sm field-type-select" 
                      name="field_configs[${index}][wmfc_field_type]" 
                      data-index="${index}" ${isPkAutoInc ? 'disabled' : ''}>
                ${typeOptionsHtml}
              </select>
              ${fkDisplayColsHtml}
            </td>
            <td>
              <div class="criteria-checkboxes">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input criteria-unique" 
                         id="unique_${index}" value="1"
                         name="field_configs[${index}][criteria_unique]"
                         data-index="${index}"
                         ${isUniqueCriteria ? 'checked' : ''}
                         ${isPk || isPkAutoInc ? 'disabled' : ''}>
                  <label class="custom-control-label" for="unique_${index}">Unique</label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input criteria-uppercase" 
                         id="uppercase_${index}" value="1"
                         name="field_configs[${index}][criteria_uppercase]"
                         data-index="${index}"
                         ${criteria.case === 'uppercase' ? 'checked' : ''}
                         ${isPkAutoInc ? 'disabled' : ''}>
                  <label class="custom-control-label" for="uppercase_${index}">Uppercase</label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input criteria-lowercase" 
                         id="lowercase_${index}" value="1"
                         name="field_configs[${index}][criteria_lowercase]"
                         data-index="${index}"
                         ${criteria.case === 'lowercase' ? 'checked' : ''}
                         ${isPkAutoInc ? 'disabled' : ''}>
                  <label class="custom-control-label" for="lowercase_${index}">Lowercase</label>
                </div>
              </div>
            </td>
            <td>
              <div class="validation-checkboxes">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input validation-required" 
                         id="required_${index}" value="1"
                         name="field_configs[${index}][validation_required]"
                         data-index="${index}"
                         ${validation.required || isPk ? 'checked' : ''}
                         ${isPk || isPkAutoInc ? 'disabled' : ''}>
                  <label class="custom-control-label" for="required_${index}">Required</label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input validation-unique" 
                         id="validation_unique_${index}" value="1"
                         name="field_configs[${index}][validation_unique]"
                         data-index="${index}"
                         ${isUniqueCriteria ? 'checked disabled' : ''}>
                  <label class="custom-control-label" for="validation_unique_${index}">Unique</label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input validation-email" 
                         id="email_${index}" value="1"
                         name="field_configs[${index}][validation_email]"
                         data-index="${index}"
                         ${validation.email ? 'checked' : ''}
                         ${isPkAutoInc ? 'disabled' : ''}>
                  <label class="custom-control-label" for="email_${index}">Email</label>
                </div>
                <div class="input-group input-group-sm mt-1">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Max</span>
                  </div>
                  <input type="number" class="form-control max-length-input" 
                         name="field_configs[${index}][validation_max]"
                         value="${validation.max || (maxLength || '')}"
                         ${maxLength ? `max="${maxLength}" placeholder="${maxLength}"` : 'placeholder="No limit"'}
                         data-max="${maxLength || ''}"
                         data-index="${index}"
                         ${isPkAutoInc ? 'disabled' : ''}>
                </div>
                <div class="input-group input-group-sm mt-1">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Min</span>
                  </div>
                  <input type="number" class="form-control validation-min" 
                         name="field_configs[${index}][validation_min]"
                         value="${validation.min || ''}"
                         placeholder="1"
                         data-index="${index}"
                         ${isPkAutoInc ? 'disabled' : ''}>
                </div>
              </div>
            </td>
            <td class="text-center">
              ${fkConfigHtml}
            </td>
            <td class="text-center">
              ${isPkAutoInc ? `
                <span class="badge badge-secondary">Hidden</span>
                <input type="hidden" name="field_configs[${index}][wmfc_is_visible]" value="0">
              ` : `
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" 
                         id="visible_${index}"
                         name="field_configs[${index}][wmfc_is_visible]" 
                         value="1" ${field.wmfc_is_visible == 1 ? 'checked' : ''}>
                  <label class="custom-control-label" for="visible_${index}"></label>
                </div>
              `}
            </td>
          </tr>
        `;
      }

      // Handle field type change - toggle uppercase/lowercase/email based on type
      $(document).on('change', '.field-type-select', function() {
        const index = $(this).data('index');
        const selectedType = $(this).val();
        const row = $(this).closest('tr');
        
        // Update row data attribute
        row.attr('data-field-type', selectedType);
        
        // Enable/disable uppercase, lowercase, email based on type
        const isTextType = ['text', 'textarea'].includes(selectedType);
        
        $(`#uppercase_${index}`).prop('disabled', !isTextType);
        $(`#lowercase_${index}`).prop('disabled', !isTextType);
        $(`#email_${index}`).prop('disabled', !isTextType);
        
        // Uncheck if disabled
        if (!isTextType) {
          $(`#uppercase_${index}`).prop('checked', false);
          $(`#lowercase_${index}`).prop('checked', false);
          $(`#email_${index}`).prop('checked', false);
        }
      });

      // Handle criteria unique change - auto check/uncheck validation unique
      $(document).on('change', '.criteria-unique', function() {
        const index = $(this).data('index');
        const validationUnique = $(`#validation_unique_${index}`);
        
        if ($(this).is(':checked')) {
          validationUnique.prop('checked', true).prop('disabled', true);
        } else {
          validationUnique.prop('checked', false).prop('disabled', false);
        }
      });

      // Handle validation unique change - auto check/uncheck criteria unique (TWO-WAY SYNC)
      $(document).on('change', '.validation-unique', function() {
        const index = $(this).data('index');
        const criteriaUnique = $(`#unique_${index}`);
        
        // Jika validation unique di-check, criteria unique juga auto-check
        if ($(this).is(':checked')) {
          criteriaUnique.prop('checked', true);
          // Disable validation unique karena sudah auto-checked dari criteria
          $(this).prop('disabled', true);
        }
        // Jika validation unique di-uncheck, criteria unique juga auto-uncheck
        // (tapi ini jarang terjadi karena biasanya disabled)
      });

      // Handle FK display columns checkbox change - update tooltip
      $(document).on('change', '.fk-display-checkbox', function() {
        const index = $(this).data('index');
        const checkedCols = [];
        
        // Ambil semua checkbox yang tercentang untuk field ini
        $(`.fk-display-checkbox[data-index="${index}"]:checked`).each(function() {
          checkedCols.push($(this).val());
        });
        
        // Update tooltip
        const tooltip = $(`.fk-detail-tooltip[data-index="${index}"]`);
        const fkTable = tooltip.closest('td').find('input[name*="wmfc_fk_table"]').val();
        const fkPkColumn = tooltip.closest('td').find('input[name*="wmfc_fk_pk_column"]').val();
        
        const newTooltip = `<strong>Tabel:</strong> ${fkTable}<br><strong>PK:</strong> ${fkPkColumn}<br><strong>Display:</strong> ${checkedCols.join(', ')}`;
        tooltip.attr('data-original-title', newTooltip).tooltip('dispose').tooltip({html: true, title: newTooltip});
      });

      // Handle max length validation - auto clamp to column max length
      $(document).on('input', '.max-length-input', function() {
        const maxAllowed = parseInt($(this).data('max'));
        const currentValue = parseInt($(this).val());
        
        if (maxAllowed && currentValue > maxAllowed) {
          $(this).val(maxAllowed);
        }
      });

      // Initial check for uppercase/lowercase/email on page load
      function initializeFieldTypeValidations() {
        $('.field-type-select').each(function() {
          const index = $(this).data('index');
          const selectedType = $(this).val();
          const isTextType = ['text', 'textarea'].includes(selectedType);
          
          $(`#uppercase_${index}`).prop('disabled', !isTextType);
          $(`#lowercase_${index}`).prop('disabled', !isTextType);
          $(`#email_${index}`).prop('disabled', !isTextType);
          
          if (!isTextType) {
            $(`#uppercase_${index}`).prop('checked', false);
            $(`#lowercase_${index}`).prop('checked', false);
            $(`#email_${index}`).prop('checked', false);
          }
        });
      }

      // Handle remove field - removed (no delete button anymore)
      // Fields are now auto-generated from table structure

      // Hapus error ketika input berubah
      $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
      });
  
      // Handle submit form
      $('#btnSubmitForm').on('click', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        const form = $('#formCreateWebMenuUrl');
        const formData = new FormData(form[0]);
        const button = $(this);
        
        // ✅ Tambahkan existing_menu_id dan is_update jika ada (untuk update case)
        const existingMenuId = form.data('existingMenuId');
        const isUpdate = form.data('isUpdate');
        
        if (existingMenuId) {
          formData.append('existing_menu_id', existingMenuId);
        }
        if (isUpdate) {
          formData.append('is_update', isUpdate);
        }
        
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
                text: response.message
              });
            } else {
              if (response.errors) {
                $.each(response.errors, function(key, value) {
                  if (key.startsWith('web_menu_url.')) {
                    const fieldName = key.replace('web_menu_url.', '');
                    $(`#${fieldName}`).addClass('is-invalid');
                    $(`#${fieldName}_error`).html(value[0]);
                  } else if (key.startsWith('field_configs.')) {
                    // Handle field config errors
                    console.error('Field config error:', key, value);
                  } else {
                    $(`#${key}`).addClass('is-invalid');
                    $(`#${key}_error`).html(value[0]);
                  }
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
            console.error('Submit error:', xhr);
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