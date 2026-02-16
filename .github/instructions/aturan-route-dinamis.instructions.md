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

jika terdapat function tambahan seperti export() yang digunakan misal di function addData() maka function tersebut dipanggil di function addData() dan tidak perlu dibuat route baru untuk function export() tersebut.

contohnya seperti ini:
```php
    public function addData($id = null)
    {
        // Logic untuk menampilkan form add/edit

        // Jika ada kebutuhan untuk export data, panggil function export() di sini
        $exportData = $this->export();

        // Return form view dengan data export jika diperlukan
        return view('form', compact('exportData'));
    }

    private function export()
    {
        // Logic untuk mengekspor data
        return 'Data yang diekspor';
    }
```

jika terdapat 2 function tambahan seperti export() dan import() yang digunakan di function addData() maka dibikin sebua function handle() yang kemudian function handle tersebut yang memanggil function export() dan import() sehingga function addData() hanya memanggil function handle() saja.
contohnya seperti ini:
```php
    public function addData($id = null)
    {
        // Logic untuk menampilkan form add/edit

        // Panggil function handle() untuk mengelola export dan import
        $handleData = $this->handle();

        // Return form view dengan data handle jika diperlukan
        return view('form', compact('handleData'));
    }

    private function handle()
    {
        // Logic untuk mengekspor data
        $exportData = $this->export();

        // Logic untuk mengimpor data
        $importData = $this->import();

        // Kembalikan hasil dari kedua proses jika diperlukan
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