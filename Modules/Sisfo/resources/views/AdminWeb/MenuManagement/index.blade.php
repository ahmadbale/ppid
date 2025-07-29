{{-- resources/views/adminweb/MenuManagement/index.blade.php --}}
@php
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
@endphp
@extends('sisfo::layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                @if(
                        Auth::user()->level->hak_akses_kode === 'SAR' ||
                        SetHakAksesModel::cekHakAkses(Auth::user()->user_id, WebMenuModel::getDynamicMenuUrl('menu-management'), 'create')
                    )
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addMenuModal">
                        <i class="fas fa-plus"></i> Tambah Menu
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="info-message alert-info">
                <div class="info-content">
                    <i class="fas fa-info-circle"></i> Drag and drop item menu untuk menyusun ulang. <br>
                    Setelah mengubah urutan, klik <strong>"Simpan Urutan"</strong> untuk menyimpan perubahan. <br>
                    <strong>Catatan:</strong> Jika menu dipindahkan ke kategori menu berbeda, jenis menu akan otomatis
                    menyesuaikan.
                </div>
            </div>
            <div class="form-group mb-4 col-md-6">
                <label for="filterKategori"><strong>Pilih Kategori Menu:</strong></label>
                <select class="form-control" id="filterKategori">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="all">Semua</option>
                    @foreach($jenisMenuList as $kode => $nama)
                        <option value="{{ $kode }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div id="menuKategoriWrapper" style="display:none;">
                <div id="menuKategoriRows">
                    @php $counter = 0; @endphp
                    @foreach($jenisMenuList as $kode => $nama)
                        @if($counter % 2 == 0 && $counter > 0)
                            </div>
                            <div class="mt-4">
                        @endif

                        <div class="menu-kategori-item" data-kode="{{ $kode }}" style="display:none;">
                            <div class="card">
                                @php
                                    // Tetapkan warna berbeda untuk setiap jenis menu
                                    $bgColors = [
                                        'SAR' => 'bg-dark',
                                        'ADM' => 'bg-primary',
                                        'MPU' => 'bg-success',
                                        'VFR' => 'bg-warning',
                                        'RPN' => 'bg-danger',
                                        'ADT' => 'bg-info'
                                    ];

                                    // Default color jika kode tidak ada di array
                                    $bgColor = $bgColors[$kode] ?? 'bg-secondary';

                                    // Tentukan warna teks berdasarkan latar belakang
                                    $textColor = ($kode == 'VFR') ? 'text-dark' : 'text-white';
                                @endphp

                                <div class="card-header {{ $bgColor }} {{ $textColor }}">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-fw fa-list-alt"></i> {{ $nama }}
                                    </h5>
                                </div>
                                <div class="card-body menu">
                                    <div class="dd nestable-{{ $kode }}" data-jenis="{{ $kode }}">
                                        <ol class="dd-list">
                                            @foreach($menusByJenis[$kode]['menus'] as $menu)
                                                @include('sisfo::AdminWeb.MenuManagement.menu-item', ['menu' => $menu, 'kode' => $kode])
                                            @endforeach
                                        </ol>
                                    </div>
                                    @if(count($menusByJenis[$kode]['menus']) == 0)
                                        <div class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> Belum ada menu untuk kategori ini
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @php $counter++; @endphp
                    @endforeach
                </div>
            </div>

            {{-- Tombol Simpan Urutan (Drag and Drop) --}}
            <button type="button" class="btn btn-primary mt-3" id="saveOrderBtn">
                <i class="fas fa-save"></i> Simpan Urutan
            </button>
        </div>
    </div>

    <!-- Include modals for CRUD operations -->
    @if(
            Auth::user()->level->hak_akses_kode === 'SAR' ||
            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, WebMenuModel::getDynamicMenuUrl('menu-management'), 'create')
        )
        @include('sisfo::AdminWeb.MenuManagement.create')
    @endif

    @include('sisfo::AdminWeb.MenuManagement.update')
    @include('sisfo::AdminWeb.MenuManagement.detail')
    @include('sisfo::AdminWeb.MenuManagement.delete')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ vendor_asset('nestable2/jquery.nestable.min.css') }}">
    <link rel="stylesheet" href="{{ vendor_asset('toastr/toastr.min.css') }}">
    <style>
        .card-body.menu,
        .card-body.menu .dd,
        .card-body.menu .dd-list,
        .card-body.menu .dd-item,
        .card-body.menu .dd-handle {
            width: 960px !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 80%;
            margin-top: 0.25rem;
        }

        /* Warna berbeda untuk setiap jenis menu */
        .nestable-SAR .dd-handle {
            border-left: 5px solid #343a40;
            /* dark */
        }

        .nestable-ADM .dd-handle {
            border-left: 5px solid #007bff;
            /* primary */
        }

        .nestable-MPU .dd-handle {
            border-left: 5px solid #28a745;
            /* success */
        }

        .nestable-VFR .dd-handle {
            border-left: 5px solid #fd7e14;
            /* warning */
        }

        .nestable-RPN .dd-handle {
            border-left: 5px solid #dc3545;
            /* danger */
        }

        .nestable-ADT .dd-handle {
            border-left: 5px solid #17a2b8;
            /* info */
        }
    </style>
@endpush

@push('js')
<script src="{{ vendor_asset('nestable2/jquery.nestable.min.js') }}"></script>
<script src="{{ vendor_asset('toastr/toastr.min.js') }}"></script>
<script>
$(function () {
    // Dapatkan hak akses user dari PHP
    const userHakAksesKode = '{{ $userHakAksesKode }}';
    
    // Sembunyikan tombol simpan urutan saat belum pilih kategori
    $('#saveOrderBtn').hide();

    // Fungsi untuk menginisialisasi nestable
    function initializeNestable() {
        $('.dd').each(function () {
            const $container = $(this);
            
            // Destroy existing nestable jika ada
            if ($container.data('nestable')) {
                $container.nestable('destroy');
            }
            
            // Inisialisasi nestable baru
            $container.nestable({
                maxDepth: 2,
                group: 'menu-group', // Gunakan nama group yang sama untuk semua
                dragStart: function(l, e) {
                    // Simpan data original untuk validasi
                    const $item = $(e);
                    $item.data('original-container', $item.closest('.dd').data('jenis'));
                },
                dragEnd: function(l, e, p) {
                    // Handler setelah drag selesai
                    const $item = $(e);
                    const $newContainer = $item.closest('.dd');
                    const newJenis = $newContainer.data('jenis');
                    const originalJenis = $item.data('original-container');

                    // Validasi SAR
                    if (userHakAksesKode !== 'SAR') {
                        // Cek jika menu SAR dipindahkan ke container non-SAR
                        if (originalJenis === 'SAR' && newJenis !== 'SAR') {
                            toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                            setTimeout(() => window.location.reload(), 1000);
                            return false;
                        }
                        
                        // Cek jika menu non-SAR dipindahkan ke container SAR
                        if (originalJenis !== 'SAR' && newJenis === 'SAR') {
                            toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                            setTimeout(() => window.location.reload(), 1000);
                            return false;
                        }
                    }

                    // Update data-jenis untuk item yang dipindahkan
                    $item.attr('data-jenis', newJenis);
                    updateMenuItemStyle($item, newJenis);

                    // Update submenu juga
                    $item.find('.dd-item').each(function() {
                        $(this).attr('data-jenis', newJenis);
                        updateMenuItemStyle($(this), newJenis);
                    });
                }
            });
        });
    }

    // Fungsi untuk update style menu item
    function updateMenuItemStyle($item, jenisKode) {
        // Remove existing jenis classes
        $item.removeClass(function (index, className) {
            return (className.match(/(^|\s)jenis-\S+/g) || []).join(' ');
        });
        
        // Add new jenis class
        $item.addClass('jenis-' + jenisKode);
        
        // Update handle color berdasarkan jenis
        const $handle = $item.children('.dd-handle');
        $handle.removeClass(function (index, className) {
            return (className.match(/(^|\s)handle-\S+/g) || []).join(' ');
        });
        $handle.addClass('handle-' + jenisKode);
    }

    // Handler untuk filter kategori
    $('#filterKategori').on('change', function () {
        var kode = $(this).val();
        if (kode && kode !== 'all') {
            $('#menuKategoriWrapper').show();
            $('.menu-kategori-item').hide();
            $('.menu-kategori-item[data-kode="' + kode + '"]').show();
            $('#saveOrderBtn').show();
            
            // Reinitialize nestable untuk container yang ditampilkan
            setTimeout(function() {
                $('.menu-kategori-item[data-kode="' + kode + '"] .dd').each(function() {
                    const $container = $(this);
                    if ($container.data('nestable')) {
                        $container.nestable('destroy');
                    }
                    initializeNestable();
                });
            }, 100);
        } else if (kode === 'all') {
            $('#menuKategoriWrapper').show();
            $('.menu-kategori-item').show();
            $('#saveOrderBtn').show();
            
            // Reinitialize semua nestable
            setTimeout(function() {
                initializeNestable();
            }, 100);
        } else {
            $('#menuKategoriWrapper').hide();
            $('#saveOrderBtn').hide();
        }
    });

    // Trigger change untuk menginisialisasi tampilan awal
    $('#filterKategori').trigger('change');

    // Fungsi untuk mengumpulkan data dari semua container
    function collectAllMenuData() {
        let allData = [];

        $('.dd:visible').each(function () {
            let jenisKode = $(this).data('jenis');
            let levelData = [];
            
            try {
                levelData = $(this).nestable('serialize');
            } catch (e) {
                console.warn('Error serializing nestable for', jenisKode, e);
                levelData = [];
            }

            if (levelData && levelData.length > 0) {
                levelData.forEach(item => {
                    item.current_level = jenisKode;
                    
                    if (item.children) {
                        item.children.forEach(child => {
                            child.current_level = jenisKode;
                            child.parent_id = item.id;
                        });
                    }
                });

                allData = allData.concat(levelData);
            }
        });

        return allData;
    }

    // Handler untuk simpan urutan
    $('#saveOrderBtn').off('click').on('click', function () {
        const data = collectAllMenuData();

        // Validasi data sebelum kirim
        if (data.length === 0) {
            toastr.warning('Tidak ada data menu untuk disimpan');
            return;
        }

        // Validasi SAR untuk keamanan ekstra
        if (userHakAksesKode !== 'SAR') {
            let hasSARChange = false;
            
            data.forEach(item => {
                // Cek apakah ada menu SAR yang termodifikasi
                const $menuItem = $('[data-id="' + item.id + '"]');
                const originalJenis = $menuItem.data('original-jenis') || $menuItem.data('jenis');
                
                if (originalJenis === 'SAR' || item.current_level === 'SAR') {
                    if (originalJenis !== item.current_level) {
                        hasSARChange = true;
                    }
                }
                
                if (item.children) {
                    item.children.forEach(child => {
                        const $childItem = $('[data-id="' + child.id + '"]');
                        const childOriginalJenis = $childItem.data('original-jenis') || $childItem.data('jenis');
                        
                        if (childOriginalJenis === 'SAR' || child.current_level === 'SAR') {
                            if (childOriginalJenis !== child.current_level) {
                                hasSARChange = true;
                            }
                        }
                    });
                }
            });

            if (hasSARChange) {
                toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                setTimeout(() => window.location.reload(), 1000);
                return;
            }
        }

        // Kirim data ke server
        $.ajax({
            url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/reorder') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                data: data
            },
            beforeSend: function() {
                $('#saveOrderBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    toastr.error(response.message);
                    if (response.message.includes('SAR')) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }
            },
            error: function (xhr) {
                let message = 'Error updating menu order';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
                
                if (message.includes('SAR')) {
                    setTimeout(() => window.location.reload(), 1000);
                }
            },
            complete: function() {
                $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Urutan');
            }
        });
    });

    // Simpan data original saat page load untuk tracking perubahan
    $(document).ready(function() {
        $('.dd-item').each(function() {
            const $item = $(this);
            const jenisKode = $item.closest('.dd').data('jenis');
            $item.data('original-jenis', jenisKode);
        });
    });

    // Fungsi helper lainnya tetap sama...
    function updateParentMenuOptions(hakAksesId, targetSelect, excludeId = null) {
        targetSelect.empty().append('<option value="">-Set Sebagai Menu Utama</option>');

        if (!hakAksesId) return;

        const dynamicUrl = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/get-parent-menus') }}";

        $.ajax({
            url: `${dynamicUrl}/${hakAksesId}`,
            type: 'GET',
            data: { exclude_id: excludeId },
            success: function (response) {
                if (response.success && response.parentMenus) {
                    response.parentMenus.forEach(function (menu) {
                        targetSelect.append(`
                            <option value="${menu.web_menu_id}" data-level="${hakAksesId}">
                                ${menu.display_name}
                            </option>
                        `);
                    });
                }
            },
            error: function () {
                toastr.error('Gagal memuat menu induk');
            }
        });
    }

    // Reset forms setelah modal ditutup
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
        $('#edit_level_menu').prop('disabled', false);
        $('#edit_parent_id').empty().append('<option value="">-Set Sebagai Menu Utama</option>');
    });
});
</script>
@endpush