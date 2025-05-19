<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\index.blade.php -->
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
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Drag and drop item menu untuk menyusun ulang. <br>
                Setelah mengubah urutan, klik <strong>"Simpan Urutan"</strong> untuk menyimpan perubahan. <br>
                <strong>Catatan:</strong> Jika menu dipindahkan ke kategori menu berbeda, jenis menu akan otomatis
                menyesuaikan.
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
                                    <div class="card-body">
                                        <div class="dd nestable-{{ $kode }}" data-jenis="{{ $kode }}">
                                            <ol class="dd-list">
                                                @foreach($menusByJenis[$kode]['menus'] as $menu)
                                                    @include('sisfo::adminweb.MenuManagement.menu-item', ['menu' => $menu, 'kode' => $kode])
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
        @include('sisfo::adminweb.MenuManagement.create')
    @endif
    
    @include('sisfo::adminweb.MenuManagement.update')
    @include('sisfo::adminweb.MenuManagement.detail')
    @include('sisfo::adminweb.MenuManagement.delete')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/nestable2/jquery.nestable.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    <style>
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
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/nestable2/jquery.nestable.min.js') }}"></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

    <script>
    // Tambahkan token CSRF ke semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Tunggu document ready hanya sekali
    $(function() {
        // Sembunyikan tombol simpan urutan saat belum pilih kategori
        $('#saveOrderBtn').hide();

        $('#filterKategori').on('change', function() {
            var kode = $(this).val();
            if (kode && kode !== 'all') {
                $('#menuKategoriWrapper').show();
                $('.menu-kategori-item').hide();
                $('.menu-kategori-item[data-kode="' + kode + '"]').show();
                $('#saveOrderBtn').show();
            } else if (kode === 'all') {
                $('#menuKategoriWrapper').show();
                $('.menu-kategori-item').show();
                $('#saveOrderBtn').show();
            } else {
                $('#menuKategoriWrapper').hide();
                $('#saveOrderBtn').hide();
            }
        });

        // Optional: trigger change on page load to ensure hidden
        $('#filterKategori').trigger('change');

        // Simpan semua menu dalam variabel JavaScript untuk digunakan dalam filter
        const allMenus = @json($menus);

        // Daftar warna untuk setiap jenis menu
        const menuColors = {
            'SAR': 'border-left-dark',
            'ADM': 'border-left-primary',
            'MPU': 'border-left-success',
            'VFR': 'border-left-warning',
            'RPN': 'border-left-danger',
            'ADT': 'border-left-info'
        };

        // Inisialisasi Nestable untuk setiap jenis menu - hanya sekali per elemen
        $('.dd').each(function() {
            const $container = $(this);
            // Periksa apakah nestable sudah diinisialisasi
            if (!$container.data('nestable-initialized')) {
                $container.nestable({
                    maxDepth: 2,
                    group: 1, // Memungkinkan drag and drop antar kelompok
                    dragStop: function(e) {
                        // Dapatkan kontainer target
                        const $container = $(e.target).closest('.dd');
                        const targetJenis = $container.data('jenis');
                        
                        // Update jenis menu untuk item yang dipindahkan
                        const $item = $(e.item);
                        $item.attr('data-jenis', targetJenis);
                        
                        // Update juga untuk semua submenu
                        $item.find('.dd-item').each(function() {
                            $(this).attr('data-jenis', targetJenis);
                            updateMenuItemStyle($(this), targetJenis);
                        });
                        
                        // Update tampilan
                        updateMenuItemStyle($item, targetJenis);
                    }
                }).data('nestable-initialized', true); // Tandai sudah diinisialisasi
            }
        });

        // Fungsi untuk mengumpulkan semua data menu dari semua kategori
        function collectAllMenuData() {
            let allData = [];

            // Kumpulkan data dari setiap nestable
            $('.dd').each(function() {
                let jenisKode = $(this).data('jenis');
                let levelData = $(this).nestable('serialize');

                if (levelData && levelData.length > 0) {
                    // Tambahkan informasi level ke setiap item menu utama (tanpa parent)
                    levelData.forEach(item => {
                        // Hanya menambahkan level ke menu tanpa parent
                        if (!item.parent_id) {
                            item.level = jenisKode;
                        }

                        // Simpan informasi level kode saat ini untuk menu ini
                        item.current_level = jenisKode;
                    });

                    allData = allData.concat(levelData);
                }
            });

            return allData;
        }

        // Handler ketika item dilepas (drop) setelah di-drag
        $('.dd').off('change').on('change', function() {
            // Dapatkan jenis kontainer tujuan
            let targetContainerJenis = $(this).data('jenis');
            let userhakAksesKode = '{{ Auth::user()->level->hak_akses_kode }}';

            // Jika container tujuan adalah SAR atau ada menu SAR yang di-drop ke container lain
            if (targetContainerJenis === 'SAR' && userhakAksesKode !== 'SAR') {
                // Cek apakah ada item non-SAR yang dipindahkan ke container SAR
                let nonSarItemInSarContainer = false;
                $(this).find('.dd-item').each(function() {
                    if ($(this).data('jenis') !== 'SAR') {
                        nonSarItemInSarContainer = true;
                        return false; // break the loop
                    }
                });

                if (nonSarItemInSarContainer) {
                    // Reload halaman untuk membatalkan perubahan
                    toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                    setTimeout(() => window.location.reload(), 1000);
                    return;
                }
            }

            // Cek juga apakah item SAR dipindahkan ke container non-SAR
            if (targetContainerJenis !== 'SAR' && userhakAksesKode !== 'SAR') {
                let sarItemInNonSarContainer = false;
                $(this).find('.dd-item[data-jenis="SAR"]').each(function() {
                    sarItemInNonSarContainer = true;
                    return false; // break the loop
                });

                if (sarItemInNonSarContainer) {
                    // Reload halaman untuk membatalkan perubahan
                    toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                    setTimeout(() => window.location.reload(), 1000);
                    return;
                }
            }

            // Loop melalui semua item level 1 (menu utama) dalam kontainer ini
            $(this).find('> .dd-list > .dd-item').each(function() {
                // Perbarui data-jenis untuk menu utama
                $(this).attr('data-jenis', targetContainerJenis);

                // Perbarui juga tampilan visual
                updateMenuItemStyle($(this), targetContainerJenis);

                // Update juga data-jenis untuk semua submenu
                $(this).find('.dd-item').each(function() {
                    $(this).attr('data-jenis', targetContainerJenis);
                    updateMenuItemStyle($(this), targetContainerJenis);
                });
            });
        });

        // Fungsi untuk memperbarui gaya tampilan menu sesuai jenisnya
        function updateMenuItemStyle(menuItem, jenis) {
            // Hapus kelas border warna lama
            menuItem.find('> .dd-handle').removeClass(function(index, className) {
                return (className.match(/(^|\s)border-left-\S+/g) || []).join(' ');
            });

            // Tambahkan kelas border warna baru sesuai jenis
            const borderClass = menuColors[jenis] || 'border-left-secondary';
            menuItem.find('> .dd-handle').addClass(borderClass);
        }

        // Simpan Urutan Menu - pastikan hanya satu handler
        $('#saveOrderBtn').off('click').on('click', function() {
            let data = collectAllMenuData();
            let userhakAksesKode = '{{ Auth::user()->level->hak_akses_kode }}';

            // Validasi menu SAR
            if (userhakAksesKode !== 'SAR') {
                // Dapatkan semua menu dengan jenis SAR
                let sarMenus = [];
                $('.dd-item[data-jenis="SAR"]').each(function() {
                    sarMenus.push({
                        id: parseInt($(this).data('id')),
                        parent_id: $(this).parent().closest('.dd-item').data('id') || null
                    });
                });

                // Cek apakah ada perubahan pada menu SAR
                let hasSARChange = false;
                for (let sarMenu of sarMenus) {
                    // Cari menu SAR di data yang akan disimpan
                    let foundInData = data.find(item => item.id === sarMenu.id);

                    // Jika tidak ditemukan di level 1, periksa anak-anak menu
                    if (!foundInData) {
                        for (let item of data) {
                            if (item.children) {
                                foundInData = item.children.find(child => child.id === sarMenu.id);
                                if (foundInData) {
                                    // Jika ditemukan di anak, verifikasi parent ID
                                    if (foundInData.parent_id !== sarMenu.parent_id) {
                                        hasSARChange = true;
                                        break;
                                    }
                                }
                            }
                        }
                    } else if (foundInData.parent_id !== sarMenu.parent_id) {
                        // Jika ditemukan tapi parent ID berbeda
                        hasSARChange = true;
                    }

                    if (hasSARChange) break;
                }

                // Jika ada menu non-SAR yang dipindahkan ke container SAR
                $('.dd[data-jenis="SAR"] .dd-item').each(function() {
                    if ($(this).data('jenis') !== 'SAR') {
                        hasSARChange = true;
                        return false; // break the loop
                    }
                });

                // Cek juga menu SAR yang dipindahkan ke container non-SAR
                $('.dd:not([data-jenis="SAR"]) .dd-item').each(function() {
                    if ($(this).data('jenis') === 'SAR') {
                        hasSARChange = true;
                        return false; // break the loop
                    }
                });

                if (hasSARChange) {
                    toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                    setTimeout(() => window.location.reload(), 1000);
                    return;
                }
            }

            $.ajax({
                url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/reorder') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    data: data
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        toastr.error(response.message);
                        if (response.message.includes('SAR')) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }
                },
                error: function() {
                    toastr.error('Error updating menu order');
                }
            });
        });

        // Fungsi untuk update opsi parent menu
        function updateParentMenuOptions(hakAksesId, targetSelect, excludeId = null) {
            // Reset dropdown
            targetSelect.empty().append('<option value="">-Set Sebagai Menu Utama</option>');

            // Jika tidak ada hakAksesId, tidak perlu memuat data
            if (!hakAksesId) return;

            // Dapatkan URL dinamis
            const dynamicUrl = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/get-parent-menus') }}";

            // Lakukan request AJAX untuk mendapatkan parent menu berdasarkan level
            $.ajax({
                url: `${dynamicUrl}/${hakAksesId}`,
                type: 'GET',
                data: { exclude_id: excludeId }, // Kirim exclude_id sebagai parameter
                success: function(response) {
                    if (response.success && response.parentMenus) {
                        response.parentMenus.forEach(function(menu) {
                            // Gunakan display_name yang sudah diproses di server
                            targetSelect.append(`
                                <option value="${menu.web_menu_id}" data-level="${hakAksesId}">
                                    ${menu.display_name}
                                </option>
                            `);
                        });
                    }
                },
                error: function() {
                    toastr.error('Gagal memuat menu induk');
                }
            });
        }

        // Reset forms dan validasi setelah modal ditutup
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            $('#edit_level_menu').prop('disabled', false);
            
            // Reset dropdown
            $('#edit_parent_id').empty().append('<option value="">-Set Sebagai Menu Utama</option>');
            
            // Reset variabel jenis menu asli
            originalMenuLevel = '';
        });
    });
    </script>
@endpush