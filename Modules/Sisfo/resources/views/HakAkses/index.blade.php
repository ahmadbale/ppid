@extends('sisfo::layouts.template')

@section('content')
<div class="text-right mt-3">
    <button type="submit" class="btn btn-success" id="btn-save-all">
        <i class="fas fa-save"></i> Simpan Semua Perubahan
    </button>
</div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-info-circle"></i> Informasi!</h5>
                {{ session('info') }}
            </div>
        @endif

        <!-- Card Pilih Level -->
        <div class="card mt-3">
            <div class="card-header bg-primeri">
                <h3 class="card-title ">Kelola Akses Tiap Level</h3>
            </div>
            <div class="card-body">
                <div class="row row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    @foreach ($levelUsers as $levelKode => $levelData)
                        <div class="col-12 col-sm-6 col-md-4 mb-3">
                            <button class="btn btn-outline-danger btn-block set-hak-level" data-level="{{ $levelKode }}"
                                data-name="{{ $levelData['nama'] }}">
                                <strong>{{ $levelData['nama'] }}</strong>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Modal Pengaturan Hak Akses -->
        <div class="modal fade" id="modalHakAksesLevel" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header bg-primeri text-white">
                        <h5 class="modal-title" id="modalTitle">Pengaturan Hak Akses level: <span id="levelTitle"
                                class="text-bold"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="formHakAksesLevel">
                            @csrf
                            <input type="hidden" id="levelKode" name="level_kode">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th>Menu</th>
                                            <th>Tampil</th>
                                            <th>Lihat</th>
                                            <th>Tambah</th>
                                            <th>Ubah</th>
                                            <th>Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="menuList">
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanHakAksesLevel">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-primeri">
                <h5 class="modal-title" id="modalTitle">Kelola Hak Akses Tiap User
                    User<span id="levelTitle"></span>
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ url('/HakAkses/updateData') }}" method="POST" id="form-hak-akses">
                    @csrf

                    @foreach ($levelUsers as $levelKode => $levelData)
                        <div class="card card-outline card-danger collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    {{ $levelData['nama'] }} ({{ $levelKode }})
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                @foreach ($levelData['menus'] as $kategori => $submenus)
                                    <div class="menu-category mb-4">
                                        <h5>{{ $kategori }}</h5>
                                        <hr>

                                        @foreach ($submenus as $submenuName => $submenuId)
                                            <div class="submenu-item mb-4">
                                                <h6 class="text-muted">* {{ $submenuName }}</h6>

                                                <div class="table-responsive table-responsive-stack">
                                                    <table class="table align-middle table-bordered table-striped">
                                                        <thead class="text-center">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Pengguna</th>
                                                                <th>Tampil Menu</th>
                                                                <th>Lihat</th>
                                                                <th>Tambah</th>
                                                                <th>Ubah</th>
                                                                <th>Hapus</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($levelData['users'] as $index => $user)
                                                                <tr>
                                                                    <td table-data-label="No" class="text-center">
                                                                        {{ $index + 1 }}</td>
                                                                    <td table-data-label="Nama Pengguna" class="text-center">
                                                                        {{ $user->nama_pengguna }}</td>

                                                                    @foreach (['menu', 'view', 'create', 'update', 'delete'] as $hak)
                                                                        <td table-data-label="{{ ucfirst($hak) }}"
                                                                            class="text-center">
                                                                            <input type="hidden"
                                                                                name="hak_akses_{{ $user->user_id }}_{{ $submenuId }}_{{ $hak }}"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="{{ $hak }}_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="hak_akses_{{ $user->user_id }}_{{ $submenuId }}_{{ $hak }}"
                                                                                    value="1"
                                                                                    data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}"
                                                                                    data-hak="{{ $hak }}">
                                                                                <label class="custom-control-label"
                                                                                    for="{{ $hak }}_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Hilangkan tombol "Tambah Hak Akses" karena fitur ini tidak diperlukan lagi sesuai revisi

            $(document).on('click', '.set-hak-level', function() {
                let levelKode = $(this).data('level');
                $('#levelKode').val(levelKode);
                $('#levelTitle').text($(this).data('name'));

                $.ajax({
                    url: `{{ url('/HakAkses/getHakAksesData') }}/${levelKode}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let html = '';

                        Object.keys(data).forEach(menu_id => {
                            let akses = data[menu_id];

                            // Tambahkan baris untuk ha_menu di modal hak akses level
                            html += `
                            <tr>
                                <td>${akses.menu_utama}</td>
                                <td>${akses.sub_menu ?? 'Null'}</td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][menu]" ${akses.ha_menu ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][view]" ${akses.ha_view ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][create]" ${akses.ha_create ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][update]" ${akses.ha_update ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][delete]" ${akses.ha_delete ? 'checked' : ''}></td>
                            </tr>
                            `;
                        });

                        $('#menuList').html(html);
                        $('#modalHakAksesLevel').modal('show');
                    },
                    error: function() {
                        alert("Terjadi kesalahan, silakan coba lagi.");
                    }
                });
            });

            $('#btnSimpanHakAksesLevel').click(function() {
                $.ajax({
                    url: `{{ url('/HakAkses/updateData') }}`,
                    type: 'POST',
                    data: $('#formHakAksesLevel').serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("Terjadi kesalahan, silakan coba lagi.");
                    }
                });
            });

            // Muat hak akses saat halaman dimuat
            loadAllHakAkses();

            // Simpan semua perubahan
            $('#btn-save-all').click(function() {
                $('#form-hak-akses').submit();
            });

            // Fungsi untuk memuat hak akses
            function loadAllHakAkses() {
                $('.hak-akses-checkbox').each(function() {
                    const userId = $(this).data('user');
                    const menuId = $(this).data('menu');
                    const hak = $(this).data('hak');
                    const checkbox = $(this);

                    // Gunakan AJAX untuk mendapatkan data hak akses
                    $.ajax({
                        url: `{{ url('/HakAkses/getHakAksesData') }}/${userId}/${menuId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Periksa apakah data ditemukan dan nilai hak akses adalah 1
                            if (data && data['ha_' + hak] === 1) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        },
                        error: function(error) {
                            console.error('Error loading hak akses:', error);
                            checkbox.prop('checked',
                                false); // Default tidak dicentang jika error
                        }
                    });
                });
            }

            // Toggle collapse untuk semua menu saat pertama kali
            $('.collapse').first().addClass('show');

            // Tambahkan toastr jika belum ada
            if (typeof toastr === 'undefined') {
                toastr = {
                    success: function(message) {
                        alert('Sukses: ' + message);
                    },
                    error: function(message) {
                        alert('Error: ' + message);
                    },
                    info: function(message) {
                        alert('Info: ' + message);
                    }
                };
            }
        });
    </script>
@endpush
