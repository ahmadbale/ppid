{{-- Modal Update Form --}}
@include('sisfo::components.menu-master.style')
@include('sisfo::components.menu-master.shared-scripts')

<div class="modal-header bg-warning">
    <h5 class="modal-title text-white">
        <i class="fas fa-edit mr-2"></i>{{ $pageTitle ?? 'Edit Data' }}
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formUpdate" action="{{ url($menuConfig->wmu_nama . '/updateData/' . $existingData->$pkColumn) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="modal-body">
        @foreach($formFields as $field)
            <div class="row mb-2">
                <div class="col-12">
                    @include('sisfo::components.menu-master.form-field-type', [
                        'field' => $field,
                        'value' => $field['value'],
                        'mode' => 'update'
                    ])
                </div>
            </div>
        @endforeach
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Batal
        </button>
        <button type="button" class="btn btn-warning" id="btnUpdate">
            <i class="fas fa-save mr-1"></i>Update
        </button>
    </div>
</form>

{{-- ============================================================
     Modal FK Search Edit — Redesigned Elegant
     ============================================================ --}}
<div class="modal fade" id="modalFkSearchEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 680px;">
        <div class="modal-content fk-modal-content">

            {{-- Header --}}
            <div class="fk-modal-header">
                <div class="fk-modal-header-inner">
                    <div class="fk-modal-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <h5 class="fk-modal-title">Pilih Data</h5>
                        <p class="fk-modal-subtitle">Cari dan pilih data yang sesuai</p>
                    </div>
                </div>
                <button type="button" class="fk-modal-close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="fk-modal-body">

                {{-- Search Bar --}}
                <div class="fk-search-wrapper">
                    <span class="fk-search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" class="fk-search-input" id="searchFkInputEdit" placeholder="Ketik nama atau kode untuk mencari...">
                    <button class="fk-search-clear btn-clear-search-fk-edit" type="button" title="Hapus pencarian" style="display:none;">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                {{-- Info Bar --}}
                <div class="fk-info-bar">
                    <span class="fk-info-badge" id="fkDataInfoEdit">
                        <i class="fas fa-database mr-1"></i>Menampilkan 0 data
                    </span>
                </div>

                {{-- Table --}}
                <div class="fk-table-wrapper">
                    <table class="fk-table" id="tableFkSearchEdit">
                        <thead>
                            <tr id="fkTableHeadersEdit"></tr>
                        </thead>
                        <tbody id="fkTableBodyEdit"></tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="fk-pagination-bar">
                    <span class="fk-page-info" id="fkPaginationInfoEdit">Halaman 0 dari 0</span>
                    <ul class="fk-pagination" id="fkPaginationEdit"></ul>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     Inisialisasi Shared JS (FK Search, Media Upload, Form Submit, dll)
     ============================================================ --}}
<script>
$(document).ready(function() {
    if (typeof window.MenuMasterShared !== 'undefined') {
        window.MenuMasterShared.init('update', '{{ $menuConfig->wmu_nama }}');
    }
});
</script>