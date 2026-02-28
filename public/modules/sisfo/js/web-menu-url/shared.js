
/**
 * ================================================================
 * WEB MENU URL - SHARED JAVASCRIPT
 * ================================================================
 * Lokasi: Modules/Sisfo/resources/views/components/web-menu-url/shared.blade.php
 *
 * Shared functions and event handlers untuk:
 * - create.blade.php
 * - update.blade.php
 * ================================================================
 */

// Guard: cegah re-inisialisasi jika script dimuat >1x di halaman yang sama
if (typeof window.WebMenuUrlShared === 'undefined') {

window.WebMenuUrlShared = {
  
  /**
   * Build HTML untuk detail perubahan struktur tabel
   * @param {Object} changes - Object berisi added, modified, removed columns
   * @returns {String} HTML content
   */
  buildChangesDetailHtml: function(changes) {
    let html = '<div style="text-align: left;">';
    
    // Added columns
    if (changes.added && changes.added.length > 0) {
      html += '<h6 class="text-success"><i class="fas fa-plus-circle"></i> Kolom Baru Ditambahkan (' + changes.added.length + '):</h6>';
      html += '<ul>';
      changes.added.forEach(col => {
        html += '<li><strong>' + col.column_name + '</strong> (' + col.column_type + ')</li>';
      });
      html += '</ul><hr>';
    }
    
    // Modified columns
    if (changes.modified && changes.modified.length > 0) {
      html += '<h6 class="text-warning"><i class="fas fa-edit"></i> Kolom yang Berubah (' + changes.modified.length + '):</h6>';
      html += '<ul>';
      changes.modified.forEach(col => {
        html += '<li><strong>' + col.column_name + '</strong> (' + col.old_type + ' → ' + col.new_type + ')</li>';
      });
      html += '</ul><hr>';
    }
    
    // Removed columns
    if (changes.removed && changes.removed.length > 0) {
      html += '<h6 class="text-danger"><i class="fas fa-minus-circle"></i> Kolom yang Dihapus (' + changes.removed.length + '):</h6>';
      html += '<ul>';
      changes.removed.forEach(col => {
        html += '<li><strong>' + col.column_name + '</strong> (' + col.column_type + ')</li>';
      });
      html += '</ul>';
    }
    
    html += '</div>';
    return html;
  },
  
  /**
   * Initialize field type validations
   * Enable/disable uppercase, lowercase, email based on field type
   */
  initializeFieldTypeValidations: function() {
    $('.field-type-select').each(function() {
      const index = $(this).data('index');
      const selectedType = $(this).val();
      const isTextType = ['text', 'textarea'].includes(selectedType);
      
      // Check if PK Auto Increment (permanently disabled)
      const isPkAutoInc = $(`#required_${index}`).prop('disabled') && $(`#validation_unique_${index}`).prop('disabled');
      
      // Only update disable state if NOT PK Auto Increment
      if (!isPkAutoInc) {
        $(`#uppercase_${index}`).prop('disabled', !isTextType);
        $(`#lowercase_${index}`).prop('disabled', !isTextType);
        $(`#email_${index}`).prop('disabled', !isTextType);
        
        if (!isTextType) {
          $(`#uppercase_${index}, #lowercase_${index}, #email_${index}`).prop('checked', false);
        }
      }
    });
  },
  
  /**
   * Clear validation errors dari form
   */
  clearValidationErrors: function() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').html('');
  },
  
  /**
   * Show SweetAlert error
   */
  showError: function(title, text) {
    Swal.fire({
      icon: 'error',
      title: title,
      text: text
    });
  },
  
  /**
   * Show SweetAlert success dengan callback
   */
  showSuccess: function(title, text, callback) {
    Swal.fire({
      icon: 'success',
      title: title,
      text: text,
      timer: 2000
    }).then(callback);
  },
  
  /**
   * Generate auto-text untuk label keterangan (Default mode)
   * Format: [Nama Label] + [Tipe Input] + [Kriteria] + [Validasi]
   * Contoh: "tk kode bisa semua karakter dan bersifat auto huruf besar dengan harus diisi min 4 max 10 huruf dan harus unique"
   * 
   * @param {Object} fieldData - Data field dari row
   * @returns {String} Auto-generated label keterangan text
   */
  generateAutoLabelKeterangan: function(fieldData) {
    let text = `tk ${fieldData.label.toLowerCase()}`;
    
    // [Tipe Input]
    const typeMap = {
      'text': 'bisa semua karakter',
      'textarea': 'bisa semua karakter dengan format panjang',
      'number': 'hanya bisa input angka',
      'date': 'pilih tanggal dari kalender',
      'date2': 'pilih rentang tanggal',
      'dropdown': 'pilih dari daftar pilihan',
      'radio': 'pilih salah satu opsi',
      'search': 'cari dan pilih data',
      'file': 'upload file dokumen',
      'gambar': 'upload file gambar'
    };
    text += ` ${typeMap[fieldData.type] || 'bisa semua karakter'}`;
    
    // [Kriteria]
    if (fieldData.criteria.uppercase) {
      text += ' dan bersifat auto huruf besar';
    } else if (fieldData.criteria.lowercase) {
      text += ' dan bersifat auto huruf kecil';
    }
    
    // [Validasi]
    const validations = [];
    if (fieldData.validation.required) validations.push('harus diisi');
    if (fieldData.validation.email) validations.push('format email');
    if (fieldData.validation.min) validations.push(`min ${fieldData.validation.min}`);
    if (fieldData.validation.max) validations.push(`max ${fieldData.validation.max} huruf`);
    if (fieldData.validation.unique) validations.push('harus unique');
    
    if (validations.length > 0) {
      text += ' dengan ' + validations.join(' ');
    }
    
    return text;
  },

  /**
   * Create field row HTML untuk field configurator table
   * @param {Object} field - Field config object
   * @param {Number} index - Row index
   * @returns {String} HTML string untuk table row
   */
  createFieldRow: function(field, index) {
    const isFk = field.wmfc_is_foreign_key == 1 || field.wmfc_field_type === 'search';
    const isPk = field.wmfc_is_primary_key == 1;
    const isAutoInc = field.wmfc_is_auto_increment == 1;
    const maxLength = field.wmfc_max_length || null;
    const isPkAutoInc = isPk && isAutoInc;
    
    const criteria = field.wmfc_criteria ? (typeof field.wmfc_criteria === 'string' ? JSON.parse(field.wmfc_criteria) : field.wmfc_criteria) : {};
    const validation = field.wmfc_validation ? (typeof field.wmfc_validation === 'string' ? JSON.parse(field.wmfc_validation) : field.wmfc_validation) : {};
    
    let typeOptionsHtml = '';
    const typeLabels = {
      text: 'Text',
      textarea: 'Textarea',
      number: 'Number',
      date: 'Date',
      date2: 'Date Range',
      dropdown: 'Dropdown',
      radio: 'Radio',
      search: 'Search (FK)',
      media: 'Media'
    };
    
    // Tambahkan 'media' untuk VARCHAR (menggantikan 'file' dan 'gambar')
    let fieldTypeOptions = isFk ? ['search'] : (field.wmfc_field_type_options || ['text', 'textarea', 'number', 'date', 'date2', 'dropdown', 'radio']);
    if (field.wmfc_column_type && field.wmfc_column_type.toLowerCase().includes('varchar')) {
      // Migrasi nilai lama 'file'/'gambar' → 'media'
      fieldTypeOptions = fieldTypeOptions.filter(t => t !== 'file' && t !== 'gambar');
      if (!fieldTypeOptions.includes('media')) fieldTypeOptions.push('media');
    }
    
    // Normalisasi wmfc_field_type lama ('file'/'gambar') → 'media' untuk UI
    const normalizedFieldType = (field.wmfc_field_type === 'file' || field.wmfc_field_type === 'gambar')
      ? 'media' : field.wmfc_field_type;
    
    fieldTypeOptions.forEach(type => {
      typeOptionsHtml += `<option value="${type}" ${normalizedFieldType === type ? 'selected' : ''}>${typeLabels[type] || type}</option>`;
    });

    const isUppercase = criteria.case === 'uppercase';
    const isLowercase = criteria.case === 'lowercase';
    
    const isRequired = isPk || validation.required === true || validation.required === 1 || validation.required === "1";
    const isUniqueValidation = isPk || validation.unique === true || validation.unique === 1 || validation.unique === "1";
    const isEmail = validation.email === true || validation.email === 1 || validation.email === "1";
    const maxValue = validation.max || '';
    const minValue = validation.min || '';
    
    const isTextType = ['text', 'textarea'].includes(normalizedFieldType);
    const isMediaType = normalizedFieldType === 'media';
    // isFileOrGambar alias untuk backward-compat dalam block validasi
    const isFileOrGambar = isMediaType;

    const fkDisplayCols = field.wmfc_fk_display_columns 
      ? (Array.isArray(field.wmfc_fk_display_columns) 
          ? field.wmfc_fk_display_columns 
          : JSON.parse(field.wmfc_fk_display_columns)) 
      : [];
    const fkLabelCols = field.wmfc_fk_label_columns
      ? (Array.isArray(field.wmfc_fk_label_columns)
          ? field.wmfc_fk_label_columns
          : JSON.parse(field.wmfc_fk_label_columns))
      : [];
    const fkDisplayText = fkDisplayCols.length > 0 ? fkDisplayCols.join(', ') : '-';

    // Get all displayable columns from FK table (for checkboxes)
    // These come from field.wmfc_fk_available_cols (injected by PHP) or fallback to fkDisplayCols
    const fkAvailableCols = field.wmfc_fk_available_cols
      ? (Array.isArray(field.wmfc_fk_available_cols) ? field.wmfc_fk_available_cols : JSON.parse(field.wmfc_fk_available_cols))
      : fkDisplayCols;
    
    let fkConfigHtml = '';
    if (isFk && field.wmfc_fk_table) {
      // Build per-column checkboxes with alias/default sub-options
      let colCheckboxesHtml = '';
      fkAvailableCols.forEach((col, colIdx) => {
        const isColChecked = fkDisplayCols.includes(col);
        const colLabelRaw = fkLabelCols[fkDisplayCols.indexOf(col)] || 'default';
        const isAliasMode = isColChecked && colLabelRaw !== 'default' && colLabelRaw !== '';
        const aliasValue = isAliasMode ? colLabelRaw : '';
        
        colCheckboxesHtml += `
          <div class="fk-col-row" data-col="${col}" data-index="${index}" data-col-idx="${colIdx}">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input fk-col-check" 
                     id="fk_col_${index}_${colIdx}"
                     data-index="${index}" data-col="${col}" data-col-idx="${colIdx}"
                     ${isColChecked ? 'checked' : ''}>
              <label class="custom-control-label" for="fk_col_${index}_${colIdx}">
                <code style="font-size:0.75rem;">${col}</code>
              </label>
            </div>
            <div class="fk-col-alias-section" id="fk_alias_sec_${index}_${colIdx}" style="display:${isColChecked ? 'block' : 'none'};">
              <div class="d-flex" style="gap:10px;">
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" class="custom-control-input fk-alias-radio" 
                         id="fk_alias_default_${index}_${colIdx}"
                         name="fk_alias_mode_${index}_${colIdx}"
                         value="default" data-index="${index}" data-col-idx="${colIdx}"
                         ${!isAliasMode ? 'checked' : ''}>
                  <label class="custom-control-label" for="fk_alias_default_${index}_${colIdx}">Default</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" class="custom-control-input fk-alias-radio" 
                         id="fk_alias_custom_${index}_${colIdx}"
                         name="fk_alias_mode_${index}_${colIdx}"
                         value="alias" data-index="${index}" data-col-idx="${colIdx}"
                         ${isAliasMode ? 'checked' : ''}>
                  <label class="custom-control-label" for="fk_alias_custom_${index}_${colIdx}">Alias</label>
                </div>
              </div>
              <input type="text" class="form-control form-control-sm fk-alias-input"
                     id="fk_alias_input_${index}_${colIdx}"
                     data-index="${index}" data-col-idx="${colIdx}"
                     placeholder="Nama alias kolom..."
                     value="${aliasValue}"
                     style="display:${isAliasMode ? 'block' : 'none'};"
                     ${!isColChecked ? 'disabled' : ''}>
            </div>
          </div>
        `;
      });

      // Build tooltip info: tabel + PK + kolom yang dipilih
      const tooltipInfo = `Tabel: ${field.wmfc_fk_table} | PK: ${field.wmfc_fk_pk_column}${fkDisplayCols.length ? ' | Kolom: ' + fkDisplayCols.join(', ') : ''}`;

      fkConfigHtml = `
        <div class="d-flex align-items-center mb-1" style="gap:4px;">
          <span class="badge badge-success">Ada</span>
          <span class="badge badge-info fk-detail-badge" 
                style="cursor:pointer;"
                data-toggle="tooltip" 
                data-placement="top" 
                data-html="true"
                title="<small><b>Tabel:</b> ${field.wmfc_fk_table}<br><b>PK:</b> ${field.wmfc_fk_pk_column}</small>">
            Detail
          </span>
        </div>
        <input type="hidden" name="field_configs[${index}][wmfc_fk_table]" value="${field.wmfc_fk_table}">
        <input type="hidden" name="field_configs[${index}][wmfc_fk_pk_column]" value="${field.wmfc_fk_pk_column}">
        <div class="fk-col-container" id="fk_col_container_${index}" 
             data-index="${index}"
             data-fk-table="${field.wmfc_fk_table}"
             data-fk-pk="${field.wmfc_fk_pk_column}">
          ${colCheckboxesHtml}
        </div>
        <div class="fk-hidden-inputs" id="fk_hidden_${index}">
          ${fkDisplayCols.map((col, i) => {
            const lbl = fkLabelCols[i] || 'default';
            return `<input type="hidden" name="field_configs[${index}][fk_display_cols][]" value="${col}">
                    <input type="hidden" name="field_configs[${index}][fk_label_cols][]" value="${lbl}">`;
          }).join('')}
        </div>
      `;
    } else {
      fkConfigHtml = '<span class="badge badge-secondary">Tidak Ada</span>';
    }
    
    // Label Keterangan - logika Butuh/Tidak
    const hasLabelKet = field.wmfc_label_keterangan !== null && field.wmfc_label_keterangan !== undefined && field.wmfc_label_keterangan !== '';
    const isCustomLabelKet = hasLabelKet && field.wmfc_label_keterangan !== 'auto';
    const defaultButuhChecked = hasLabelKet || field.wmfc_label_keterangan === null;
    const defaultModeIsDefault = !isCustomLabelKet;
    
    // Display List - Default: checked (1)
    const isDisplayList = field.wmfc_display_list === 1 || field.wmfc_display_list === '1' || field.wmfc_display_list === true || field.wmfc_display_list === null;
    
    // Ukuran Max (hanya untuk file/gambar)
    const ukuranMax = field.wmfc_ukuran_max || '';

    // Validasi format file dari wmfc_validation
    const validasiGambar = ['png', 'jpg', 'jpeg'];
    const validasiFile = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    const validasiExts = validation.mimes ? validation.mimes.split(',') : [];

    const rowHtml = `
      <tr data-index="${index}" data-field-type="${normalizedFieldType}">
        <td class="text-center">${index + 1}</td>
        <td>
          <strong>${field.wmfc_column_name}</strong>
          ${isPk ? '<span class="badge badge-pk ml-1">PK</span>' : ''}
          ${isFk ? '<span class="badge badge-fk ml-1">FK</span>' : ''}
          <br><small class="text-muted">${field.wmfc_column_type || ''}</small>
          <input type="hidden" name="field_configs[${index}][wmfc_column_name]" value="${field.wmfc_column_name}">
          <input type="hidden" name="field_configs[${index}][wmfc_column_type]" value="${field.wmfc_column_type || ''}">
          <input type="hidden" name="field_configs[${index}][wmfc_max_length]" value="${maxLength || ''}">
          <input type="hidden" name="field_configs[${index}][wmfc_order]" value="${field.wmfc_order}">
          <input type="hidden" name="field_configs[${index}][wmfc_is_primary_key]" value="${field.wmfc_is_primary_key}">
          <input type="hidden" name="field_configs[${index}][wmfc_is_auto_increment]" value="${field.wmfc_is_auto_increment}">
          <input type="hidden" name="field_configs[${index}][web_menu_field_config_id]" value="${field.web_menu_field_config_id || ''}">
        </td>
        <td>
          ${isPkAutoInc ? `
            <input type="hidden" name="field_configs[${index}][wmfc_field_label]" value="${field.wmfc_field_label}">
            <input type="text" class="form-control form-control-sm" value="${field.wmfc_field_label}" disabled>
          ` : `
            <input type="text" class="form-control form-control-sm" 
                   name="field_configs[${index}][wmfc_field_label]" 
                   value="${field.wmfc_field_label}">
          `}
        </td>
        <td>
          ${isPkAutoInc ? `
            <input type="hidden" name="field_configs[${index}][wmfc_field_type]" value="${normalizedFieldType}">
            <select class="form-control form-control-sm" disabled>
              <option selected>${typeLabels[normalizedFieldType] || normalizedFieldType}</option>
            </select>
          ` : `
            <select class="form-control form-control-sm field-type-select" 
                    name="field_configs[${index}][wmfc_field_type]" 
                    data-index="${index}">
              ${typeOptionsHtml}
            </select>
          `}
        </td>
        <td>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" 
                   id="uppercase_${index}" value="1"
                   name="field_configs[${index}][criteria_uppercase]"
                   data-index="${index}"
                   ${isUppercase ? 'checked' : ''}
                   ${isPkAutoInc || !isTextType ? 'disabled' : ''}>
            <label class="custom-control-label" for="uppercase_${index}">Uppercase</label>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" 
                   id="lowercase_${index}" value="1"
                   name="field_configs[${index}][criteria_lowercase]"
                   data-index="${index}"
                   ${isLowercase ? 'checked' : ''}
                   ${isPkAutoInc || !isTextType ? 'disabled' : ''}>
            <label class="custom-control-label" for="lowercase_${index}">Lowercase</label>
          </div>
        </td>
        <td>
          <div class="d-flex flex-wrap" style="gap:2px;">
            <!-- Baris 1: Required, Unique, Email -->
            <div class="custom-control custom-checkbox" style="min-width:90px;">
              <input type="checkbox" class="custom-control-input" 
                     id="required_${index}" value="1"
                     name="field_configs[${index}][validation_required]"
                     ${isRequired ? 'checked' : ''}
                     ${isPk || isPkAutoInc ? 'disabled' : ''}>
              <label class="custom-control-label" for="required_${index}">Required</label>
            </div>
            <div class="custom-control custom-checkbox" style="min-width:80px;">
              <input type="checkbox" class="custom-control-input" 
                     id="validation_unique_${index}" value="1"
                     name="field_configs[${index}][validation_unique]"
                     ${isUniqueValidation ? 'checked' : ''}
                     ${isPk || isPkAutoInc ? 'disabled' : ''}>
              <label class="custom-control-label" for="validation_unique_${index}">Unique</label>
            </div>
            <div class="custom-control custom-checkbox" style="min-width:75px;">
              <input type="checkbox" class="custom-control-input" 
                     id="email_${index}" value="1"
                     name="field_configs[${index}][validation_email]"
                     ${isEmail ? 'checked' : ''}
                     ${isPkAutoInc || !isTextType ? 'disabled' : ''}>
              <label class="custom-control-label" for="email_${index}">Email</label>
            </div>
            <!-- Baris 2: Min, Max -->
            <div class="w-100 d-flex mt-1" style="gap:4px;">
              <div class="input-group input-group-sm flex-fill">
                <div class="input-group-prepend"><span class="input-group-text">Min</span></div>
                <input type="number" class="form-control" 
                       name="field_configs[${index}][validation_min]"
                       value="${minValue}"
                       placeholder="1"
                       ${isPkAutoInc || !['text','textarea','number'].includes(normalizedFieldType) ? 'disabled' : ''}>
              </div>
              <div class="input-group input-group-sm flex-fill">
                <div class="input-group-prepend"><span class="input-group-text">Max</span></div>
                <input type="number" class="form-control max-length-input" 
                       name="field_configs[${index}][validation_max]"
                       value="${maxValue || (maxLength || '')}"
                       ${maxLength ? `max="${maxLength}" data-max="${maxLength}" placeholder="${maxLength}"` : 'placeholder="No limit"'}
                       data-toggle="tooltip"
                       ${isPkAutoInc || !['text','textarea','number'].includes(normalizedFieldType) ? 'disabled' : ''}>
              </div>
            </div>
            <!-- Baris 3: Format Media (muncul saat tipe = media) -->
            <div class="w-100 mt-1 validasi-format-media" id="vfm_${index}" style="display:${isMediaType ? 'block' : 'none'};">
              <small class="text-muted d-block mb-1"><i class="fas fa-image fa-xs"></i> Format Gambar:</small>
              <div class="d-flex flex-wrap mb-1" style="gap:2px;">
                ${validasiGambar.map(ext => `
                <div class="custom-control custom-checkbox" style="min-width:55px;">
                  <input type="checkbox" class="custom-control-input validasi-ext-media" 
                         id="vext_${ext}_${index}" value="${ext}"
                         name="field_configs[${index}][validation_mimes_${ext}]"
                         data-index="${index}"
                         ${validasiExts.includes(ext) ? 'checked' : ''}
                         ${!isMediaType ? 'disabled' : ''}>
                  <label class="custom-control-label" for="vext_${ext}_${index}">${ext}</label>
                </div>`).join('')}
              </div>
              <small class="text-muted d-block mb-1"><i class="fas fa-file fa-xs"></i> Format File:</small>
              <div class="d-flex flex-wrap" style="gap:2px;">
                ${validasiFile.map(ext => `
                <div class="custom-control custom-checkbox" style="min-width:${ext.length > 3 ? '64px' : '55px'};">
                  <input type="checkbox" class="custom-control-input validasi-ext-media" 
                         id="vext_${ext}_${index}" value="${ext}"
                         name="field_configs[${index}][validation_mimes_${ext}]"
                         data-index="${index}"
                         ${validasiExts.includes(ext) ? 'checked' : ''}
                         ${!isMediaType ? 'disabled' : ''}>
                  <label class="custom-control-label" for="vext_${ext}_${index}">${ext}</label>
                </div>`).join('')}
              </div>
            </div>
          </div>
        </td>
        <td class="text-center">${fkConfigHtml}</td>
        <td>
          <!-- Label Keterangan: Butuh / Tidak -->
          <div class="d-flex" style="gap:8px;">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input label-ket-butuh" 
                     id="label_ket_butuh_${index}" 
                     data-index="${index}"
                     ${defaultButuhChecked ? 'checked' : ''}
                     ${isPkAutoInc ? 'disabled' : ''}>
              <label class="custom-control-label" for="label_ket_butuh_${index}">Butuh</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input label-ket-tidak" 
                     id="label_ket_tidak_${index}" 
                     data-index="${index}"
                     ${!defaultButuhChecked ? 'checked' : ''}
                     ${isPkAutoInc ? 'disabled' : ''}>
              <label class="custom-control-label" for="label_ket_tidak_${index}">Tidak</label>
            </div>
          </div>
          <div class="ml-1 mt-1 label-ket-options" id="label_ket_options_${index}" style="display: ${defaultButuhChecked ? 'block' : 'none'};">
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input label-ket-type" 
                     id="label_ket_default_${index}" 
                     name="label_ket_type_${index}" 
                     value="default"
                     data-index="${index}"
                     ${defaultModeIsDefault ? 'checked' : ''}
                     ${isPkAutoInc || !defaultButuhChecked ? 'disabled' : ''}>
              <label class="custom-control-label" for="label_ket_default_${index}">Default</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input label-ket-type" 
                     id="label_ket_custom_${index}" 
                     name="label_ket_type_${index}" 
                     value="custom"
                     data-index="${index}"
                     ${isCustomLabelKet ? 'checked' : ''}
                     ${isPkAutoInc || !defaultButuhChecked ? 'disabled' : ''}>
              <label class="custom-control-label" for="label_ket_custom_${index}">Custom</label>
            </div>
            <textarea class="form-control form-control-sm mt-1 label-ket-custom-input" 
                      id="label_ket_custom_input_${index}"
                      data-index="${index}"
                      rows="2"
                      placeholder="Masukkan keterangan custom..."
                      style="display: ${isCustomLabelKet ? 'block' : 'none'};"
                      ${isPkAutoInc ? 'disabled' : ''}>${isCustomLabelKet && field.wmfc_label_keterangan !== null && field.wmfc_label_keterangan !== 'auto' ? field.wmfc_label_keterangan : ''}</textarea>
          </div>
          <input type="hidden" class="label-ket-final" 
                 name="field_configs[${index}][wmfc_label_keterangan]" 
                 data-index="${index}"
                 value="${!defaultButuhChecked ? '' : (isCustomLabelKet && field.wmfc_label_keterangan !== null && field.wmfc_label_keterangan !== 'auto' ? field.wmfc_label_keterangan : 'auto')}">
        </td>
        <td class="text-center">
          ${isPkAutoInc ? `
            <span class="badge badge-secondary">Hidden</span>
            <input type="hidden" name="field_configs[${index}][wmfc_display_list]" value="0">
          ` : `
            <input type="hidden" name="field_configs[${index}][wmfc_display_list]" value="0">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" 
                     id="display_list_${index}"
                     name="field_configs[${index}][wmfc_display_list]" 
                     value="1" ${isDisplayList ? 'checked' : ''}>
              <label class="custom-control-label" for="display_list_${index}"></label>
            </div>
          `}
        </td>
        <td>
          <div class="input-group input-group-sm">
            <input type="number" class="form-control ukuran-max-input" 
                   id="ukuran_max_${index}"
                   name="field_configs[${index}][wmfc_ukuran_max]"
                   value="${ukuranMax}"
                   min="1"
                   placeholder="MB"
                   data-index="${index}"
                   ${isFileOrGambar ? '' : 'disabled'}
                   ${isPkAutoInc ? 'disabled' : ''}>
            <div class="input-group-append"><span class="input-group-text">MB</span></div>
          </div>
        </td>
        <td class="text-center">
          ${isPkAutoInc ? `
            <span class="badge badge-secondary">Hidden</span>
            <input type="hidden" name="field_configs[${index}][wmfc_is_visible]" value="0">
          ` : `
            <input type="hidden" name="field_configs[${index}][wmfc_is_visible]" value="0">
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
    
    return rowHtml;
  }
};

/**
 * ================================================================
 * DOCUMENT READY - SHARED EVENT HANDLERS
 * ================================================================
 */
$(document).ready(function() {
  
  // Handle field type change - toggle uppercase/lowercase/email based on type
  $(document).on('change', '.field-type-select', function() {
    const index = $(this).data('index');
    const selectedType = $(this).val();
    const row = $(this).closest('tr');
    
    // Update row data attribute
    row.attr('data-field-type', selectedType);
    
    // Enable/disable uppercase, lowercase, email based on type
    const isTextType = ['text', 'textarea'].includes(selectedType);
    const isMedia = selectedType === 'media';
    
    // Check if PK Auto Increment (permanently disabled)
    const isPkAutoInc = $(`#required_${index}`).prop('disabled') && $(`#validation_unique_${index}`).prop('disabled');
    
    // Only update disable state if NOT PK Auto Increment
    if (!isPkAutoInc) {
      $(`#uppercase_${index}`).prop('disabled', !isTextType);
      $(`#lowercase_${index}`).prop('disabled', !isTextType);
      $(`#email_${index}`).prop('disabled', !isTextType);
      
      if (!isTextType) {
        $(`#uppercase_${index}, #lowercase_${index}, #email_${index}`).prop('checked', false);
      }
      
      // Enable/disable min/max hanya untuk text, textarea, number
      const isMinMaxType = ['text', 'textarea', 'number'].includes(selectedType);
      const $minInput = row.find(`input[name="field_configs[${index}][validation_min]"]`);
      const $maxInput = row.find(`input[name="field_configs[${index}][validation_max]"]`);
      $minInput.prop('disabled', !isMinMaxType);
      $maxInput.prop('disabled', !isMinMaxType);
      if (!isMinMaxType) {
        $minInput.val('');
        $maxInput.val('');
      }
      
      // Enable/disable ukuran max hanya untuk tipe media
      const $ukuranMaxInput = $(`#ukuran_max_${index}`);
      if (isMedia) {
        $ukuranMaxInput.prop('disabled', false).prop('required', true);
      } else {
        $ukuranMaxInput.prop('disabled', true).prop('required', false).val('');
      }
      
      // Handle format media checkboxes (satu section gabungan)
      const $vfm = $(`#vfm_${index}`);
      const $mediaChecks = $vfm.find('.validasi-ext-media');
      if (isMedia) {
        $vfm.show();
        $mediaChecks.prop('disabled', false);
      } else {
        $vfm.hide();
        $mediaChecks.prop('disabled', true).prop('checked', false);
      }
    }
  });
  
  // ========================================
  // FK CONFIG - Per-column checkbox handlers
  // ========================================

  // Init tooltip untuk badge Detail FK (delegated, karena row di-render dinamis)
  $(document).on('mouseenter', '.fk-detail-badge', function() {
    if (!$(this).data('bs.tooltip')) {
      $(this).tooltip({ html: true, trigger: 'hover', placement: 'top' });
    }
    $(this).tooltip('show');
  }).on('mouseleave', '.fk-detail-badge', function() {
    $(this).tooltip('hide');
  });

  /**
   * Saat checkbox kolom FK di-toggle:
   * - Tampilkan/sembunyikan alias section
   * - Update hidden inputs fk_display_cols[] dan fk_label_cols[]
   */
  function updateFkHiddenInputs(index) {
    const $container = $(`#fk_col_container_${index}`);
    const $hiddenDiv = $(`#fk_hidden_${index}`);
    
    let displayInputs = '';
    $container.find('.fk-col-check:checked').each(function() {
      const col = $(this).data('col');
      const colIdx = $(this).data('col-idx');
      const aliasMode = $(`input[name="fk_alias_mode_${index}_${colIdx}"]:checked`).val() || 'default';
      const aliasVal = aliasMode === 'alias' ? ($(`#fk_alias_input_${index}_${colIdx}`).val().trim() || 'default') : 'default';
      displayInputs += `<input type="hidden" name="field_configs[${index}][fk_display_cols][]" value="${col}">`;
      displayInputs += `<input type="hidden" name="field_configs[${index}][fk_label_cols][]" value="${aliasVal}">`;
    });
    
    $hiddenDiv.html(displayInputs);
  }

  // Toggle alias section saat checkbox kolom di-check/uncheck
  $(document).on('change', '.fk-col-check', function() {
    const index = $(this).data('index');
    const colIdx = $(this).data('col-idx');
    const isChecked = $(this).prop('checked');
    
    const $aliasSection = $(`#fk_alias_sec_${index}_${colIdx}`);
    const $aliasInput = $(`#fk_alias_input_${index}_${colIdx}`);
    
    if (isChecked) {
      $aliasSection.show();
      $aliasInput.prop('disabled', false);
    } else {
      $aliasSection.hide();
      $aliasInput.prop('disabled', true);
      // Reset ke default saat uncheck
      $(`input[name="fk_alias_mode_${index}_${colIdx}"][value="default"]`).prop('checked', true);
      $aliasInput.hide().val('');
    }
    
    updateFkHiddenInputs(index);
  });

  // Toggle input alias saat radio alias/default berubah
  $(document).on('change', '.fk-alias-radio', function() {
    const index = $(this).data('index');
    const colIdx = $(this).data('col-idx');
    const mode = $(this).val();
    
    const $aliasInput = $(`#fk_alias_input_${index}_${colIdx}`);
    
    if (mode === 'alias') {
      $aliasInput.show().prop('disabled', false).focus();
    } else {
      $aliasInput.hide().prop('disabled', true).val('');
    }
    
    updateFkHiddenInputs(index);
  });

  // Sync alias input ke hidden inputs saat user mengetik
  $(document).on('input', '.fk-alias-input', function() {
    const index = $(this).data('index');
    updateFkHiddenInputs(index);
  });

  // Handle max length validation - auto clamp to column max length
  $(document).on('input', '.max-length-input', function() {
    const input = $(this);
    const maxVal = input.data('max');
    
    if (maxVal && input.val()) {
      const currentVal = parseInt(input.val());
      
      if (currentVal > maxVal) {
        input.val(maxVal);
        
        // Show warning tooltip
        if (!input.attr('data-original-title')) {
          input.attr('data-toggle', 'tooltip');
          input.attr('data-original-title', `Max length tidak boleh melebihi ${maxVal} (sesuai column max)`);
          input.tooltip('show');
          setTimeout(() => input.tooltip('hide'), 2000);
        }
      }
    }
  });
  
  // Hapus error ketika input berubah (hanya dalam form, bukan alert)
  $(document).on('input change', '#formCreate input, #formCreate select, #formCreate textarea, #formUpdate input, #formUpdate select, #formUpdate textarea', function() {
    $(this).removeClass('is-invalid');
    $(this).siblings('.invalid-feedback').html('');
  });
  
  // ========================================
  // LABEL KETERANGAN HANDLERS
  // ========================================
  
  // Toggle label keterangan options (Butuh/Tidak)
  $(document).on('change', '.label-ket-butuh', function() {
    const index = $(this).data('index');
    const isChecked = $(this).prop('checked');
    
    if (isChecked) {
      // Butuh dipilih → uncheck Tidak, enable dan tampilkan sub-opsi
      $(`#label_ket_tidak_${index}`).prop('checked', false);
      $(`#label_ket_options_${index}`).show();
      $(`#label_ket_default_${index}, #label_ket_custom_${index}`).prop('disabled', false);
      
      // Cek mode saat ini — jika custom, pastikan textarea juga enable
      const typeSelected = $(`input[name="label_ket_type_${index}"]:checked`).val() || 'default';
      if (typeSelected === 'default') {
        $(`#label_ket_default_${index}`).prop('checked', true);
        $(`#label_ket_custom_input_${index}`).prop('disabled', false).hide();
        $(`.label-ket-final[data-index="${index}"]`).val('auto');
      } else {
        // Custom mode — enable dan tampilkan textarea
        $(`#label_ket_custom_input_${index}`).prop('disabled', false).show();
        const customVal = $(`#label_ket_custom_input_${index}`).val();
        $(`.label-ket-final[data-index="${index}"]`).val(customVal);
      }
    } else {
      // Butuh di-uncheck (manual) → set Tidak checked, disable sub-opsi
      $(`#label_ket_tidak_${index}`).prop('checked', true);
      $(`#label_ket_options_${index}`).hide();
      $(`#label_ket_default_${index}, #label_ket_custom_${index}`).prop('disabled', true);
      $(`#label_ket_custom_input_${index}`).prop('disabled', true);
      $(`.label-ket-final[data-index="${index}"]`).val('');
    }
  });
  
  // Handle "Tidak" checkbox — kebalikan dari Butuh
  $(document).on('change', '.label-ket-tidak', function() {
    const index = $(this).data('index');
    const isChecked = $(this).prop('checked');
    
    if (isChecked) {
      // Tidak dipilih → uncheck Butuh, sembunyikan dan disable sub-opsi, set pink
      $(`#label_ket_butuh_${index}`).prop('checked', false);
      $(`#label_ket_options_${index}`).hide();
      $(`#label_ket_default_${index}, #label_ket_custom_${index}, #label_ket_custom_input_${index}`)
        .prop('disabled', true)
        .closest('.custom-control, textarea').addClass('disabled-pink');
      $(`.label-ket-final[data-index="${index}"]`).val('');
    } else {
      // Tidak di-uncheck → enable Butuh, reset ke state default
      $(`#label_ket_butuh_${index}`).prop('checked', true);
      $(`#label_ket_options_${index}`).show();
      $(`#label_ket_default_${index}, #label_ket_custom_${index}`)
        .prop('disabled', false)
        .closest('.custom-control').removeClass('disabled-pink');
      $(`#label_ket_custom_input_${index}`).prop('disabled', false).closest('textarea').removeClass('disabled-pink');
      
      const typeSelected = $(`input[name="label_ket_type_${index}"]:checked`).val() || 'default';
      if (typeSelected === 'default') {
        $(`.label-ket-final[data-index="${index}"]`).val('auto');
      } else {
        const customVal = $(`#label_ket_custom_input_${index}`).val();
        $(`.label-ket-final[data-index="${index}"]`).val(customVal);
      }
    }
  });
  
  // Toggle label keterangan type (Default/Custom)
  $(document).on('change', '.label-ket-type', function() {
    const index = $(this).data('index');
    const selectedType = $(this).val();
    
    const $customInput = $(`#label_ket_custom_input_${index}`);
    const $finalInput = $(`.label-ket-final[data-index="${index}"]`);
    
    // Pastikan custom input selalu di-enable dulu sebelum show/hide
    // (bisa ter-disable saat user pernah klik "Tidak" sebelumnya)
    $customInput.prop('disabled', false);
    
    if (selectedType === 'custom') {
      // Custom → show textarea, sync textarea value ke final input
      $customInput.show();
      $finalInput.val($customInput.val());
    } else {
      // Default → hide textarea, set final input to 'auto' for backend processing
      $customInput.hide();
      $finalInput.val('auto');
    }
  });
  
  // Sync custom textarea to final hidden input
  $(document).on('input', '.label-ket-custom-input', function() {
    const index = $(this).data('index');
    const customValue = $(this).val();
    
    // Jika custom dipilih, sync value ke final input
    if ($(`#label_ket_custom_${index}`).prop('checked')) {
      $(`.label-ket-final[data-index="${index}"]`).val(customValue);
    }
  });
  
});

} // end guard: typeof window.WebMenuUrlShared === 'undefined'