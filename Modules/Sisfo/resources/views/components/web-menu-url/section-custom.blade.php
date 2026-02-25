{{-- 
  Component: Section Custom
  Form fields untuk kategori Custom (Module Type & Controller Name)
  
  Props:
  - $mode: 'create' atau 'update'
  - $webMenuUrl: (optional) Data existing menu untuk mode update
--}}

<div id="section-custom" style="display:none;">
  <div class="form-group">
    <label for="module_type">Module Type <span class="text-danger">*</span></label>
    <select class="form-control" id="module_type" name="web_menu_url[module_type]">
      <option value="">Pilih Module Type</option>
      <option value="sisfo" {{ isset($webMenuUrl) && $webMenuUrl->module_type === 'sisfo' ? 'selected' : '' }}>Sisfo</option>
      <option value="user" {{ isset($webMenuUrl) && $webMenuUrl->module_type === 'user' ? 'selected' : '' }}>User</option>
    </select>
    <div class="invalid-feedback" id="module_type_error"></div>
  </div>

  <div class="form-group">
    <label for="controller_name">Controller Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="controller_name" 
           name="web_menu_url[controller_name]" 
           value="{{ isset($webMenuUrl) ? $webMenuUrl->controller_name : '' }}" 
           maxlength="255">
    <small class="text-muted">
      @if($mode === 'create')
        Contoh: AdminWeb\Footer\FooterController
      @else
        Namespace controller (tanpa Modules\Sisfo\App\Http\Controllers\)
      @endif
    </small>
    <div class="invalid-feedback" id="controller_name_error"></div>
  </div>
  
  @if($mode === 'update')
    <div class="form-group" id="groupParentMenu">
      <label for="wmu_parent_id">Parent Menu</label>
      <select class="form-control" id="wmu_parent_id" name="web_menu_url[wmu_parent_id]">
        <option value="">Tidak ada parent (Root Menu)</option>
        {{-- Options akan diisi via controller --}}
      </select>
      <small class="text-muted">Pilih parent jika menu ini adalah sub-menu</small>
      <div class="invalid-feedback" id="wmu_parent_id_error"></div>
    </div>
  @endif
</div>
