{{-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\update.blade.php --}}
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp

{{-- Include shared styles (inject sekali per halaman) --}}
@once
    @include('sisfo::components.web-menu-global.style')
@endonce

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

{{-- Include shared scripts --}}
@include('sisfo::components.web-menu-global.shared-scripts')

<script>
$(document).ready(function () {
    /**
     * Data menu URL dari server
     * Urutan terbaru di atas untuk menampilkan item terbaru terlebih dahulu
     */
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

    /**
     * Inisialisasi Web Menu Global untuk UPDATE mode
     * Menggunakan shared handler dari WebMenuGlobalShared
     * Pass nilai yang sudah dipilih dari database
     */
    WebMenuGlobalShared.initUpdate(menuUrlData, '{{ $webMenuGlobal->fk_web_menu_url }}');
});
</script>