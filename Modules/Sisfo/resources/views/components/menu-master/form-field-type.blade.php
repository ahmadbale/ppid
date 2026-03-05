{{--
    Form Field Type Component

    Reusable component untuk render berbagai tipe field input.
    Digunakan oleh create.blade.php dan update.blade.php.

    @param array  $field - Konfigurasi field (dari MasterMenuService::buildFormFields)
    @param mixed  $value - Nilai saat ini (untuk mode update)
    @param string $mode  - 'create' atau 'update'

    Catatan:
    - CSS dikelola di: components/menu-master/style.blade.php
    - JS  dikelola di: public/modules/sisfo/js/menu-master/shared.js
--}}

@php
    $value = $value ?? old($field['column']);
    $isReadonly = ($mode === 'update' && ($field['is_pk'] || $field['is_auto_increment']));
    $requiredAttr = $field['is_required'] ? 'required' : '';

    // Kriteria auto-transform (uppercase/lowercase)
    $criteria = $field['criteria'] ?? [];
    $caseTransform = '';
    if (!empty($criteria['case'])) {
        $caseTransform = $criteria['case'] === 'uppercase' ? 'text-uppercase'
                       : ($criteria['case'] === 'lowercase' ? 'text-lowercase' : '');
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
    <div class="time-input-wrap">
        <input type="date" 
               class="form-control" 
               id="{{ $field['column'] }}" 
               name="{{ $field['column'] }}" 
               value="{{ $value }}"
               {{ $requiredAttr }}>
        <span class="time-input-ico"><i class="far fa-calendar-alt"></i></span>
    </div>

@elseif($field['type'] === 'datetime')
    {{-- DateTime Picker --}}
    <div class="time-input-wrap">
        <input type="datetime-local" 
               class="form-control" 
               id="{{ $field['column'] }}" 
               name="{{ $field['column'] }}" 
               value="{{ $value ? \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i') : '' }}"
               {{ $requiredAttr }}>
        <span class="time-input-ico"><i class="far fa-calendar-check"></i></span>
    </div>

@elseif($field['type'] === 'time')
    {{-- Time Picker --}}
    <div class="time-input-wrap">
        <input type="time" 
               class="form-control" 
               id="{{ $field['column'] }}" 
               name="{{ $field['column'] }}" 
               value="{{ $value }}"
               {{ $requiredAttr }}>
        <span class="time-input-ico"><i class="far fa-clock"></i></span>
    </div>

@elseif($field['type'] === 'year')
    {{-- Year — input number biasa, min 4 digit (1000), max 4 digit (9999) --}}
    <input type="number" 
           class="form-control year-input" 
           id="{{ $field['column'] }}" 
           name="{{ $field['column'] }}" 
           placeholder="contoh: 2026"
           min="1000" max="9999"
           value="{{ $value }}"
           {{ $requiredAttr }}>

@elseif($field['type'] === 'date2')
    {{-- Date Range --}}
    <div class="range-input-container">
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">Dari</span>
            </div>
            <div class="range-input-col">
                <div class="time-input-wrap">
                    <input type="date" 
                           class="form-control" 
                           id="{{ $field['column'] }}_start" 
                           name="{{ $field['column'] }}_start" 
                           value="{{ $field['value_start'] ?? '' }}"
                           {{ $requiredAttr }}>
                    <span class="time-input-ico"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </div>
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">s/d</span>
            </div>
            <div class="range-input-col">
                <div class="time-input-wrap">
                    <input type="date" 
                           class="form-control" 
                           id="{{ $field['column'] }}_end" 
                           name="{{ $field['column'] }}_end" 
                           value="{{ $field['value_end'] ?? '' }}"
                           {{ $requiredAttr }}>
                    <span class="time-input-ico"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </div>
    </div>

@elseif($field['type'] === 'datetime2')
    {{-- DateTime Range --}}
    <div class="range-input-container">
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">Dari</span>
            </div>
            <div class="range-input-col">
                <div class="time-input-wrap">
                    <input type="datetime-local" 
                           class="form-control" 
                           id="{{ $field['column'] }}_start" 
                           name="{{ $field['column'] }}_start" 
                           value="{{ $field['value_start'] ?? '' }}"
                           {{ $requiredAttr }}>
                    <span class="time-input-ico"><i class="far fa-calendar-check"></i></span>
                </div>
            </div>
        </div>
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">s/d</span>
            </div>
            <div class="range-input-col">
                <div class="time-input-wrap">
                    <input type="datetime-local" 
                           class="form-control" 
                           id="{{ $field['column'] }}_end" 
                           name="{{ $field['column'] }}_end" 
                           value="{{ $field['value_end'] ?? '' }}"
                           {{ $requiredAttr }}>
                    <span class="time-input-ico"><i class="far fa-calendar-check"></i></span>
                </div>
            </div>
        </div>
    </div>

@elseif($field['type'] === 'time2')
    {{-- Time Range --}}
    <div class="range-input-container">
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">Dari</span>
            </div>
            <div class="range-input-col">
                <div class="time-input-wrap">
                    <input type="time" 
                           class="form-control" 
                           id="{{ $field['column'] }}_start" 
                           name="{{ $field['column'] }}_start" 
                           value="{{ $field['value_start'] ?? '' }}"
                           {{ $requiredAttr }}>
                    <span class="time-input-ico"><i class="far fa-clock"></i></span>
                </div>
            </div>
        </div>
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">s/d</span>
            </div>
            <div class="range-input-col">
                <div class="time-input-wrap">
                    <input type="time" 
                           class="form-control" 
                           id="{{ $field['column'] }}_end" 
                           name="{{ $field['column'] }}_end" 
                           value="{{ $field['value_end'] ?? '' }}"
                           {{ $requiredAttr }}>
                    <span class="time-input-ico"><i class="far fa-clock"></i></span>
                </div>
            </div>
        </div>
    </div>

@elseif($field['type'] === 'year2')
    {{-- Year Range --}}
    <div class="range-input-container">
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">Dari</span>
            </div>
            <div class="range-input-col">
                <input type="number" 
                       class="form-control year-input" 
                       id="{{ $field['column'] }}_start" 
                       name="{{ $field['column'] }}_start" 
                       placeholder="contoh: 2026"
                       min="1000" max="9999"
                       value="{{ $field['value_start'] ?? '' }}"
                       {{ $requiredAttr }}>
            </div>
        </div>
        <div class="range-input-row">
            <div class="range-input-label-col">
                <span class="range-label">s/d</span>
            </div>
            <div class="range-input-col">
                <input type="number" 
                       class="form-control year-input" 
                       id="{{ $field['column'] }}_end" 
                       name="{{ $field['column'] }}_end" 
                       placeholder="contoh: 2026"
                       min="1000" max="9999"
                       value="{{ $field['value_end'] ?? '' }}"
                       {{ $requiredAttr }}>
            </div>
        </div>
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

@elseif($field['type'] === 'media')
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

{{-- Kontainer pesan error validasi --}}
<div class="invalid-feedback" id="error-{{ $field['column'] }}"></div>
</div>
{{-- End .form-group --}}
