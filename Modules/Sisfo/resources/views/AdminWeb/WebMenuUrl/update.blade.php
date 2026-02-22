<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\update.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah URL Menu</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateWebMenuUrl" action="{{ url($webMenuUrlPath . '/updateData/' . $webMenuUrl->web_menu_url_id) }}"
        method="POST">
        @csrf

        <style>
          /* Modal width & margin adjustment */
          #myModal .modal-dialog {
            max-width: 80% !important;
            margin-left: calc(250px + 2rem) !important;
            margin-right: 2rem !important;
          }

          /* Field Config Table */
          .field-config-table {
            font-size: 0.85rem;
          }
          .field-config-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            white-space: nowrap;
          }
          .field-config-table td {
            vertical-align: middle;
          }
          .field-config-table input,
          .field-config-table select {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
          }
          .field-config-table .form-control-sm {
            height: calc(1.5em + 0.5rem);
          }
          .field-config-table .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
          }
          .badge-pk {
            background-color: #007bff;
          }
          .badge-fk {
            background-color: #28a745;
          }
        </style>

        <div class="form-group">
          <label for="fk_m_application">Aplikasi <span class="text-danger">*</span></label>
          <select class="form-control" id="fk_m_application" name="web_menu_url[fk_m_application]">
            <option value="">Pilih Aplikasi</option>
            @foreach($applications as $app)
              <option value="{{ $app->application_id }}" {{ $webMenuUrl->fk_m_application == $app->application_id ? 'selected' : '' }}>
                {{ $app->app_nama }}
              </option>
            @endforeach
          </select>
          <div class="invalid-feedback" id="fk_m_application_error"></div>
        </div>

        <div class="form-group">
          <label for="wmu_kategori_menu">Kategori Menu <span class="text-danger">*</span></label>
          <select class="form-control" id="wmu_kategori_menu" name="web_menu_url[wmu_kategori_menu]" disabled>
            <option value="master" {{ $webMenuUrl->wmu_kategori_menu === 'master' ? 'selected' : '' }}>Master (Template CRUD Otomatis)</option>
            <option value="pengajuan" {{ $webMenuUrl->wmu_kategori_menu === 'pengajuan' ? 'selected' : '' }}>Pengajuan (Template Approval)</option>
            <option value="custom" {{ $webMenuUrl->wmu_kategori_menu === 'custom' ? 'selected' : '' }}>Custom (Manual Coding)</option>
          </select>
          <small class="text-muted">Kategori menu tidak bisa diubah setelah dibuat</small>
          <input type="hidden" name="web_menu_url[wmu_kategori_menu]" value="{{ $webMenuUrl->wmu_kategori_menu }}">
          <div class="invalid-feedback" id="wmu_kategori_menu_error"></div>
        </div>

        <!-- Section untuk Master -->
        <div id="sectionMaster" style="display: {{ $webMenuUrl->wmu_kategori_menu === 'master' ? 'block' : 'none' }};">
          <div class="form-group">
            <label for="wmu_akses_tabel">Nama Tabel Database <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="text" class="form-control" id="wmu_akses_tabel" 
                     name="web_menu_url[wmu_akses_tabel]" 
                     value="{{ $webMenuUrl->wmu_akses_tabel }}"
                     placeholder="Contoh: m_kategori, m_user, t_transaksi" 
                     maxlength="255" readonly>
              <div class="input-group-append">
                <button type="button" class="btn btn-primary" id="btnCheckTable">
                  <i class="fas fa-search mr-1"></i> Re-Check Tabel
                </button>
              </div>
            </div>
            <small class="text-muted">Tabel yang akan dikelola oleh menu master ini (readonly, klik Re-Check untuk validasi ulang)</small>
            <div class="invalid-feedback" id="wmu_akses_tabel_error"></div>
          </div>

          <!-- Alert Info -->
          <div class="alert alert-info" id="alertTableInfo" style="display: none;">
            <i class="fas fa-info-circle mr-2"></i>
            <span id="alertTableInfoText"></span>
          </div>

          <!-- Field Configurations Table -->
          <div id="fieldConfigsSection" style="display: none;">
            <hr>
            <h6 class="font-weight-bold mb-3">
              <i class="fas fa-cogs mr-2"></i> Konfigurasi Field
            </h6>
            
            <div class="table-responsive">
              <table class="table table-bordered field-config-table" id="tableFieldConfigs">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kolom Database</th>
                    <th width="12%">Label Field</th>
                    <th width="12%">Type Input</th>
                    <th width="18%">Kriteria <small class="text-muted">(opsional)</small></th>
                    <th width="18%">Validasi <small class="text-muted">(opsional)</small></th>
                    <th width="10%">FK Config</th>
                    <th width="10%">Visible</th>
                  </tr>
                </thead>
                <tbody id="tbodyFieldConfigs">
                  <!-- Will be populated via AJAX -->
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Section untuk Custom -->
        <div id="sectionCustom" style="display: {{ $webMenuUrl->wmu_kategori_menu === 'custom' ? 'block' : 'none' }};">
          <div class="form-group">
            <label for="module_type">Module Type <span class="text-danger">*</span></label>
            <select class="form-control" id="module_type" name="web_menu_url[module_type]">
              <option value="sisfo" {{ $webMenuUrl->module_type === 'sisfo' ? 'selected' : '' }}>Sisfo</option>
              <option value="user" {{ $webMenuUrl->module_type === 'user' ? 'selected' : '' }}>User</option>
            </select>
            <div class="invalid-feedback" id="module_type_error"></div>
          </div>

          <div class="form-group" id="groupControllerName" style="display: {{ $webMenuUrl->module_type === 'sisfo' ? 'block' : 'none' }};">
            <label for="controller_name">Controller Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="controller_name" 
                   name="web_menu_url[controller_name]" 
                   value="{{ $webMenuUrl->controller_name }}"
                   placeholder="Contoh: AdminWeb\Footer\FooterController" 
                   maxlength="255">
            <small class="text-muted">Namespace controller (tanpa Modules\Sisfo\App\Http\Controllers\)</small>
            <div class="invalid-feedback" id="controller_name_error"></div>
          </div>

          <div class="form-group" id="groupParentMenu">
            <label for="wmu_parent_id">Parent Menu</label>
            <select class="form-control" id="wmu_parent_id" name="web_menu_url[wmu_parent_id]">
              <option value="">-- Tidak Ada Parent (Menu Utama) --</option>
              <!-- Will be populated via AJAX if needed -->
            </select>
            <small class="text-muted">Pilih parent jika menu ini adalah sub-menu</small>
            <div class="invalid-feedback" id="wmu_parent_id_error"></div>
          </div>
        </div>

        <div class="form-group">
            <label for="wmu_nama">URL Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmu_nama" name="web_menu_url[wmu_nama]" 
                 value="{{ $webMenuUrl->wmu_nama }}" maxlength="255">
                 <small class="text-muted">Contoh: management-aplikasi, management-user, E-Form/permohonan-informasi</small>
          <div class="invalid-feedback" id="wmu_nama_error"></div>
        </div>
        
        <div class="form-group">
          <label for="wmu_keterangan">Deskripsi URL Menu</label>
          <textarea class="form-control" id="wmu_keterangan" name="web_menu_url[wmu_keterangan]" rows="3">{{ $webMenuUrl->wmu_keterangan }}</textarea>
          <div class="invalid-feedback" id="wmu_keterangan_error"></div>
        </div>

        <!-- Hidden fields untuk Master category -->
        <input type="hidden" name="is_update" value="0">
        <input type="hidden" name="existing_menu_id" value="{{ $webMenuUrl->web_menu_url_id }}">
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<!-- Modal for Table Changes Detail -->
<div class="modal fade" id="modalChangesDetail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle mr-2"></i>Detail Perubahan Struktur Tabel
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="changesDetailContent">
        <!-- Will be populated via JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function () {
        const currentCategory = '{{ $webMenuUrl->wmu_kategori_menu }}';
        const currentTableName = '{{ $webMenuUrl->wmu_akses_tabel }}';
        const menuUrlId = '{{ $webMenuUrl->web_menu_url_id }}';
        
        let existingFieldConfigs = @json($webMenuUrl->fieldConfigs ?? []);
        let currentFkRow = null;
        let tableValidated = false;

        if (currentCategory === 'master') {
            const $alert = $('#alertTableInfo');
            
            // Update classes
            $alert.removeClass('alert-danger alert-warning');
            $alert.addClass('alert-info');
            
            // Update text
            $('#alertTableInfoText').html('<strong>Info:</strong> Silakan klik tombol <strong>"Re-Check Tabel"</strong> untuk memvalidasi struktur tabel dan melihat konfigurasi field.');
            
            // Force show alert
            $alert.show();
            
            // Hide field config section
            $('#fieldConfigsSection').hide();
            
            // Disable submit button
            $('#btnSubmitForm').prop('disabled', true);
        }

        // Module type change handler (Custom only)
        $('#module_type').on('change', function() {
            const moduleType = $(this).val();
            if (moduleType === 'sisfo') {
                $('#groupControllerName').show();
                $('#controller_name').prop('required', true);
            } else {
                $('#groupControllerName').hide();
                $('#controller_name').prop('required', false).val('');
            }
        });

        // Re-Check Table Button (Master only)
        $('#btnCheckTable').on('click', function() {
            const tableName = $('#wmu_akses_tabel').val().trim();
            
            if (!tableName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Nama tabel tidak boleh kosong'
                });
                return;
            }

            const button = $(this);
            button.html('<i class="fas fa-spinner fa-spin"></i> Checking...').prop('disabled', true);
            
            const ajaxUrl = '{{ url($webMenuUrlPath . "/editData/" . $webMenuUrl->web_menu_url_id) }}';
            const ajaxData = {
                action: 'validateTable',
                table_name: tableName,
                menu_url_id: menuUrlId
            };
            
            $.ajax({
                url: ajaxUrl,
                method: 'GET',
                data: ajaxData,
                success: function(response) {
                    button.html('<i class="fas fa-search mr-1"></i> Re-Check Tabel').prop('disabled', false);

                    if (response.success && response.data) {
                        const data = response.data;
                        
                        if (data.hasChanges === false || data.hasChanges === '' || data.hasChanges === 0 || !data.hasChanges) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Struktur Tabel Sama',
                                html: `Tabel <strong>${data.table_name}</strong> masih sama strukturnya dengan konfigurasi yang tersimpan.<br><br>Anda dapat melakukan update konfigurasi field.`,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            
                            renderFieldConfigs(data.fields);
                            $('#fieldConfigsSection').show();
                            $('#alertTableInfo').hide();
                            $('#btnSubmitForm').prop('disabled', false);
                            tableValidated = true;
                            $('input[name="is_update"]').val('0');
                        } 
                        else {
                            showChangesWarning(data);
                        }
                    } else {
                        showTableNotFoundError(response.message || 'Tabel tidak ditemukan');
                    }
                },
                error: function(xhr) {
                    button.html('<i class="fas fa-search mr-1"></i> Re-Check Tabel').prop('disabled', false);
                    const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat validasi tabel';
                    showTableNotFoundError(errorMsg);
                }
            });
        });

        // Function to show changes warning
        function showChangesWarning(data) {
            const changesHtml = buildChangesDetailHtml(data.changes);
            
            Swal.fire({
                icon: 'info',
                title: 'Perubahan Struktur Tabel Terdeteksi',
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>Tabel ${data.table_name}</strong> memiliki perubahan struktur:</p>
                        <ul class="mb-3">
                            ${data.changes.added && data.changes.added.length > 0 ? `<li><strong>${data.changes.added.length}</strong> kolom ditambahkan</li>` : ''}
                            ${data.changes.removed && data.changes.removed.length > 0 ? `<li><strong>${data.changes.removed.length}</strong> kolom dihapus</li>` : ''}
                            ${data.changes.modified && data.changes.modified.length > 0 ? `<li><strong>${data.changes.modified.length}</strong> kolom dimodifikasi</li>` : ''}
                        </ul>
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Mode UPDATE:</strong> Sistem akan melakukan <strong>TRUE UPDATE</strong> (update data existing).<br>
                            Field config yang ditampilkan adalah struktur tabel terbaru.
                        </div>
                        <button type="button" class="btn btn-sm btn-info" id="btnShowChangesDetail">
                            <i class="fas fa-info-circle mr-1"></i> Lihat Detail Perubahan
                        </button>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'OK, Lanjutkan Update',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                allowOutsideClick: false
            });

            // Show detail changes modal jika user klik button
            $(document).on('click', '#btnShowChangesDetail', function() {
                $('#changesDetailContent').html(changesHtml);
                $('#modalChangesDetail').modal('show');
            });
            
            // ✅ UPDATE MODE: Render field configs dengan struktur terbaru
            renderFieldConfigs(data.fields);
            $('#fieldConfigsSection').show();
            
            // Show alert info (WARNING, bukan DANGER)
            const $alert = $('#alertTableInfo');
            $alert.removeClass('alert-info alert-danger');
            $alert.addClass('alert-warning');
            $('#alertTableInfoText').html(
                '<i class="fas fa-info-circle mr-2"></i>' +
                '<strong>Info:</strong> Struktur tabel berubah. Sistem akan melakukan <strong>TRUE UPDATE</strong> (update existing records).'
            );
            
            // Show alert
            $alert.show();
            
            // ✅ ENABLE submit button (allow update)
            $('#btnSubmitForm').prop('disabled', false);
            tableValidated = true;
        }

        // Function to show table not found error
        function showTableNotFoundError(errorMsg) {
            Swal.fire({
                icon: 'error',
                title: 'Tabel Tidak Ditemukan',
                html: errorMsg + '<br><br><strong>Silakan periksa nama tabel atau buat menu baru.</strong>',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            });
            
            // Hide field configs section
            $('#fieldConfigsSection').hide();
            
            // Show alert
            const $alert = $('#alertTableInfo');
            $alert.removeClass('alert-info alert-warning');
            $alert.addClass('alert-danger');
            $('#alertTableInfoText').html(
                '<i class="fas fa-times-circle mr-2"></i>' +
                '<strong>Error:</strong> ' + errorMsg + 
                ' <strong>Anda tidak dapat melakukan update.</strong>'
            );
            
            // Show alert
            $alert.show();
            
            // Disable submit button
            $('#btnSubmitForm').prop('disabled', true);
            tableValidated = false;
        }

        // Build changes detail HTML (same as create.blade.php)
        function buildChangesDetailHtml(changes) {
            let html = '';
            
            if (changes.added.length > 0) {
                html += `
                    <div class="mb-3">
                        <h6 class="text-success"><i class="fas fa-plus-circle mr-1"></i> Kolom Ditambahkan (${changes.added.length})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr><th>Kolom</th><th>Type</th><th>Nullable</th></tr>
                                </thead>
                                <tbody>
                                    ${changes.added.map(col => `
                                        <tr>
                                            <td><code>${col.column}</code></td>
                                            <td>${col.type}</td>
                                            <td>${col.nullable ? 'YES' : 'NO'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
            
            if (changes.removed.length > 0) {
                html += `
                    <div class="mb-3">
                        <h6 class="text-danger"><i class="fas fa-minus-circle mr-1"></i> Kolom Dihapus (${changes.removed.length})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr><th>Kolom</th><th>Label</th></tr>
                                </thead>
                                <tbody>
                                    ${changes.removed.map(col => `
                                        <tr>
                                            <td><code>${col.column}</code></td>
                                            <td>${col.label}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
            
            if (changes.modified.length > 0) {
                html += `
                    <div class="mb-3">
                        <h6 class="text-warning"><i class="fas fa-edit mr-1"></i> Kolom Dimodifikasi (${changes.modified.length})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr><th>Kolom</th><th>Type Lama</th><th>Type Baru</th></tr>
                                </thead>
                                <tbody>
                                    ${changes.modified.map(col => `
                                        <tr>
                                            <td><code>${col.column}</code></td>
                                            <td>${col.old_type}</td>
                                            <td><strong class="text-warning">${col.new_type}</strong></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
            
            return html;
        }

        // Render field configs table (same as create.blade.php but with existing data)
        function renderFieldConfigs(fields) {
            const tbody = $('#tbodyFieldConfigs');
            tbody.empty();

            fields.forEach((field, index) => {
                const isPk = field.wmfc_is_pk == 1;
                const isAutoIncrement = field.wmfc_is_auto_increment == 1;
                const isFk = field.wmfc_fk_table ? true : false;
                const maxLength = field.wmfc_max_length || null;
                
                // Determine disable states (sama seperti create)
                const isPkAutoInc = isPk && isAutoIncrement; // PK + Auto Increment - DISABLE semua
                
                // Badges
                let badges = '';
                if (isPk) badges += '<span class="badge badge-primary ml-1">PK</span>';
                if (isAutoIncrement) badges += '<span class="badge badge-info ml-1">Auto</span>';
                if (isFk) badges += '<span class="badge badge-success ml-1">FK</span>';

                // Parse JSON criteria & validation
                const criteria = field.wmfc_criteria ? JSON.parse(field.wmfc_criteria) : {};
                const validation = field.wmfc_validation ? JSON.parse(field.wmfc_validation) : {};
                
                // Type options - sama seperti create
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
                    fieldTypeOptions = ['text', 'textarea', 'number', 'date', 'date2', 'dropdown', 'radio'];
                }
                
                fieldTypeOptions.forEach(type => {
                    typeOptionsHtml += `<option value="${type}" ${field.wmfc_field_type === type ? 'selected' : ''}>${typeLabels[type]}</option>`;
                });

                // ✅ FIX: Criteria checkboxes - sesuai create.blade.php
                // Check unique criteria - auto untuk PK (baik auto-inc maupun non auto-inc)
                const isUniqueCriteria = criteria.unique === true || isPk;
                
                // ✅ FIX: Uppercase/Lowercase check dari criteria.case
                const isUppercase = criteria.case === 'uppercase';
                const isLowercase = criteria.case === 'lowercase';
                
                // ✅ FIX: Validation checkboxes
                const isRequired = validation.required === true || isPk;
                const isEmail = validation.email === true;
                const maxValue = validation.max || '';
                const minValue = validation.min || '';

                // FK Display Columns
                const fkDisplayCols = field.wmfc_fk_display_columns ? JSON.parse(field.wmfc_fk_display_columns) : [];
                const fkDisplayText = fkDisplayCols.length > 0 ? fkDisplayCols.join(', ') : '-';
                
                // ✅ FK Config HTML (sama seperti create)
                let fkConfigHtml = '';
                if (isFk && field.wmfc_fk_table) {
                    fkConfigHtml = `
                        <span class="badge badge-success">Ada</span>
                        <button type="button" class="badge badge-info border-0 fk-detail-tooltip" 
                                data-toggle="tooltip" 
                                data-html="true"
                                data-index="${index}"
                                title="<strong>Tabel:</strong> ${field.wmfc_fk_table}<br><strong>Display:</strong> ${fkDisplayText}">
                          Detail
                        </button>
                        <input type="hidden" name="field_configs[${index}][wmfc_fk_table]" value="${field.wmfc_fk_table || ''}">
                        <input type="hidden" name="field_configs[${index}][wmfc_fk_pk_column]" value="${field.wmfc_fk_pk_column || ''}">
                        <input type="hidden" name="field_configs[${index}][wmfc_fk_display_columns]" value='${field.wmfc_fk_display_columns || '[]'}'>
                    `;
                } else {
                    fkConfigHtml = '<span class="badge badge-secondary">Tidak Ada</span>';
                }

                const row = `
                    <tr data-field-index="${index}">
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <strong>${field.wmfc_column_name}</strong>
                            ${badges}
                            <br>
                            <small class="text-muted">${field.wmfc_column_type || ''}</small>
                            <input type="hidden" name="field_configs[${index}][wmfc_column_name]" value="${field.wmfc_column_name}">
                            <input type="hidden" name="field_configs[${index}][wmfc_column_type]" value="${field.wmfc_column_type || ''}">
                            <input type="hidden" name="field_configs[${index}][wmfc_max_length]" value="${maxLength || ''}">
                            <input type="hidden" name="field_configs[${index}][wmfc_is_primary_key]" value="${isPk ? 1 : 0}">
                            <input type="hidden" name="field_configs[${index}][wmfc_is_auto_increment]" value="${isAutoIncrement ? 1 : 0}">
                            <input type="hidden" name="field_configs[${index}][wmfc_fk_table]" value="${field.wmfc_fk_table || ''}">
                            <input type="hidden" name="field_configs[${index}][wmfc_order]" value="${index + 1}">
                            ${field.web_menu_field_config_id ? `<input type="hidden" name="field_configs[${index}][web_menu_field_config_id]" value="${field.web_menu_field_config_id}">` : ''}
                        </td>
                        <td>
                            ${isPkAutoInc ? `
                                <input type="text" class="form-control form-control-sm" 
                                       value="${field.wmfc_field_label || field.wmfc_column_name}" 
                                       disabled>
                                <input type="hidden" name="field_configs[${index}][wmfc_field_label]" value="${field.wmfc_field_label || field.wmfc_column_name}">
                            ` : `
                                <input type="text" class="form-control form-control-sm" 
                                       name="field_configs[${index}][wmfc_field_label]" 
                                       value="${field.wmfc_field_label || field.wmfc_column_name}" 
                                       placeholder="Label field" required>
                            `}
                        </td>
                        <td>
                            ${isPkAutoInc ? `
                                <select class="form-control form-control-sm" disabled>
                                    <option>-</option>
                                </select>
                                <input type="hidden" name="field_configs[${index}][wmfc_field_type]" value="${field.wmfc_field_type}">
                            ` : `
                                <select class="form-control form-control-sm field-type-select" 
                                        name="field_configs[${index}][wmfc_field_type]" 
                                        data-index="${index}" required>
                                    ${typeOptionsHtml}
                                </select>
                            `}
                        </td>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="field_configs[${index}][criteria_unique]" value="0">
                                <input type="checkbox" class="custom-control-input criteria-unique" 
                                       id="unique_${index}" value="1"
                                       name="field_configs[${index}][criteria_unique]"
                                       data-index="${index}"
                                       ${isUniqueCriteria ? 'checked' : ''}
                                       ${isPk || isPkAutoInc ? 'disabled' : ''}>
                                <label class="custom-control-label" for="unique_${index}">Unique</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="field_configs[${index}][criteria_uppercase]" value="0">
                                <input type="checkbox" class="custom-control-input criteria-uppercase" 
                                       id="uppercase_${index}" value="1"
                                       name="field_configs[${index}][criteria_uppercase]"
                                       data-index="${index}"
                                       ${isUppercase ? 'checked' : ''}
                                       ${isPkAutoInc ? 'disabled' : ''}>
                                <label class="custom-control-label" for="uppercase_${index}">Uppercase</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="field_configs[${index}][criteria_lowercase]" value="0">
                                <input type="checkbox" class="custom-control-input criteria-lowercase" 
                                       id="lowercase_${index}" value="1"
                                       name="field_configs[${index}][criteria_lowercase]"
                                       data-index="${index}"
                                       ${isLowercase ? 'checked' : ''}
                                       ${isPkAutoInc ? 'disabled' : ''}>
                                <label class="custom-control-label" for="lowercase_${index}">Lowercase</label>
                            </div>
                        </td>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input validation-required" 
                                       id="required_${index}" value="1"
                                       name="field_configs[${index}][validation_required]"
                                       data-index="${index}"
                                       ${isRequired ? 'checked' : ''}
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
                                       ${isEmail ? 'checked' : ''}
                                       ${isPkAutoInc ? 'disabled' : ''}>
                                <label class="custom-control-label" for="email_${index}">Email</label>
                            </div>
                            <div class="input-group input-group-sm mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Max</span>
                                </div>
                                <input type="number" class="form-control max-length-input" 
                                       name="field_configs[${index}][validation_max]"
                                       value="${maxValue || (maxLength || '')}"
                                       ${maxLength ? `max="${maxLength}" placeholder="${maxLength}"` : 'placeholder="No limit"'}
                                       data-max="${maxLength || ''}"
                                       data-index="${index}"
                                       ${isPkAutoInc ? 'disabled' : ''}>
                            </div>
                            <div class="input-group input-group-sm mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Min</span>
                                </div>
                                <input type="number" class="form-control" 
                                       name="field_configs[${index}][validation_min]" 
                                       value="${minValue}" placeholder="Min" ${isPkAutoInc ? 'disabled' : ''}>
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
                
                tbody.append(row);
            });

            // Re-bind FK columns button (if needed - untuk create saja)
            // bindFkColumnsButton();
            
            // Initialize Bootstrap tooltips for FK detail badges
            $('[data-toggle="tooltip"]').tooltip({html: true});
            
            // Initialize field type validations (disable uppercase/lowercase/email for non-text types)
            initializeFieldTypeValidations();
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
        });

        // Initial check for uppercase/lowercase/email on page load or after render
        function initializeFieldTypeValidations() {
            $('.field-type-select').each(function() {
                const index = $(this).data('index');
                const selectedType = $(this).val();
                const isTextType = ['text', 'textarea'].includes(selectedType);
                
                $(`#uppercase_${index}`).prop('disabled', !isTextType);
                $(`#lowercase_${index}`).prop('disabled', !isTextType);
                $(`#email_${index}`).prop('disabled', !isTextType);
            });
        }

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
            
            const form = $('#formUpdateWebMenuUrl');
            
            $('input[type="checkbox"]').each(function() {
                const $checkbox = $(this);
                const name = $checkbox.attr('name');
                
                if (name && (name.includes('[criteria_') || name.includes('[validation_') || name.includes('[wmfc_is_visible]'))) {
                    if ($checkbox.prop('disabled')) {
                        const value = $checkbox.is(':checked') ? '1' : '0';
                        $('<input>').attr({
                            type: 'hidden',
                            name: name,
                            value: value,
                            class: 'temp-disabled-value'
                        }).insertBefore($checkbox);
                    }
                    else if (!$checkbox.is(':checked')) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: name,
                            value: '0',
                            class: 'temp-unchecked-value'
                        }).insertBefore($checkbox);
                    }
                }
            });
            
            const formData = new FormData(form[0]);
            const button = $(this);
            
            $('.temp-unchecked-value, .temp-disabled-value').remove();
            
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
                    let errorMsg = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorList = '<ul class="text-left mb-0">';
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorList += `<li><strong>${key}:</strong> ${value[0]}</li>`;
                        });
                        errorList += '</ul>';
                        errorMsg = errorList;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal (Error ' + xhr.status + ')',
                        html: errorMsg
                    });
                },
                complete: function() {
                    button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                }
            });
        });
    });
</script>