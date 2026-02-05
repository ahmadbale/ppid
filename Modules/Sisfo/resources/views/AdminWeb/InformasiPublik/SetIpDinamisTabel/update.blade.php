@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Illuminate\Support\Facades\Storage;
  $setIpDinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel');
  
  // Detect type: menu, submenu_utama, or submenu
  $type = 'menu';
  $title = 'Ubah Menu Utama';
  $formId = 'formUpdateMenuUtama';
  $buttonColor = 'primary';
  $cardColor = 'primary';
  $iconColor = 'primary';
  
  if (isset($ipSubMenu)) {
      $type = 'submenu';
      $title = 'Ubah Sub Menu: ' . $ipSubMenu->nama_ip_sm;
      $formId = 'formUpdateSubMenu';
      $buttonColor = 'info';
      $cardColor = 'info';
      $iconColor = 'info';
  } elseif (isset($ipSubMenuUtama)) {
      $type = 'submenu_utama';
      $title = 'Ubah Sub Menu Utama: ' . $ipSubMenuUtama->nama_ip_smu;
      $formId = 'formUpdateSubMenuUtama';
      $buttonColor = 'success';
      $cardColor = 'success';
      $iconColor = 'success';
  } elseif (isset($ipMenuUtama)) {
      $title = 'Ubah Menu Utama: ' . $ipMenuUtama->nama_ip_mu;
  }
@endphp
<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    @if($type == 'submenu')
        {{-- SUB MENU FORM --}}
        <form id="{{ $formId }}" action="{{ url($setIpDinamisTabelUrl . '/updateData/' . $ipSubMenu->ip_sub_menu_id . '?type=submenu') }}"
            method="POST" enctype="multipart/form-data">
            @csrf

            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> 
                <strong>Menu Utama:</strong> {{ $ipSubMenu->IpSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }} <br>
                <strong>Sub Menu Utama:</strong> {{ $ipSubMenu->IpSubMenuUtama->nama_ip_smu ?? 'N/A' }} <br>
                <strong>Sub Menu:</strong> {{ $ipSubMenu->nama_ip_sm }}
            </div>

            <div class="card border-{{ $cardColor }} mb-3">
                <div class="card-header bg-{{ $cardColor }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-edit mr-2"></i>Informasi Sub Menu
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_ip_sm">Nama Sub Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_ip_sm" name="nama_ip_sm" maxlength="255"
                            value="{{ $ipSubMenu->nama_ip_sm }}" required>
                        <div class="invalid-feedback" id="nama_ip_sm_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="dokumen_ip_sm">Dokumen Sub Menu (PDF)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="dokumen_ip_sm" name="dokumen_ip_sm" accept=".pdf">
                            <label class="custom-file-label" for="dokumen_ip_sm">
                                @if($ipSubMenu->dokumen_ip_sm)
                                    {{ basename($ipSubMenu->dokumen_ip_sm) }} (Ganti file)
                                @else
                                    Pilih file PDF (maks. 5 MB)
                                @endif
                            </label>
                        </div>
                        <div class="invalid-feedback" id="dokumen_ip_sm_error"></div>
                        <small class="text-muted">Format: PDF, Ukuran maksimal: 5 MB</small>
                        
                        @if($ipSubMenu->dokumen_ip_sm)
                        <div class="mt-2">
                            <a href="{{ Storage::url($ipSubMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-file-pdf"></i> Lihat Dokumen Saat Ini
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <input type="hidden" name="existing_dokumen" value="{{ $ipSubMenu->dokumen_ip_sm }}">
        </form>

    @elseif($type == 'submenu_utama')
        {{-- SUB MENU UTAMA FORM --}}
        <form id="{{ $formId }}" action="{{ url($setIpDinamisTabelUrl . '/updateData/' . $ipSubMenuUtama->ip_sub_menu_utama_id . '?type=submenu_utama') }}"
            method="POST" enctype="multipart/form-data">
            @csrf

            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> 
                <strong>Menu Utama:</strong> {{ $ipSubMenuUtama->IpMenuUtama->nama_ip_mu ?? 'N/A' }} <br>
                <strong>Sub Menu Utama:</strong> {{ $ipSubMenuUtama->nama_ip_smu }}
            </div>

            <div class="card border-{{ $cardColor }} mb-3">
                <div class="card-header bg-{{ $cardColor }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-folder-open mr-2"></i>Informasi Sub Menu Utama
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_ip_smu">Nama Sub Menu Utama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_ip_smu" name="nama_ip_smu" maxlength="255"
                            value="{{ $ipSubMenuUtama->nama_ip_smu }}" required>
                        <div class="invalid-feedback" id="nama_ip_smu_error"></div>
                    </div>

                    @if($ipSubMenuUtama->IpSubMenu->count() > 0)
                        {{-- Sub Menu Utama dengan children (memiliki sub menu) --}}
                        <div class="alert alert-success">
                            <i class="fas fa-sitemap mr-2"></i>
                            Sub Menu Utama ini memiliki {{ $ipSubMenuUtama->IpSubMenu->count() }} Sub Menu
                        </div>

                        {{-- Daftar Sub Menu yang ada --}}
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-file mr-2"></i>Daftar Sub Menu
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="existing_sub_menu_container">
                                    @foreach($ipSubMenuUtama->IpSubMenu as $index => $subMenu)
                                        <div class="card border-secondary mb-3 sub-menu-item" data-id="{{ $subMenu->ip_sub_menu_id }}">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-file mr-2"></i>{{ $subMenu->nama_ip_sm }}
                                                </h6>
                                                <div class="btn-group btn-group-sm">
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
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <strong>Status:</strong> 
                                                        @if($subMenu->dokumen_ip_sm)
                                                            <span class="badge badge-success">Memiliki Dokumen</span>
                                                        @else
                                                            <span class="badge badge-warning">Tidak ada dokumen</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        @if($subMenu->dokumen_ip_sm)
                                                            <a href="{{ Storage::url($subMenu->dokumen_ip_sm) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-file-pdf"></i> Lihat Dokumen
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3 text-center">
                                    <button type="button" class="btn btn-info" id="add_sub_menu_btn">
                                        <i class="fas fa-plus"></i> Tambah Sub Menu Baru
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Field untuk dokumen jika semua sub menu dihapus --}}
                        <div id="dokumen_required_container" style="display: none;">
                            <div class="form-group">
                                <label for="dokumen_ip_smu">Dokumen Sub Menu Utama (PDF) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="dokumen_ip_smu" name="dokumen_ip_smu" accept=".pdf" required>
                                    <label class="custom-file-label" for="dokumen_ip_smu">Pilih file PDF (maks. 5 MB)</label>
                                </div>
                                <div class="invalid-feedback" id="dokumen_ip_smu_error"></div>
                                <small class="text-danger">Wajib upload dokumen karena tidak memiliki Sub Menu</small>
                            </div>
                        </div>

                    @else
                        {{-- Sub Menu Utama tanpa children (tidak memiliki sub menu) --}}
                        <div class="alert alert-warning">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Sub Menu Utama ini memiliki dokumen PDF
                        </div>

                        <div class="form-group">
                            <label for="dokumen_ip_smu">Dokumen Sub Menu Utama (PDF)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="dokumen_ip_smu" name="dokumen_ip_smu" accept=".pdf">
                                <label class="custom-file-label" for="dokumen_ip_smu">
                                    @if($ipSubMenuUtama->dokumen_ip_smu)
                                        {{ basename($ipSubMenuUtama->dokumen_ip_smu) }} (Ganti file)
                                    @else
                                        Pilih file PDF (maks. 5 MB)
                                    @endif
                                </label>
                            </div>
                            <div class="invalid-feedback" id="dokumen_ip_smu_error"></div>
                            
                            @if($ipSubMenuUtama->dokumen_ip_smu)
                            <div class="mt-2">
                                <a href="{{ Storage::url($ipSubMenuUtama->dokumen_ip_smu) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-pdf"></i> Lihat Dokumen Saat Ini
                                </a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="menambah_sub_menu" name="menambah_sub_menu" value="ya">
                                <label class="form-check-label" for="menambah_sub_menu">
                                    <strong>Tambah Sub Menu</strong> (akan menghapus dokumen yang ada)
                                </label>
                            </div>
                        </div>

                        <div id="new_sub_menu_container" style="display: none;">
                            <div class="form-group">
                                <label for="jumlah_sub_menu_baru">Jumlah Sub Menu yang akan ditambahkan</label>
                                <input type="number" class="form-control" id="jumlah_sub_menu_baru" 
                                    name="jumlah_sub_menu_baru" min="1" max="10" placeholder="1-10">
                                <div class="invalid-feedback" id="jumlah_sub_menu_baru_error"></div>
                            </div>
                            <div id="new_sub_menu_fields"></div>
                        </div>
                    @endif
                </div>
            </div>

            <input type="hidden" name="existing_dokumen" value="{{ $ipSubMenuUtama->dokumen_ip_smu }}">
        </form>

    @else
        {{-- MENU UTAMA FORM --}}
        <form id="{{ $formId }}" action="{{ url($setIpDinamisTabelUrl . '/updateData/' . $ipMenuUtama->ip_menu_utama_id) }}"
            method="POST" enctype="multipart/form-data">
            @csrf

            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> 
                <strong>Menu Utama:</strong> {{ $ipMenuUtama->nama_ip_mu }} <br>
                <strong>Kategori:</strong> {{ $ipMenuUtama->IpDinamisTabel->ip_nama_submenu ?? 'N/A' }}
            </div>

            <div class="card border-{{ $cardColor }} mb-3">
                <div class="card-header bg-{{ $cardColor }} text-white">
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

                    @if($ipMenuUtama->IpSubMenuUtama->count() > 0)
                        {{-- Menu Utama dengan children (memiliki sub menu utama) --}}
                        <div class="alert alert-success">
                            <i class="fas fa-sitemap mr-2"></i>
                            Menu ini memiliki {{ $ipMenuUtama->IpSubMenuUtama->count() }} Sub Menu Utama
                        </div>

                        {{-- Daftar Sub Menu Utama yang ada --}}
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
                                                    <i class="fas fa-folder mr-2"></i>{{ $subMenuUtama->nama_ip_smu }}
                                                </h6>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-warning edit-sub-menu-utama" 
                                                        data-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}"
                                                        data-nama="{{ $subMenuUtama->nama_ip_smu }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if($subMenuUtama->IpSubMenu->count() == 0)
                                                        <button type="button" class="btn btn-danger delete-sub-menu-utama" 
                                                            data-id="{{ $subMenuUtama->ip_sub_menu_utama_id }}"
                                                            data-nama="{{ $subMenuUtama->nama_ip_smu }}">
                                                            <i class="fas fa-trash"></i>
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

                        {{-- Field untuk dokumen jika semua sub menu utama dihapus --}}
                        <div id="dokumen_required_container" style="display: none;">
                            <div class="form-group">
                                <label for="dokumen_ip_mu">Dokumen Menu Utama (PDF) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="dokumen_ip_mu" name="dokumen_ip_mu" accept=".pdf" required>
                                    <label class="custom-file-label" for="dokumen_ip_mu">Pilih file PDF (maks. 5 MB)</label>
                                </div>
                                <div class="invalid-feedback" id="dokumen_ip_mu_error"></div>
                                <small class="text-danger">Wajib upload dokumen karena tidak memiliki Sub Menu Utama</small>
                            </div>
                        </div>

                    @else
                        {{-- Menu Utama tanpa children (tidak memiliki sub menu utama) --}}
                        <div class="alert alert-warning">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Menu ini memiliki dokumen PDF
                        </div>

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

            <input type="hidden" name="existing_dokumen" value="{{ $ipMenuUtama->dokumen_ip_mu }}">
        </form>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-{{ $buttonColor }}" form="{{ $formId }}">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
    $(document).ready(function () {
        const pageType = '{{ $type }}';
        const formId = '#{{ $formId }}';
        let newItemCounter = 0;
        
        @if($type == 'menu')
            let hasChildren = {{ $ipMenuUtama->IpSubMenuUtama->count() > 0 ? 'true' : 'false' }};
        @elseif($type == 'submenu_utama')
            let hasChildren = {{ $ipSubMenuUtama->IpSubMenu->count() > 0 ? 'true' : 'false' }};
        @else
            let hasChildren = false;
        @endif

        // Custom file input handler
        $(document).on('change', '.custom-file-input', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file PDF (maks. 5 MB)');
        });

        // MENU UTAMA: Edit Sub Menu Utama
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
                    const form = $(formId);
                    form.append(`<input type="hidden" name="update_sub_menu_utama_${id}" value="${result.value}">`);
                    
                    $(this).closest('.card-header').find('h6').html(`<i class="fas fa-folder mr-2"></i>${result.value}`);
                    $(this).data('nama', result.value);
                    
                    Swal.fire('Berhasil!', 'Perubahan akan disimpan saat form disubmit', 'success');
                }
            });
        });

        // MENU UTAMA: Delete Sub Menu Utama
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
                    const form = $(formId);
                    form.append(`<input type="hidden" name="delete_sub_menu_utama_${id}" value="1">`);
                    
                    $(this).closest('.sub-menu-utama-item').hide();
                    
                    const visibleItems = $('.sub-menu-utama-item:visible').length;
                    if (visibleItems === 0) {
                        $('#dokumen_required_container').show();
                    }
                    
                    Swal.fire('Berhasil!', 'Sub Menu Utama akan dihapus saat form disubmit', 'success');
                }
            });
        });

        // MENU UTAMA: Add Sub Menu Utama
        $('#add_sub_menu_utama_btn').on('click', function() {
            newItemCounter++;
            const newId = `new_${newItemCounter}`;
            
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
                            <input type="text" class="form-control" name="new_sub_menu_utama_nama_${newId}" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Apakah akan memiliki Sub Menu?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="new_sub_menu_utama_type_${newId}" value="dokumen" checked>
                                <label class="form-check-label">Tidak (Upload dokumen)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="new_sub_menu_utama_type_${newId}" value="submenu">
                                <label class="form-check-label">Ya (Akan ada Sub Menu)</label>
                            </div>
                        </div>
                        <div class="dokumen-section">
                            <div class="form-group">
                                <label>Dokumen PDF <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="new_sub_menu_utama_dokumen_${newId}" accept=".pdf" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#existing_sub_menu_utama_container').append(html);
            
            const form = $(formId);
            let newSubMenus = form.find('input[name="new_sub_menu_utama[]"]').val();
            if (!newSubMenus) {
                form.append(`<input type="hidden" name="new_sub_menu_utama[]" value="${newId}">`);
            } else {
                newSubMenus += `,${newId}`;
                form.find('input[name="new_sub_menu_utama[]"]').val(newSubMenus);
            }
        });

        // MENU UTAMA: Remove new sub menu utama
        $(document).on('click', '.remove-new-sub-menu-utama', function() {
            const id = $(this).data('id');
            const form = $(formId);
            
            form.find(`input[name*="${id}"]`).remove();
            $(this).closest('.new-sub-menu-utama-item').remove();
        });

        // MENU UTAMA: Handle radio button change for new sub menu utama
        $(document).on('change', 'input[name*="new_sub_menu_utama_type_"]', function() {
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

        // MENU UTAMA: Toggle menambah sub menu utama
        $('#menambah_sub_menu_utama').on('change', function() {
            const container = $('#new_sub_menu_utama_container');
            if ($(this).is(':checked')) {
                container.show();
                $('#dokumen_ip_mu').prop('disabled', true);
            } else {
                container.hide();
                $('#new_sub_menu_utama_fields').empty();
                $('#jumlah_sub_menu_utama_baru').val('');
                $('#dokumen_ip_mu').prop('disabled', false);
            }
        });

        // MENU UTAMA: Generate fields untuk sub menu utama baru
        $('#jumlah_sub_menu_utama_baru').on('input', function() {
            const jumlah = parseInt($(this).val());
            const container = $('#new_sub_menu_utama_fields');
            
            if (isNaN(jumlah) || jumlah < 1 || jumlah > 10) {
                container.empty();
                return;
            }

            container.empty();
            for (let i = 1; i <= jumlah; i++) {
                const html = `
                    <div class="card border-secondary mb-2">
                        <div class="card-header">
                            <h6 class="mb-0">Sub Menu Utama ${i}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Sub Menu Utama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="new_sub_menu_utama_nama_${i}" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label>Dokumen PDF <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="new_sub_menu_utama_dokumen_${i}" accept=".pdf" required>
                            </div>
                        </div>
                    </div>
                `;
                container.append(html);
            }
        });

        // SUB MENU UTAMA: Edit Sub Menu
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
                    const form = $(formId);
                    form.append(`<input type="hidden" name="update_sub_menu_${id}" value="${result.value}">`);
                    
                    $(this).closest('.card-header').find('h6').html(`<i class="fas fa-file mr-2"></i>${result.value}`);
                    $(this).data('nama', result.value);
                    
                    Swal.fire('Berhasil!', 'Perubahan akan disimpan saat form disubmit', 'success');
                }
            });
        });

        // SUB MENU UTAMA: Delete Sub Menu
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
                    const form = $(formId);
                    form.append(`<input type="hidden" name="delete_sub_menu_${id}" value="1">`);
                    
                    $(this).closest('.sub-menu-item').hide();
                    
                    const visibleItems = $('.sub-menu-item:visible').length;
                    if (visibleItems === 0) {
                        $('#dokumen_required_container').show();
                    }
                    
                    Swal.fire('Berhasil!', 'Sub Menu akan dihapus saat form disubmit', 'success');
                }
            });
        });

        // SUB MENU UTAMA: Add Sub Menu
        $('#add_sub_menu_btn').on('click', function() {
            newItemCounter++;
            const newId = `new_${newItemCounter}`;
            
            const html = `
                <div class="card border-secondary mb-3 new-sub-menu-item" data-id="${newId}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-file mr-2"></i>Sub Menu Baru
                            <span class="badge badge-info ml-2">Baru</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-danger remove-new-sub-menu" data-id="${newId}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Sub Menu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="new_sub_menu_nama_${newId}" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Dokumen PDF <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="new_sub_menu_dokumen_${newId}" accept=".pdf" required>
                        </div>
                    </div>
                </div>
            `;
            
            $('#existing_sub_menu_container').append(html);
            
            const form = $(formId);
            let newSubMenus = form.find('input[name="new_sub_menu[]"]').val();
            if (!newSubMenus) {
                form.append(`<input type="hidden" name="new_sub_menu[]" value="${newId}">`);
            } else {
                newSubMenus += `,${newId}`;
                form.find('input[name="new_sub_menu[]"]').val(newSubMenus);
            }
        });

        // SUB MENU UTAMA: Remove new sub menu
        $(document).on('click', '.remove-new-sub-menu', function() {
            const id = $(this).data('id');
            const form = $(formId);
            
            form.find(`input[name*="${id}"]`).remove();
            $(this).closest('.new-sub-menu-item').remove();
        });

        // SUB MENU UTAMA: Toggle menambah sub menu
        $('#menambah_sub_menu').on('change', function() {
            const container = $('#new_sub_menu_container');
            if ($(this).is(':checked')) {
                container.show();
                $('#dokumen_ip_smu').prop('disabled', true);
            } else {
                container.hide();
                $('#new_sub_menu_fields').empty();
                $('#jumlah_sub_menu_baru').val('');
                $('#dokumen_ip_smu').prop('disabled', false);
            }
        });

        // SUB MENU UTAMA: Generate fields untuk sub menu baru
        $('#jumlah_sub_menu_baru').on('input', function() {
            const jumlah = parseInt($(this).val());
            const container = $('#new_sub_menu_fields');
            
            if (isNaN(jumlah) || jumlah < 1 || jumlah > 10) {
                container.empty();
                return;
            }

            container.empty();
            for (let i = 1; i <= jumlah; i++) {
                const html = `
                    <div class="card border-secondary mb-2">
                        <div class="card-header">
                            <h6 class="mb-0">Sub Menu ${i}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Sub Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="new_sub_menu_nama_${i}" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label>Dokumen PDF <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="new_sub_menu_dokumen_${i}" accept=".pdf" required>
                            </div>
                        </div>
                    </div>
                `;
                container.append(html);
            }
        });

        // Form submission - UNIVERSAL untuk semua tipe
        $(`button[form="${formId.substring(1)}"]`).on('click', function(e) {
            e.preventDefault();
            const form = $(formId);
            const button = $(this);
            const formData = new FormData(form[0]);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

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
                            text: response.message || 'Data berhasil diperbarui'
                        });
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).html(value[0]);
                            });
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat menyimpan data'
                        });
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

        // Remove validation errors on input
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });
    });
</script>