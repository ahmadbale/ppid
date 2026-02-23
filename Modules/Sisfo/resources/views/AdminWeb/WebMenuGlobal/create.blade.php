<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\WebMenuGlobal\create.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuGlobalPath = WebMenuModel::getDynamicMenuUrl('management-menu-global');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah Menu Global Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
</div>
  
<div class="modal-body">
    <form id="formCreateWebMenuGlobal" action="{{ url($webMenuGlobalPath . '/createData') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="wmg_nama_default">Nama Default Menu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="wmg_nama_default" name="web_menu_global[wmg_nama_default]" maxlength="255">
            <div class="invalid-feedback" id="wmg_nama_default_error"></div>
        </div>

        <div class="form-group">
            <label for="wmg_kategori_menu">Kategori Menu <span class="text-danger">*</span></label>
            <select class="form-control" id="wmg_kategori_menu" name="web_menu_global[wmg_kategori_menu]">
                <option value="">Pilih Kategori Menu</option>
                <option value="Menu Biasa">Menu Biasa</option>
                <option value="Group Menu">Group Menu</option>
                <option value="Sub Menu">Sub Menu</option>
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
                    <option value="{{ $parent->web_menu_global_id }}">{{ $parent->wmg_nama_default }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="wmg_parent_id_error"></div>
        </div>

        <div class="form-group" id="menu_url_group">
            <label for="fk_web_menu_url">Menu URL <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_web_menu_url" name="web_menu_global[fk_web_menu_url]">
                <option value="">Pilih Menu URL</option>
                @foreach($menuUrls as $url)
                    <option value="{{ $url->web_menu_url_id }}">
                        {{ $url->application->app_nama }} | {{ $url->wmu_nama }} | {{ $url->wmu_keterangan ?? '-' }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_web_menu_url_error"></div>
        </div>

        <div class="form-group" id="icon_group">
            <label for="wmg_icon">
                Icon Menu <span class="text-danger" id="icon_required">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i id="icon_preview" class="fas fa-cog"></i></span>
                </div>
                <input type="text" class="form-control" id="wmg_icon" name="web_menu_global[wmg_icon]" 
                       placeholder="Contoh: fa-home, fa-users, fa-cog" maxlength="50">
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
                <option value="general">General</option>
                <option value="special">Special</option>
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
                <option value="ya">Ya, tampilkan notifikasi</option>
                <option value="tidak" selected>Tidak</option>
            </select>
            <input type="hidden" id="wmg_badge_method" name="web_menu_global[wmg_badge_method]" value="">
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
                <option value="aktif" selected>Aktif</option>
                <option value="nonaktif">Non-aktif</option>
            </select>
            <div class="invalid-feedback" id="wmg_status_menu_error"></div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Informasi:</strong> Urutan menu akan ditentukan secara otomatis oleh sistem berdasarkan data yang ada.
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
        // Handle kategori menu change
        $('#wmg_kategori_menu').on('change', function() {
            const kategori = $(this).val();
            
            if (kategori === 'Sub Menu') {
                // Sub Menu: parent required, URL required, icon optional
                $('#parent_menu_group').show();
                $('#menu_url_group').show();
                $('#icon_group').show();
                $('#wmg_parent_id').attr('required', true);
                $('#fk_web_menu_url').attr('required', true);
                $('#wmg_icon').attr('required', false);
                $('#icon_required').hide();
                
            } else if (kategori === 'Group Menu') {
                // Group Menu: no parent, no URL, icon required
                $('#parent_menu_group').hide();
                $('#menu_url_group').hide();
                $('#icon_group').show();
                $('#wmg_parent_id').attr('required', false).val('');
                $('#fk_web_menu_url').attr('required', false).val('');
                $('#wmg_icon').attr('required', true);
                $('#icon_required').show();
                
            } else if (kategori === 'Menu Biasa') {
                // Menu Biasa: no parent, URL required, icon required
                $('#parent_menu_group').hide();
                $('#menu_url_group').show();
                $('#icon_group').show();
                $('#wmg_parent_id').attr('required', false).val('');
                $('#fk_web_menu_url').attr('required', true);
                $('#wmg_icon').attr('required', true);
                $('#icon_required').show();
                
            } else {
                // Default: hide all
                $('#parent_menu_group').hide();
                $('#menu_url_group').hide();
                $('#icon_group').hide();
                $('#wmg_parent_id').attr('required', false);
                $('#fk_web_menu_url').attr('required', false);
                $('#wmg_icon').attr('required', false);
            }
        });

        // Handle icon input - live preview
        $('#wmg_icon').on('input', function() {
            const iconValue = $(this).val().trim();
            if (iconValue) {
                // Remove 'fa-' prefix if exists, then add it back
                const iconClass = iconValue.startsWith('fa-') ? iconValue : 'fa-' + iconValue;
                $('#icon_preview').attr('class', 'fas ' + iconClass);
            } else {
                $('#icon_preview').attr('class', 'fas fa-cog'); // default icon
            }
        });

        // Handle badge indicator change
        $('#wmg_badge_indicator').on('change', function() {
            const value = $(this).val();
            if (value === 'ya') {
                $('#wmg_badge_method').val('getBadgeCount');
            } else {
                $('#wmg_badge_method').val('');
            }
        });

        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });
  
        // Handle submit form
        $('#btnSubmitForm').on('click', function() {
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            
            const form = $('#formCreateWebMenuGlobal');
            const formData = new FormData(form[0]);
            const button = $(this);
            
            // Tampilkan loading state pada tombol submit
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
                            // Tampilkan pesan error pada masing-masing field
                            $.each(response.errors, function(key, value) {
                                // Untuk web_menu_global fields
                                if (key.startsWith('web_menu_global.')) {
                                    const fieldName = key.replace('web_menu_global.', '');
                                    $(`#${fieldName}`).addClass('is-invalid');
                                    $(`#${fieldName}_error`).html(value[0]);
                                } else {
                                    // Untuk field biasa (wmg_badge_indicator)
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                    });
                },
                complete: function() {
                    // Kembalikan tombol submit ke keadaan semula
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                }
            });
        });
    });
</script>