<!-- View Create Berita (Form Tambah) -->
<div class="modal-header bg-primary text-white py-3">
    <h5 class="modal-title font-weight-bold">Tambah Berita Baru</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formCreateBerita" enctype="multipart/form-data">
        @csrf
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Informasi Dasar</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="fk_m_berita_dinamis" class="font-weight-medium">
                        Kategori Berita <span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-control-lg" id="fk_m_berita_dinamis" name="t_berita[fk_m_berita_dinamis]">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($beritaDinamis as $kategori)
                            <option value="{{ $kategori->berita_dinamis_id }}">
                                {{ $kategori->bd_nama_submenu }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="fk_m_berita_dinamis_error"></div>
                </div>

                <div class="form-group">
                    <label for="berita_judul" class="font-weight-medium">
                        Judul Berita <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-lg" id="berita_judul" 
                           name="t_berita[berita_judul]" maxlength="140" 
                           placeholder="Masukkan judul berita...">
                    <div class="invalid-feedback" id="berita_judul_error"></div>
                </div>

                <div class="form-group">
                    <label for="status_berita" class="font-weight-medium">
                        Status Berita <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_aktif" name="t_berita[status_berita]" 
                                   class="custom-control-input" value="aktif">
                            <label class="custom-control-label" for="status_aktif">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_nonaktif" name="t_berita[status_berita]" 
                                   class="custom-control-input" value="nonaktif">
                            <label class="custom-control-label" for="status_nonaktif">Nonaktif</label>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="status_berita_error"></div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Thumbnail</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="berita_thumbnail" class="font-weight-medium">
                                Thumbnail Berita
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="berita_thumbnail" 
                                       name="berita_thumbnail" accept="image/*">
                                <label class="custom-file-label" for="berita_thumbnail">Pilih file</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i> 
                                Format yang didukung: JPG, PNG, GIF. Ukuran maks 2.5 MB
                            </small>
                            <div class="invalid-feedback" id="berita_thumbnail_error"></div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <label for="berita_thumbnail_deskripsi" class="font-weight-medium">
                                Deskripsi Thumbnail
                            </label>
                            <textarea class="form-control" id="berita_thumbnail_deskripsi"
                                      name="t_berita[berita_thumbnail_deskripsi]" maxlength="255"
                                      placeholder="Contoh: Malang, 24 Maret 2025 - Penjelasan Singkat Informasi Berita"
                                      onkeyup="validateInput(this)" rows="3"></textarea>
                            <small class="form-text text-muted">Format: Lokasi, Tanggal - Deskripsi singkat berita (maksimal 255 karakter)</small>
                            <div class="text-danger error-message mt-1" id="berita_thumbnail_deskripsi_error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="preview-container mt-md-4 text-center">
                            <div id="thumbnail-preview" class="mb-2">
                                <div class="placeholder-img d-flex justify-content-center align-items-center" 
                                     style="min-height: 150px; border: 2px dashed #ccc; border-radius: 8px;">
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p class="mb-0">Preview Thumbnail</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Konten Berita</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="berita_deskripsi" class="font-weight-medium">
                        Konten Berita <span class="text-danger">*</span>
                    </label>
                    <textarea id="berita_deskripsi" name="t_berita[berita_deskripsi]" 
                              class="form-control"></textarea>
                    <div class="invalid-feedback" id="berita_deskripsi_error"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer bg-light py-3">
    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
        <i class="fas fa-times mr-1"></i> Batal
    </button>
    <button type="button" class="btn btn-success px-4" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan
    </button>
</div>

<script>
    $(document).ready(function() {
        // Validation rules
        const validationRules = {
            'fk_m_berita_dinamis': {
                required: true,
                message: 'Kategori berita wajib dipilih'
            },
            'berita_judul': {
                required: true,
                minLength: 3,
                maxLength: 140,
                message: 'Judul berita wajib diisi (3-140 karakter)'
            },
            'status_berita': {
                required: true,
                message: 'Status berita wajib dipilih'
            },
            'berita_thumbnail_deskripsi': {
                maxLength: 255,
                message: 'Deskripsi thumbnail maksimal 255 karakter'
            },
            'berita_deskripsi': {
                required: true,
                minLength: 10,
                message: 'Konten berita wajib diisi (minimal 10 karakter)'
            }
        };
    
        // Validation function
        const validateField = (fieldName, value) => {
            const rules = validationRules[fieldName];
            if (!rules) return true;
    
            if (rules.required && !value) {
                showError(fieldName, rules.message);
                return false;
            }
    
            if (rules.minLength && value.length < rules.minLength) {
                showError(fieldName, `Minimal ${rules.minLength} karakter`);
                return false;
            }
    
            if (rules.maxLength && value.length > rules.maxLength) {
                showError(fieldName, `Maksimal ${rules.maxLength} karakter`);
                return false;
            }
    
            return true;
        };
    
        // Show error message
        const showError = (fieldName, message) => {
            $(`#${fieldName}`).addClass('is-invalid');
            $(`#${fieldName}_error`).html(message);
            
            // Special handling for Summernote
            if (fieldName === 'berita_deskripsi') {
                $('#berita_deskripsi').next('.note-editor').addClass('is-invalid');
            }
        };
    
        // Reset validation states
        const resetValidation = () => {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback, .error-message').html('');
            $('.note-editor').removeClass('is-invalid');
        };
    
        // Real-time validation
        $('#berita_judul').on('input', function() {
            validateField('berita_judul', $(this).val().trim());
        });
    
        $('#fk_m_berita_dinamis').on('change', function() {
            validateField('fk_m_berita_dinamis', $(this).val());
        });
    
        $('#berita_thumbnail_deskripsi').on('input', function() {
            validateField('berita_thumbnail_deskripsi', $(this).val().trim());
        });
    
        $('input[name="t_berita[status_berita]"]').on('change', function() {
            validateField('status_berita', $('input[name="t_berita[status_berita]"]:checked').val());
        });
    
        // File validation
        $('#berita_thumbnail').on('change', function() {
            const file = this.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showError('berita_thumbnail', 'Format file harus JPG, PNG, atau GIF');
                    this.value = '';
                    return false;
                }
    
                // Validate file size (2.5MB)
                if (file.size > 2.5 * 1024 * 1024) {
                    showError('berita_thumbnail', 'Ukuran file maksimal 2.5MB');
                    this.value = '';
                    return false;
                }
            }
        });
    
        // Form submission handler
        $('#btnSubmitForm').on('click', function() {
            resetValidation();
            let isValid = true;
    
            // Validate all fields
            Object.keys(validationRules).forEach(fieldName => {
                let value;
                
                if (fieldName === 'status_berita') {
                    value = $('input[name="t_berita[status_berita]"]:checked').val();
                } else if (fieldName === 'berita_deskripsi') {
                    value = $('#berita_deskripsi').summernote('code').replace(/<[^>]*>/g, '').trim();
                } else {
                    value = $(`#${fieldName}`).val()?.trim();
                }
    
                if (!validateField(fieldName, value)) {
                    isValid = false;
                }
            });
    
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon periksa kembali input Anda'
                });
    
                // Scroll to first error
                const firstError = $('.is-invalid:first');
                if (firstError.length) {
                    $('.modal-body').animate({
                        scrollTop: firstError.offset().top - 200
                    }, 500);
                }
                return false;
            }
    
            // If validation passes, submit the form
            submitForm($(this));
        });
    
        // Form submission function
        const submitForm = (button) => {
            const form = $('#formCreateBerita');
            const formData = new FormData(form[0]);
    
            button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
    
            $.ajax({
                url: '{{ url("adminweb/berita/createData") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#myModal').modal('hide');
                        loadBeritaData(1, '');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                },
                complete: function() {
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                }
            });
        };
    });
    </script>