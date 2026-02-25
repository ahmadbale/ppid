{{-- 
  Component: Section Master
  Form fields untuk kategori Master (Tabel Database & Field Configurator)
  
  Props:
  - $mode: 'create' atau 'update'
  - $webMenuUrl: (optional) Data existing menu untuk mode update
--}}

<div id="section-master" style="display:none;">
  <div class="form-group">
    <label for="wmu_akses_tabel">Nama Tabel Database <span class="text-danger">*</span></label>
    <div class="input-group">
      <input type="text" class="form-control" id="wmu_akses_tabel" 
             name="web_menu_url[wmu_akses_tabel]" 
             value="{{ isset($webMenuUrl) ? $webMenuUrl->wmu_akses_tabel : '' }}" 
             maxlength="100"
             {{ $mode === 'update' ? 'readonly' : '' }}>
      <div class="input-group-append">
        @if($mode === 'create')
          <button type="button" class="btn btn-primary" id="btnCekTabel">
            <i class="fas fa-search"></i> Cek Tabel
          </button>
        @else
          <button type="button" class="btn btn-info" id="btnCheckTable">
            <i class="fas fa-sync-alt"></i> Re-Check Tabel
          </button>
        @endif
      </div>
    </div>
    <small class="text-muted">
      @if($mode === 'create')
        Contoh: m_kategori, m_footer, web_banner
      @else
        Tabel yang akan dikelola oleh menu master ini (readonly, klik Re-Check untuk validasi ulang)
      @endif
    </small>
    <div class="invalid-feedback" id="wmu_akses_tabel_error"></div>
    @if($mode === 'create')
      {{-- Info box untuk feedback status cek tabel (lebih robust dari valid-feedback) --}}
      <div id="wmu_akses_tabel_success" class="mt-2" style="display:none;"></div>
    @endif
  </div>

  @if($mode === 'update')
    <!-- Alert Info (hanya di update mode) - persistent, tidak hilang -->
    <div class="alert" id="alertTableInfo" style="display: none;">
      <i class="fas fa-info-circle mr-2" id="alertTableIcon"></i>
      <span id="alertTableInfoText"></span>
    </div>
  @endif

  <!-- Field Configurator -->
  <div id="{{ $mode === 'create' ? 'field-configurator' : 'fieldConfigsSection' }}" style="display:none;">
    <hr>
    <h6 class="font-weight-bold{{ $mode === 'update' ? ' mb-3' : '' }}">
      <i class="fas fa-cogs mr-2"></i> Konfigurasi Field
    </h6>
    
    <div class="table-responsive">
      <table class="table table-bordered table-sm{{ $mode === 'update' ? ' field-config-table' : '' }}" 
             id="{{ $mode === 'create' ? 'tableFieldConfig' : 'tableFieldConfigs' }}">
        <thead class="thead-light">
          <tr>
            <th width="30">No</th>
            <th width="140">Kolom Database</th>
            <th width="130">Label Field</th>
            <th width="130">Type Input</th>
            <th width="110">Kriteria</th>
            <th width="300">Validasi</th>
            <th width="100">FK Config</th>
            <th width="190">Label Keterangan</th>
            <th width="70" class="text-center">Display List</th>
            <th width="100">Ukuran Max</th>
            <th width="70" class="text-center">Visible</th>
          </tr>
        </thead>
        <tbody id="{{ $mode === 'create' ? 'fieldConfigBody' : 'tbodyFieldConfigs' }}">
          <!-- Rows akan di-generate via JavaScript -->
        </tbody>
      </table>
    </div>
  </div>
</div>