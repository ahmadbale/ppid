@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp

<!-- Add Menu Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMenuForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jumlah_menu">Masukkan jumlah menu yang ingin ditambah:</label>
                        <input type="number" class="form-control" id="jumlah_menu" name="jumlah_menu" min="1" max="10" value="1">
                        <small class="form-text text-muted">Maksimum 10 menu dalam satu kali input</small>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="menu-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Hak Akses</th>
                                    <th style="width: 18%">Kategori Menu</th>
                                    <th style="width: 18%">Nama Group Menu</th>
                                    <th style="width: 24%">Nama Menu</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 10%">Pilih</th>
                                </tr>
                            </thead>
                            <tbody id="menu-table-body">
                                <!-- Baris menu akan dibuat secara dinamis -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pengaturan Hak Akses di bawah tabel -->
                    <div class="mt-4 pt-3 border-top">
                        <h5>Pengaturan Hak Akses</h5>
                        <div class="info-message alert-info small">
                            <i class="fas fa-info-circle"></i> Silakan pilih menu yang ingin diatur hak aksesnya dengan mencentang kotak di kolom "Pilih".
                        </div>
                        
                        <!-- Container untuk pengaturan hak akses -->
                        <div id="hak-akses-container" style="display: none;" class="card">
                            <!-- Konten akan dibuat secara dinamis -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
// Dinamis mengubah jumlah baris tabel
$('#jumlah_menu').on('change', function() {
    const jumlah = parseInt($(this).val());
    if (jumlah < 1) {
        $(this).val(1);
        return;
    }
    if (jumlah > 10) {
        $(this).val(10);
        return;
    }
    
    generateMenuRows(jumlah);
});

// Fungsi untuk menghasilkan baris menu sesuai jumlah yang diinginkan
function generateMenuRows(count) {
    $('#menu-table-body').empty();
    $('#hak-akses-container').hide();
    
    for (let i = 0; i < count; i++) {
        const index = i;
        let row = `
            <tr>
                <td>${i+1}</td>
                <td>
                    <select class="form-control level-menu" name="menus[${index}][fk_m_hak_akses]" data-index="${index}" required>
                        <option value="">Pilih Hak Akses</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->hak_akses_id }}" data-kode="{{ $level->hak_akses_kode }}">{{ $level->hak_akses_nama }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="form-control kategori-menu" name="menus[${index}][kategori_menu]" required data-index="${index}">
                        <option value="menu_biasa">- Set sebagai menu biasa</option>
                        <option value="group_menu">- Set sebagai group menu</option>
                        <option value="sub_menu">- Set sebagai sub menu</option>
                    </select>
                </td>
                <td>
                    <select class="form-control nama-group-menu" name="menus[${index}][wm_parent_id]" disabled>
                        <option value="">Pilih Nama Group Menu</option>
                        <optgroup label="Group Menu" class="group-menu-options">
                            @foreach($groupMenusGlobal as $menu)
                                <option value="{{ $menu->web_menu_global_id }}" data-menu-type="global">
                                    {{ $menu->wmg_nama_default }}
                                </option>
                            @endforeach
                        </optgroup>
                        
                        <optgroup label="Menu Utama" class="sub-menu-options" style="display:none;">
                            @foreach($groupMenusFromWebMenu as $menu)
                                <option value="{{ $menu->web_menu_id }}" data-menu-type="parent">
                                    {{ $menu->wm_menu_nama ?: $menu->WebMenuGlobal->wmg_nama_default }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </td>
                <td>
                    <select class="form-control nama-menu" name="menus[${index}][fk_web_menu_global]">
                        <option value="">Pilih Nama Menu</option>
                        @foreach($nonGroupMenus as $menu)
                            <option value="{{ $menu->web_menu_global_id }}">
                                {{ $menu->wmg_nama_default }} 
                                @if($menu->WebMenuUrl && $menu->WebMenuUrl->application)
                                    ({{ $menu->WebMenuUrl->application->app_nama }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <input type="text" class="form-control mt-2 alias-menu" 
                        name="menus[${index}][wm_menu_nama]" placeholder="Alias menu (opsional)">
                </td>
                <td>
                    <select class="form-control status-menu" name="menus[${index}][wm_status_menu]" required>
                        <option value="">Pilih Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </td>
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="select_menu_${index}" name="selected_menu[]" class="custom-control-input select-menu-checkbox" data-index="${index}">
                        <label class="custom-control-label" for="select_menu_${index}"></label>
                    </div>
                    <input type="hidden" name="menus[${index}][hak_akses_kode]" class="hak-akses-kode-input">
                </td>
            </tr>
        `;
        $('#menu-table-body').append(row);
    }
    
    // Ikat kembali event untuk kategori menu dan level menu
    bindCategoryChangeEvents();
    bindLevelChangeEvents();
    bindSelectMenuEvents();
}

// Event handler untuk dropdown Hak Akses
function bindLevelChangeEvents() {
    $('.level-menu').off('change').on('change', function() {
        const selectedValue = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        const selectedKode = $(this).find('option:selected').data('kode');
        const rowIndex = $(this).data('index');
        
        // Set kode hak akses pada input hidden di baris yang sama
        $(`input[name="menus[${rowIndex}][hak_akses_kode]"]`).val(selectedKode);
        
        // Perbarui tampilan jika menu ini dipilih
        if ($(`#select_menu_${rowIndex}`).is(':checked')) {
            updateHakAksesDisplay();
        }
    });
}

// Event handler untuk dropdown Kategori Menu
function bindCategoryChangeEvents() {
    $('.kategori-menu').off('change').on('change', function() {
        const selectedValue = $(this).val();
        const $row = $(this).closest('tr');
        
        const $namaGroupMenu = $row.find('.nama-group-menu');
        const $namaMenu = $row.find('.nama-menu');
        
        if (selectedValue === 'menu_biasa') {
            // Set sebagai menu biasa
            $namaGroupMenu.prop('disabled', true).val('');
            $namaMenu.prop('disabled', false);
            
            // Tampilkan opsi yang sesuai
            $row.find('.group-menu-options').show();
            $row.find('.sub-menu-options').hide();
        } 
        else if (selectedValue === 'group_menu') {
            // Set sebagai group menu
            $namaMenu.prop('disabled', true).val('');
            $namaGroupMenu.prop('disabled', false);
            
            // Tampilkan opsi untuk group menu
            $row.find('.group-menu-options').show();
            $row.find('.sub-menu-options').hide();
        }
        else if (selectedValue === 'sub_menu') {
            // Set sebagai sub menu
            $namaMenu.prop('disabled', false);
            $namaGroupMenu.prop('disabled', false);
            
            // Tampilkan opsi untuk sub menu
            $row.find('.group-menu-options').hide();
            $row.find('.sub-menu-options').show();
        }
        
        // Perbarui tampilan jika menu ini dipilih
        const rowIndex = $(this).data('index');
        if ($(`#select_menu_${rowIndex}`).is(':checked')) {
            updateHakAksesDisplay();
        }
    });
}

// Event handler untuk checkbox pilih menu
function bindSelectMenuEvents() {
    $('.select-menu-checkbox').off('change').on('change', function() {
        // Perbarui tampilan hak akses berdasarkan menu yang dipilih
        updateHakAksesDisplay();
    });
}

// Fungsi baru untuk memperbarui tampilan hak akses berdasarkan menu yang dipilih
function updateHakAksesDisplay() {
    // Dapatkan semua checkbox yang dipilih
    const selectedCheckboxes = $('.select-menu-checkbox:checked');
    
    // Jika tidak ada menu yang dipilih, sembunyikan container
    if (selectedCheckboxes.length === 0) {
        $('#hak-akses-container').hide();
        return;
    }
    
    // Bersihkan konten sebelumnya
    $('#hak-akses-container').empty().show();
    
    // Kelompokkan menu yang dipilih berdasarkan level akses
    const menusByLevel = {};
    
    selectedCheckboxes.each(function() {
        const menuIndex = $(this).data('index');
        const $row = $(this).closest('tr');
        const hakAksesId = $row.find('.level-menu').val();
        const hakAksesText = $row.find('.level-menu option:selected').text();
        const hakAksesKode = $row.find('.level-menu option:selected').data('kode');
        
        if (!hakAksesId) return; // Lewati jika tidak ada level yang dipilih
        
        // Inisialisasi grup jika belum ada
        if (!menusByLevel[hakAksesId]) {
            menusByLevel[hakAksesId] = {
                name: hakAksesText,
                kode: hakAksesKode,
                menus: []
            };
        }
        
        // Dapatkan nama menu berdasarkan kategori
        let menuName = '';
        const kategori = $row.find('.kategori-menu').val();
        if (kategori === 'group_menu') {
            menuName = $row.find('.nama-group-menu option:selected').text();
        } else {
            menuName = $row.find('.nama-menu option:selected').text();
            if (!menuName || menuName === 'Pilih Nama Menu') {
                const aliasMenu = $row.find('.alias-menu').val();
                menuName = aliasMenu || `Menu #${menuIndex+1}`;
            }
        }
        
        // Tambahkan menu ke grup
        menusByLevel[hakAksesId].menus.push({
            index: menuIndex,
            name: menuName
        });
        
        // Perbarui nilai input hidden untuk indeks menu yang dipilih
        $('#selected-menu-indices').val(
            $('.select-menu-checkbox:checked').map(function() {
                return $(this).data('index');
            }).get().join(',')
        );
    });
    
    // Buat header untuk container hak akses
    // const $header = $('<div class="card-header bg-light mb-3"><h6 class="mb-0">Pengaturan Hak Akses untuk Menu yang Dipilih</h6></div>');
    // $('#hak-akses-container').append($header);
    
    // Buat tabel untuk setiap level
    $.each(menusByLevel, function(levelId, level) {
        const $levelCard = $(`
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Pengaturan Hak Akses untuk Level: <strong>${level.name}</strong></h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Tampil Menu</th>
                                <th>Lihat</th>
                                <th>Tambah</th>
                                <th>Ubah</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="hak-akses-tbody-${levelId}">
                        </tbody>
                    </table>
                </div>
            </div>
        `);
        
        $('#hak-akses-container').append($levelCard);
        
        // Tambahkan setiap menu ke tabel level-nya
        $.each(level.menus, function(i, menu) {
            const $row = $(`
                <tr>
                    <td>${menu.name}</td>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="hak_menu_${menu.index}" name="menus[${menu.index}][hak_akses][menu]" value="1">
                            <label class="custom-control-label" for="hak_menu_${menu.index}"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="hak_view_${menu.index}" name="menus[${menu.index}][hak_akses][view]" value="1">
                            <label class="custom-control-label" for="hak_view_${menu.index}"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="hak_create_${menu.index}" name="menus[${menu.index}][hak_akses][create]" value="1">
                            <label class="custom-control-label" for="hak_create_${menu.index}"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="hak_update_${menu.index}" name="menus[${menu.index}][hak_akses][update]" value="1">
                            <label class="custom-control-label" for="hak_update_${menu.index}"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="hak_delete_${menu.index}" name="menus[${menu.index}][hak_akses][delete]" value="1">
                            <label class="custom-control-label" for="hak_delete_${menu.index}"></label>
                        </div>
                    </td>
                </tr>
            `);
            
            $(`#hak-akses-tbody-${levelId}`).append($row);
        });
    });
    
    // Tambahkan input hidden untuk semua indeks menu yang dipilih
    if (!$('#selected-menu-indices').length) {
        $('#hak-akses-container').append('<input type="hidden" id="selected-menu-indices" name="selected_menu_indices" value="">');
    }
    
    // Perbarui nilai input hidden
    $('#selected-menu-indices').val(
        $('.select-menu-checkbox:checked').map(function() {
            return $(this).data('index');
        }).get().join(',')
    );
    
    // Tambahkan pesan informasi
    $('#hak-akses-container').append(`
        <div class="alert alert-info small mt-3">
            <i class="fas fa-info-circle"></i> Pengaturan hak akses ini akan diterapkan untuk semua pengguna dengan level yang dipilih.
        </div>
    `);
}

// Form submission
$('#addMenuForm').off('submit').on('submit', function(e) {
    e.preventDefault();
    
    // Reset validasi
    $('.is-invalid').removeClass('is-invalid');
    
    // Validasi form
    let isValid = true;
    
    // Siapkan data untuk dikirim
    const formData = new FormData(this);
    
    // Disable button saat proses submit
    $('#addMenuForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    
    $.ajax({
        url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/store') }}",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                toastr.error(response.message);
                $('#addMenuForm button[type="submit"]').prop('disabled', false).html('Simpan');
            }
        },
        error: function(xhr) {
            console.error('Error creating menu:', xhr.responseText);
            toastr.error('Terjadi kesalahan saat menyimpan menu');
            $('#addMenuForm button[type="submit"]').prop('disabled', false).html('Simpan');
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    const fieldName = key.replace(/menus\[\d+\]\[(.+)\]/, '$1');
                    $(`[name="${key}"]`).addClass('is-invalid');
                });
            }
        }
    });
});

// Reset form saat modal ditampilkan
$('#addMenuModal').on('show.bs.modal', function() {
    $('#addMenuForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('#jumlah_menu').val(1);
    generateMenuRows(1);
    $('#hak-akses-container').hide();
});

// Inisialisasi baris pertama
$(function() {
    generateMenuRows(1);
});
</script>
@endpush