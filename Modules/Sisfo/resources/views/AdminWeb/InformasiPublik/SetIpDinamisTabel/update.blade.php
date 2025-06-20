@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Ubah Set Informasi Publik Dinamis Tabel</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateSetIpDinamisTabel" action="{{ url($setIpDinamisTabelUrl . '/updateData/' . $ipMenuUtama->ip_menu_utama_id) }}"
        method="POST" enctype="multipart/form-data">
        @csrf

        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i> Anda dapat mengedit struktur menu secara dinamis. Perubahan akan mempengaruhi hierarki menu dan dokumen yang terkait.
        </div>

        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-folder-open mr-2"></i>Informasi Menu Utama
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_ip_mu">Nama Menu Utama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_ip_mu" name="nama_ip_mu" maxlength="255"
                        value="{{ $ipMenuUtama->nama_ip_mu }}" required>
                    <div class="invalid-feedback" id="nama_ip_mu_error"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kategori Tabel Dinamis</label>
                            <input type="text" class="form-control" readonly 
                                value="{{ $ipMenuUtama->IpDinamisTabel->ip_nama_submenu ?? 'N/A' }} - {{ $ipMenuUtama->IpDinamisTabel->ip_judul ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Menu</label>
                            <div class="form-control-plaintext">
                                @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
                                    <span class="badge badge-info">Memiliki {{ $ipMenuUtama->IpSubMenuUtama->count() }} Sub Menu Utama</span>
                                @else
                                    <span class="badge badge-success">Menu Tunggal (Memiliki Dokumen)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($ipMenuUtama->IpSubMenuUtama->count() == 0)
                    <!-- Menu tanpa children - bisa upload dokumen atau menambah sub menu -->
                    <div class="form-group">
                        <label for="dokumen_ip_mu">Dokumen Menu Utama (PDF)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="dokumen_ip_mu" name="dokumen_ip_mu" accept=".pdf">
                            <label class="custom-file-label" for="dokumen_ip_mu">
                                @if($ipMenuUtama->dokumen_ip_mu)
                                    {{ basename($ipMenuUtama->dokumen_ip_mu) }} (Ganti file)
                                @else
                                    Pilih file PDF (maks. 5 MB)
                                @endif
                            </label>
                        </div>
                        <div class="invalid-feedback" id="dokumen_ip_mu_error"></div>
                        
                        @if($ipMenuUtama->dokumen_ip_mu)
                        <div class="mt-2">
                            <a href="{{ Storage::url($ipMenuUtama->dokumen_ip_mu) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-file-pdf"></i> Lihat Dokumen Saat Ini
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="menambah_sub_menu_utama" name="menambah_sub_menu_utama" value="ya">
                            <label class="form-check-label" for="menambah_sub_menu_utama">
                                <strong>Tambah Sub Menu Utama</strong> (akan menghapus dokumen yang ada)
                            </label>
                        </div>
                    </div>

                    <div id="new_sub_menu_utama_container" style="display: none;">
                        <div class="form-group">
                            <label for="jumlah_sub_menu_utama_baru">Jumlah Sub Menu Utama yang akan ditambahkan</label>
                            <input type="number" class="form-control" id="jumlah_sub_menu_utama_baru" 
                                name="jumlah_sub_menu_utama_baru" min="1" max="10" placeholder="1-10">
                            <div class="invalid-feedback" id="jumlah_sub_menu_utama_baru_error"></div>
                        </div>
                        <div id="new_sub_menu_utama_fields"></div>
                    </div>
                @endif
            </div>
        </div>

        @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
            <!-- Sub Menu Utama yang sudah ada -->
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-folder mr-2"></i>Daftar Sub Menu Utama
                    </h6>
                </div>
                <div class="card-body">
                    <div id="existing_sub_menu_utama_container">
                        @foreach($ipMenuUtama->IpSubMenuUtama as $index => $subMenuUtama)
                            <div class="card border-secondary mb-3 sub-menu-utama-item" data-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-folder mr-2"></i>Sub Menu Utama {{ $index + 1 }}: {{ $subMenuUtama->nama_ip_smu }}
                                    </h6>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-warning edit-sub-menu-utama" 
                                            data-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}"
                                            data-nama="{{ $subMenuUtama->nama_ip_smu }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        @if($subMenuUtama->IpSubMenu->count() == 0)
                                            <button type="button" class="btn btn-danger delete-sub-menu-utama" 
                                                data-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}"
                                                data-nama="{{ $subMenuUtama->nama_ip_smu }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <strong>Status:</strong> 
                                            @if($subMenuUtama->IpSubMenu->count() > 0)
                                                <span class="badge badge-info">{{ $subMenuUtama->IpSubMenu->count() }} Sub Menu</span>
                                            @else
                                                <span class="badge badge-success">Memiliki Dokumen</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-right">
                                            @if($subMenuUtama->dokumen_ip_smu)
                                                <a href="{{ Storage::url($subMenuUtama->dokumen_ip_smu) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-pdf"></i> Lihat Dokumen
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    @if($subMenuUtama->IpSubMenu->count() > 0)
                                        <!-- Daftar Sub Menu -->
                                        <div class="mt-3">
                                            <h6>Sub Menu:</h6>
                                            <div class="list-group">
                                                @foreach($subMenuUtama->IpSubMenu as $subIndex => $subMenu)
                                                    <div class="list-group-item d-flex justify-content-between align-items-center sub-menu-item" 
                                                        data-id="{{ $subMenu->ip_sub_menu_id }}">
                                                        <div>
                                                            <i class="fas fa-file mr-2"></i>{{ $subMenu->nama_ip_sm }}
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            @if($subMenu->dokumen_ip_sm)
                                                                <a href="{{ Storage::url($subMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                            @endif
                                                            <button type="button" class="btn btn-warning edit-sub-menu" 
                                                                data-id="{{ $subMenu->ip_sub_menu_id }}"
                                                                data-nama="{{ $subMenu->nama_ip_sm }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger delete-sub-menu" 
                                                                data-id="{{ $subMenu->ip_sub_menu_id }}"
                                                                data-nama="{{ $subMenu->nama_ip_sm }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-success add-sub-menu" 
                                                    data-parent-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}">
                                                    <i class="fas fa-plus"></i> Tambah Sub Menu
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Sub Menu Utama tanpa children - bisa menambah sub menu -->
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-success add-sub-menu" 
                                                data-parent-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}">
                                                <i class="fas fa-plus"></i> Tambah Sub Menu (akan menghapus dokumen)
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-success" id="add_sub_menu_utama_btn">
                            <i class="fas fa-plus"></i> Tambah Sub Menu Utama Baru
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hidden inputs untuk tracking perubahan -->
        <input type="hidden" name="update_sub_menu_utama" value="1">
        <input type="hidden" name="existing_dokumen" value="{{ $ipMenuUtama->dokumen_ip_mu }}">
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary" form="formUpdateSetIpDinamisTabel">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
    $(document).ready(function () {
        let newItemCounter = 0;

        // Custom file input handler
        $(document).on('change', '.custom-file-input', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file PDF (maks. 5 MB)');
        });

        // Toggle menambah sub menu utama pada menu tanpa children
        $('#menambah_sub_menu_utama').on('change', function() {
            const container = $('#new_sub_menu_utama_container');
            if ($(this).is(':checked')) {
                container.show();
                // Disable dokumen upload
                $('#dokumen_ip_mu').prop('disabled', true);
            } else {
                container.hide();
                $('#new_sub_menu_utama_fields').empty();
                $('#jumlah_sub_menu_utama_baru').val('');
                // Enable dokumen upload
                $('#dokumen_ip_mu').prop('disabled', false);
            }
        });

        // Generate fields untuk sub menu utama baru
        $('#jumlah_sub_menu_utama_baru').on('input', function() {
            const jumlah = parseInt($(this).val());
            const container = $('#new_sub_menu_utama_fields');
            
            if (isNaN(jumlah) || jumlah < 1 || jumlah > 10) {
                container.empty();
                return;
            }

            container.empty();
            for (let i = 1; i <= jumlah; i++) {
                const html = generateSubMenuUtamaForm(i, true);
                container.append(html);
            }
        });

        // Edit Sub Menu Utama
        $(document).on('click', '.edit-sub-menu-utama', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            
            Swal.fire({
                title: 'Edit Sub Menu Utama',
                input: 'text',
                inputValue: nama,
                inputAttributes: {
                    maxlength: 255,
                    required: true
                },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Nama Sub Menu Utama wajib diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add hidden input untuk update
                    const form = $('#formUpdateSetIpDinamisTabel');
                    form.append(`<input type="hidden" name="update_sub_menu_utama_${id}" value="${result.value}">`);
                    
                    // Update display
                    $(this).closest('.card-header').find('h6').html(`<i class="fas fa-folder mr-2"></i>Sub Menu Utama: ${result.value}`);
                    $(this).data('nama', result.value);
                    
                    Swal.fire('Berhasil!', 'Perubahan akan disimpan saat form disubmit', 'success');
                }
            });
        });

        // Delete Sub Menu Utama
        $(document).on('click', '.delete-sub-menu-utama', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus Sub Menu Utama "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add hidden input untuk delete
                    const form = $('#formUpdateSetIpDinamisTabel');
                    form.append(`<input type="hidden" name="delete_sub_menu_utama_${id}" value="1">`);
                    
                    // Hide element
                    $(this).closest('.sub-menu-utama-item').hide();
                    
                    Swal.fire('Berhasil!', 'Sub Menu Utama akan dihapus saat form disubmit', 'success');
                }
            });
        });

        // Edit Sub Menu
        $(document).on('click', '.edit-sub-menu', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            
            Swal.fire({
                title: 'Edit Sub Menu',
                input: 'text',
                inputValue: nama,
                inputAttributes: {
                    maxlength: 255,
                    required: true
                },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Nama Sub Menu wajib diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add hidden input untuk update
                    const form = $('#formUpdateSetIpDinamisTabel');
                    form.append(`<input type="hidden" name="update_sub_menu_${id}" value="${result.value}">`);
                    
                    // Update display
                    const listItem = $(this).closest('.sub-menu-item');
                    listItem.find('div:first').html(`<i class="fas fa-file mr-2"></i>${result.value}`);
                    $(this).data('nama', result.value);
                    
                    Swal.fire('Berhasil!', 'Perubahan akan disimpan saat form disubmit', 'success');
                }
            });
        });

        // Delete Sub Menu
        $(document).on('click', '.delete-sub-menu', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus Sub Menu "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add hidden input untuk delete
                    const form = $('#formUpdateSetIpDinamisTabel');
                    form.append(`<input type="hidden" name="delete_sub_menu_${id}" value="1">`);
                    
                    // Hide element
                    $(this).closest('.sub-menu-item').hide();
                    
                    Swal.fire('Berhasil!', 'Sub Menu akan dihapus saat form disubmit', 'success');
                }
            });
        });

        // Add Sub Menu
        $(document).on('click', '.add-sub-menu', function() {
            const parentId = $(this).data('parent-id');
            
            Swal.fire({
                title: 'Tambah Sub Menu Baru',
                html: `
                    <div class="form-group text-left">
                        <label for="nama_sub_menu_baru">Nama Sub Menu:</label>
                        <input type="text" id="nama_sub_menu_baru" class="form-control" maxlength="255" required>
                    </div>
                    <div class="form-group text-left">
                        <label for="dokumen_sub_menu_baru">Dokumen PDF:</label>
                        <input type="file" id="dokumen_sub_menu_baru" class="form-control" accept=".pdf" required>
                        <small class="text-muted">Maksimal 5 MB</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Tambah',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const nama = document.getElementById('nama_sub_menu_baru').value;
                    const file = document.getElementById('dokumen_sub_menu_baru').files[0];
                    
                    if (!nama) {
                        Swal.showValidationMessage('Nama Sub Menu wajib diisi');
                        return false;
                    }
                    
                    if (!file) {
                        Swal.showValidationMessage('Dokumen PDF wajib diupload');
                        return false;
                    }
                    
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.showValidationMessage('Ukuran file maksimal 5 MB');
                        return false;
                    }
                    
                    return { nama: nama, file: file };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    newItemCounter++;
                    const newId = `new_${newItemCounter}`;
                    
                    // Add to form
                    const form = $('#formUpdateSetIpDinamisTabel');
                    form.append(`<input type="hidden" name="add_sub_menu_parent_${parentId}" value="${newId}">`);
                    form.append(`<input type="hidden" name="add_sub_menu_nama_${newId}" value="${result.value.nama}">`);
                    
                    // Add file input
                    const fileInput = $(`<input type="file" name="add_sub_menu_dokumen_${newId}" style="display:none">`);
                    form.append(fileInput);
                    
                    // Transfer file to hidden input (this is a workaround for file handling)
                    const dt = new DataTransfer();
                    dt.items.add(result.value.file);
                    fileInput[0].files = dt.files;
                    
                    // Update display
                    const parentCard = $(this).closest('.card-body');
                    let listGroup = parentCard.find('.list-group');
                    
                    if (listGroup.length === 0) {
                        parentCard.append(`
                            <div class="mt-3">
                                <h6>Sub Menu:</h6>
                                <div class="list-group"></div>
                            </div>
                        `);
                        listGroup = parentCard.find('.list-group');
                    }
                    
                    listGroup.append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center" data-id="${newId}">
                            <div><i class="fas fa-file mr-2"></i>${result.value.nama} <span class="badge badge-success">Baru</span></div>
                            <button type="button" class="btn btn-sm btn-danger remove-new-item" data-id="${newId}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                    
                    Swal.fire('Berhasil!', 'Sub Menu baru akan ditambahkan saat form disubmit', 'success');
                }
            });
        });

        // Remove new item before submit
        $(document).on('click', '.remove-new-item', function() {
            const id = $(this).data('id');
            const form = $('#formUpdateSetIpDinamisTabel');
            
            // Remove related hidden inputs
            form.find(`input[name*="${id}"]`).remove();
            
            // Remove from display
            $(this).closest('.list-group-item').remove();
        });

        // Add Sub Menu Utama Baru
        $('#add_sub_menu_utama_btn').on('click', function() {
            newItemCounter++;
            const newId = `new_smu_${newItemCounter}`;
            
            const html = `
                <div class="card border-secondary mb-3 new-sub-menu-utama-item" data-id="${newId}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-folder mr-2"></i>Sub Menu Utama Baru
                            <span class="badge badge-success ml-2">Baru</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-danger remove-new-sub-menu-utama" data-id="${newId}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Sub Menu Utama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="add_sub_menu_utama_nama_${newId}" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Apakah akan memiliki Sub Menu?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="add_sub_menu_utama_type_${newId}" value="dokumen" checked>
                                <label class="form-check-label">Tidak (Upload dokumen)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="add_sub_menu_utama_type_${newId}" value="submenu">
                                <label class="form-check-label">Ya (Akan ada Sub Menu)</label>
                            </div>
                        </div>
                        <div class="dokumen-section">
                            <div class="form-group">
                                <label>Dokumen PDF <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="add_sub_menu_utama_dokumen_${newId}" accept=".pdf" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#existing_sub_menu_utama_container').append(html);
            
            // Add hidden input untuk tracking
            const form = $('#formUpdateSetIpDinamisTabel');
            form.append(`<input type="hidden" name="add_sub_menu_utama" value="${newId}">`);
        });

        // Remove new sub menu utama
        $(document).on('click', '.remove-new-sub-menu-utama', function() {
            const id = $(this).data('id');
            const form = $('#formUpdateSetIpDinamisTabel');
            
            // Remove related hidden inputs
            form.find(`input[name*="${id}"]`).remove();
            
            // Remove from display
            $(this).closest('.new-sub-menu-utama-item').remove();
        });

        // Handle radio button change for new sub menu utama
        $(document).on('change', 'input[name*="add_sub_menu_utama_type_"]', function() {
            const container = $(this).closest('.card-body');
            const dokumenSection = container.find('.dokumen-section');
            const dokumenInput = container.find('input[type="file"]');
            
            if ($(this).val() === 'dokumen') {
                dokumenSection.show();
                dokumenInput.prop('required', true);
            } else {
                dokumenSection.hide();
                dokumenInput.prop('required', false);
            }
        });

        // Helper function
        function generateSubMenuUtamaForm(index, isNew = false) {
            const prefix = isNew ? 'new_sub_menu_utama' : 'sub_menu_utama';
            return `
                <div class="card border-secondary mb-2">
                    <div class="card-header">
                        <h6 class="mb-0">Sub Menu Utama ${index}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Sub Menu Utama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="${prefix}_nama_${index}" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Dokumen PDF <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="${prefix}_dokumen_${index}" accept=".pdf" required>
                        </div>
                    </div>
                </div>
            `;
        }

        // Form submission
        $('button[form="formUpdateSetIpDinamisTabel"]').on('click', function(e) {
            e.preventDefault();
            const form = $('#formUpdateSetIpDinamisTabel');
            const button = $(this);
            const formData = new FormData(form[0]);

            // Reset errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            // Loading state
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
                            text: response.message || 'Set Informasi Publik Dinamis Tabel berhasil diperbarui'
                        });
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).html(value[0]);
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
                    button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                }
            });
        });

        // Remove error on input change
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });
    });
</script>

<style>
    .sub-menu-utama-item, .new-sub-menu-utama-item {
        transition: all 0.3s ease;
    }
    
    .sub-menu-utama-item:hover, .new-sub-menu-utama-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.8em;
    }
    
    .card-header h6 {
        font-weight: 600;
    }
</style>