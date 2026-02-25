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
                    data-fk-display="{{ json_encode($field['fk_display_columns'] ?? []) }}">
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
    <div class="custom-file">
        <input type="file"
               class="custom-file-input media-upload"
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
        <label class="custom-file-label" for="{{ $field['column'] }}">
            {{ $acceptAttr ? 'Pilih file (' . $acceptAttr . ')' : 'Pilih file...' }}
        </label>
    </div>

    {{-- Preview Area --}}
    <div class="mt-2 media-preview-area" id="preview_{{ $field['column'] }}">
        @if($mode === 'update' && $value)
            @php
                $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
            @endphp
            @if($isImg)
                <div class="preview-image-wrap">
                    <img src="{{ asset('storage/' . $value) }}"
                         alt="Preview"
                         class="img-thumbnail"
                         style="max-width:200px; max-height:200px;">
                    <br><small class="text-muted">Gambar saat ini</small>
                </div>
            @else
                <div class="preview-file-wrap d-flex align-items-center p-2 border rounded bg-light">
                    <i class="fas fa-file-alt fa-2x text-secondary mr-2"></i>
                    <div>
                        <a href="{{ asset('storage/' . $value) }}" target="_blank" class="font-weight-bold">
                            {{ basename($value) }}
                        </a>
                        <br><small class="text-muted">File saat ini</small>
                    </div>
                </div>
            @endif
        @endif
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
@push('js')
<script>
/**
 * Media Upload Preview Handler
 * Menampilkan preview gambar atau info file saat user memilih file
 */
$(document).on('change', '.media-upload', function() {
    const input = this;
    const column = $(input).data('column');
    const hasImage = $(input).data('has-image') === 1 || $(input).data('has-image') === '1';
    const hasFile = $(input).data('has-file') === 1 || $(input).data('has-file') === '1';
    const maxSizeMB = parseFloat($(input).data('max-size')) || 0;
    const $previewArea = $(`#preview_${column}`);
    const $label = $(input).siblings('.custom-file-label');

    if (!input.files || !input.files[0]) {
        return;
    }

    const file = input.files[0];
    
    // Update label
    $label.text(file.name);

    // Validasi ukuran
    if (maxSizeMB > 0) {
        const fileSizeMB = file.size / (1024 * 1024);
        if (fileSizeMB > maxSizeMB) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: `Ukuran file maksimal ${maxSizeMB}MB. File yang dipilih: ${fileSizeMB.toFixed(2)}MB`,
            });
            input.value = '';
            $label.text('Pilih file...');
            $previewArea.empty();
            return;
        }
    }

    const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    const ext = file.name.split('.').pop().toLowerCase();
    const isImage = imageExts.includes(ext);

    $previewArea.empty();

    if (isImage && hasImage) {
        // Preview gambar
        const reader = new FileReader();
        reader.onload = function(e) {
            $previewArea.html(`
                <div class="preview-image-wrap mt-2">
                    <img src="${e.target.result}" alt="Preview"
                         class="img-thumbnail"
                         style="max-width:200px; max-height:200px;">
                    <br><small class="text-muted">Preview gambar baru</small>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    } else if (!isImage && hasFile) {
        // Preview info file/dokumen
        const sizeText = file.size > 1024 * 1024
            ? (file.size / (1024 * 1024)).toFixed(2) + ' MB'
            : (file.size / 1024).toFixed(1) + ' KB';

        const iconMap = {
            pdf: 'fa-file-pdf text-danger',
            doc: 'fa-file-word text-primary', docx: 'fa-file-word text-primary',
            xls: 'fa-file-excel text-success', xlsx: 'fa-file-excel text-success',
            ppt: 'fa-file-powerpoint text-warning', pptx: 'fa-file-powerpoint text-warning',
        };
        const iconClass = iconMap[ext] || 'fa-file-alt text-secondary';

        $previewArea.html(`
            <div class="preview-file-wrap d-flex align-items-center p-2 border rounded bg-light mt-2" style="max-width:300px;">
                <i class="fas ${iconClass} fa-2x mr-2"></i>
                <div>
                    <div class="font-weight-bold text-truncate" style="max-width:220px;">${file.name}</div>
                    <small class="text-muted">${sizeText}</small>
                </div>
            </div>
        `);
    }
});
</script>
@endpush
@endonce
