# 📚 MENU MASTER - TEMPLATE-BASED CRUD SYSTEM

**Sistem untuk membuat menu CRUD otomatis tanpa coding, hanya dengan konfigurasi.**

---

## 🗄️ DATABASE STRUCTURE

### **Tabel Utama:**

1. **`web_menu_url`** - Mapping URL menu ke controller & akses tabel
   - `wmu_nama` - URL slug menu
   - `wmu_kategori_menu` - ENUM: 'master', 'pengajuan', 'custom'
   - `wmu_akses_tabel` - Nama tabel database untuk menu master
   - `controller_name` - Class controller (master = `Template\MasterController`)
   - `module_type` - ENUM: 'sisfo', 'user'

2. **`web_menu_field_config`** - Konfigurasi field per menu master
   - `fk_web_menu_url` - FK ke web_menu_url
   - `wmfc_column_name` - Nama kolom database
   - `wmfc_column_type` - Tipe data (VARCHAR(200), TEXT, INT)
   - `wmfc_field_type` - Type input UI (text, textarea, number, date, dropdown, search)
   - `wmfc_criteria` - JSON: unique, uppercase/lowercase
   - `wmfc_validation` - JSON: required, max, min, email
   - `wmfc_fk_table` - Tabel referensi FK
   - `wmfc_fk_display_columns` - JSON: kolom yang ditampilkan untuk FK

3. **`web_menu_global`** - Menu sidebar global
   - `fk_web_menu_url` - FK ke web_menu_url
   - `wmg_default_name` - Label menu di sidebar
   - `wmg_icon` - Icon FontAwesome
   - `wmg_type` - ENUM: 'general', 'special'
   - `wmg_kategori` - Kategori: menu, sub-menu, divider

---

## 📂 FILE STRUCTURE

### **Backend:**

- **Models:**
  - `WebMenuUrlModel` - CRUD menu URL, auto-generate field configs, detect table changes
  - `WebMenuFieldConfigModel` - CRUD field configs
  - `WebMenuGlobalModel` - CRUD menu sidebar

- **Controllers:**
  - `Template/MasterController` - Universal CRUD controller untuk semua menu master
  - `AdminWeb/MenuManagement/WebMenuUrlController` - Manage menu URL config
  - `PageController` - Dynamic routing resolver (route → controller → action)

- **Services:**
  - `DatabaseSchemaService` - Inspeksi struktur database (DESCRIBE table)
  - `MasterMenuService` - Business logic CRUD dinamis

- **Helpers:**
  - `RouteHelper` - Validasi URL untuk dynamic routing
  - `DatabaseSchemaHelper` - Mapping MySQL type ke field type
  - `ValidationHelper` - Build Laravel validation rules dari config

- **Middleware:**
  - `CheckDynamicRoute` - Filter URL yang boleh masuk dynamic routing

### **Frontend:**

- **Views Template Master:**
  - `Template/Master/index.blade.php` - List data dengan pagination
  - `Template/Master/data.blade.php` - Table rows partial
  - `Template/Master/create.blade.php` - Form create
  - `Template/Master/update.blade.php` - Form update
  - `Template/Master/detail.blade.php` - Detail view
  - `Template/Master/delete.blade.php` - Delete confirmation

- **Views Management:**
  - `AdminWeb/WebMenuUrl/create.blade.php` - Form konfigurasi menu master
  - `AdminWeb/WebMenuUrl/index.blade.php` - List menu URL

---

## 🔄 MEKANISME MANAGEMENT MENU URL

### **A. CREATE MENU MASTER (Tabel Baru)**

**Flow:**
1. User pilih kategori "Master"
2. Input nama tabel → Klik "Cek Tabel"
3. Backend validate:
   - Tabel exists?
   - Punya 7 common fields? (isDeleted, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by)
   - Sudah terdaftar? (duplicate check)
4. Auto-generate field configs dari `DESCRIBE table`
5. User customize:
   - Field label
   - Field type (text, textarea, number, date, search untuk FK)
   - Criteria (unique, uppercase, lowercase)
   - Validation (required, max, min, email)
   - FK display columns (untuk foreign key)
6. Submit → Create `web_menu_url` + batch create `web_menu_field_config`

**Auto-Generate Logic:**
- PK auto increment → Hidden field
- VARCHAR/TEXT → Text/Textarea
- INT/DECIMAL → Number
- DATE/DATETIME → Date
- ENUM → Dropdown/Radio
- Foreign Key → Search dengan modal selector

**Duplicate Prevention:**
- Jika tabel sudah terdaftar exact same → ❌ Block submit
- Jika tabel sudah terdaftar ada perubahan → ⚠️ Warning + show detail changes
- User bisa re-configure → Soft delete old menu + create new

### **B. DETECT TABLE CHANGES**

**Change Detection:**
- **Added columns** - Kolom baru di database
- **Removed columns** - Kolom dihapus dari database
- **Modified columns** - Type data berubah (VARCHAR(50) → VARCHAR(100))

**Update Mechanism:**
- Soft delete `web_menu_url` lama (isDeleted = 1)
- Soft delete semua `web_menu_field_config` lama
- Create new menu + field configs
- Transaction log: UPDATED

---

## 🎯 KATEGORI MENU

### **1. MASTER** ✅ (Implemented)
- Template CRUD untuk 1 tabel
- Auto-generate dari database schema
- Tidak perlu coding controller/model/view
- Contoh: Kategori, User, Jabatan

### **2. PENGAJUAN** 🔜 (Future)
- Template approval workflow
- Parent-child relationship
- Multi-step approval
- Contoh: Permohonan, Surat, Pengaduan

### **3. CUSTOM** ✅ (Existing)
- Manual coding seperti biasa
- Untuk menu kompleks
- Tidak pakai template
- Contoh: Dashboard, Laporan, WhatsApp Management

---

## 🔀 DYNAMIC ROUTING SYSTEM

**Pattern:**
```
URL: /menu-kategori/editData/5
       ↓
PageController::index($page='menu-kategori', $action='editData', $id='5')
       ↓
Query: SELECT controller_name FROM web_menu_url WHERE wmu_nama = 'menu-kategori'
       ↓
Resolve: Template\MasterController
       ↓
Call: Template\MasterController->editData(5)
```

**8 Standard Routes:**
1. `index()` - List data
2. `getData()` - AJAX DataTable
3. `addData()` - Show form create
4. `createData()` - Process create
5. `editData($id)` - Show form edit
6. `updateData($id)` - Process update
7. `detailData($id)` - Show detail
8. `deleteData($id)` - Show confirm / process delete

**Exclusion:**
- User Module URLs → Route manual
- Non-standard Sisfo URLs → Route manual (e.g., whatsapp-management)

---

## 🎨 TEMPLATE MASTER FEATURES

### **Field Types:**
- **text** - Input text biasa
- **textarea** - Text multi-line
- **number** - Input numeric only
- **date** - Date picker single
- **date2** - Date range picker
- **dropdown** - Select option (untuk ENUM atau FK)
- **radio** - Radio button (untuk ENUM)
- **search** - FK search dengan modal selector

### **Validations:**
- **required** - Wajib diisi
- **unique** - Tidak boleh duplikat
- **max** - Maksimal karakter/angka
- **min** - Minimal karakter/angka
- **email** - Format email valid

### **Criteria:**
- **unique** - Unique constraint database
- **uppercase** - Auto convert ke uppercase
- **lowercase** - Auto convert ke lowercase

---

## 🚀 WORKFLOW PENGGUNAAN

### **Membuat Menu Master Baru:**
1. Siapkan tabel di database dengan 7 common fields
2. Buka "Management Menu URL" → Tambah
3. Pilih kategori "Master"
4. Input nama tabel → Cek Tabel
5. Customize field configs (label, type, validation)
6. Simpan
7. Buka "Menu Management Global" → Tambah menu sidebar
8. Link ke menu URL yang baru dibuat
9. Set permission di "Management Menu"

### **Update Struktur Tabel:**
1. ALTER TABLE di database
2. Buka "Management Menu URL" → Tambah (bukan edit!)
3. Input nama tabel yang sama → Cek Tabel
4. System detect changes → Show detail
5. Re-configure field configs
6. Simpan → Old menu di-archive, new menu created

---

## 📌 KEY POINTS

- **Zero Code:** Menu master tidak perlu coding controller/view
- **Database-Driven:** Struktur menu 100% dari database schema
- **Change Detection:** Auto-detect table structure changes
- **Soft Delete:** Update = archive old + create new
- **Dynamic Routing:** 1 controller handle semua menu master
- **Template Views:** 6 blade files universal untuk semua menu

---

## 🔧 TROUBLESHOOTING

**Problem:** Tabel tidak terdeteksi
- **Solution:** Pastikan tabel punya 7 common fields

**Problem:** Duplikasi menu
- **Solution:** Cek tabel sudah terdaftar atau belum via "Cek Tabel"

**Problem:** FK tidak muncul opsi search
- **Solution:** Pastikan kolom nama `fk_*` dan ada constraint FK di database

**Problem:** Validation tidak jalan
- **Solution:** Cek `wmfc_validation` JSON format valid

---

**© PPID Polinema - Template Master System v1.0**