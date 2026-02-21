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
@endphp

@if($field['type'] === 'text')
    {{-- Text Input --}}
    <input type="text" 
           class="form-control {{ $isReadonly ? 'bg-light' : '' }}" 
           id="{{ $field['column'] }}" 
           name="{{ $field['column'] }}" 
           placeholder="Masukkan {{ strtolower($field['label']) }}"
           value="{{ $value }}"
           {{ $isReadonly ? 'readonly' : '' }}
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
                    data-fk-display="{{ json_encode($field['fk_display']) }}">
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

@else
    {{-- Unsupported Field Type --}}
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Field type "{{ $field['type'] }}" belum didukung
    </div>
@endif

{{-- Help Text (Optional) --}}
@if(isset($field['help_text']) && !empty($field['help_text']))
    <small class="form-text text-muted">{{ $field['help_text'] }}</small>
@endif

{{-- Error Feedback Container --}}
<div class="invalid-feedback" id="error-{{ $field['column'] }}"></div>
