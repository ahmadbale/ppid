# 1. Untuk Function dicontroller:
function utama hanya ada 8 function saja yaitu:
```php 
    public function index(Request $request)
    {
        // Return list view
    }

    // 2. ✅ GET DATA (AJAX untuk datatable)
    public function getData(Request $request)
    {
        // Return JSON data
    }

    // 3. ✅ SHOW ADD FORM
    public function addData($id = null)
    {
        // Return form view
    }

    // 4. ✅ CREATE DATA (POST /)
    public function createData(Request $request)
    {
        // Validate & save
        // Redirect atau return JSON
    }

    // 5. ✅ SHOW EDIT FORM
    public function editData($id)
    {
        // Get data by ID
        // Return form view
    }

    // 6. ✅ UPDATE DATA (POST/PUT /updateData/{id})
    public function updateData(Request $request, $id)
    {
        // Validate & update
        // Redirect atau return JSON
    }

    // 7. ✅ SHOW DETAIL
    public function detailData($id)
    {
        // Get data by ID
        // Return detail view
    }

    // 8. ✅ DELETE DATA - GET (confirm page)
    // 9. ✅ DELETE DATA - DELETE (process)
    public function deleteData(Request $request, $id)
    {
        // Check HTTP method
        if ($request->isMethod('GET')) {
            // Show confirm page
        } else {
            // Process delete
        }
    }
```

## Aturan Function Tambahan

### Rule 1: Function tambahan HARUS private
Semua function tambahan (yang bukan dari 8 function utama) HARUS dibuat private agar tidak melanggar aturan maksimal 8 public function.

### Rule 2: Pemanggilan di Function Main
Function tambahan dipanggil di function main (index, getData, addData, createData, editData, updateData, detailData, deleteData) sesuai kebutuhan logika bisnis.

**Tidak ada aturan ketat harus dipanggil di function tertentu** - pilih function main yang paling sesuai dengan konteks bisnis.

### Rule 3: Jika HANYA 1 Function Tambahan
Panggil langsung di function main yang sesuai.

**Contoh: Export di addData()**
```php
public function addData($id = null)
{
    // Logic untuk menampilkan form add/edit
    
    // Panggil function export() langsung jika dibutuhkan
    $exportData = $this->export();
    
    // Return form view dengan data export
    return view('form', compact('exportData'));
}

private function export()
{
    // Logic untuk mengekspor data
    return 'Data yang diekspor';
}
```

**Contoh: Validasi AJAX di createData()**
```php
public function createData(Request $request)
{
    // Handle AJAX validation request
    if ($request->input('action') === 'validate') {
        return $this->validateAjax($request);
    }
    
    // Normal create logic
    $result = Model::createData($request);
    return $this->jsonSuccess($result);
}

private function validateAjax(Request $request)
{
    // Logic validasi AJAX
    return response()->json(['valid' => true]);
}
```

### Rule 4: Jika LEBIH DARI 1 Function Tambahan
Wajib buat function **handle()** sebagai dispatcher, kemudian handle() yang memanggil function-function tambahan.

**Contoh: Export dan Import di addData()**
```php
public function addData($id = null)
{
    // Logic untuk menampilkan form add/edit
    
    // Panggil function handle() untuk mengelola multiple operations
    $handleData = $this->handle();
    
    // Return form view dengan data handle
    return view('form', compact('handleData'));
}

private function handle()
{
    // Dispatcher untuk multiple functions
    $exportData = $this->export();
    $importData = $this->import();
    
    // Kembalikan hasil dari semua proses
    return [
        'export' => $exportData,
        'import' => $importData,
    ];
}

private function export()
{
    // Logic untuk mengekspor data
    return 'Data yang diekspor';
}

private function import()
{
    // Logic untuk mengimpor data
    return 'Data yang diimpor';
}
```

**Contoh: ValidateTable dan AutoGenerateFields di createData()**
```php
public function createData(Request $request)
{
    try {
        // Handle AJAX requests dari function tambahan (validateTable, autoGenerateFields)
        $handled = $this->handle($request);
        if ($handled !== null) {
            return $handled; // Return AJAX response
        }

        // Normal create logic (bukan AJAX)
        Model::validasiData($request);
        $result = Model::createData($request);
        return $this->jsonSuccess($result);
    } catch (\Exception $e) {
        return $this->jsonError($e);
    }
}

private function handle(Request $request)
{
    $action = $request->input('action');
    
    // Dispatcher berdasarkan action parameter
    if ($action === 'validateTable') {
        return $this->validateTable($request);
    }
    
    if ($action === 'autoGenerateFields') {
        return $this->autoGenerateFields($request);
    }
    
    return null; // Bukan AJAX request, lanjut ke normal flow
}

private function validateTable(Request $request)
{
    $tableName = $request->input('table_name');
    $result = Model::validateTable($tableName);
    return $this->jsonSuccess($result);
}

private function autoGenerateFields(Request $request)
{
    $tableName = $request->input('table_name');
    $result = Model::autoGenerateFields($tableName);
    return $this->jsonSuccess($result);
}
```

### Kapan Pakai handle()?
- ✅ **WAJIB** jika ada 2+ function tambahan yang dipanggil di function main yang sama
- ✅ **WAJIB** jika ada conditional logic untuk memilih function tambahan mana yang dipanggil
- ❌ **TIDAK PERLU** jika hanya 1 function tambahan (panggil langsung saja)

### Keuntungan Pattern Ini:
1. ✅ Controller tetap bersih dengan maksimal 8 public methods
2. ✅ Function tambahan tetap private (tidak exposed ke route)
3. ✅ Tidak perlu tambah route manual di web.php
4. ✅ Mudah maintain karena dispatcher terpusat di handle()
5. ✅ Fleksibel - function main bisa disesuaikan dengan konteks bisnis

# 2. Untuk Route:
dikarenakan terdapat 9 method dan 8 function utama maka untuk route hanya ada 9 route saja yaitu:
```php
Route::get('/', [NamaController::class, 'index']);
Route::get('/getData', [NamaController::class, 'getData']);
Route::get('/addData', [NamaController::class, 'addData']);
Route::post('/createData', [NamaController::class, 'createData']);
Route::get('/editData/{id}', [NamaController::class, 'editData']);
Route::post('/updateData/{id}', [NamaController::class, 'updateData']);
Route::get('/detailData/{id}', [NamaController::class, 'detailData']);
Route::get('/deleteData/{id}', [NamaController::class, 'deleteData']);
Route::delete('/deleteData/{id}', [NamaController::class, 'deleteData']);
```

nah dengan didteapkan route standart seperti diatas maka semua menu dengan route yang sama tidak perlu menuliskan kode route lagi jadi tinggal menggunakan route patern sebagai berikut:
```php
    // Pattern 1: /{page} → index() atau store() tergantung HTTP method
    Route::match(['GET', 'POST'], '/{page}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', RouteHelper::getDynamicRoutePattern());

    // Pattern 2: /{page}/{action} → getData(), addData(), editData/{id}, dll
    Route::match(['GET', 'POST'], '/{page}/{action}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', RouteHelper::getDynamicRoutePattern())
        ->where('action', '[a-zA-Z0-9\-]+');

    // Pattern 3: /{page}/{action}/{id} → editData/123, updateData/123, deleteData/123
    Route::match(['GET', 'POST', 'PUT', 'DELETE'], '/{page}/{action}/{id}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', RouteHelper::getDynamicRoutePattern())
        ->where('action', '[a-zA-Z0-9\-]+')
        ->where('id', '[0-9]+');
```

jadi semua menu dengan route yang tidak lebih dari route standart (boleh kurang) tidak perlu menuliskan kode route lagi

# 3. Untuk Views
agar semua menu mudah dimaintenance maka ditentukan views utama dan paten yaitu hanya boleh menggunakan 6 views utama tidak boleh lebih tapi boleh kurang yaitu:
- index.blade.php 
- data.blade.php
- create.blade.php
- update.blade.php
- detail.blade.php
- delete.blade.php