# 📋 QUICK REFERENCE - DYNAMIC ROUTING PPID POLINEMA

**Last Updated:** 26 Januari 2026

---

## 🎯 **KONSEP DASAR**

```
URL Request → getDynamicMenuUrl() → Database Query → Route Prefix
```

**4 Tabel Utama:**
```
m_application → web_menu_url → web_menu_global → web_menu (per role)
```

---

## 📊 **DATABASE STATS**

| Item | Count | Status |
|------|-------|--------|
| **Aplikasi** | 2 | app ppid, app siakad |
| **URL Endpoints** | 59 | Semua untuk app ppid |
| **Roles** | 5 | SAR, ADM, MPU, VFR, RPN |
| **Total Tables** | 71 | Database polinema_ppid |

---

## 🔑 **KEY TABLES**

### **1. m_application**
```sql
-- Primary Key: application_id
-- Unique Key: app_key

Data:
ID 1: 'app ppid' - Pejabat Pengelola Informasi dan Dokumentasi
ID 2: 'app siakad' - Sistem Informasi Akademik
```

### **2. web_menu_url**
```sql
-- Primary Key: web_menu_url_id
-- Foreign Key: fk_m_application
-- Important: wmu_nama (URL slug)

Sample Data:
ID 1:  'menu-management'
ID 3:  'beranda'
ID 4:  'profile-ppid'
ID 7:  'permohonan-informasi'
ID 50: 'management-menu-url'
```

### **3. web_menu_global**
```sql
-- Primary Key: web_menu_global_id
-- Foreign Key: fk_web_menu_url
-- Features: Hierarchical (wmg_parent_id)
-- Categories: Menu Biasa, Group Menu, Sub Menu

Key Fields:
- wmg_nama_default: Default menu name
- wmg_urutan_menu: Display order
- wmg_kategori_menu: Menu type
- wmg_status_menu: aktif/nonaktif
```

### **4. web_menu**
```sql
-- Primary Key: web_menu_id
-- Foreign Keys: fk_web_menu_global, fk_m_hak_akses
-- Purpose: Menu instances per role

Key Fields:
- wm_menu_nama: Override name (NULL = use default)
- wm_urutan_menu: Order per role
- wm_parent_id: Parent menu
- wm_status_menu: aktif/nonaktif
```

### **5. m_hak_akses**
```sql
-- Primary Key: hak_akses_id
-- Unique Key: hak_akses_kode

Roles:
ID 1: SAR - Super Administrator
ID 2: ADM - Administrator
ID 3: MPU - Manajemen dan Pimpinan Unit
ID 4: VFR - Verifikator
ID 5: RPN - Responden
```

---

## 🔧 **CORE METHOD**

### **getDynamicMenuUrl()**
```php
// Location: app/Models/WebMenuModel.php

public static function getDynamicMenuUrl(
    $menuName,           // Required: URL slug
    $appKey = 'app ppid' // Default: app ppid
) {
    $result = DB::table('web_menu_url')
        ->join('m_application', 
               'web_menu_url.fk_m_application', 
               '=', 
               'm_application.application_id')
        ->where('m_application.app_key', $appKey)
        ->where('web_menu_url.wmu_nama', $menuName)
        ->where('web_menu_url.isDeleted', 0)
        ->where('m_application.isDeleted', 0)
        ->select('web_menu_url.wmu_nama')
        ->first();
    
    return $result ? $result->wmu_nama : 'error-404';
}
```

**SQL Executed:**
```sql
SELECT web_menu_url.wmu_nama 
FROM web_menu_url
INNER JOIN m_application 
    ON web_menu_url.fk_m_application = m_application.application_id
WHERE m_application.app_key = 'app ppid'
  AND web_menu_url.wmu_nama = 'menu-management'
  AND web_menu_url.isDeleted = 0
  AND m_application.isDeleted = 0;
```

---

## 🚀 **USAGE EXAMPLES**

### **1. Route Registration**
```php
// ✅ CORRECT
Route::group([
    'prefix' => WebMenuModel::getDynamicMenuUrl('menu-management'),
    'middleware' => ['auth', 'role:SAR,ADM']
], function () {
    Route::get('/', [MenuManagementController::class, 'index']);
    Route::post('/store', [MenuManagementController::class, 'store']);
});

// Result URLs:
// http://ppid-polinema.test/menu-management
// http://ppid-polinema.test/menu-management/store
```

### **2. Menu Rendering (Sidebar)**
```php
// Controller
public function sidebar()
{
    $roleCode = auth()->user()->hak_akses_kode; // 'ADM'
    
    $menus = Cache::remember("menu_structure_{$roleCode}", 3600, function() use ($roleCode) {
        return DB::table('web_menu as wm')
            ->join('web_menu_global as wmg', 'wm.fk_web_menu_global', '=', 'wmg.web_menu_global_id')
            ->join('web_menu_url as wmu', 'wmg.fk_web_menu_url', '=', 'wmu.web_menu_url_id')
            ->join('m_hak_akses as ha', 'wm.fk_m_hak_akses', '=', 'ha.hak_akses_id')
            ->where('ha.hak_akses_kode', $roleCode)
            ->where('wm.isDeleted', 0)
            ->where('wm.wm_status_menu', 'aktif')
            ->select('wm.*', 'wmg.*', 'wmu.wmu_nama as url_slug')
            ->orderBy('wm.wm_urutan_menu')
            ->get();
    });
    
    return view('layouts.sidebar', compact('menus'));
}
```

```blade
<!-- Blade Template -->
<ul class="sidebar-menu">
    @foreach($menus as $menu)
        <li>
            <a href="{{ url($menu->url_slug) }}">
                {{ $menu->wm_menu_nama ?? $menu->wmg_nama_default }}
            </a>
        </li>
    @endforeach
</ul>
```

### **3. Authorization Check**
```php
// Check if user has access to specific menu
public function canAccessMenu($userId, $menuName)
{
    return DB::table('web_menu as wm')
        ->join('web_menu_global as wmg', 'wm.fk_web_menu_global', '=', 'wmg.web_menu_global_id')
        ->join('web_menu_url as wmu', 'wmg.fk_web_menu_url', '=', 'wmu.web_menu_url_id')
        ->join('users as u', 'wm.fk_m_hak_akses', '=', 'u.fk_m_hak_akses')
        ->where('u.id', $userId)
        ->where('wmu.wmu_nama', $menuName)
        ->where('wm.wm_status_menu', 'aktif')
        ->where('wm.isDeleted', 0)
        ->exists();
}
```

---

## ➕ **ADD NEW MENU (Quick Steps)**

```sql
-- STEP 1: Insert URL
INSERT INTO web_menu_url (fk_m_application, wmu_nama, wmu_keterangan, isDeleted, created_by, created_at)
VALUES (1, 'new-menu-url', 'Description here', 0, 'admin', NOW());
-- Result: web_menu_url_id = X

-- STEP 2: Insert Global Template
INSERT INTO web_menu_global (fk_web_menu_url, wmg_parent_id, wmg_kategori_menu, wmg_urutan_menu, wmg_nama_default, wmg_status_menu, isDeleted, created_by, created_at)
VALUES (X, NULL, 'Menu Biasa', 10, 'New Menu Name', 'aktif', 0, 'admin', NOW());
-- Result: web_menu_global_id = Y

-- STEP 3: Insert Per Role (repeat for each role)
INSERT INTO web_menu (fk_web_menu_global, fk_m_hak_akses, wm_parent_id, wm_urutan_menu, wm_menu_nama, wm_status_menu, isDeleted, created_by, created_at)
VALUES (Y, 1, NULL, 20, NULL, 'aktif', 0, 'admin', NOW()); -- SAR
-- Repeat for ADM (fk_m_hak_akses = 2), MPU (3), VFR (4), RPN (5)

-- STEP 4: Clear cache
php artisan cache:clear
```

```php
// STEP 5: Register route
Route::group([
    'prefix' => WebMenuModel::getDynamicMenuUrl('new-menu-url'),
    'middleware' => ['auth', 'role:SAR,ADM']
], function () {
    Route::get('/', [NewMenuController::class, 'index']);
});
```

---

## ⚡ **PERFORMANCE OPTIMIZATION**

### **Caching Strategy**
```php
// Cache menu structure (1 hour)
Cache::remember("menu_structure_{$roleCode}", 3600, function() {
    // Query here
});

// Cache URL resolution (1 hour)
Cache::remember("dynamic_url_{$menuName}", 3600, function() {
    return WebMenuModel::getDynamicMenuUrl($menuName);
});

// Clear cache on update
public function clearMenuCache()
{
    $roles = ['SAR', 'ADM', 'MPU', 'VFR', 'RPN'];
    foreach ($roles as $role) {
        Cache::forget("menu_structure_{$role}");
    }
}
```

### **Database Indexes**
```sql
-- Already exist (check with):
SHOW INDEX FROM web_menu_url;
SHOW INDEX FROM web_menu;
SHOW INDEX FROM web_menu_global;

-- Add if missing:
ALTER TABLE web_menu_url ADD INDEX idx_app_nama (fk_m_application, wmu_nama);
ALTER TABLE web_menu ADD INDEX idx_role_status (fk_m_hak_akses, wm_status_menu);
ALTER TABLE web_menu ADD INDEX idx_parent_order (wm_parent_id, wm_urutan_menu);
ALTER TABLE web_menu_global ADD INDEX idx_url_status (fk_web_menu_url, wmg_status_menu);
```

---

## 🐛 **TROUBLESHOOTING**

### **Problem: 404 Not Found**
```sql
-- Check 1: URL exists?
SELECT * FROM web_menu_url WHERE wmu_nama = 'your-url' AND isDeleted = 0;

-- Check 2: Application mapping?
SELECT wmu.*, ma.* 
FROM web_menu_url wmu
JOIN m_application ma ON wmu.fk_m_application = ma.application_id
WHERE wmu.wmu_nama = 'your-url';
```

### **Problem: Menu Not Showing**
```sql
-- Check menu for role
SELECT wm.*, wmg.*, wmu.*
FROM web_menu wm
JOIN web_menu_global wmg ON wm.fk_web_menu_global = wmg.web_menu_global_id
JOIN web_menu_url wmu ON wmg.fk_web_menu_url = wmu.web_menu_url_id
WHERE wm.fk_m_hak_akses = 2  -- ADM role
  AND wm.isDeleted = 0
  AND wm.wm_status_menu = 'aktif';
```

### **Problem: 403 Forbidden**
```php
// Check user's role
$user = Auth::user();
dd($user->fk_m_hak_akses, $user->hak_akses_kode);

// Check access
$hasAccess = DB::table('web_menu as wm')
    ->join('web_menu_global as wmg', 'wm.fk_web_menu_global', '=', 'wmg.web_menu_global_id')
    ->join('web_menu_url as wmu', 'wmg.fk_web_menu_url', '=', 'wmu.web_menu_url_id')
    ->where('wmu.wmu_nama', 'your-url')
    ->where('wm.fk_m_hak_akses', $user->fk_m_hak_akses)
    ->where('wm.wm_status_menu', 'aktif')
    ->exists();
```

---

## 📝 **COMMON QUERIES**

### **Get All URLs**
```sql
SELECT web_menu_url_id, wmu_nama, wmu_keterangan 
FROM web_menu_url 
WHERE isDeleted = 0 
ORDER BY web_menu_url_id;
```

### **Get Menu by Role**
```sql
SELECT 
    wm.web_menu_id,
    wm.wm_menu_nama,
    wm.wm_urutan_menu,
    wm.wm_parent_id,
    wmg.wmg_nama_default,
    wmg.wmg_kategori_menu,
    wmu.wmu_nama as url_slug,
    ha.hak_akses_kode as role
FROM web_menu wm
JOIN web_menu_global wmg ON wm.fk_web_menu_global = wmg.web_menu_global_id
JOIN web_menu_url wmu ON wmg.fk_web_menu_url = wmu.web_menu_url_id
JOIN m_hak_akses ha ON wm.fk_m_hak_akses = ha.hak_akses_id
WHERE ha.hak_akses_kode = 'ADM'
  AND wm.isDeleted = 0
  AND wm.wm_status_menu = 'aktif'
ORDER BY wm.wm_urutan_menu;
```

### **Count Menu Per Role**
```sql
SELECT 
    ha.hak_akses_kode,
    ha.hak_akses_nama,
    COUNT(*) as total_menu
FROM web_menu wm
JOIN m_hak_akses ha ON wm.fk_m_hak_akses = ha.hak_akses_id
WHERE wm.isDeleted = 0
  AND wm.wm_status_menu = 'aktif'
GROUP BY ha.hak_akses_kode, ha.hak_akses_nama;
```

### **Find Unused URLs**
```sql
-- URLs without global menu
SELECT wmu.web_menu_url_id, wmu.wmu_nama
FROM web_menu_url wmu
LEFT JOIN web_menu_global wmg ON wmu.web_menu_url_id = wmg.fk_web_menu_url
WHERE wmg.web_menu_global_id IS NULL
  AND wmu.isDeleted = 0;
```

---

## 🎯 **BEST PRACTICES**

### ✅ **DO:**
1. Always use `getDynamicMenuUrl()` for route prefixes
2. Implement caching (TTL: 1 hour minimum)
3. Use soft delete (`isDeleted = 1`)
4. Check `wm_status_menu = 'aktif'`
5. Add database indexes on foreign keys
6. Clear cache after menu updates
7. Document menu additions in migrations

### ❌ **DON'T:**
1. Hardcode URLs in route files
2. Query database on every page load
3. Delete records (use `isDeleted`)
4. Modify `wmu_nama` after deployment
5. Skip authorization checks
6. Forget to clear cache after updates
7. Create menu without all required roles

---

## 📚 **RELATED FILES**

### **Documentation**
- `ANALISIS-ROUTING-DINAMIS.md` - Full analysis (15,000+ words)
- `ROUTING-FLOW-DIAGRAM.md` - Visual diagrams
- `README.md` - Project overview

### **Code Files**
- `app/Models/WebMenuModel.php` - Core routing model
- `routes/web.php` - Main route file
- `Modules/Sisfo/Routes/web.php` - Sisfo module routes
- `Modules/User/Routes/web.php` - User module routes

### **Database**
- Database: `polinema_ppid`
- Host: `127.0.0.1:3306`
- User: `root` (no password)
- Tables: 71 total

### **MCP Server**
- `mcp-server/server.js` - Database query server
- `mcp-server/package.json` - Dependencies
- `.vscode/mcp_settings.json` - VS Code config

---

## 🔧 **USEFUL COMMANDS**

```bash
# Clear all cache
php artisan cache:clear

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# View all routes
php artisan route:list | grep menu-management

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# MCP server (for direct DB access)
node mcp-server/server.js
```

---

## 📊 **METRICS**

| Metric | Value | Notes |
|--------|-------|-------|
| **Avg Query Time** | ~50ms | Without cache |
| **Avg Query Time** | ~1ms | With cache |
| **Cache Hit Rate** | 95%+ | Expected with 1hr TTL |
| **DB Queries/Request** | 1-3 | Sidebar + route |
| **Routes Registered** | 59+ | All URLs |

---

## 💡 **QUICK TIPS**

1. **Always check cache first** before querying database
2. **Use composite indexes** on (fk_m_hak_akses, wm_status_menu, isDeleted)
3. **Log slow queries** (>100ms) for optimization
4. **Monitor cache hit rate** - should be 95%+
5. **Document menu hierarchies** in comments
6. **Test with all roles** before deployment
7. **Backup database** before bulk menu changes

---

**📧 Need Help?**
- Full Documentation: `ANALISIS-ROUTING-DINAMIS.md`
- Visual Diagrams: `ROUTING-FLOW-DIAGRAM.md`
- Database: Use MCP server (`node mcp-server/server.js`)
- Laravel Tinker: `php artisan tinker`

**🔄 Last Sync:** 26 Januari 2026, 71 tables, 59 URLs, 5 roles
