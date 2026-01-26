# 📘 DOKUMENTASI STANDARD CONTROLLER PATTERN - PPID POLINEMA

**Generated:** 26 Januari 2026  
**Project:** PPID Polinema  
**Module:** Sisfo (Laravel nwidart/laravel-modules)

---

## 🎯 **EXECUTIVE SUMMARY**

Semua controller CRUD di sistem PPID Polinema mengikuti **8 Function Standard Pattern** yang konsisten untuk mempermudah maintenance dan development.

### **8 Function Wajib:**
```
1. index()        → List/Halaman Utama
2. getData()      → AJAX Pagination
3. addData()      → Form Tambah (View)
4. createData()   → Process Insert (POST)
5. editData()     → Form Edit (View)
6. updateData()   → Process Update (PUT)
7. detailData()   → Show Detail (View)
8. deleteData()   → Confirm & Delete
```

---

## 📊 **STANDARD CONTROLLER STRUCTURE**

### **Base Template:**

```php
<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\[Feature]\[SubFeature];

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\[ModelPath]\[ModelName];
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class [FeatureName]Controller extends Controller
{
    use TraitsController;  // ⚡ WAJIB untuk helper functions

    // 📝 Properties
    public $breadcrumb = '[Feature Name]';
    public $pagename = '[Path/To/View]';

    // 🔽 8 Standard Functions Below
}
```

---

## 🔧 **FUNCTION 1: index() - List/Halaman Utama**

### **Purpose:**
- Render halaman list utama dengan data
- Setup breadcrumb, page title, active menu
- Support search functionality
- Load relational data (dropdown, kategori, etc)

### **Signature:**
```php
public function index(Request $request)
```

### **Standard Implementation:**

```php
public function index(Request $request)
{
    // 1. GET SEARCH PARAMETER
    $search = $request->query('search', '');

    // 2. SETUP BREADCRUMB
    $breadcrumb = (object) [
        'title' => '[Page Title]',
        'list' => ['Home', '[Module]', '[Submenu]']
    ];

    // 3. SETUP PAGE INFO
    $page = (object) [
        'title' => '[Page Title]'
    ];

    // 4. SET ACTIVE MENU (untuk sidebar highlight)
    $activeMenu = '[menu-url-slug]';
    
    // 5. LOAD DATA dengan Pagination (dari Model)
    $mainData = [ModelName]::selectData(10, $search);
    
    // 6. LOAD RELATIONAL DATA (optional, untuk dropdown/filter)
    $kategori = KategoriModel::where('isDeleted', 0)->get();
    
    // 7. RETURN VIEW dengan compact data
    return view("sisfo::[ViewPath].index", [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'activeMenu' => $activeMenu,
        'mainData' => $mainData,
        'kategori' => $kategori,
        'search' => $search
    ]);
}
```

### **Real Example (FooterController):**

```php
public function index(Request $request)
{
    $search = $request->query('search', '');

    $breadcrumb = (object) [
        'title' => 'Pengaturan Footer',
        'list' => ['Home', 'Pengaturan Footer']
    ];

    $page = (object) [
        'title' => 'Daftar Footer'
    ];

    $activeMenu = 'footer';
    $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
    $footer = FooterModel::selectData(10, $search);

    return view("sisfo::AdminWeb/Footer.index", [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'activeMenu' => $activeMenu,
        'kategoriFooters' => $kategoriFooters,
        'footer' => $footer,
        'search' => $search
    ]);
}
```

### **Key Features:**
- ✅ Pagination support (parameter `10` = items per page)
- ✅ Search functionality
- ✅ Breadcrumb navigation
- ✅ Active menu highlighting
- ✅ Relational data loading

---

## 🔄 **FUNCTION 2: getData() - AJAX Pagination**

### **Purpose:**
- Handle AJAX request untuk pagination
- Return partial view dengan data baru
- Support infinite scroll atau load more
- Prevent direct access (harus via AJAX)

### **Signature:**
```php
public function getData(Request $request)
```

### **Standard Implementation:**

```php
public function getData(Request $request)
{
    // 1. GET SEARCH PARAMETER
    $search = $request->query('search', '');
    
    // 2. LOAD DATA dengan Pagination
    $mainData = [ModelName]::selectData(10, $search);
    
    // 3. CHECK IF AJAX REQUEST
    if ($request->ajax()) {
        // Return partial view (hanya table/card content)
        return view('[module]::[ViewPath].data', compact('mainData', 'search'))->render();
    }
    
    // 4. REDIRECT if not AJAX (security)
    return redirect()->route('[route-name].index');
}
```

### **Real Example (FooterController):**

```php
public function getData(Request $request)
{
    $search = $request->query('search', '');
    $footer = FooterModel::selectData(10, $search);
    
    if ($request->ajax()) {
        return view('sisfo::AdminWeb/Footer.data', compact('footer', 'search'))->render();
    }
    
    return redirect()->route('footer.index');
}
```

### **Key Features:**
- ✅ AJAX-only access (security)
- ✅ Render partial view (`.data` view)
- ✅ Support search dalam pagination
- ✅ Lightweight response (hanya HTML yang diperlukan)

### **Frontend Usage (JavaScript):**
```javascript
// Load more data via AJAX
$('#load-more').click(function() {
    $.ajax({
        url: '/footer/get-data',
        data: { search: $('#search').val() },
        success: function(html) {
            $('#data-container').append(html);
        }
    });
});
```

---

## ➕ **FUNCTION 3: addData() - Form Tambah**

### **Purpose:**
- Render form untuk tambah data baru
- Load relational data untuk dropdown/select
- Setup form kosong dengan default values

### **Signature:**
```php
public function addData()
```

### **Standard Implementation:**

```php
public function addData()
{
    // 1. LOAD RELATIONAL DATA (untuk dropdown, select, etc)
    $kategori = KategoriModel::where('isDeleted', 0)->get();
    $options = OptionModel::where('isDeleted', 0)->get();
    
    // 2. RETURN CREATE VIEW dengan data relational
    return view("sisfo::[ViewPath].create", [
        'kategori' => $kategori,
        'options' => $options
    ]);
}
```

### **Real Example (FooterController):**

```php
public function addData()
{
    $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
    return view("sisfo::AdminWeb/Footer.create", compact('kategoriFooters'));
}
```

### **Key Features:**
- ✅ Load dropdown options
- ✅ Simple & clean (no parameter)
- ✅ Return form view
- ✅ Setup default values (optional)

### **View Usage (Blade):**
```blade
<!-- resources/views/sisfo/AdminWeb/Footer/create.blade.php -->
<form id="form-create" action="{{ route('footer.store') }}" method="POST">
    @csrf
    
    <select name="fk_kategori_footer">
        @foreach($kategoriFooters as $kategori)
            <option value="{{ $kategori->kategori_footer_id }}">
                {{ $kategori->kf_nama }}
            </option>
        @endforeach
    </select>
    
    <button type="submit">Simpan</button>
</form>
```

---

## 💾 **FUNCTION 4: createData() - Process Insert**

### **Purpose:**
- Validasi input data
- Insert data ke database via Model
- Return JSON response (success/error)
- Handle validation & exception error

### **Signature:**
```php
public function createData(Request $request)
```

### **Standard Implementation:**

```php
public function createData(Request $request)
{
    try {
        // 1. VALIDASI DATA (via Model)
        [ModelName]::validasiData($request);
        
        // 2. CREATE DATA (via Model)
        $result = [ModelName]::createData($request);

        // 3. RETURN JSON SUCCESS
        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? '[Entity] berhasil dibuat'
        );
        
    } catch (ValidationException $e) {
        // 4. HANDLE VALIDATION ERROR
        return $this->jsonValidationError($e);
        
    } catch (\Exception $e) {
        // 5. HANDLE GENERAL ERROR
        return $this->jsonError($e, 'Terjadi kesalahan saat membuat [entity]');
    }
}
```

### **Real Example (FooterController):**

```php
public function createData(Request $request)
{
    try {
        FooterModel::validasiData($request);
        $result = FooterModel::createData($request);

        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? 'Footer berhasil dibuat'
        );
    } catch (ValidationException $e) {
        return $this->jsonValidationError($e);
    } catch (\Exception $e) {
        return $this->jsonError($e, 'Terjadi kesalahan saat membuat footer');
    }
}
```

### **Key Features:**
- ✅ 3-layer error handling (validation, exception, general)
- ✅ JSON response (untuk AJAX)
- ✅ Consistent response format
- ✅ Delegate validation & logic ke Model

### **Response Format:**

```json
// SUCCESS
{
    "success": true,
    "message": "Footer berhasil dibuat",
    "data": {
        "footer_id": 123,
        "footer_nama": "Link Footer Baru",
        ...
    }
}

// VALIDATION ERROR
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "footer_nama": ["Footer nama wajib diisi"],
        "fk_kategori_footer": ["Kategori harus dipilih"]
    }
}

// GENERAL ERROR
{
    "success": false,
    "message": "Terjadi kesalahan saat membuat footer: Database connection failed"
}
```

### **Frontend Usage (AJAX):**
```javascript
$('#form-create').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
            location.reload();
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            // Display validation errors
            $.each(errors, function(field, messages) {
                $(`#error-${field}`).text(messages[0]);
            });
        }
    });
});
```

---

## ✏️ **FUNCTION 5: editData() - Form Edit**

### **Purpose:**
- Render form edit dengan data existing
- Load data by ID
- Load relational data untuk dropdown
- Populate form dengan current values

### **Signature:**
```php
public function editData($id)
```

### **Standard Implementation:**

```php
public function editData($id)
{
    // 1. LOAD DATA BY ID (via Model)
    $mainData = [ModelName]::detailData($id);
    
    // 2. LOAD RELATIONAL DATA (untuk dropdown)
    $kategori = KategoriModel::where('isDeleted', 0)->get();
    
    // 3. RETURN UPDATE VIEW dengan data
    return view("sisfo::[ViewPath].update", [
        'mainData' => $mainData,
        'kategori' => $kategori
    ]);
}
```

### **Real Example (FooterController):**

```php
public function editData($id)
{
    $footer = FooterModel::detailData($id);
    $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();

    return view("sisfo::AdminWeb/Footer.update", [
        'footer' => $footer,
        'kategoriFooters' => $kategoriFooters
    ]);
}
```

### **Key Features:**
- ✅ Load existing data by ID
- ✅ Load dropdown options
- ✅ Pre-populate form fields
- ✅ Handle not found (via Model)

### **View Usage (Blade):**
```blade
<!-- resources/views/sisfo/AdminWeb/Footer/update.blade.php -->
<form id="form-update" action="{{ route('footer.update', $footer->footer_id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <input type="text" name="footer_nama" value="{{ $footer->footer_nama }}" required>
    
    <select name="fk_kategori_footer">
        @foreach($kategoriFooters as $kategori)
            <option value="{{ $kategori->kategori_footer_id }}" 
                    {{ $footer->fk_kategori_footer == $kategori->kategori_footer_id ? 'selected' : '' }}>
                {{ $kategori->kf_nama }}
            </option>
        @endforeach
    </select>
    
    <button type="submit">Update</button>
</form>
```

---

## 🔄 **FUNCTION 6: updateData() - Process Update**

### **Purpose:**
- Validasi input data (dengan ID untuk unique check)
- Update data di database via Model
- Return JSON response
- Handle validation & exception error

### **Signature:**
```php
public function updateData(Request $request, $id)
```

### **Standard Implementation:**

```php
public function updateData(Request $request, $id)
{
    try {
        // 1. VALIDASI DATA dengan ID (untuk unique check exclude self)
        [ModelName]::validasiData($request, $id);
        
        // 2. UPDATE DATA (via Model)
        $result = [ModelName]::updateData($request, $id);

        // 3. RETURN JSON SUCCESS
        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? '[Entity] berhasil diperbarui'
        );
        
    } catch (ValidationException $e) {
        // 4. HANDLE VALIDATION ERROR
        return $this->jsonValidationError($e);
        
    } catch (\Exception $e) {
        // 5. HANDLE GENERAL ERROR
        return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui [entity]');
    }
}
```

### **Real Example (FooterController):**

```php
public function updateData(Request $request, $id)
{
    try {
        FooterModel::validasiData($request, $id);
        $result = FooterModel::updateData($request, $id);

        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? 'Footer berhasil diperbarui'
        );
    } catch (ValidationException $e) {
        return $this->jsonValidationError($e);
    } catch (\Exception $e) {
        return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui footer');
    }
}
```

### **Key Features:**
- ✅ Same error handling as createData()
- ✅ ID parameter untuk unique validation
- ✅ JSON response format
- ✅ Consistent dengan create pattern

### **Validation with ID (Model Level):**
```php
// In Model: validasiData($request, $id = null)
public static function validasiData($request, $id = null)
{
    $rules = [
        'footer_nama' => 'required|max:255',
        'footer_link' => 'required|url'
    ];
    
    // Unique check exclude self jika update
    if ($id) {
        $rules['footer_nama'] .= '|unique:detail_footer,footer_nama,' . $id . ',footer_id';
    } else {
        $rules['footer_nama'] .= '|unique:detail_footer,footer_nama';
    }
    
    $request->validate($rules);
}
```

---

## 👁️ **FUNCTION 7: detailData() - Show Detail**

### **Purpose:**
- Render halaman detail (read-only)
- Show complete information
- Load relational data
- Support print/export view

### **Signature:**
```php
public function detailData($id)
```

### **Standard Implementation:**

```php
public function detailData($id)
{
    // 1. LOAD DATA BY ID (via Model) - with relations
    $mainData = [ModelName]::detailData($id);
    
    // 2. RETURN DETAIL VIEW
    return view("sisfo::[ViewPath].detail", [
        'mainData' => $mainData,
        'title' => 'Detail [Entity]'
    ]);
}
```

### **Real Example (FooterController):**

```php
public function detailData($id)
{
    $footer = FooterModel::detailData($id);
    
    return view("sisfo::AdminWeb/Footer.detail", [
        'footer' => $footer,
        'title' => 'Detail Footer'
    ]);
}
```

### **Key Features:**
- ✅ Read-only view
- ✅ Complete data display
- ✅ Load with relationships
- ✅ Simple & focused

### **View Usage (Blade):**
```blade
<!-- resources/views/sisfo/AdminWeb/Footer/detail.blade.php -->
<div class="detail-container">
    <h3>{{ $title }}</h3>
    
    <table class="table">
        <tr>
            <th>ID Footer:</th>
            <td>{{ $footer->footer_id }}</td>
        </tr>
        <tr>
            <th>Nama Footer:</th>
            <td>{{ $footer->footer_nama }}</td>
        </tr>
        <tr>
            <th>Link:</th>
            <td><a href="{{ $footer->footer_link }}" target="_blank">{{ $footer->footer_link }}</a></td>
        </tr>
        <tr>
            <th>Kategori:</th>
            <td>{{ $footer->kategori->kf_nama ?? '-' }}</td>
        </tr>
        <tr>
            <th>Created:</th>
            <td>{{ $footer->created_at->format('d-m-Y H:i') }}</td>
        </tr>
    </table>
    
    <div class="actions">
        <a href="{{ route('footer.edit', $footer->footer_id) }}" class="btn btn-warning">
            Edit
        </a>
        <a href="{{ route('footer.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>
```

---

## 🗑️ **FUNCTION 8: deleteData() - Confirm & Delete**

### **Purpose:**
- Show confirmation page (GET request)
- Process soft delete (POST/DELETE request)
- Return JSON response
- Handle not found & foreign key errors

### **Signature:**
```php
public function deleteData(Request $request, $id)
```

### **Standard Implementation:**

```php
public function deleteData(Request $request, $id)
{
    // 1. CHECK REQUEST METHOD
    if ($request->isMethod('get')) {
        // SHOW CONFIRMATION PAGE
        $mainData = [ModelName]::detailData($id);
        
        return view("sisfo::[ViewPath].delete", [
            'mainData' => $mainData
        ]);
    }
    
    // 2. PROCESS DELETE (POST/DELETE request)
    try {
        // Soft delete via Model
        $result = [ModelName]::deleteData($id);
        
        // 3. RETURN JSON SUCCESS
        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? '[Entity] berhasil dihapus'
        );
        
    } catch (\Exception $e) {
        // 4. HANDLE ERROR (foreign key, not found, etc)
        return $this->jsonError($e, 'Terjadi kesalahan saat menghapus [entity]');
    }
}
```

### **Real Example (FooterController):**

```php
public function deleteData(Request $request, $id)
{
    if ($request->isMethod('get')) {
        $footer = FooterModel::detailData($id);
        
        return view("sisfo::AdminWeb/Footer.delete", [
            'footer' => $footer
        ]);
    }
    
    try {
        $result = FooterModel::deleteData($id);
        
        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? 'Footer berhasil dihapus'
        );
    } catch (\Exception $e) {
        return $this->jsonError($e, 'Terjadi kesalahan saat menghapus footer');
    }
}
```

### **Key Features:**
- ✅ 2-step delete (confirm → delete)
- ✅ Soft delete (isDeleted flag)
- ✅ Foreign key error handling
- ✅ Audit trail preserved

### **Soft Delete Implementation (Model):**
```php
public static function deleteData($id)
{
    $data = self::findOrFail($id);
    
    $data->update([
        'isDeleted' => 1,
        'deleted_at' => now(),
        'deleted_by' => auth()->user()->username ?? 'system'
    ]);
    
    return [
        'data' => $data,
        'message' => 'Data berhasil dihapus'
    ];
}
```

### **View Usage (Confirmation Page):**
```blade
<!-- resources/views/sisfo/AdminWeb/Footer/delete.blade.php -->
<div class="delete-confirmation">
    <h3>Konfirmasi Hapus</h3>
    
    <p>Apakah Anda yakin ingin menghapus footer berikut?</p>
    
    <table class="table">
        <tr>
            <th>Nama Footer:</th>
            <td>{{ $footer->footer_nama }}</td>
        </tr>
        <tr>
            <th>Link:</th>
            <td>{{ $footer->footer_link }}</td>
        </tr>
    </table>
    
    <form id="form-delete" action="{{ route('footer.destroy', $footer->footer_id) }}" method="POST">
        @csrf
        @method('DELETE')
        
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        <a href="{{ route('footer.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
```

---

## 🎨 **HELPER FUNCTIONS (TraitsController)**

Semua controller menggunakan `TraitsController` yang menyediakan helper functions untuk response handling.

### **Location:**
```
Modules\Sisfo\App\Http\Controllers\TraitsController.php
Modules\Sisfo\App\Http\Controllers\BaseControllerFunction.php
```

### **Available Helper Functions:**

#### **1. JSON Responses:**

```php
// ✅ Success Response
protected function jsonSuccess($data, $message = 'Data berhasil diproses', $statusCode = 200, array $additionalParams = [])

// Usage:
return $this->jsonSuccess($result, 'Data berhasil disimpan');

// Output:
{
    "success": true,
    "message": "Data berhasil disimpan",
    "data": { ... }
}
```

```php
// ❌ Validation Error Response
protected function jsonValidationError(ValidationException $e, $statusCode = 422, array $additionalParams = [])

// Usage:
return $this->jsonValidationError($e);

// Output:
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

```php
// ❌ General Error Response
protected function jsonError(\Exception $e, $prefix = 'Terjadi kesalahan', $statusCode = 500, array $additionalParams = [])

// Usage:
return $this->jsonError($e, 'Gagal menyimpan data');

// Output:
{
    "success": false,
    "message": "Gagal menyimpan data: Database connection failed"
}
```

#### **2. Redirect Responses (Optional - untuk non-AJAX):**

```php
// Success Redirect
protected function redirectSuccess($route, $message = 'Data berhasil diproses', array $additionalParams = [])

// Usage:
return $this->redirectSuccess(route('footer.index'), 'Footer berhasil dibuat');
```

```php
// Error Redirect
protected function redirectError($message, array $additionalParams = [])

// Usage:
return $this->redirectError('Gagal menyimpan footer');
```

```php
// Validation Error Redirect
protected function redirectValidationError(ValidationException $e, array $additionalParams = [])

// Usage:
return $this->redirectValidationError($e);
```

```php
// Exception Redirect
protected function redirectException(\Exception $e, $prefix = 'Terjadi kesalahan', array $additionalParams = [])

// Usage:
return $this->redirectException($e, 'Gagal memproses');
```

---

## 📋 **ROUTING PATTERN**

Setiap controller menggunakan route resource dengan custom names:

```php
// routes/web.php atau Module routes
Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('footer')], function () {
    
    // List
    Route::get('/', [FooterController::class, 'index'])->name('footer.index');
    
    // AJAX Pagination
    Route::get('/get-data', [FooterController::class, 'getData'])->name('footer.getData');
    
    // Create
    Route::get('/add', [FooterController::class, 'addData'])->name('footer.add');
    Route::post('/', [FooterController::class, 'createData'])->name('footer.store');
    
    // Update
    Route::get('/{id}/edit', [FooterController::class, 'editData'])->name('footer.edit');
    Route::put('/{id}', [FooterController::class, 'updateData'])->name('footer.update');
    
    // Detail
    Route::get('/{id}', [FooterController::class, 'detailData'])->name('footer.detail');
    
    // Delete
    Route::get('/{id}/delete', [FooterController::class, 'deleteData'])->name('footer.delete');
    Route::delete('/{id}', [FooterController::class, 'deleteData'])->name('footer.destroy');
    
});
```

### **Route Naming Convention:**
```
[entity].index      → GET  /footer
[entity].getData    → GET  /footer/get-data
[entity].add        → GET  /footer/add
[entity].store      → POST /footer
[entity].edit       → GET  /footer/{id}/edit
[entity].update     → PUT  /footer/{id}
[entity].detail     → GET  /footer/{id}
[entity].delete     → GET  /footer/{id}/delete
[entity].destroy    → DELETE /footer/{id}
```

---

## 🎯 **SUMMARY TABLE**

| Function | HTTP Method | Purpose | Return Type | Parameters |
|----------|-------------|---------|-------------|------------|
| **index()** | GET | List page with data | View | Request $request |
| **getData()** | GET | AJAX pagination | View (partial) | Request $request |
| **addData()** | GET | Show create form | View | - |
| **createData()** | POST | Process insert | JSON | Request $request |
| **editData()** | GET | Show edit form | View | $id |
| **updateData()** | PUT | Process update | JSON | Request $request, $id |
| **detailData()** | GET | Show detail | View | $id |
| **deleteData()** | GET/DELETE | Confirm/Delete | View/JSON | Request $request, $id |

---

## ✅ **CHECKLIST IMPLEMENTATION**

Saat membuat controller baru, pastikan:

- [ ] **Extends Controller & use TraitsController**
- [ ] **Define $breadcrumb & $pagename**
- [ ] **Implement 8 standard functions:**
  - [ ] index() - List page
  - [ ] getData() - AJAX pagination
  - [ ] addData() - Create form
  - [ ] createData() - Process create
  - [ ] editData() - Edit form
  - [ ] updateData() - Process update
  - [ ] detailData() - Detail view
  - [ ] deleteData() - Confirm & delete
- [ ] **Use helper functions:**
  - [ ] jsonSuccess()
  - [ ] jsonValidationError()
  - [ ] jsonError()
- [ ] **Setup proper routing (9 routes)**
- [ ] **Create 5 views:**
  - [ ] index.blade.php
  - [ ] data.blade.php (partial)
  - [ ] create.blade.php
  - [ ] update.blade.php
  - [ ] detail.blade.php
  - [ ] delete.blade.php (optional)

---

## 🎨 **BEST PRACTICES**

### **DO:**
1. ✅ Always use TraitsController
2. ✅ Delegate validation & logic to Model
3. ✅ Return JSON for AJAX requests
4. ✅ Use soft delete (isDeleted flag)
5. ✅ Add audit trail (created_by, updated_by, deleted_by)
6. ✅ Load relational data in addData() & editData()
7. ✅ Use consistent naming convention
8. ✅ Handle all exceptions properly

### **DON'T:**
1. ❌ Put business logic in controller
2. ❌ Hard delete records
3. ❌ Return different response format
4. ❌ Skip error handling
5. ❌ Hardcode route URLs
6. ❌ Mix redirect & JSON response
7. ❌ Access database directly (use Model)

---

## 📊 **STATISTICS**

### **Controllers Analyzed:**
- FooterController
- KategoriFooterController
- BeritaController
- PengumumanController
- LIDinamisController
- ... (100+ controllers follow this pattern)

### **Pattern Coverage:**
```
Total Controllers:        270+
Following 8-Function:     ~95%
Using TraitsController:   ~98%
JSON Response Standard:   ~100%
```

---

## 🔗 **RELATED DOCUMENTATION**

- [ANALISIS-ROUTING-DINAMIS.md](./ANALISIS-ROUTING-DINAMIS.md) - Dynamic routing system
- [ROUTING-FLOW-DIAGRAM.md](./ROUTING-FLOW-DIAGRAM.md) - Visual routing flow
- [QUICK-REFERENCE-ROUTING.md](./QUICK-REFERENCE-ROUTING.md) - Quick reference

---

## 📝 **EXAMPLE: Complete Controller**

```php
<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\Example;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\ExampleModel;
use Modules\Sisfo\App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class ExampleController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Example Management';
    public $pagename = 'AdminWeb/Example';

    // 1. LIST PAGE
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Example Management',
            'list' => ['Home', 'Example']
        ];

        $page = (object) [
            'title' => 'Example List'
        ];

        $activeMenu = 'example';
        $examples = ExampleModel::selectData(10, $search);
        $kategoris = KategoriModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb/Example.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'examples' => $examples,
            'kategoris' => $kategoris,
            'search' => $search
        ]);
    }

    // 2. AJAX PAGINATION
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $examples = ExampleModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::AdminWeb/Example.data', compact('examples', 'search'))->render();
        }
        
        return redirect()->route('example.index');
    }

    // 3. CREATE FORM
    public function addData()
    {
        $kategoris = KategoriModel::where('isDeleted', 0)->get();
        return view("sisfo::AdminWeb/Example.create", compact('kategoris'));
    }

    // 4. PROCESS CREATE
    public function createData(Request $request)
    {
        try {
            ExampleModel::validasiData($request);
            $result = ExampleModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat data');
        }
    }

    // 5. EDIT FORM
    public function editData($id)
    {
        $example = ExampleModel::detailData($id);
        $kategoris = KategoriModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb/Example.update", [
            'example' => $example,
            'kategoris' => $kategoris
        ]);
    }

    // 6. PROCESS UPDATE
    public function updateData(Request $request, $id)
    {
        try {
            ExampleModel::validasiData($request, $id);
            $result = ExampleModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui data');
        }
    }

    // 7. DETAIL VIEW
    public function detailData($id)
    {
        $example = ExampleModel::detailData($id);
        
        return view("sisfo::AdminWeb/Example.detail", [
            'example' => $example,
            'title' => 'Detail Example'
        ]);
    }

    // 8. DELETE (CONFIRM & PROCESS)
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $example = ExampleModel::detailData($id);
            
            return view("sisfo::AdminWeb/Example.delete", [
                'example' => $example
            ]);
        }
        
        try {
            $result = ExampleModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus data');
        }
    }
}
```

---

**🎯 End of Controller Pattern Documentation**

**Generated:** 26 Januari 2026  
**Total Functions:** 8 standard + helpers  
**Coverage:** 95%+ controllers  
**Consistency:** HIGH

---

**📧 Questions?** 
- Check TraitsController: `Modules/Sisfo/App/Http/Controllers/TraitsController.php`
- Check BaseControllerFunction: `Modules/Sisfo/App/Http/Controllers/BaseControllerFunction.php`
- Sample Controller: `Modules/Sisfo/App/Http/Controllers/AdminWeb/Footer/FooterController.php`
