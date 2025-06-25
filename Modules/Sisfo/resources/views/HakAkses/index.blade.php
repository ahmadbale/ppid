@extends('sisfo::layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Hak Akses</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-all">
                    <i class="fas fa-save"></i> Simpan Semua Perubahan (Tombol Perubahan Pengaturan Hak Akses Per User)
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-info-circle"></i> Informasi!</h5>
                    {{ session('info') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-3">Pengaturan Hak Akses Berdasarkan Level</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($levelUsers as $hakAksesKode => $levelData)
                            <div class="col-md-4 mb-2">
                                <button class="btn btn-warning btn-block text-center set-hak-level"
                                    data-level="{{ $hakAksesKode }}" data-name="{{ $levelData['nama'] }}">
                                    <strong>{{ $levelData['nama'] }}</strong>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- Modal untuk Pengaturan Hak Akses Per Level -->
                <div class="modal fade" id="modalHakAksesLevel" tabindex="-1" aria-labelledby="modalTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTitle">Pengaturan Hak Akses untuk <span
                                        id="levelTitle"></span>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="formHakAksesLevel">
                                    @csrf
                                    <input type="hidden" id="hakAksesKode" name="hak_akses_kode">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Menu Utama</th>
                                                <th>Sub Menu</th>
                                                <th class="text-center">Tampil Menu</th>
                                                <th class="text-center">Lihat</th>
                                                <th class="text-center">Tambah</th>
                                                <th class="text-center">Ubah</th>
                                                <th class="text-center">Hapus</th>
                                            </tr>
                                        </thead>
                                        <tbody id="menuList">
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" id="btnSimpanHakAksesLevel">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-footer">
                <h5 class="modal-title" id="modalTitle">Pengaturan Hak Akses Untuk Setiap User Berdasarkan Level
                    User<span id="levelTitle"></span>
                </h5>
            </div>

            <form action="{{ url('/HakAkses/updateData') }}" method="POST" id="form-hak-akses">
                @csrf
                <div class="accordion" id="accordionHakAkses">
                    @foreach($levelUsers as $hakAksesKode => $levelData)
                        <div class="card">
                            <div class="card-header" id="heading{{ $hakAksesKode }}">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                        data-target="#collapse{{ $hakAksesKode }}" aria-expanded="true"
                                        aria-controls="collapse{{ $hakAksesKode }}">
                                        <strong>{{ $levelData['nama'] }} ({{ $hakAksesKode }})</strong>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapse{{ $hakAksesKode }}" class="collapse" aria-labelledby="heading{{ $hakAksesKode }}"
                                data-parent="#accordionHakAkses">
                                <div class="card-body">
                                    @foreach($levelData['menus'] as $kategori => $submenus)
                                        <div class="menu-category mb-4">
                                            <h5>{{ $kategori }}</h5>
                                            <hr>

                                            @foreach($submenus as $submenuName => $submenuId)
                                                <div class="submenu-item mb-4">
                                                    <h6 class="text-muted">* {{ $submenuName }}</h6>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 5%">No</th>
                                                                    <th style="width: 30%">Nama Pengguna</th>
                                                                    <th style="width: 13%">Tampil Menu</th>
                                                                    <th style="width: 13%">Lihat</th>
                                                                    <th style="width: 13%">Tambah</th>
                                                                    <th style="width: 13%">Ubah</th>
                                                                    <th style="width: 13%">Hapus</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($levelData['users'] as $index => $user)
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td>{{ $user->nama_pengguna }}</td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_menu"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="menu_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_menu"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="menu">
                                                                                <label class="custom-control-label"
                                                                                    for="menu_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_view"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="view_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_view"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="view">
                                                                                <label class="custom-control-label"
                                                                                    for="view_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_create"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="create_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_create"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="create">
                                                                                <label class="custom-control-label"
                                                                                    for="create_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_update"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="update_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_update"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="update">
                                                                                <label class="custom-control-label"
                                                                                    for="update_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_delete"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="delete_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_delete"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="delete">
                                                                                <label class="custom-control-label"
                                                                                    for="delete_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
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
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Pastikan document ready hanya dipanggil sekali
        $(function () {
            // Tambahkan flag untuk mencegah submit ganda
            let isSubmitting = false;
            
            let changedCheckboxes = new Set();

            // Set handler untuk tombol set-hak-level menggunakan event delegation
            $(document).off('click', '.set-hak-level').on('click', '.set-hak-level', function () {
                let hakAksesKode = $(this).data('level');
                $('#hakAksesKode').val(hakAksesKode);
                $('#levelTitle').text($(this).data('name'));

                console.log('Loading hak akses for level:', hakAksesKode);

                // Tampilkan loading spinner atau pesan
                $('#menuList').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

                // Gunakan AJAX untuk mendapatkan data hak akses level
                $.ajax({
                    url: `{{ url('/HakAkses/getHakAksesData') }}/${hakAksesKode}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log('Received data for level:', data);
                        let html = '';

                        if (Object.keys(data).length === 0) {
                            html = '<tr><td colspan="7" class="text-center">Tidak ada menu yang tersedia</td></tr>';
                        } else {
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
                        }

                        $('#menuList').html(html);
                        $('#modalHakAksesLevel').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading hak akses level:', error, xhr.responseText);
                        $('#menuList').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>');
                        toastr.error("Terjadi kesalahan saat memuat data hak akses.");
                    }
                });
            });

            // PERBAIKAN: Handler untuk tombol Simpan di modal hak akses level
            $('#btnSimpanHakAksesLevel').off('click').on('click', function () {
                // Cek apakah sedang dalam proses submit
                if (isSubmitting) return;

                // Set flag submit
                isSubmitting = true;

                // Ubah teks tombol untuk indikasi loading
                const $btn = $(this);
                const originalText = $btn.text();
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $btn.prop('disabled', true);

                // PERBAIKAN: Serialize data dengan benar untuk level
                var formData = {};
                var hakAksesKode = $('#hakAksesKode').val();
                formData['_token'] = $('input[name="_token"]').val();
                formData['hak_akses_kode'] = hakAksesKode;
                formData['menu_akses'] = {};

                // Ambil semua checkbox dalam modal
                $('#modalHakAksesLevel input[type="checkbox"]').each(function() {
                    var name = $(this).attr('name');
                    var isChecked = $(this).is(':checked');
                    
                    if (name && name.match(/menu_akses\[(\d+)\]\[(\w+)\]/)) {
                        var matches = name.match(/menu_akses\[(\d+)\]\[(\w+)\]/);
                        var menuId = matches[1];
                        var permission = matches[2];
                        
                        if (!formData['menu_akses'][menuId]) {
                            formData['menu_akses'][menuId] = {};
                        }
                        
                        // Hanya set nilai jika checkbox dicentang
                        if (isChecked) {
                            formData['menu_akses'][menuId][permission] = '1';
                        }
                        // Jika tidak dicentang, tidak perlu set nilai (akan default ke 0 di server)
                    }
                });

                $.ajax({
                    url: `{{ url('/HakAkses/updateData') }}`,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            // Reset tombol
                            $btn.html(originalText);
                            $btn.prop('disabled', false);
                            isSubmitting = false;
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = "Terjadi kesalahan, silakan coba lagi.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                        // Reset tombol
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                        isSubmitting = false;
                    }
                });
            });

            // PERBAIKAN: Track perubahan pada checkbox individual
            $(document).on('change', '.hak-akses-checkbox', function() {
                var checkboxName = $(this).attr('name');
                changedCheckboxes.add(checkboxName);
                console.log('Checkbox changed:', checkboxName, 'Checked:', $(this).is(':checked'));
            });

            // Muat hak akses saat halaman dimuat
            loadAllHakAkses();

            // PERBAIKAN: Handler untuk tombol simpan semua perubahan (individual)
            $('#btn-save-all').off('click').on('click', function (e) {
                e.preventDefault();

                // Cek apakah sedang dalam proses submit
                if (isSubmitting) return;

                // Set flag submit
                isSubmitting = true;

                // Ubah teks tombol untuk indikasi loading
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $btn.prop('disabled', true);

                // PERBAIKAN: Hanya kirim data yang berubah
                var formData = {};
                formData['_token'] = $('input[name="_token"]').val();

                // Ambil hanya checkbox yang berubah
                changedCheckboxes.forEach(function(checkboxName) {
                    var $checkbox = $('input[name="' + checkboxName + '"]');
                    if ($checkbox.length > 0) {
                        var isChecked = $checkbox.is(':checked');
                        // Set nilai berdasarkan status checkbox
                        formData[checkboxName] = isChecked ? '1' : '0';
                        console.log('Sending changed data:', checkboxName, '=', formData[checkboxName]);
                    }
                });

                // Jika tidak ada perubahan
                if (Object.keys(formData).length <= 1) { // <= 1 karena ada _token
                    toastr.info('Tidak ada perubahan yang perlu disimpan');
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                    isSubmitting = false;
                    return;
                }

                console.log('Form data yang akan dikirim:', formData);

                $.ajax({
                    url: $('#form-hak-akses').attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Reset tracking perubahan
                            changedCheckboxes.clear();
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            // Reset tombol
                            $btn.html(originalText);
                            $btn.prop('disabled', false);
                            isSubmitting = false;
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = "Terjadi kesalahan, silakan coba lagi.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                        // Reset tombol
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                        isSubmitting = false;
                    }
                });
            });

            // Fungsi untuk memuat hak akses
            function loadAllHakAkses() {
                // Kumpulkan semua checkbox dalam satu array untuk meminimalkan jumlah request
                const checkboxes = [];
                $('.hak-akses-checkbox').each(function () {
                    const userId = $(this).data('user');
                    const menuId = $(this).data('menu');
                    const hak = $(this).data('hak');
                    checkboxes.push({
                        userId: userId,
                        menuId: menuId,
                        hak: hak,
                        element: this
                    });
                });

                // Buat fungsi untuk memproses checkbox secara batch
                function processCheckboxes(start, batchSize) {
                    const end = Math.min(start + batchSize, checkboxes.length);
                    const currentBatch = checkboxes.slice(start, end);

                    if (currentBatch.length === 0) return; // Semua sudah diproses

                    // Proses batch saat ini
                    const requests = currentBatch.map(item => {
                        return $.ajax({
                            url: `{{ url('/HakAkses/getHakAksesData') }}/${item.userId}/${item.menuId}`,
                            type: 'GET',
                            dataType: 'json'
                        }).then(data => {
                            // Periksa apakah data ditemukan dan nilai hak akses adalah 1
                            if (data && data['ha_' + item.hak] === 1) {
                                $(item.element).prop('checked', true);
                            } else {
                                $(item.element).prop('checked', false);
                            }
                        }).catch(error => {
                            console.error(`Error loading hak akses for user ${item.userId}, menu ${item.menuId}:`, error);
                            $(item.element).prop('checked', false);
                        });
                    });

                    // Setelah batch saat ini selesai, proses batch berikutnya
                    $.when.apply($, requests).always(() => {
                        if (end < checkboxes.length) {
                            setTimeout(() => {
                                processCheckboxes(end, batchSize);
                            }, 100); // Tambahkan sedikit delay untuk menghindari overload
                        }
                    });
                }

                // Mulai memproses checkbox dalam batch (50 per batch)
                processCheckboxes(0, 50);
            }

            // Toggle collapse untuk semua menu saat pertama kali
            $('.collapse').first().addClass('show');

            // Tambahkan toastr jika belum ada
            if (typeof toastr === 'undefined') {
                toastr = {
                    success: function (message) { alert('Sukses: ' + message); },
                    error: function (message) { alert('Error: ' + message); },
                    info: function (message) { alert('Info: ' + message); }
                };
            }
        });
    </script>
@endpush