{{--
    Form Field Type Component
    
    Reusable component untuk render berbagai tipe field input
    Digunakan oleh create.blade.php dan update.blade.php
    
    @param array $field - Field configuration
    @param mixed $value - Current value (untuk update)
    @param string $mode - 'create' atau 'update'
--}}

@php
    $value = $value ?? old($field['column']);
    $isReadonly = ($mode === 'update' && ($field['is_pk'] || $field['is_auto_increment']));
    $requiredAttr = $field['is_required'] ? 'required' : '';
    
    // Detect criteria for auto-transform
    $criteria = $field['criteria'] ?? [];
    $caseTransform = '';
    if (!empty($criteria['case'])) {
        $caseTransform = $criteria['case'] === 'uppercase' ? 'text-uppercase' : 
                         ($criteria['case'] === 'lowercase' ? 'text-lowercase' : '');
    }
    $dataCase = !empty($criteria['case']) ? 'data-case="' . $criteria['case'] . '"' : '';
@endphp

<div class="form-group">
    <label for="{{ $field['column'] }}" class="font-weight-bold">
        {{ $field['label'] }}
        @if($field['is_required'])
            <span class="text-danger">*</span>
        @endif
        @if(!empty($criteria['case']))
            <small class="text-muted">(Auto {{ ucfirst($criteria['case']) }})</small>
        @endif
    </label>

@if($field['type'] === 'text')
    {{-- Text Input --}}
    <input type="text" 
           class="form-control {{ $isReadonly ? 'bg-light' : '' }} {{ $caseTransform }}" 
           id="{{ $field['column'] }}" 
           name="{{ $field['column'] }}" 
           placeholder="Masukkan {{ strtolower($field['label']) }}"
           value="{{ $value }}"
           {{ $isReadonly ? 'readonly' : '' }}
           {!! $dataCase !!}
           {{ $requiredAttr }}>

@elseif($field['type'] === 'textarea')
    {{-- Textarea --}}
    <textarea class="form-control" 
              id="{{ $field['column'] }}" 
              name="{{ $field['column'] }}" 
              rows="4"
              placeholder="Masukkan {{ strtolower($field['label']) }}"
              {{ $requiredAttr }}>{{ $value }}</textarea>

@elseif($field['type'] === 'number')
    {{-- Number Input --}}
    <input type="number" 
           class="form-control {{ $isReadonly ? 'bg-light' : '' }}" 
           id="{{ $field['column'] }}" 
           name="{{ $field['column'] }}" 
           placeholder="Masukkan {{ strtolower($field['label']) }}"
           value="{{ $value }}"
           step="any"
           {{ $isReadonly ? 'readonly' : '' }}
           {{ $requiredAttr }}>

@elseif($field['type'] === 'date')
    {{-- Date Picker --}}
    <input type="date" 
           class="form-control" 
           id="{{ $field['column'] }}" 
           name="{{ $field['column'] }}" 
           value="{{ $value }}"
           {{ $requiredAttr }}>

@elseif($field['type'] === 'date2')
    {{-- Date Range --}}
    <div class="input-group">
        <input type="date" 
               class="form-control" 
               id="{{ $field['column'] }}_start" 
               name="{{ $field['column'] }}_start" 
               placeholder="Dari tanggal"
               value="{{ $field['value_start'] ?? '' }}"
               {{ $requiredAttr }}>
        <div class="input-group-prepend input-group-append">
            <span class="input-group-text">s/d</span>
        </div>
        <input type="date" 
               class="form-control" 
               id="{{ $field['column'] }}_end" 
               name="{{ $field['column'] }}_end" 
               placeholder="Sampai tanggal"
               value="{{ $field['value_end'] ?? '' }}"
               {{ $requiredAttr }}>
    </div>

@elseif($field['type'] === 'dropdown')
    {{-- Dropdown (Select2) --}}
    <select class="form-control select2" 
            id="{{ $field['column'] }}" 
            name="{{ $field['column'] }}"
            {{ $requiredAttr }}>
        <option value="">-- Pilih {{ $field['label'] }} --</option>
        @if(isset($field['options']) && is_array($field['options']))
            @foreach($field['options'] as $optValue => $optLabel)
                <option value="{{ $optValue }}" {{ $value == $optValue ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endforeach
        @endif
    </select>

@elseif($field['type'] === 'radio')
    {{-- Radio Buttons --}}
    <div class="form-check-group">
        @if(isset($field['options']) && is_array($field['options']))
            @foreach($field['options'] as $optValue => $optLabel)
                <div class="form-check">
                    <input class="form-check-input" 
                           type="radio" 
                           id="{{ $field['column'] }}_{{ $optValue }}" 
                           name="{{ $field['column'] }}" 
                           value="{{ $optValue }}"
                           {{ $value == $optValue ? 'checked' : '' }}
                           {{ $requiredAttr }}>
                    <label class="form-check-label" for="{{ $field['column'] }}_{{ $optValue }}">
                        {{ $optLabel }}
                    </label>
                </div>
            @endforeach
        @endif
    </div>

@elseif($field['type'] === 'search')
    {{-- FK Search Modal --}}
    <div class="input-group">
        <input type="hidden" 
               id="{{ $field['column'] }}" 
               name="{{ $field['column'] }}"
               value="{{ $value }}">
        <input type="text" 
               class="form-control" 
               id="{{ $field['column'] }}_display" 
               value="{{ $field['display_value'] ?? '' }}"
               placeholder="Klik untuk pilih {{ strtolower($field['label']) }}"
               readonly
               {{ $requiredAttr }}>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary btn-search-fk" 
                    type="button"
                    data-column="{{ $field['column'] }}"
                    data-fk-table="{{ $field['fk_table'] }}"
                    data-fk-pk="{{ $field['fk_pk'] }}"
                    data-fk-display="{{ json_encode($field['fk_display_columns'] ?? []) }}"
                    data-fk-labels="{{ json_encode($field['fk_label_columns'] ?? []) }}"
                    data-fk-priority="{{ $field['fk_priority_display'] ?? '' }}">
                <i class="fas fa-search"></i>
            </button>
            @if(!$field['is_required'])
                <button class="btn btn-outline-danger btn-clear-fk" 
                        type="button"
                        data-column="{{ $field['column'] }}">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
    <small class="form-text text-muted">
        Klik tombol cari untuk memilih {{ strtolower($field['label']) }}
    </small>

@elseif($field['type'] === 'media' || $field['type'] === 'file' || $field['type'] === 'gambar')
    @php
        // Tentukan accept attribute dari mimes yang dikonfigurasi
        $mimesRaw = $field['mimes'] ?? null;
        $acceptAttr = '';
        if ($mimesRaw) {
            $exts = array_map('trim', explode(',', $mimesRaw));
            $acceptAttr = implode(',', array_map(fn($e) => '.' . $e, $exts));
        }
        // Deteksi apakah hanya gambar (semua ext adalah gambar)
        $imageExts = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp'];
        $configuredExts = $mimesRaw ? array_map('trim', explode(',', $mimesRaw)) : [];
        $isImageOnly = !empty($configuredExts) && count(array_diff($configuredExts, $imageExts)) === 0;
        // Deteksi apakah ada gambar sama sekali (untuk preview)
        $hasImageExt = !empty($configuredExts)
            ? count(array_intersect($configuredExts, $imageExts)) > 0
            : true; // jika tidak dikonfigurasi, tampilkan preview gambar
        $fileExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip'];
        $hasFileExt = !empty($configuredExts)
            ? count(array_intersect($configuredExts, $fileExts)) > 0
            : true;
    @endphp

    {{-- Media Upload (File / Gambar / Keduanya) --}}
    <div class="media-upload-wrapper">
        <div class="custom-file-upload-area" id="uploadArea_{{ $field['column'] }}">
            <input type="file"
                   class="media-upload-input"
                   id="{{ $field['column'] }}"
                   name="{{ $field['column'] }}"
                   accept="{{ $acceptAttr }}"
                   {{ $isReadonly ? 'disabled' : '' }}
                   {{ $requiredAttr }}
                   data-column="{{ $field['column'] }}"
                   data-has-image="{{ $hasImageExt ? '1' : '0' }}"
                   data-has-file="{{ $hasFileExt ? '1' : '0' }}"
                   @if(isset($field['ukuran_max']) && $field['ukuran_max'])
                       data-max-size="{{ $field['ukuran_max'] }}"
                   @endif>
            <div class="upload-placeholder" id="placeholder_{{ $field['column'] }}">
                <div class="upload-icon">
                    @if($isImageOnly)
                        <i class="fas fa-image text-info"></i>
                    @elseif(!$hasImageExt && $hasFileExt)
                        <i class="fas fa-file-upload text-primary"></i>
                    @else
                        <i class="fas fa-cloud-upload-alt text-secondary"></i>
                    @endif
                </div>
                <div class="upload-text">
                    <span class="upload-main-text">Klik atau seret file ke sini</span>
                    <span class="upload-sub-text">
                        @if($acceptAttr)
                            Format: {{ str_replace('.', '', $acceptAttr) }}
                        @else
                            Semua format file
                        @endif
                        @if(isset($field['ukuran_max']) && $field['ukuran_max'])
                            &bull; Maks. {{ $field['ukuran_max'] }} MB
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Preview Area --}}
        <div class="media-preview-container" id="preview_{{ $field['column'] }}">
            @if($mode === 'update' && isset($field['existing_file']))
                @php
                    $existingFile = $field['existing_file'];
                    $ext = strtolower(pathinfo($existingFile['name'], PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                @endphp
                @if($isImg)
                    <div class="preview-card preview-card-image">
                        <div class="preview-card-thumb">
                            <img src="{{ $existingFile['url'] }}" alt="Preview">
                        </div>
                        <div class="preview-card-info">
                            <div class="preview-card-name" title="{{ $existingFile['name'] }}">{{ $existingFile['name'] }}</div>
                            <div class="preview-card-meta">Gambar saat ini</div>
                        </div>
                        <div class="preview-card-actions">
                            <a href="{{ $existingFile['url'] }}" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                @else
                    @php
                        $iconMapBlade = [
                            'pdf' => 'fa-file-pdf text-danger',
                            'doc' => 'fa-file-word text-primary', 'docx' => 'fa-file-word text-primary',
                            'xls' => 'fa-file-excel text-success', 'xlsx' => 'fa-file-excel text-success',
                            'ppt' => 'fa-file-powerpoint text-warning', 'pptx' => 'fa-file-powerpoint text-warning',
                            'txt' => 'fa-file-alt text-secondary', 'csv' => 'fa-file-csv text-success',
                            'zip' => 'fa-file-archive text-warning', 'rar' => 'fa-file-archive text-warning',
                        ];
                        $iconClass = $iconMapBlade[$ext] ?? 'fa-file text-secondary';
                    @endphp
                    <div class="preview-card preview-card-file">
                        <div class="preview-card-icon">
                            <i class="fas {{ $iconClass }} fa-2x"></i>
                        </div>
                        <div class="preview-card-info">
                            <div class="preview-card-name" title="{{ $existingFile['name'] }}">{{ $existingFile['name'] }}</div>
                            <div class="preview-card-meta">Dokumen saat ini &bull; .{{ strtoupper($ext) }}</div>
                        </div>
                        <div class="preview-card-actions">
                            <a href="{{ $existingFile['url'] }}" target="_blank" class="btn btn-sm btn-outline-info" title="Buka">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

@else
    {{-- Unsupported Field Type --}}
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Field type "{{ $field['type'] }}" belum didukung
    </div>
@endif

{{-- Help Text (wmfc_label_keterangan) --}}
@if(isset($field['label_keterangan']) && !empty($field['label_keterangan']))
    <small class="form-text text-muted">{!! $field['label_keterangan'] !!}</small>
@endif

{{-- Legacy Help Text (optional) --}}
@if(isset($field['help_text']) && !empty($field['help_text']))
    <small class="form-text text-muted">{{ $field['help_text'] }}</small>
@endif

{{-- Error Feedback Container --}}
<div class="invalid-feedback" id="error-{{ $field['column'] }}"></div>
</div>
{{-- End of form-group --}}

@once
@push('css')
<style>
/* ==========================================
   Media Upload Styles
   ========================================== */
.media-upload-wrapper {
    position: relative;
}
.custom-file-upload-area {
    position: relative;
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fafbfc;
}
.custom-file-upload-area:hover {
    border-color: #4299e1;
    background: #ebf8ff;
}
.custom-file-upload-area.drag-over {
    border-color: #48bb78;
    background: #f0fff4;
}
.custom-file-upload-area .media-upload-input {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    pointer-events: none;
}
.upload-icon i {
    font-size: 28px;
    opacity: 0.6;
}
.upload-main-text {
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    display: block;
}
.upload-sub-text {
    font-size: 11px;
    color: #a0aec0;
    display: block;
}

/* Preview Card */
.media-preview-container {
    margin-top: 8px;
}
.preview-card {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    gap: 12px;
    margin-top: 8px;
    transition: box-shadow 0.15s;
}
.preview-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.preview-card-thumb {
    flex-shrink: 0;
    width: 64px;
    height: 64px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #f7fafc;
}
.preview-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.preview-card-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: #f7fafc;
    display: flex;
    align-items: center;
    justify-content: center;
}
.preview-card-info {
    flex: 1;
    min-width: 0;
}
.preview-card-name {
    font-size: 13px;
    font-weight: 600;
    color: #2d3748;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.preview-card-meta {
    font-size: 11px;
    color: #a0aec0;
    margin-top: 2px;
}
.preview-card-actions {
    flex-shrink: 0;
    display: flex;
    gap: 4px;
}
.preview-card .btn-remove-file {
    color: #e53e3e;
    background: none;
    border: 1px solid #fed7d7;
    border-radius: 6px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
    padding: 0;
}
.preview-card .btn-remove-file:hover {
    background: #fff5f5;
    border-color: #fc8181;
}

/* FK Search Modal Row Hover */
.fk-row-selectable:hover, .fk-row-selectable-edit:hover {
    background-color: #ebf8ff !important;
}
</style>
@endpush

@push('js')
<script>
/**
 * Media Upload Preview Handler — Enhanced
 * Preview gambar + dokumen, tampilkan ukuran file (KB/MB)
 */
$(document).on('change', '.media-upload-input', function() {
    const input = this;
    const column = $(input).data('column');
    const hasImage = $(input).data('has-image') === 1 || $(input).data('has-image') === '1';
    const hasFile = $(input).data('has-file') === 1 || $(input).data('has-file') === '1';
    const maxSizeMB = parseFloat($(input).data('max-size')) || 0;
    const $previewArea = $(`#preview_${column}`);
    const $uploadArea = $(`#uploadArea_${column}`);

    if (!input.files || !input.files[0]) {
        return;
    }

    const file = input.files[0];

    // Validasi ukuran
    if (maxSizeMB > 0) {
        const fileSizeMB = file.size / (1024 * 1024);
        if (fileSizeMB > maxSizeMB) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: `Ukuran file maksimal ${maxSizeMB} MB. File yang dipilih: ${fileSizeMB.toFixed(2)} MB`,
            });
            input.value = '';
            $previewArea.empty();
            return;
        }
    }

    // Format file size
    const sizeText = file.size >= 1024 * 1024
        ? (file.size / (1024 * 1024)).toFixed(2) + ' MB'
        : (file.size / 1024).toFixed(1) + ' KB';

    const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    const ext = file.name.split('.').pop().toLowerCase();
    const isImage = imageExts.includes(ext);

    $previewArea.empty();

    if (isImage && hasImage) {
        // Preview gambar
        const reader = new FileReader();
        reader.onload = function(e) {
            $previewArea.html(`
                <div class="preview-card preview-card-image">
                    <div class="preview-card-thumb">
                        <img src="${e.target.result}" alt="Preview">
                    </div>
                    <div class="preview-card-info">
                        <div class="preview-card-name" title="${file.name}">${file.name}</div>
                        <div class="preview-card-meta">${sizeText} &bull; .${ext.toUpperCase()} &bull; Gambar baru</div>
                    </div>
                    <div class="preview-card-actions">
                        <button type="button" class="btn-remove-file" data-column="${column}" title="Hapus file">
                            <i class="fas fa-times" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    } else {
        // Preview dokumen
        const iconMap = {
            pdf: 'fa-file-pdf text-danger',
            doc: 'fa-file-word text-primary', docx: 'fa-file-word text-primary',
            xls: 'fa-file-excel text-success', xlsx: 'fa-file-excel text-success',
            ppt: 'fa-file-powerpoint text-warning', pptx: 'fa-file-powerpoint text-warning',
            txt: 'fa-file-alt text-secondary', csv: 'fa-file-csv text-success',
            zip: 'fa-file-archive text-warning', rar: 'fa-file-archive text-warning',
        };
        const iconClass = iconMap[ext] || 'fa-file text-secondary';

        $previewArea.html(`
            <div class="preview-card preview-card-file">
                <div class="preview-card-icon">
                    <i class="fas ${iconClass} fa-2x"></i>
                </div>
                <div class="preview-card-info">
                    <div class="preview-card-name" title="${file.name}">${file.name}</div>
                    <div class="preview-card-meta">${sizeText} &bull; .${ext.toUpperCase()} &bull; File baru</div>
                </div>
                <div class="preview-card-actions">
                    <button type="button" class="btn-remove-file" data-column="${column}" title="Hapus file">
                        <i class="fas fa-times" style="font-size:12px;"></i>
                    </button>
                </div>
            </div>
        `);
    }
});

// Remove file
$(document).on('click', '.btn-remove-file', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const column = $(this).data('column');
    const $input = $(`#${column}`);
    $input.val('');
    $(`#preview_${column}`).empty();
});

// Drag & Drop visual feedback
$(document).on('dragover', '.custom-file-upload-area', function(e) {
    e.preventDefault();
    $(this).addClass('drag-over');
});
$(document).on('dragleave drop', '.custom-file-upload-area', function(e) {
    $(this).removeClass('drag-over');
});
</script>
@endpush
@endonce
