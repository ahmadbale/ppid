/**
 * ================================================================
 * WEB MENU GLOBAL - SHARED JAVASCRIPT
 * ================================================================
 * Shared functions dan event handlers yang digunakan oleh:
 * - AdminWeb/WebMenuGlobal/create.blade.php
 * - AdminWeb/WebMenuGlobal/update.blade.php
 *
 * Berisi:
 * - Searchable Select Component (custom dropdown dengan pencarian)
 * - Field Toggle berdasarkan kategori menu
 * - Icon Preview handler
 * - Badge/Notification Indicator toggle
 * - Validasi dan Error Handling
 * - Form Submit via AJAX (create & update)
 * ================================================================
 */

// Guard: cegah re-inisialisasi jika script dimuat >1x
if (typeof window.WebMenuGlobalShared === 'undefined') {

window.WebMenuGlobalShared = {

    /**
     * Inisialisasi Searchable Select Component
     * Factory function yang dapat dipakai ulang untuk create dan update
     *
     * @param {Object} cfg - Konfigurasi:
     *   - data:          Array of {value, app, name, desc, isNew}
     *   - hiddenInput:   jQuery selector untuk hidden input value
     *   - trigger:       jQuery selector untuk trigger/display element
     *   - display:       jQuery selector untuk display area
     *   - dropdown:      jQuery selector untuk dropdown panel
     *   - searchInput:   jQuery selector untuk search input
     *   - stats:         jQuery selector untuk stats counter
     *   - optionList:    jQuery selector untuk options list
     *   - errorDiv:      jQuery selector untuk error message div
     *   - selectedValue: String value yang sudah dipilih (opsional)
     */
    initSearchableSelect: function (cfg) {
        const $hidden     = $(cfg.hiddenInput);
        const $trigger    = $(cfg.trigger);
        const $display    = $(cfg.display);
        const $dropdown   = $(cfg.dropdown);
        const $search     = $(cfg.searchInput);
        const $stats      = $(cfg.stats);
        const $optList    = $(cfg.optionList);
        const $errorDiv   = $(cfg.errorDiv);

        let focusedIndex  = -1;
        let currentValue  = String(cfg.selectedValue || '');
        const totalCount  = cfg.data.length;

        /* ── Utility: Escape HTML ─────────── */
        function esc(str) {
            return String(str).replace(/[&<>"']/g, m =>
                ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
        }

        /* ── Utility: Highlight query dalam text ─ */
        function highlight(text, query) {
            if (!query) return esc(text);
            const escaped = esc(text);
            const escapedQ = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return escaped.replace(new RegExp(`(${escapedQ})`, 'gi'), '<mark>$1</mark>');
        }

        /* ── Render option list dengan filter ─ */
        function renderOptions(query) {
            const q = (query || '').toLowerCase().trim();
            let html = '';
            let shownCount = 0;
            let currentGroup = '';

            cfg.data.forEach(function(item, idx) {
                const matchApp  = item.app.toLowerCase().includes(q);
                const matchName = item.name.toLowerCase().includes(q);
                const matchDesc = item.desc.toLowerCase().includes(q);
                if (q && !matchApp && !matchName && !matchDesc) return;

                shownCount++;

                // Grup header ketika app berubah
                if (item.app !== currentGroup) {
                    currentGroup = item.app;
                    html += `<div class="ss-group-header">${esc(item.app)}</div>`;
                }

                // Gunakan String() untuk perbandingan yang aman
                const isSelected = String(item.value) === currentValue;
                const badgeHtml  = item.isNew ? '<span class="ss-badge-new">BARU</span>' : '';

                html += `
                <div class="ss-option-item${isSelected ? ' selected' : ''}"
                     data-value="${esc(item.value)}"
                     data-index="${idx}">
                    <div class="ss-opt-name">
                        ${highlight(item.name, q)}${badgeHtml}
                    </div>
                    ${item.desc
                        ? `<div class="ss-opt-desc">${highlight(item.desc, q)}</div>`
                        : ''}
                </div>`;
            });

            // Empty state
            if (shownCount === 0) {
                html = `<div class="ss-empty">
                    <i class="fas fa-search"></i>
                    Tidak ada URL yang cocok dengan "<strong>${esc(q)}</strong>"
                </div>`;
                $stats.text(`Tidak ditemukan dari ${totalCount} URL`);
            } else {
                const statsText = q
                    ? `Menampilkan ${shownCount} dari ${totalCount} URL`
                    : `Menampilkan semua ${totalCount} URL — terbaru di atas`;
                $stats.text(statsText);
            }

            $optList.html(html);
            focusedIndex = -1;

            // Scroll ke item yang selected
            const $sel = $optList.find('.ss-option-item.selected');
            if ($sel.length) {
                $optList.scrollTop($sel.position().top - 60);
            }
        }

        /* ── Update display label ─────────── */
        function updateDisplay(value) {
            if (!value) {
                $display.html('Pilih Menu URL').addClass('placeholder').removeClass('ss-display-selected');
                $trigger.removeClass('has-value');
                return;
            }
            const item = cfg.data.find(d => String(d.value) === String(value));
            if (!item) return;
            $display.html(`
                <div class="ss-display-selected">
                    <div class="ss-sel-app">${esc(item.app)}</div>
                    <div class="ss-sel-name">${esc(item.name)}</div>
                    ${item.desc ? `<div class="ss-sel-desc">${esc(item.desc)}</div>` : ''}
                </div>
            `).removeClass('placeholder').addClass('ss-display-selected');
            $trigger.addClass('has-value');
        }

        /* ── Keyboard navigation helpers ─── */
        function getVisibleItems() {
            return $optList.find('.ss-option-item');
        }

        function moveFocus(dir) {
            const $items = getVisibleItems();
            if (!$items.length) return;
            $items.removeClass('focused');
            focusedIndex = Math.max(0, Math.min($items.length - 1, focusedIndex + dir));
            const $focused = $items.eq(focusedIndex).addClass('focused');
            // Scroll into view
            const listTop  = $optList.scrollTop();
            const listH    = $optList.outerHeight();
            const itemTop  = $focused.position().top + listTop;
            const itemH    = $focused.outerHeight();
            if (itemTop < listTop) $optList.scrollTop(itemTop - 4);
            else if (itemTop + itemH > listTop + listH) $optList.scrollTop(itemTop + itemH - listH + 4);
        }

        /* ── Open / Close dropdown ───────── */
        function openDropdown() {
            $dropdown.addClass('open');
            $trigger.addClass('open').attr('aria-expanded', 'true');
            renderOptions('');
            $search.val('').focus();
        }

        function closeDropdown() {
            $dropdown.removeClass('open');
            $trigger.removeClass('open').attr('aria-expanded', 'false');
        }

        /* ── Select an item ─────────────── */
        function selectItem(value) {
            currentValue = String(value);
            $hidden.val(value);
            updateDisplay(value);
            // Clear error state
            $trigger.removeClass('is-invalid');
            $errorDiv.html('');
            closeDropdown();
        }

        /* ── Event Handlers ───────────────── */

        // Toggle dropdown on trigger click
        $trigger.on('click', function(e) {
            $dropdown.hasClass('open') ? closeDropdown() : openDropdown();
        });

        // Keyboard on trigger
        $trigger.on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                e.preventDefault();
                openDropdown();
            }
        });

        // Search input
        $search.on('input', function() {
            renderOptions($(this).val());
        });

        // Keyboard in search
        $search.on('keydown', function(e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); moveFocus(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); moveFocus(-1); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                const $focused = getVisibleItems().filter('.focused');
                // Gunakan attr() bukan data() — jQuery auto-cast int
                if ($focused.length) selectItem($focused.attr('data-value'));
            }
            else if (e.key === 'Escape') closeDropdown();
        });

        // Click option item
        $optList.on('click', '.ss-option-item', function() {
            selectItem($(this).attr('data-value'));
        });

        // Click outside (close dropdown)
        const uniqClass = 'ss_' + cfg.trigger.replace(/[^a-z0-9]/gi,'');
        $(document).on('click.' + uniqClass, function(e) {
            if (!$(e.target).closest(cfg.trigger).length &&
                !$(e.target).closest(cfg.dropdown).length) {
                closeDropdown();
            }
        });

        // Init: render initial state
        if (currentValue) updateDisplay(currentValue);
        renderOptions('');
    },

    /**
     * Toggle form fields berdasarkan kategori menu
     * @param {string} kategori - Nilai dari #wmg_kategori_menu
     */
    toggleFieldsByKategori: function (kategori) {
        if (kategori === 'Sub Menu') {
            $('#parent_menu_group').show();
            $('#menu_url_group').show();
            $('#icon_group').show();
            $('#wmg_parent_id').attr('required', true);
            $('#wmg_icon').attr('required', false);
            $('#icon_required').hide();
        } else if (kategori === 'Group Menu') {
            $('#parent_menu_group').hide();
            $('#menu_url_group').hide();
            $('#icon_group').show();
            $('#wmg_parent_id').attr('required', false).val('');
            $('#wmg_icon').attr('required', true);
            $('#icon_required').show();
        } else if (kategori === 'Menu Biasa') {
            $('#parent_menu_group').hide();
            $('#menu_url_group').show();
            $('#icon_group').show();
            $('#wmg_parent_id').attr('required', false).val('');
            $('#wmg_icon').attr('required', true);
            $('#icon_required').show();
        } else {
            $('#parent_menu_group').hide();
            $('#menu_url_group').hide();
            $('#icon_group').hide();
            $('#wmg_parent_id').attr('required', false);
            $('#wmg_icon').attr('required', false);
        }
    },

    /**
     * Inisialisasi event listener untuk form
     */
    initFormEvents: function () {
        // Kategori menu change
        $(document).on('change', '#wmg_kategori_menu', function() {
            WebMenuGlobalShared.toggleFieldsByKategori($(this).val());
        });

        // Icon preview live update
        $(document).on('input', '#wmg_icon', function() {
            const iconValue = $(this).val().trim();
            const iconClass = iconValue
                ? (iconValue.startsWith('fa-') ? iconValue : 'fa-' + iconValue)
                : 'fa-cog';
            $('#icon_preview').attr('class', 'fas ' + iconClass);
        });

        // Badge indicator change
        $(document).on('change', '#wmg_badge_indicator', function() {
            $('#wmg_badge_method').val($(this).val() === 'ya' ? 'getBadgeCount' : '');
        });

        // Clear error on input change
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            $(`#${$(this).attr('id')}_error`).html('');
        });
    },

    /**
     * Inisialisasi event listener untuk submit form
     * @param {string} mode - 'create' atau 'update'
     */
    initFormSubmit: function (mode) {
        const isCreate = mode === 'create';
        const btnId    = '#btnSubmitForm';
        const formId   = isCreate ? '#formCreateWebMenuGlobal' : '#formUpdateWebMenuGlobal';
        const triggerId = isCreate ? '#ssTriggerCreate' : '#ssTriggerUpdate';
        const loadingText = isCreate
            ? '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...'
            : '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';
        const successMsg = isCreate
            ? 'Data berhasil ditambahkan'
            : 'Data berhasil diupdate';
        const btnText = isCreate
            ? '<i class="fas fa-save mr-1"></i> Simpan'
            : '<i class="fas fa-save mr-1"></i> Simpan Perubahan';

        $(document).off('click', btnId).on('click', btnId, function(e) {
            e.preventDefault();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Validasi custom select
            const urlVal  = $('#fk_web_menu_url').val();
            const kategori = $('#wmg_kategori_menu').val();
            if ((kategori === 'Sub Menu' || kategori === 'Menu Biasa') && !urlVal) {
                $(triggerId).addClass('is-invalid');
                $('#fk_web_menu_url_error').html('Menu URL wajib dipilih.');
                return;
            }

            const form = $(formId);
            const formData = new FormData(form[0]);
            const button = $(this);
            const originalText = button.html();

            button.html(loadingText).attr('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#myModal').modal('hide');
                        if (typeof reloadTable === 'function') {
                            reloadTable();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || successMsg
                        });
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                if (key === 'web_menu_global.fk_web_menu_url') {
                                    $(triggerId).addClass('is-invalid');
                                    $('#fk_web_menu_url_error').html(value[0]);
                                } else if (key.startsWith('web_menu_global.')) {
                                    const fieldName = key.replace('web_menu_global.', '');
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
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                    });
                },
                complete: function() {
                    button.html(btnText).attr('disabled', false);
                }
            });
        });
    },

    /**
     * Inisialisasi keseluruhan untuk create mode
     * @param {Array} menuUrlData - Data menu URL dari Blade
     */
    initCreate: function (menuUrlData) {
        // Inisialisasi searchable select untuk create
        this.initSearchableSelect({
            data:         menuUrlData,
            hiddenInput:  '#fk_web_menu_url',
            trigger:      '#ssTriggerCreate',
            display:      '#ssDisplayCreate',
            dropdown:     '#ssDropdownCreate',
            searchInput:  '#ssSearchCreate',
            stats:        '#ssStatsCreate',
            optionList:   '#ssOptionListCreate',
            errorDiv:     '#fk_web_menu_url_error',
            selectedValue: ''
        });

        // Inisialisasi form events
        this.initFormEvents();

        // Inisialisasi form submit untuk create
        this.initFormSubmit('create');
    },

    /**
     * Inisialisasi keseluruhan untuk update mode
     * @param {Array} menuUrlData - Data menu URL dari Blade
     * @param {string} preSelectedValue - Nilai yang sudah dipilih
     */
    initUpdate: function (menuUrlData, preSelectedValue) {
        // Inisialisasi searchable select untuk update
        this.initSearchableSelect({
            data:          menuUrlData,
            hiddenInput:   '#fk_web_menu_url',
            trigger:       '#ssTriggerUpdate',
            display:       '#ssDisplayUpdate',
            dropdown:      '#ssDropdownUpdate',
            searchInput:   '#ssSearchUpdate',
            stats:         '#ssStatsUpdate',
            optionList:    '#ssOptionListUpdate',
            errorDiv:      '#fk_web_menu_url_error',
            selectedValue: preSelectedValue
        });

        // Inisialisasi kategori awal
        const initialKategori = $('#wmg_kategori_menu').val();
        if (initialKategori) {
            this.toggleFieldsByKategori(initialKategori);
        }

        // Inisialisasi form events
        this.initFormEvents();

        // Inisialisasi form submit untuk update
        this.initFormSubmit('update');
    }
};

} // end guard
