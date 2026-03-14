{{-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\update.blade.php --}}
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp

{{-- CSS sama persis dengan create.blade.php — pertimbangkan pindah ke layout/asset jika sudah stabil --}}
<style>
/* ── Searchable Select Component ─────────────────────────── */
.ss-wrapper {
    position: relative;
    font-family: inherit;
}
.ss-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 12px;
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    min-height: 38px;
    transition: border-color .15s, box-shadow .15s;
    user-select: none;
}
.ss-trigger:hover  { border-color: #adb5bd; }
.ss-trigger.open   { border-color: #80bdff; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25); }
.ss-trigger.is-invalid { border-color: #dc3545; }
.ss-trigger.is-invalid.open { box-shadow: 0 0 0 .2rem rgba(220,53,69,.25); }

.ss-display {
    flex: 1;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: .875rem;
}
.ss-display.placeholder { color: #6c757d; }

.ss-display-selected .ss-sel-app  { font-size: .7rem; color: #6c757d; line-height: 1.2; }
.ss-display-selected .ss-sel-name { font-size: .85rem; font-weight: 600; color: #343a40; line-height: 1.3; }
.ss-display-selected .ss-sel-desc { font-size: .72rem; color: #868e96; line-height: 1.2; }

.ss-actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
.ss-clear-btn {
    display: none;
    background: none; border: none; padding: 0 2px;
    color: #adb5bd; cursor: pointer; line-height: 1;
    font-size: .8rem; transition: color .15s;
}
.ss-clear-btn:hover { color: #dc3545; }
.ss-arrow { color: #6c757d; font-size: .75rem; transition: transform .2s; }
.ss-trigger.open .ss-arrow { transform: rotate(180deg); }

.ss-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 3px);
    left: 0; right: 0;
    z-index: 1055;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.08);
    overflow: hidden;
}
.ss-dropdown.open { display: block; }

.ss-search-wrap {
    padding: 8px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    position: sticky; top: 0; z-index: 1;
}
.ss-search-input {
    width: 100%;
    padding: 6px 10px 6px 30px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: .85rem;
    outline: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 8px center;
}
.ss-search-input:focus { border-color: #80bdff; box-shadow: 0 0 0 2px rgba(0,123,255,.15); }
.ss-search-input::placeholder { color: #adb5bd; }

.ss-stats {
    padding: 4px 10px;
    font-size: .72rem;
    color: #868e96;
    background: #fafafa;
    border-bottom: 1px solid #f5f5f5;
}

.ss-options-list {
    max-height: 280px;
    overflow-y: auto;
    overscroll-behavior: contain;
}
.ss-options-list::-webkit-scrollbar { width: 5px; }
.ss-options-list::-webkit-scrollbar-track { background: #f8f9fa; }
.ss-options-list::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 3px; }

.ss-group-header {
    padding: 6px 12px 2px;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #868e96;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    position: sticky; top: 0;
}
.ss-group-header:first-child { border-top: none; }

.ss-option-item {
    padding: 8px 12px;
    cursor: pointer;
    transition: background .1s;
    border-bottom: 1px solid #f8f9fa;
}
.ss-option-item:hover,
.ss-option-item.focused  { background: #e8f4fd; }
.ss-option-item.selected { background: #d4edff; }

.ss-opt-name { font-size: .83rem; font-weight: 600; color: #212529; line-height: 1.3; }
.ss-opt-desc { font-size: .73rem; color: #6c757d; line-height: 1.3; margin-top: 1px; }
.ss-opt-name mark, .ss-opt-desc mark {
    background: #fff3cd; padding: 0 1px; border-radius: 2px; color: inherit;
}
.ss-badge-new {
    display: inline-block;
    font-size: .62rem; font-weight: 700;
    padding: 1px 5px;
    background: #28a745; color: #fff;
    border-radius: 10px;
    vertical-align: middle;
    margin-left: 4px;
    letter-spacing: .03em;
}
.ss-empty {
    padding: 24px 16px;
    text-align: center;
    color: #adb5bd;
    font-size: .83rem;
}
.ss-empty i { display: block; font-size: 1.5rem; margin-bottom: 8px; opacity: .5; }
</style>

<div class="modal-header">
    <h5 class="modal-title">Ubah Menu Global</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateWebMenuGlobal" action="{{ url($webMenuGlobalPath . '/updateData/' . $webMenuGlobal->web_menu_global_id) }}"
        method="POST">
        @csrf

        <div class="form-group">
            <label for="wmg_nama_default">Nama Default Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmg_nama_default" name="web_menu_global[wmg_nama_default]" 
                   value="{{ $webMenuGlobal->wmg_nama_default }}" maxlength="255">
            <div class="invalid-feedback" id="wmg_nama_default_error"></div>
        </div>

        <div class="form-group">
            <label for="wmg_kategori_menu">Kategori Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_kategori_menu" name="web_menu_global[wmg_kategori_menu]">
                <option value="">Pilih Kategori Menu</option>
                <option value="Menu Biasa" {{ $webMenuGlobal->wmg_kategori_menu === 'Menu Biasa' ? 'selected' : '' }}>Menu Biasa</option>
                <option value="Group Menu" {{ $webMenuGlobal->wmg_kategori_menu === 'Group Menu' ? 'selected' : '' }}>Group Menu</option>
                <option value="Sub Menu"   {{ $webMenuGlobal->wmg_kategori_menu === 'Sub Menu'   ? 'selected' : '' }}>Sub Menu</option>
            </select>
            <div class="invalid-feedback" id="wmg_kategori_menu_error"></div>
            <small class="form-text text-muted">
                <strong>Menu Biasa:</strong> Menu standalone tanpa submenu<br>
                <strong>Group Menu:</strong> Menu kelompok yang memiliki submenu<br>
                <strong>Sub Menu:</strong> Menu anak dari Group Menu
            </small>
        </div>

        <div class="form-group" id="parent_menu_group" style="display: none;">
            <label for="wmg_parent_id">Menu Induk <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_parent_id" name="web_menu_global[wmg_parent_id]">
                <option value="">Pilih Menu Induk</option>
                @foreach($parentMenus as $parent)
                    <option value="{{ $parent->web_menu_global_id }}" {{ $webMenuGlobal->wmg_parent_id == $parent->web_menu_global_id ? 'selected' : '' }}>
                        {{ $parent->wmg_nama_default }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="wmg_parent_id_error"></div>
        </div>

        {{-- ── CUSTOM SEARCHABLE SELECT: Menu URL ───────────── --}}
        <div class="form-group" id="menu_url_group">
            <label>Menu URL <span class="text-danger">*</span></label>

            {{-- Hidden input — nilai yang dikirim ke server (pre-filled dengan nilai sekarang) --}}
            <input type="hidden" id="fk_web_menu_url" name="web_menu_global[fk_web_menu_url]"
                   value="{{ $webMenuGlobal->fk_web_menu_url }}">

            <div class="ss-wrapper" id="ssWrapUpdate">
                <div class="ss-trigger" id="ssTriggerUpdate" tabindex="0" role="combobox"
                     aria-haspopup="listbox" aria-expanded="false">
                    <div class="ss-display placeholder" id="ssDisplayUpdate">Pilih Menu URL</div>
                    <div class="ss-actions">
                        <button type="button" class="ss-clear-btn" id="ssClearUpdate" title="Hapus pilihan">
                            <i class="fas fa-times"></i>
                        </button>
                        <i class="fas fa-chevron-down ss-arrow"></i>
                    </div>
                </div>

                <div class="ss-dropdown" id="ssDropdownUpdate" role="listbox">
                    <div class="ss-search-wrap">
                        <input type="text" class="ss-search-input" id="ssSearchUpdate"
                               placeholder="Cari nama menu, URL, atau deskripsi..." autocomplete="off">
                    </div>
                    <div class="ss-stats" id="ssStatsUpdate">
                        Menampilkan semua — terbaru di atas
                    </div>
                    <div class="ss-options-list" id="ssOptionListUpdate"></div>
                </div>
            </div>

            <div class="invalid-feedback d-block" id="fk_web_menu_url_error"></div>
            <small class="form-text text-muted mt-1">
                <i class="fas fa-info-circle"></i>
                Ketik untuk mencari. Urutan terbaru ditampilkan di atas.
            </small>
        </div>
        {{-- ── END CUSTOM SEARCHABLE SELECT ───────────────── --}}

        <div class="form-group" id="icon_group">
            <label for="wmg_icon">
                Icon Menu <span class="text-danger" id="icon_required">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i id="icon_preview" class="fas {{ $webMenuGlobal->wmg_icon ?? 'fa-cog' }}"></i></span>
                </div>
                <input type="text" class="form-control" id="wmg_icon" name="web_menu_global[wmg_icon]" 
                       value="{{ $webMenuGlobal->wmg_icon }}" placeholder="Contoh: fa-home, fa-users, fa-cog" maxlength="50">
            </div>
            <div class="invalid-feedback" id="wmg_icon_error"></div>
            <small class="form-text text-muted">
                Gunakan icon Font Awesome 5 (contoh: fa-home, fa-users, fa-file-alt). 
                <a href="https://fontawesome.com/v5/search?m=free" target="_blank">Lihat daftar icon</a>
            </small>
        </div>

        <div class="form-group">
            <label for="wmg_type">Tipe Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_type" name="web_menu_global[wmg_type]">
                <option value="">Pilih Tipe Menu</option>
                <option value="general" {{ $webMenuGlobal->wmg_type === 'general' ? 'selected' : '' }}>General</option>
                <option value="special" {{ $webMenuGlobal->wmg_type === 'special' ? 'selected' : '' }}>Special</option>
            </select>
            <div class="invalid-feedback" id="wmg_type_error"></div>
            <small class="form-text text-muted">
                <strong>General:</strong> Menu akan muncul di sidebar (untuk halaman operasional dengan sidebar) dan di header (untuk halaman tanpa sidebar seperti halaman user)<br>
                <strong>Special:</strong> Menu hanya akan muncul di header pada halaman yang memiliki sidebar (menu tambahan khusus)
            </small>
        </div>

        <div class="form-group">
            <label for="wmg_badge_indicator">Indikator Notifikasi <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_badge_indicator" name="wmg_badge_indicator">
                <option value="">Pilih Opsi</option>
                <option value="ya"    {{ $webMenuGlobal->wmg_badge_method === 'getBadgeCount' ? 'selected' : '' }}>Ya, tampilkan notifikasi</option>
                <option value="tidak" {{ !$webMenuGlobal->wmg_badge_method ? 'selected' : '' }}>Tidak</option>
            </select>
            <input type="hidden" id="wmg_badge_method" name="web_menu_global[wmg_badge_method]" 
                   value="{{ $webMenuGlobal->wmg_badge_method ?? '' }}">
            <div class="invalid-feedback" id="wmg_badge_indicator_error"></div>
            <small class="form-text text-muted">
                Pilih <strong>Ya</strong> jika menu ini membutuhkan badge notifikasi (contoh: jumlah data pending). 
                Pilih <strong>Tidak</strong> jika tidak memerlukan notifikasi.
            </small>
        </div>

        <div class="form-group">
            <label for="wmg_status_menu">Status Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_status_menu" name="web_menu_global[wmg_status_menu]">
                <option value="">Pilih Status</option>
                <option value="aktif"    {{ $webMenuGlobal->wmg_status_menu === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $webMenuGlobal->wmg_status_menu === 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
            </select>
            <div class="invalid-feedback" id="wmg_status_menu_error"></div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Informasi:</strong> 
            @if($webMenuGlobal->wmg_parent_id)
                Menu ini adalah <strong>Sub Menu</strong> dari {{ $webMenuGlobal->parentMenu->wmg_nama_default }} dengan urutan ke-{{ $webMenuGlobal->wmg_urutan_menu }}.
            @else
                Menu ini adalah <strong>{{ $webMenuGlobal->wmg_kategori_menu }}</strong> dengan urutan ke-{{ $webMenuGlobal->wmg_urutan_menu }} di menu utama.
            @endif
            <br>
            Jika mengubah kategori atau parent menu, urutan akan disesuaikan otomatis oleh sistem.
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
$(document).ready(function () {

    /* ══════════════════════════════════════════════════════════════
       DATA: Siapkan data URL dari server (diisi Blade, urutan terbaru di atas)
       ══════════════════════════════════════════════════════════════ */
    const menuUrlData = [
        @foreach(array_reverse($menuUrls->toArray()) as $index => $url)
        {
            value: "{{ $url['web_menu_url_id'] }}",
            app:   "{{ addslashes($url['application']['app_nama'] ?? '') }}",
            name:  "{{ addslashes($url['wmu_nama'] ?? '') }}",
            desc:  "{{ addslashes($url['wmu_keterangan'] ?? '') }}",
            isNew: {{ $index < 5 ? 'true' : 'false' }}
        },
        @endforeach
    ];

    /* ══════════════════════════════════════════════════════════════
       SEARCHABLE SELECT — factory function
       ══════════════════════════════════════════════════════════════ */
    function initSearchableSelect(cfg) {
        const $hidden   = $(cfg.hiddenInput);
        const $trigger  = $(cfg.trigger);
        const $display  = $(cfg.display);
        const $clearBtn = $(cfg.clearBtn);
        const $dropdown = $(cfg.dropdown);
        const $search   = $(cfg.searchInput);
        const $stats    = $(cfg.stats);
        const $optList  = $(cfg.optionList);
        const $errorDiv = $(cfg.errorDiv);

        let focusedIndex = -1;
        let currentValue = cfg.selectedValue || '';
        const totalCount = cfg.data.length;

        function esc(str) {
            return String(str).replace(/[&<>"']/g, m =>
                ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
        }
        function highlight(text, query) {
            if (!query) return esc(text);
            const escaped  = esc(text);
            const escapedQ = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return escaped.replace(new RegExp(`(${escapedQ})`, 'gi'), '<mark>$1</mark>');
        }

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

                if (item.app !== currentGroup) {
                    currentGroup = item.app;
                    html += `<div class="ss-group-header">${esc(item.app)}</div>`;
                }

                const isSelected = item.value === currentValue;
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

            if (shownCount === 0) {
                html = `<div class="ss-empty">
                    <i class="fas fa-search"></i>
                    Tidak ada URL yang cocok dengan "<strong>${esc(q)}</strong>"
                </div>`;
                $stats.text(`Tidak ditemukan dari ${totalCount} URL`);
            } else {
                $stats.text(q
                    ? `Menampilkan ${shownCount} dari ${totalCount} URL`
                    : `Menampilkan semua ${totalCount} URL — terbaru di atas`);
            }

            $optList.html(html);
            focusedIndex = -1;

            const $sel = $optList.find('.ss-option-item.selected');
            if ($sel.length) {
                $optList.scrollTop($sel.position().top - 60);
            }
        }

        function updateDisplay(value) {
            if (!value) {
                $display.html('Pilih Menu URL').addClass('placeholder').removeClass('ss-display-selected');
                $clearBtn.hide();
                return;
            }
            const item = cfg.data.find(d => d.value === value);
            if (!item) return;
            $display.html(`
                <div class="ss-display-selected">
                    <div class="ss-sel-app">${esc(item.app)}</div>
                    <div class="ss-sel-name">${esc(item.name)}</div>
                    ${item.desc ? `<div class="ss-sel-desc">${esc(item.desc)}</div>` : ''}
                </div>
            `).removeClass('placeholder').addClass('ss-display-selected');
            $clearBtn.show();
        }

        function getVisibleItems() { return $optList.find('.ss-option-item'); }
        function moveFocus(dir) {
            const $items = getVisibleItems();
            if (!$items.length) return;
            $items.removeClass('focused');
            focusedIndex = Math.max(0, Math.min($items.length - 1, focusedIndex + dir));
            const $f = $items.eq(focusedIndex).addClass('focused');
            const listTop = $optList.scrollTop();
            const listH   = $optList.outerHeight();
            const itemTop = $f.position().top + listTop;
            const itemH   = $f.outerHeight();
            if (itemTop < listTop) $optList.scrollTop(itemTop - 4);
            else if (itemTop + itemH > listTop + listH) $optList.scrollTop(itemTop + itemH - listH + 4);
        }

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

        function selectItem(value) {
            currentValue = value;
            $hidden.val(value);
            updateDisplay(value);
            $trigger.removeClass('is-invalid');
            $errorDiv.html('');
            closeDropdown();
        }

        $trigger.on('click', function(e) {
            if ($(e.target).closest('.ss-clear-btn').length) return;
            $dropdown.hasClass('open') ? closeDropdown() : openDropdown();
        });
        $trigger.on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                e.preventDefault(); openDropdown();
            }
        });
        $search.on('input', function() { renderOptions($(this).val()); });
        $search.on('keydown', function(e) {
            if      (e.key === 'ArrowDown') { e.preventDefault(); moveFocus(1); }
            else if (e.key === 'ArrowUp')   { e.preventDefault(); moveFocus(-1); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                const $f = getVisibleItems().filter('.focused');
                if ($f.length) selectItem($f.data('value'));
            }
            else if (e.key === 'Escape') closeDropdown();
        });
        $optList.on('click', '.ss-option-item', function() {
            selectItem($(this).data('value'));
        });
        $clearBtn.on('click', function(e) {
            e.stopPropagation();
            currentValue = '';
            $hidden.val('');
            updateDisplay('');
        });
        $(document).on('click.ss_update_url', function(e) {
            if (!$(e.target).closest(cfg.trigger).length &&
                !$(e.target).closest(cfg.dropdown).length) {
                closeDropdown();
            }
        });

        // Init: tampilkan nilai yang sudah dipilih
        if (currentValue) updateDisplay(currentValue);
        renderOptions('');
    }

    /* ══════════════════════════════════════════════════════════════
       INISIALISASI untuk form UPDATE (pre-selected)
       ══════════════════════════════════════════════════════════════ */
    initSearchableSelect({
        data:          menuUrlData,
        hiddenInput:   '#fk_web_menu_url',
        trigger:       '#ssTriggerUpdate',
        display:       '#ssDisplayUpdate',
        clearBtn:      '#ssClearUpdate',
        dropdown:      '#ssDropdownUpdate',
        searchInput:   '#ssSearchUpdate',
        stats:         '#ssStatsUpdate',
        optionList:    '#ssOptionListUpdate',
        errorDiv:      '#fk_web_menu_url_error',
        selectedValue: '{{ $webMenuGlobal->fk_web_menu_url }}'   {{-- pre-selected --}}
    });

    /* ══════════════════════════════════════════════════════════════
       EXISTING LOGIC
       ══════════════════════════════════════════════════════════════ */

    function toggleFieldsByKategori(kategori) {
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
    }

    const initialKategori = $('#wmg_kategori_menu').val();
    if (initialKategori) toggleFieldsByKategori(initialKategori);

    $('#wmg_kategori_menu').on('change', function() {
        toggleFieldsByKategori($(this).val());
    });

    $('#wmg_icon').on('input', function() {
        const iconValue = $(this).val().trim();
        const iconClass = iconValue
            ? (iconValue.startsWith('fa-') ? iconValue : 'fa-' + iconValue)
            : 'fa-cog';
        $('#icon_preview').attr('class', 'fas ' + iconClass);
    });

    $('#wmg_badge_indicator').on('change', function() {
        $('#wmg_badge_method').val($(this).val() === 'ya' ? 'getBadgeCount' : '');
    });

    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        $(`#${$(this).attr('id')}_error`).html('');
    });

    $('#btnSubmitForm').on('click', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        // Validasi custom select
        const urlVal  = $('#fk_web_menu_url').val();
        const kategori = $('#wmg_kategori_menu').val();
        if ((kategori === 'Sub Menu' || kategori === 'Menu Biasa') && !urlVal) {
            $('#ssTriggerUpdate').addClass('is-invalid');
            $('#fk_web_menu_url_error').html('Menu URL wajib dipilih.');
            return;
        }

        const form   = $('#formUpdateWebMenuGlobal');
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
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            if (key === 'web_menu_global.fk_web_menu_url') {
                                $('#ssTriggerUpdate').addClass('is-invalid');
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
                        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Mohon periksa kembali input Anda' });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Terjadi kesalahan saat menyimpan data' });
                    }
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.' });
            },
            complete: function() {
                button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
            }
        });
    });
});
</script>