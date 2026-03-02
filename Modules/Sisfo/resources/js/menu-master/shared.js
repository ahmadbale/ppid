/**
 * ================================================================
 * MENU MASTER - SHARED JAVASCRIPT
 * ================================================================
 * Shared functions dan event handlers yang digunakan oleh:
 * - Template/Master/create.blade.php
 * - Template/Master/update.blade.php
 *
 * Berisi:
 * - Media Upload Preview Handler (gambar + dokumen)
 * - Drag & Drop visual feedback
 * - Criteria Auto-Transform (uppercase/lowercase)
 * - Input Change Validation Error Remover
 * - FK Search Modal Logic (paginasi, filter, pilih baris)
 * - Form Submit Handler (create & update via AJAX)
 * ================================================================
 */

// Guard: cegah re-inisialisasi jika script dimuat >1x
if (typeof window.MenuMasterShared === 'undefined') {

window.MenuMasterShared = {

    // ==================================================
    // MEDIA UPLOAD — Preview gambar + dokumen
    // ==================================================
    initMediaUpload: function () {
        // Perubahan file input
        $(document).on('change', '.media-upload-input', function () {
            const input = this;
            const column = $(input).data('column');
            const hasImage = $(input).data('has-image') === 1 || $(input).data('has-image') === '1';
            const hasFile  = $(input).data('has-file')  === 1 || $(input).data('has-file')  === '1';
            const maxSizeMB = parseFloat($(input).data('max-size')) || 0;
            const $previewArea  = $(`#preview_${column}`);

            if (!input.files || !input.files[0]) return;

            const file = input.files[0];

            // Validasi ukuran file
            if (maxSizeMB > 0) {
                const fileSizeMB = file.size / (1024 * 1024);
                if (fileSizeMB > maxSizeMB) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: `Ukuran file maksimal ${maxSizeMB} MB. File yang dipilih: ${fileSizeMB.toFixed(2)} MB`,
                    });
                    input.value = '';
                    $previewArea.empty();
                    return;
                }
            }

            // Format ukuran file
            const sizeText = file.size >= 1024 * 1024
                ? (file.size / (1024 * 1024)).toFixed(2) + ' MB'
                : (file.size / 1024).toFixed(1) + ' KB';

            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
            const ext = file.name.split('.').pop().toLowerCase();
            const isImage = imageExts.includes(ext);

            $previewArea.empty();

            if (isImage && hasImage) {
                // Preview gambar
                const reader = new FileReader();
                reader.onload = function (e) {
                    $previewArea.html(`
                        <div class="preview-card preview-card-image">
                            <div class="preview-card-thumb">
                                <img src="${e.target.result}" alt="Preview">
                            </div>
                            <div class="preview-card-info">
                                <div class="preview-card-name" title="${file.name}">${file.name}</div>
                                <div class="preview-card-meta">${sizeText} &bull; .${ext.toUpperCase()} &bull; Gambar baru</div>
                            </div>
                            <div class="preview-card-actions">
                                <button type="button" class="btn-remove-file" data-column="${column}" title="Hapus file">
                                    <i class="fas fa-times" style="font-size:12px;"></i>
                                </button>
                            </div>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            } else {
                // Preview dokumen
                const iconMap = {
                    pdf:  'fa-file-pdf text-danger',
                    doc:  'fa-file-word text-primary',  docx: 'fa-file-word text-primary',
                    xls:  'fa-file-excel text-success', xlsx: 'fa-file-excel text-success',
                    ppt:  'fa-file-powerpoint text-warning', pptx: 'fa-file-powerpoint text-warning',
                    txt:  'fa-file-alt text-secondary', csv: 'fa-file-csv text-success',
                    zip:  'fa-file-archive text-warning', rar: 'fa-file-archive text-warning',
                };
                const iconClass = iconMap[ext] || 'fa-file text-secondary';

                $previewArea.html(`
                    <div class="preview-card preview-card-file">
                        <div class="preview-card-icon">
                            <i class="fas ${iconClass} fa-2x"></i>
                        </div>
                        <div class="preview-card-info">
                            <div class="preview-card-name" title="${file.name}">${file.name}</div>
                            <div class="preview-card-meta">${sizeText} &bull; .${ext.toUpperCase()} &bull; File baru</div>
                        </div>
                        <div class="preview-card-actions">
                            <button type="button" class="btn-remove-file" data-column="${column}" title="Hapus file">
                                <i class="fas fa-times" style="font-size:12px;"></i>
                            </button>
                        </div>
                    </div>
                `);
            }
        });

        // Hapus file yang dipilih
        $(document).on('click', '.btn-remove-file', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const column = $(this).data('column');
            $(`#${column}`).val('');
            $(`#preview_${column}`).empty();
        });

        // Drag & Drop visual feedback
        $(document).on('dragover', '.custom-file-upload-area', function (e) {
            e.preventDefault();
            $(this).addClass('drag-over');
        });
        $(document).on('dragleave drop', '.custom-file-upload-area', function () {
            $(this).removeClass('drag-over');
        });
    },

    // ==================================================
    // CRITERIA AUTO-TRANSFORM (Uppercase/Lowercase)
    // ==================================================
    initCriteriaTransform: function () {
        $(document).on('input', 'input[data-case], textarea[data-case]', function () {
            const caseType   = $(this).data('case');
            const cursorPos  = this.selectionStart;
            const cursorEnd  = this.selectionEnd;

            if (caseType === 'uppercase') {
                $(this).val($(this).val().toUpperCase());
            } else if (caseType === 'lowercase') {
                $(this).val($(this).val().toLowerCase());
            }

            this.setSelectionRange(cursorPos, cursorEnd);
        });
    },

    // ==================================================
    // INPUT CHANGE — Hapus pesan validasi saat input berubah
    // ==================================================
    initValidationErrorRemover: function () {
        $(document).on('input change', 'input, select, textarea', function () {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').html('');
        });
    },

    // ==================================================
    // FK SEARCH — Modal pencarian foreign key
    // Mendukung mode: 'create' (modalFkSearch) dan 'update' (modalFkSearchEdit)
    // ==================================================
    initFkSearch: function (mode, menuUrl) {
        const isCreate = mode === 'create';

        // ID elemen sesuai mode
        const modalId         = isCreate ? '#modalFkSearch'         : '#modalFkSearchEdit';
        const searchInputId   = isCreate ? '#searchFkInput'         : '#searchFkInputEdit';
        const clearBtnClass   = isCreate ? '.btn-clear-search-fk'   : '.btn-clear-search-fk-edit';
        const tableHeadersId  = isCreate ? '#fkTableHeaders'        : '#fkTableHeadersEdit';
        const tableBodyId     = isCreate ? '#fkTableBody'           : '#fkTableBodyEdit';
        const paginationId    = isCreate ? '#fkPagination'          : '#fkPaginationEdit';
        const paginationInfoId= isCreate ? '#fkPaginationInfo'      : '#fkPaginationInfoEdit';
        const dataInfoId      = isCreate ? '#fkDataInfo'            : '#fkDataInfoEdit';
        const rowClass        = isCreate ? 'fk-row-selectable'      : 'fk-row-selectable-edit';
        const selectBtnClass  = isCreate ? 'btn-select-fk'          : 'btn-select-fk-edit';
        const paginationAttr  = isCreate ? 'data-fk-page'           : 'data-fk-page-edit';

        const FK_PER_PAGE = 5;

        let currentFkField   = null;
        let fkSearchData     = [];
        let fkFilteredData   = [];
        let fkCurrentPage    = 1;
        let fkColumns        = [];
        let fkHeaders        = [];
        let fkPkColumn       = '';
        let fkPriorityCol    = '';

        // Buka modal saat klik tombol pencarian FK
        $(document).on('click', '.btn-search-fk', function () {
            const fieldName     = $(this).data('column');
            const fkTable       = $(this).data('fk-table');
            const fkPk          = $(this).data('fk-pk');
            const rawDisplay    = $(this).data('fk-display');
            const rawLabels     = $(this).data('fk-labels');
            const priorityCol   = $(this).data('fk-priority') || '';

            const displayColumns = Array.isArray(rawDisplay) ? rawDisplay
                : (typeof rawDisplay === 'string' ? JSON.parse(rawDisplay) : []);
            const labelColumns   = Array.isArray(rawLabels)  ? rawLabels
                : (rawLabels ? JSON.parse(rawLabels) : []);

            currentFkField = fieldName;
            fkCurrentPage  = 1;
            $(searchInputId).val('');
            $(clearBtnClass).hide();

            $.ajax({
                url: '/' + menuUrl + '/getFkData',
                data: {
                    table:   fkTable,
                    columns: displayColumns,
                    labels:  labelColumns,
                },
                beforeSend: function () {
                    $(tableBodyId).html('<tr><td colspan="20"><div class="fk-empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div></td></tr>');
                    $(paginationId).empty();
                    $(paginationInfoId).text('');
                    $(dataInfoId).html('<i class="fas fa-spinner fa-spin mr-1"></i>Memuat...');
                    $(modalId).modal('show');
                },
                success: function (response) {
                    fkSearchData   = response.data;
                    fkFilteredData = [...fkSearchData];
                    fkColumns      = displayColumns;
                    fkHeaders      = response.headers || [];
                    fkPkColumn     = response.pkColumn || fkPk;
                    fkPriorityCol  = priorityCol;
                    fkCurrentPage  = 1;
                    renderTable();
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Gagal mengambil data',
                    });
                },
            });
        });

        // Render tabel FK dengan paginasi
        function renderTable() {
            // Header
            let headerHtml = '<th>No</th>';
            fkColumns.forEach((col, i) => {
                const label = (fkHeaders[i] && fkHeaders[i] !== 'default')
                    ? fkHeaders[i]
                    : col.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                headerHtml += '<th>' + label + '</th>';
            });
            headerHtml += '<th>Aksi</th>';
            $(tableHeadersId).html(headerHtml);

            // Hitung paginasi
            const totalItems = fkFilteredData.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / FK_PER_PAGE));
            if (fkCurrentPage > totalPages) fkCurrentPage = totalPages;

            const startIdx = (fkCurrentPage - 1) * FK_PER_PAGE;
            const endIdx   = Math.min(startIdx + FK_PER_PAGE, totalItems);
            const pageData = fkFilteredData.slice(startIdx, endIdx);

            // Build rows
            let rows = '';
            if (pageData.length === 0) {
                rows = '<tr><td colspan="' + (fkColumns.length + 2) + '">'
                     + '<div class="fk-empty-state"><i class="fas fa-inbox"></i><p>Tidak ada data ditemukan</p></div>'
                     + '</td></tr>';
            } else {
                pageData.forEach((row, index) => {
                    rows += '<tr class="' + rowClass + '">';
                    rows += '<td>' + (startIdx + index + 1) + '</td>';
                    fkColumns.forEach(col => {
                        rows += '<td>' + (row[col] !== null && row[col] !== undefined
                            ? row[col]
                            : '<span style="color:#cbd5e1;">—</span>') + '</td>';
                    });
                    const displayVal = (fkPriorityCol && row[fkPriorityCol] !== undefined)
                        ? row[fkPriorityCol]
                        : (row[fkColumns[0]] || '');
                    rows += '<td>'
                         + '<button type="button" class="fk-btn-select ' + selectBtnClass + '"'
                         + ' data-id="' + row[fkPkColumn] + '"'
                         + ' data-display="' + $('<div>').text(String(displayVal)).html() + '"'
                         + ' title="Pilih data ini">'
                         + '<i class="fas fa-check"></i> Pilih'
                         + '</button>'
                         + '</td></tr>';
                });
            }
            $(tableBodyId).html(rows);

            // Info teks
            if (totalItems > 0) {
                $(dataInfoId).html('<i class="fas fa-database mr-1"></i>Menampilkan ' + (startIdx + 1) + '–' + endIdx + ' dari ' + totalItems + ' data');
                $(paginationInfoId).text('Halaman ' + fkCurrentPage + ' dari ' + totalPages);
            } else {
                $(dataInfoId).html('<i class="fas fa-database mr-1"></i>0 data ditemukan');
                $(paginationInfoId).text('');
            }

            // Build pagination
            let pagHtml = '';
            if (totalPages > 1) {
                const prevDisabled = fkCurrentPage === 1         ? 'disabled' : '';
                const nextDisabled = fkCurrentPage === totalPages ? 'disabled' : '';

                pagHtml += '<li class="fk-pg-arrow ' + prevDisabled + '">'
                         + '<a href="#" ' + paginationAttr + '="' + (fkCurrentPage - 1) + '"><i class="fas fa-chevron-left"></i></a></li>';

                let startPage = Math.max(1, fkCurrentPage - 2);
                let endPage   = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

                for (let p = startPage; p <= endPage; p++) {
                    pagHtml += '<li class="' + (p === fkCurrentPage ? 'active' : '') + '">'
                             + '<a href="#" ' + paginationAttr + '="' + p + '">' + p + '</a></li>';
                }

                pagHtml += '<li class="fk-pg-arrow ' + nextDisabled + '">'
                         + '<a href="#" ' + paginationAttr + '="' + (fkCurrentPage + 1) + '"><i class="fas fa-chevron-right"></i></a></li>';
            }
            $(paginationId).html(pagHtml);
        }

        // Klik tombol paginasi
        $(document).on('click', paginationId + ' a', function (e) {
            e.preventDefault();
            const page = parseInt($(this).attr(paginationAttr));
            if (page && page !== fkCurrentPage) {
                fkCurrentPage = page;
                renderTable();
            }
        });

        // Pencarian dengan debounce
        let searchTimeout = null;
        $(document).on('keyup', searchInputId, function () {
            const searchTerm = $(this).val().toLowerCase().trim();
            $(clearBtnClass).toggle(searchTerm.length > 0);

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                if (searchTerm === '') {
                    fkFilteredData = [...fkSearchData];
                } else {
                    fkFilteredData = fkSearchData.filter(row =>
                        fkColumns.some(col => {
                            const val = row[col];
                            return val !== null && val !== undefined
                                && String(val).toLowerCase().includes(searchTerm);
                        })
                    );
                }
                fkCurrentPage = 1;
                renderTable();
            }, 200);
        });

        // Tombol hapus pencarian
        $(document).on('click', clearBtnClass, function () {
            $(searchInputId).val('').trigger('keyup').focus();
        });

        // Pilih baris (tombol Pilih)
        $(document).on('click', '.' + selectBtnClass, function (e) {
            e.stopPropagation();
            const selectedId = $(this).data('id');
            const displayVal = $(this).data('display')
                || $(this).closest('tr').find('td:eq(1)').text();
            $('#' + currentFkField).val(selectedId);
            $('#' + currentFkField + '_display').val(displayVal);
            $(modalId).modal('hide');
        });

        // Klik baris untuk pilih
        $(document).on('click', '.' + rowClass, function (e) {
            if (!$(e.target).is('.fk-btn-select') && !$(e.target).closest('.fk-btn-select').length) {
                $(this).find('.' + selectBtnClass).click();
            }
        });
    },

    // ==================================================
    // FORM SUBMIT — Submit form via AJAX (create/update)
    // ==================================================
    initFormSubmit: function (mode) {
        const isCreate    = mode === 'create';
        const btnSelector = isCreate ? '#btnSubmit'  : '#btnUpdate';
        const formId      = isCreate ? '#formCreate' : '#formUpdate';
        const loadingText = isCreate ? '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...'
                                     : '<i class="fas fa-spinner fa-spin mr-1"></i>Mengupdate...';
        const successMsg  = isCreate ? 'Data berhasil ditambahkan' : 'Data berhasil diupdate';

        $(document).off('click', btnSelector).on('click', btnSelector, function (e) {
            e.preventDefault();

            const form      = $(formId);
            const submitBtn = $(this);
            const origText  = submitBtn.html();

            if (submitBtn.prop('disabled')) return false;

            // Bersihkan error sebelumnya
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Nonaktifkan tombol
            submitBtn.prop('disabled', true).html(loadingText);

            // Gunakan FormData agar mendukung file upload
            const formData = new FormData(form[0]);

            $.ajax({
                url:         form.attr('action'),
                method:      'POST',
                data:        formData,
                processData: false,
                contentType: false,
                dataType:    'json',
                headers: {
                    'Accept':           'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                success: function (response) {
                    if (response.success) {
                        $('#myModal').modal('hide');

                        // Pastikan backdrop hilang
                        setTimeout(function () {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                        }, 300);

                        // Tampilkan alert sukses
                        setTimeout(function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || successMsg,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745',
                            }).then(() => {
                                if (typeof window.reloadTable === 'function') {
                                    window.reloadTable();
                                } else {
                                    location.reload();
                                }
                            });
                        }, 400);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Terjadi kesalahan',
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        $.each(errors, function (field, messages) {
                            const $input = $('[name="' + field + '"]');
                            $input.addClass('is-invalid');
                            $('#' + field + '_display').addClass('is-invalid');
                            $('#error-' + field).html(messages[0]).css('display', 'block');
                        });

                        let errorMsg = '<ul class="text-left mb-0">';
                        $.each(errors, function (key, value) {
                            errorMsg += '<li>' + value[0] + '</li>';
                        });
                        errorMsg += '</ul>';

                        Swal.fire({
                            icon: 'warning',
                            title: 'Validasi Gagal',
                            html: errorMsg,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server',
                        });
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(origText);
                },
            });
        });

        // Reset tombol saat modal dibuka
        $(document).off('shown.bs.modal', '#myModal').on('shown.bs.modal', '#myModal', function () {
            $(btnSelector).prop('disabled', false);
        });
    },

    // ==================================================
    // INISIALISASI SEMUA FITUR SEKALIGUS
    // mode: 'create' atau 'update'
    // menuUrl: slug URL menu (contoh: 'm-testing')
    // ==================================================
    init: function (mode, menuUrl) {
        this.initMediaUpload();
        this.initCriteriaTransform();
        this.initValidationErrorRemover();
        this.initFkSearch(mode, menuUrl);
        this.initFormSubmit(mode);
    },
};

} // end guard
