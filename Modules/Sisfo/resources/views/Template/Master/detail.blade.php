{{-- Modal Detail View --}}
<div class="modal-header bg-info">
    <h5 class="modal-title text-white">
        <i class="fas fa-info-circle mr-2"></i>Detail {{ $pageTitle ?? 'Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    @foreach($fields as $field)
        <div class="row mb-3">
            <div class="col-12">
                <div class="form-group">
                    <label class="font-weight-bold">{{ $field->wmfc_field_label }}</label>
                    <div class="form-control-plaintext border rounded p-2 bg-light">
                        @php
                            $columnName = $field->wmfc_column_name;
                            $value = $detailData->$columnName ?? '-';
                            
                            // Format berdasarkan tipe field
                            if ($field->wmfc_field_type === 'date' && $value !== '-') {
                                $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                            } elseif ($field->wmfc_field_type === 'date2' && $value !== '-') {
                                $value = \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
                            } elseif ($field->wmfc_field_type === 'textarea') {
                                $value = nl2br(e($value));
                            }
                        @endphp
                        {!! $value !!}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    
    {{-- Audit Trail Section --}}
    @if(isset($detailData->created_at) || isset($detailData->updated_at))
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-history mr-2"></i>Informasi Audit
                </h6>
            </div>
            
            @if(isset($detailData->created_at))
                <div class="col-md-6 mb-2">
                    <small class="text-muted">Dibuat pada:</small>
                    <div class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($detailData->created_at)->format('d/m/Y H:i:s') }}
                    </div>
                    @if(isset($detailData->created_by))
                        <small class="text-muted">Oleh: {{ $detailData->createdBy->name ?? $detailData->created_by }}</small>
                    @endif
                </div>
            @endif
            
            @if(isset($detailData->updated_at) && $detailData->updated_at != $detailData->created_at)
                <div class="col-md-6 mb-2">
                    <small class="text-muted">Terakhir diupdate:</small>
                    <div class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($detailData->updated_at)->format('d/m/Y H:i:s') }}
                    </div>
                    @if(isset($detailData->updated_by))
                        <small class="text-muted">Oleh: {{ $detailData->updatedBy->name ?? $detailData->updated_by }}</small>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times mr-1"></i>Tutup
    </button>
</div>
