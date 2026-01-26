---
applyTo: '**'
---

# 📘 MINIMUM ROUTE DINAMIS - Panduan Implementasi Sederhana

## 🎯 TUJUAN

Implementasi sistem routing yang **konsisten** dan **terpusat** menggunakan 1 controller universal (PageController) untuk meng-handle semua route dengan pattern yang seragam:

```php
/{page}              → index()   // List data
/{page}              → store()   // Save data (POST)
/{page}/add          → add()     // Form tambah
/{page}/edit/{id}    → edit()    // Form edit
/{page}/show/{id}    → show()    // Detail
/{page}/destroy/{id} → destroy() // Delete
/{page}/api/{action} → api()     // API endpoint
```

**TIDAK TERMASUK:**
- ❌ Auto-generate CRUD
- ❌ Layout system (standr, master, report)
- ❌ sys_table configuration
- ❌ Permission system yang kompleks

**YANG TERMASUK:**
- ✅ Route pattern yang konsisten
- ✅ Controller resolution otomatis
- ✅ Basic authorization check
- ✅ Simple database structure

---

## 📊 BAGIAN 1: STRUKTUR DATABASE MINIMAL

### 1.1 Tabel `sys_menu` - Daftar Menu/Halaman

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_menu', function (Blueprint $table) {
            $table->char('menu_id', 10)->primary();      // ID menu: users, products, orders
            $table->string('menu_name', 50);             // Nama menu: User Management
            $table->string('url', 50)->unique();         // URL routing: users, products
            $table->string('controller', 50);            // Nama controller: UserController
            $table->string('icon', 50)->nullable();      // Icon (optional)
            $table->integer('sort_order')->default(0);   // Urutan tampil
            $table->enum('is_active', [0, 1])->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_menu');
    }
};
```

**Penjelasan Field:**
- `menu_id`: ID unik menu (primary key)
- `url`: URL yang akan diakses (harus match dengan route `/{page}`)
- `controller`: Nama controller yang akan di-resolve (tanpa namespace)
- `icon`: Icon untuk sidebar/menu (optional)
- `sort_order`: Urutan tampil di menu

---

### 1.2 Tabel `sys_roles` - Master Role

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_roles', function (Blueprint $table) {
            $table->char('role_id', 10)->primary();      // ID role: admin, user, manager
            $table->string('role_name', 50);             // Nama role: Administrator
            $table->text('description')->nullable();      // Deskripsi role
            $table->enum('is_active', [0, 1])->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_roles');
    }
};
```

---

### 1.3 Tabel `sys_menu_access` - Permission Sederhana

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_menu_access', function (Blueprint $table) {
            $table->char('role_id', 10);
            $table->char('menu_id', 10);
            $table->enum('can_view', [0, 1])->default(1);   // Boleh akses menu
            $table->enum('can_create', [0, 1])->default(0); // Boleh create
            $table->enum('can_edit', [0, 1])->default(0);   // Boleh edit
            $table->enum('can_delete', [0, 1])->default(0); // Boleh delete
            $table->enum('is_active', [0, 1])->default(1);
            $table->timestamps();
            
            $table->primary(['role_id', 'menu_id']);
            $table->foreign('role_id')->references('role_id')->on('sys_roles')->onDelete('cascade');
            $table->foreign('menu_id')->references('menu_id')->on('sys_menu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_menu_access');
    }
};
```

---

### 1.4 Update Tabel Users (Tambah role_id)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('role_id', 10)->nullable()->after('password');
            $table->foreign('role_id')->references('role_id')->on('sys_roles');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
```

---

## 🔧 BAGIAN 2: SEEDER DATA

### 2.1 DatabaseSeeder.php

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insert Roles
        DB::table('sys_roles')->insert([
            [
                'role_id' => 'admin',
                'role_name' => 'Administrator',
                'description' => 'Full system access',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_id' => 'user',
                'role_name' => 'Regular User',
                'description' => 'Limited access',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 2. Insert Users
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role_id' => 'user',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 3. Insert Menu
        DB::table('sys_menu')->insert([
            [
                'menu_id' => 'dashboard',
                'menu_name' => 'Dashboard',
                'url' => 'dashboard',
                'controller' => 'DashboardController',
                'icon' => 'fas fa-home',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'menu_id' => 'users',
                'menu_name' => 'User Management',
                'url' => 'users',
                'controller' => 'UserController',
                'icon' => 'fas fa-users',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'menu_id' => 'products',
                'menu_name' => 'Products',
                'url' => 'products',
                'controller' => 'ProductController',
                'icon' => 'fas fa-box',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 4. Insert Menu Access - Admin (Full Access)
        DB::table('sys_menu_access')->insert([
            [
                'role_id' => 'admin',
                'menu_id' => 'dashboard',
                'can_view' => 1,
                'can_create' => 1,
                'can_edit' => 1,
                'can_delete' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_id' => 'admin',
                'menu_id' => 'users',
                'can_view' => 1,
                'can_create' => 1,
                'can_edit' => 1,
                'can_delete' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_id' => 'admin',
                'menu_id' => 'products',
                'can_view' => 1,
                'can_create' => 1,
                'can_edit' => 1,
                'can_delete' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 5. Insert Menu Access - User (Limited Access)
        DB::table('sys_menu_access')->insert([
            [
                'role_id' => 'user',
                'menu_id' => 'dashboard',
                'can_view' => 1,
                'can_create' => 0,
                'can_edit' => 0,
                'can_delete' => 0,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_id' => 'user',
                'menu_id' => 'products',
                'can_view' => 1,
                'can_create' => 0,
                'can_edit' => 0,
                'can_delete' => 0,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
```

---

## 🚀 BAGIAN 3: ROUTES & CONTROLLER

### 3.1 Routes (routes/web.php)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;

// Landing page
Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ⭐ DYNAMIC ROUTING - Semua route di-handle oleh PageController
Route::middleware('auth')->group(function () {
    
    // GET /{page} → index() untuk list data
    Route::get('/{page}', [PageController::class, 'index']);
    
    // POST /{page} → store() untuk simpan data
    Route::post('/{page}', [PageController::class, 'index']);
    
    // GET /{page}/{action} → add(), edit(), show(), dll
    Route::get('/{page}/{action}', [PageController::class, 'index']);
    
    // PUT /{page}/{action} → update()
    Route::put('/{page}/{action}', [PageController::class, 'index']);
    
    // DELETE /{page}/{action} → destroy()
    Route::delete('/{page}/{action}', [PageController::class, 'index']);
    
    // GET /{page}/{action}/{id} → Aksi dengan ID (edit/123, show/456)
    Route::get('/{page}/{action}/{id}', [PageController::class, 'index']);
});
```

---

### 3.2 PageController (Controller Universal)

**File:** `app/Http/Controllers/PageController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    /**
     * 🎯 METHOD UTAMA - Universal Router
     * 
     * Method ini akan:
     * 1. Resolve URL ke menu di database
     * 2. Check authorization
     * 3. Resolve ke controller spesifik
     * 4. Call method yang sesuai (index, add, store, dll)
     */
    public function index(string $page, string $action = 'index', string $id = null)
    {
        // 1. GET USER INFO
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        // 2. RESOLVE ACTION BERDASARKAN HTTP METHOD
        $originalAction = $action;
        
        if ($action == 'index') {
            // Jika action = index, cek HTTP method
            if (request()->isMethod('POST')) {
                $action = 'store';
            } elseif (request()->isMethod('PUT')) {
                $action = 'update';
            } elseif (request()->isMethod('DELETE')) {
                $action = 'destroy';
            }
        } else {
            // Jika ada action spesifik (add, edit, dll), cek HTTP method untuk override
            if (request()->isMethod('DELETE')) {
                $action = 'destroy';
                $id = $originalAction; // action jadi ID
            } elseif (request()->isMethod('PUT')) {
                $action = 'update';
                $id = $originalAction; // action jadi ID
            }
        }
        
        // 3. GET MENU DARI DATABASE
        $menu = DB::table('sys_menu')
            ->where('url', $page)
            ->where('is_active', 1)
            ->first();
        
        if (!$menu) {
            abort(404, "Menu '{$page}' not found");
        }
        
        // 4. CHECK AUTHORIZATION
        $access = DB::table('sys_menu_access')
            ->where('role_id', $user->role_id)
            ->where('menu_id', $menu->menu_id)
            ->where('is_active', 1)
            ->first();
        
        if (!$access || !$access->can_view) {
            abort(403, 'Unauthorized access');
        }
        
        // 5. CHECK PERMISSION UNTUK ACTION TERTENTU
        if ($action == 'add' || $action == 'store') {
            if (!$access->can_create) {
                abort(403, 'You do not have permission to create');
            }
        } elseif ($action == 'edit' || $action == 'update') {
            if (!$access->can_edit) {
                abort(403, 'You do not have permission to edit');
            }
        } elseif ($action == 'destroy') {
            if (!$access->can_delete) {
                abort(403, 'You do not have permission to delete');
            }
        }
        
        // 6. PREPARE DATA UNTUK CONTROLLER
        $data = [
            'user' => $user,
            'menu' => $menu,
            'access' => $access,
            'page' => $page,
            'action' => $action,
            'id' => $id
        ];
        
        // 7. RESOLVE KE CONTROLLER SPESIFIK
        $controllerClass = "App\\Http\\Controllers\\" . $menu->controller;
        
        if (!class_exists($controllerClass)) {
            abort(500, "Controller {$menu->controller} not found");
        }
        
        $controller = app($controllerClass);
        
        if (!method_exists($controller, $action)) {
            abort(500, "Method {$action}() not found in {$menu->controller}");
        }
        
        // 8. CALL METHOD DI CONTROLLER
        return $controller->$action($data);
    }
}
```

---

## 📦 BAGIAN 4: CONTOH CONTROLLER IMPLEMENTASI

### 4.1 DashboardController (Contoh Sederhana)

**File:** `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index($data)
    {
        return view('dashboard.index', $data);
    }
}
```

---

### 4.2 UserController (Contoh CRUD Lengkap)

**File:** `app/Http/Controllers/UserController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display list of users
     */
    public function index($data)
    {
        $data['users'] = User::where('role_id', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('users.index', $data);
    }
    
    /**
     * Show create form
     */
    public function add($data)
    {
        return view('users.add', $data);
    }
    
    /**
     * Store new user
     */
    public function store($data)
    {
        $validated = request()->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:sys_roles,role_id'
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        
        return redirect('/' . $data['page'])
            ->with('success', 'User created successfully');
    }
    
    /**
     * Show edit form
     */
    public function edit($data)
    {
        $data['user_edit'] = User::findOrFail($data['id']);
        
        return view('users.edit', $data);
    }
    
    /**
     * Update user
     */
    public function update($data)
    {
        $user = User::findOrFail($data['id']);
        
        $validated = request()->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:sys_roles,role_id'
        ]);
        
        $user->update($validated);
        
        return redirect('/' . $data['page'])
            ->with('success', 'User updated successfully');
    }
    
    /**
     * Show detail
     */
    public function show($data)
    {
        $data['user_detail'] = User::findOrFail($data['id']);
        
        return view('users.show', $data);
    }
    
    /**
     * Delete user
     */
    public function destroy($data)
    {
        $user = User::findOrFail($data['id']);
        $user->delete();
        
        return redirect('/' . $data['page'])
            ->with('success', 'User deleted successfully');
    }
    
    /**
     * API endpoint (opsional untuk AJAX)
     */
    public function api($data)
    {
        // Handle berbagai API request
        $apiAction = $data['id'] ?? 'list';
        
        switch ($apiAction) {
            case 'list':
                $users = User::all();
                return response()->json($users);
                
            case 'search':
                $query = request('q');
                $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->get();
                return response()->json($users);
                
            default:
                return response()->json(['error' => 'Invalid API action'], 400);
        }
    }
}
```

---

### 4.3 ProductController (Contoh Lain)

**File:** `app/Http/Controllers/ProductController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index($data)
    {
        $data['products'] = DB::table('products')
            ->where('is_active', 1)
            ->get();
        
        return view('products.index', $data);
    }
    
    public function add($data)
    {
        return view('products.add', $data);
    }
    
    public function store($data)
    {
        $validated = request()->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);
        
        DB::table('products')->insert([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return redirect('/' . $data['page'])
            ->with('success', 'Product created successfully');
    }
    
    public function edit($data)
    {
        $data['product'] = DB::table('products')
            ->where('id', $data['id'])
            ->first();
        
        return view('products.edit', $data);
    }
    
    public function update($data)
    {
        $validated = request()->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);
        
        DB::table('products')
            ->where('id', $data['id'])
            ->update([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'updated_at' => now()
            ]);
        
        return redirect('/' . $data['page'])
            ->with('success', 'Product updated successfully');
    }
    
    public function show($data)
    {
        $data['product'] = DB::table('products')
            ->where('id', $data['id'])
            ->first();
        
        return view('products.show', $data);
    }
    
    public function destroy($data)
    {
        DB::table('products')
            ->where('id', $data['id'])
            ->update([
                'is_active' => 0,
                'updated_at' => now()
            ]);
        
        return redirect('/' . $data['page'])
            ->with('success', 'Product deleted successfully');
    }
}
```

---

## 🔍 BAGIAN 5: ALUR KERJA SISTEM

### 5.1 Flow Diagram

```
┌─────────────────────────────────────────┐
│ 1. USER REQUEST                         │
│    GET /users/edit/123                  │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│ 2. ROUTE MATCHING                       │
│    Route: /{page}/{action}/{id}         │
│    → $page = 'users'                    │
│    → $action = 'edit'                   │
│    → $id = '123'                        │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│ 3. PageController::index()              │
│    ┌──────────────────────────────┐    │
│    │ 3.1 Get User Session         │    │
│    │     → Auth::user()            │    │
│    └────────────┬─────────────────┘    │
│                 │                       │
│    ┌────────────▼─────────────────┐    │
│    │ 3.2 Query sys_menu           │    │
│    │     WHERE url = 'users'      │    │
│    │     → controller = UserCtrl  │    │
│    └────────────┬─────────────────┘    │
│                 │                       │
│    ┌────────────▼─────────────────┐    │
│    │ 3.3 Check sys_menu_access    │    │
│    │     WHERE role_id = user     │    │
│    │       AND menu_id = users    │    │
│    │     → can_edit = 1           │    │
│    └────────────┬─────────────────┘    │
│                 │                       │
│    ┌────────────▼─────────────────┐    │
│    │ 3.4 Resolve Controller       │    │
│    │     → UserController         │    │
│    └────────────┬─────────────────┘    │
│                 │                       │
│    ┌────────────▼─────────────────┐    │
│    │ 3.5 Call Method              │    │
│    │     → edit($data)            │    │
│    └────────────┬─────────────────┘    │
└─────────────────┼─────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│ 4. UserController::edit($data)          │
│    - Get user by ID                     │
│    - Return view users.edit             │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│ 5. RESPONSE                             │
│    View: resources/views/users/edit.php │
└─────────────────────────────────────────┘
```

---

### 5.2 Mapping URL ke Method

| HTTP Method | URL Pattern | Controller Method | Keterangan |
|------------|-------------|-------------------|------------|
| GET | `/users` | `index()` | List users |
| GET | `/users/add` | `add()` | Form tambah user |
| POST | `/users` | `store()` | Simpan user baru |
| GET | `/users/edit/123` | `edit()` | Form edit user ID 123 |
| PUT | `/users/edit/123` | `update()` | Update user ID 123 |
| GET | `/users/show/123` | `show()` | Detail user ID 123 |
| DELETE | `/users/destroy/123` | `destroy()` | Hapus user ID 123 |
| GET | `/users/api/search` | `api()` | API endpoint |

---

## ✅ BAGIAN 6: CHECKLIST IMPLEMENTASI

```
[ ] 1. DATABASE SETUP
    [ ] Buat migration sys_menu
    [ ] Buat migration sys_roles
    [ ] Buat migration sys_menu_access
    [ ] Update migration users (tambah role_id)
    [ ] Run: php artisan migrate
    [ ] Buat DatabaseSeeder dengan data sample
    [ ] Run: php artisan db:seed

[ ] 2. ROUTES
    [ ] Update routes/web.php dengan pattern dinamis
    [ ] Test: php artisan route:list

[ ] 3. CONTROLLERS
    [ ] Buat PageController (universal router)
    [ ] Buat DashboardController (contoh)
    [ ] Buat UserController (contoh CRUD)
    [ ] Buat ProductController (contoh CRUD)

[ ] 4. VIEWS (Optional - sesuai kebutuhan)
    [ ] Buat views/dashboard/index.blade.php
    [ ] Buat views/users/index.blade.php
    [ ] Buat views/users/add.blade.php
    [ ] Buat views/users/edit.blade.php
    [ ] Buat views/users/show.blade.php

[ ] 5. TESTING
    [ ] Test akses menu dengan role admin
    [ ] Test akses menu dengan role user
    [ ] Test CRUD operations
    [ ] Test authorization (403 error)
    [ ] Test menu not found (404 error)
```

---

## 📝 BAGIAN 7: CARA TAMBAH MENU BARU

### Contoh: Tambah Menu "Orders"

**STEP 1: Insert ke sys_menu**
```php
DB::table('sys_menu')->insert([
    'menu_id' => 'orders',
    'menu_name' => 'Order Management',
    'url' => 'orders',
    'controller' => 'OrderController',
    'icon' => 'fas fa-shopping-cart',
    'sort_order' => 4,
    'is_active' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);
```

**STEP 2: Set Permission**
```php
// Admin - Full Access
DB::table('sys_menu_access')->insert([
    'role_id' => 'admin',
    'menu_id' => 'orders',
    'can_view' => 1,
    'can_create' => 1,
    'can_edit' => 1,
    'can_delete' => 1,
    'is_active' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);
```

**STEP 3: Buat Controller**
```php
php artisan make:controller OrderController
```

**STEP 4: Implement Methods**
```php
<?php

namespace App\Http\Controllers;

class OrderController extends Controller
{
    public function index($data)
    {
        // List orders
        return view('orders.index', $data);
    }
    
    public function add($data)
    {
        // Form create order
        return view('orders.add', $data);
    }
    
    public function store($data)
    {
        // Save order
    }
    
    // ... method lainnya
}
```

**SELESAI!** Menu baru otomatis bisa diakses di `/orders`

---

## 🎓 CONTOH PENGGUNAAN

### 1. Akses List User
```
URL: GET /users
→ PageController::index('users', 'index', null)
→ UserController::index($data)
→ View: users/index.blade.php
```

### 2. Tambah User
```
URL: GET /users/add
→ PageController::index('users', 'add', null)
→ UserController::add($data)
→ View: users/add.blade.php
```

### 3. Simpan User Baru
```
URL: POST /users
→ PageController::index('users', 'index', null) + POST method
→ Resolve action = 'store'
→ UserController::store($data)
→ Redirect to /users
```

### 4. Edit User
```
URL: GET /users/edit/123
→ PageController::index('users', 'edit', '123')
→ UserController::edit($data) dengan $data['id'] = 123
→ View: users/edit.blade.php
```

### 5. Update User
```
URL: PUT /users/edit/123
→ PageController::index('users', 'edit', '123') + PUT method
→ Resolve action = 'update'
→ UserController::update($data) dengan $data['id'] = 123
→ Redirect to /users
```

### 6. Hapus User
```
URL: DELETE /users/destroy/123
→ PageController::index('users', 'destroy', '123') + DELETE method
→ UserController::destroy($data) dengan $data['id'] = 123
→ Redirect to /users
```

---

## 🔚 PENUTUP

### Keunggulan Minimum Route Dinamis:
1. ✅ **Simple** - Hanya 3 tabel, 1 PageController
2. ✅ **Consistent** - Pattern route yang sama untuk semua menu
3. ✅ **Flexible** - Mudah tambah menu baru tanpa edit route
4. ✅ **Secure** - Basic authorization check
5. ✅ **Clean Code** - Tidak perlu banyak route definition

### Yang TIDAK Ada (vs Versi Kompleks):
- ❌ Auto-generate CRUD
- ❌ sys_table configuration
- ❌ Multiple layout system
- ❌ Advanced permission system

### Untuk Upgrade ke Versi Kompleks:
Lihat: `route-dinamis.instructions.md`

---

**©Dynamic Routing System**