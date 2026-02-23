# 📚 MENU MASTER - ZERO CODE CRUD SYSTEM

**Sistem untuk membuat menu CRUD otomatis tanpa coding, hanya dengan konfigurasi database.**

---

## 🎯 KONSEP DASAR

### **Apa itu Menu Master?**
Menu Master adalah sistem template-based CRUD yang memungkinkan pembuatan menu baru **tanpa menulis kode**. Cukup dengan konfigurasi, sistem otomatis generate:
- Controller (menggunakan universal `Template\MasterController`)
- Views (6 template blade universal)
- Routing (dynamic routing system)
- Validation (dari field config)
- CRUD operations (auto-generated)

### **Kategori Menu:**
1. **MASTER** - Template CRUD 1 tabel, auto-generate, zero coding
2. **PENGAJUAN** - Template approval workflow (future)
3. **CUSTOM** - Manual coding untuk menu kompleks

---

## 🗄️ DATABASE STRUKTUR

### **3 Tabel Utama:**

**1. web_menu_url** - Mapping URL ke controller
- URL menu, kategori (master/custom/pengajuan), tabel akses, controller name, module type

**2. web_menu_field_config** - Konfigurasi field per menu
- Column name, field type, label, validation, criteria, FK config, max length, visible

**3. web_menu_global** - Menu sidebar
- Link ke web_menu_url, label, icon, parent/child hierarchy

### **Field Types Support:**
- text, textarea, number, date, date2, dropdown, radio, search (FK)

### **Validation Support:**
- required, unique, max, min, email

### **Criteria Support:**
- unique, uppercase, lowercase

---

## 🔄 ALUR KERJA SISTEM

### **A. Membuat Menu Master Baru**

**Langkah User:**
1. Buka Management Menu URL → Tambah
2. Pilih kategori "Master"
3. Input nama tabel → Klik "Cek Tabel"
4. System auto-generate field configs dari database schema
5. Customize: label, field type, validation, criteria, FK display columns
6. Simpan → System create web_menu_url + field configs

**Validasi Backend:**
- Tabel harus exists di database
- Tabel harus punya 7 common fields (isDeleted, created_at/by, updated_at/by, deleted_at/by)
- Check duplikasi: jika tabel sudah terdaftar → warning atau block
- Detect table changes: added/removed/modified columns

**Auto-Generate Logic:**
- PK auto increment → hidden field
- VARCHAR/TEXT → text/textarea
- INT/DECIMAL → number
- DATE/DATETIME → date
- FK columns → search field dengan modal selector
- ENUM → dropdown

### **B. Update Menu Master**

**Skenario 1 - Struktur tabel tidak berubah:**
- Edit web_menu_url biasa
- Update field configs sesuai kebutuhan

**Skenario 2 - Struktur tabel berubah (ALTER TABLE):**
- Buka Management Menu URL → Tambah (bukan edit)
- Input nama tabel sama → Cek Tabel
- System detect changes: added/removed/modified columns
- User re-configure field configs
- Simpan → Soft delete menu lama + create menu baru

### **C. Penggunaan Menu Master**

**User Access Flow:**
1. User buka URL menu (contoh: /menu-kategori)
2. Dynamic routing detect → resolve ke Template\MasterController
3. MasterController query field configs dari database
4. Render view dengan field configs dynamis
5. CRUD operations auto-handled

---

## 📂 KOMPONEN SISTEM

### **Backend Components:**

**Models:**
- WebMenuUrlModel - CRUD menu URL, auto-generate configs, detect changes
- WebMenuFieldConfigModel - CRUD field configs
- WebMenuGlobalModel - CRUD menu sidebar

**Controllers:**
- Template\MasterController - Universal CRUD untuk semua menu master
- WebMenuUrlController - Management menu URL (create/update/delete config)
- PageController - Dynamic routing resolver

**Services:**
- DatabaseSchemaService - Inspeksi struktur tabel (DESCRIBE)
- MasterMenuService - Business logic CRUD dinamis

**Helpers:**
- RouteHelper - Validasi URL untuk dynamic routing
- DatabaseSchemaHelper - Mapping MySQL type ke field type
- ValidationHelper - Build Laravel validation rules

**Middleware:**
- CheckDynamicRoute - Filter URL yang boleh masuk dynamic routing

### **Frontend Components:**

**Views Management (AdminWeb/WebMenuUrl/):**
- index.blade.php - List semua menu URL
- create.blade.php - Form konfigurasi menu baru dengan field config table
- update.blade.php - Form edit menu dengan re-check table feature
- detail.blade.php - Detail lengkap menu + field configs + audit info
- delete.blade.php - Konfirmasi hapus dengan warning field configs yang terhapus
- data.blade.php - Partial table rows

**Views Template (Template/Master/):**
- index.blade.php - List data universal
- create.blade.php - Form create universal
- update.blade.php - Form update universal
- detail.blade.php - Detail view universal
- delete.blade.php - Delete confirmation universal
- data.blade.php - Table rows partial universal

---

## 🎨 FITUR UI/UX

### **Create & Update Views:**
- Modal 80% width untuk ruang konfigurasi
- Badge PK (biru) dan FK (hijau) inline dengan nama kolom
- Field config table: No, Kolom Database, Label Field, Type Input, Kriteria, Validasi, FK Config, Visible
- Auto-disable: Required & Unique untuk PK, Uppercase/Lowercase untuk non-text
- Auto-clamp: Max length tidak boleh melebihi column max length
- Pink styling untuk disabled elements
- Blue checked styling untuk disabled checked checkboxes
- FK Display Columns: Multi-select checkbox dengan tooltip preview

### **Detail View:**
- Card-based layout dengan color coding
- Section: Informasi Umum, Konfigurasi Custom/Master, Field Configurations, Audit Trail
- Badge kategori menu (Success=Master, Info=Custom, Warning=Pengajuan)
- Tabel field configs lengkap dengan badge PK/FK

### **Delete View:**
- Header merah untuk warning visual
- Info lengkap: kategori, URL, tabel, controller
- List field configs yang akan terhapus (jika Master)
- Warning dampak: menu global, hak akses, field configs

---

## 🔀 DYNAMIC ROUTING

**8 Standard Routes (Auto-handled):**
1. index() - List data
2. getData() - AJAX DataTable
3. addData() - Show form create
4. createData() - Process create
5. editData($id) - Show form edit
6. updateData($id) - Process update
7. detailData($id) - Show detail
8. deleteData($id) - Process delete (GET confirm, DELETE execute)

**Route Pattern:**
- /{page} → index() atau createData()
- /{page}/{action} → getData(), addData(), dll
- /{page}/{action}/{id} → editData(5), updateData(5), deleteData(5)

**Exclusion:**
- User Module URLs → route manual
- Non-standard Sisfo URLs → route manual (ditambahkan di RouteHelper)

---

## ✅ VALIDATION & CRITERIA

### **Field Validation:**
- Required, Unique, Max, Min, Email
- Stored as JSON: `{"required": true, "max": 100}`
- Auto-checked untuk PK: required + unique
- Auto-disabled untuk PK (tidak bisa diubah)

### **Field Criteria:**
- Unique (database constraint)
- Uppercase (auto convert input)
- Lowercase (auto convert input)
- Stored as JSON: `{"unique": true, "case": "uppercase"}`

### **Auto-Clamp:**
- Max length input tidak boleh > column max length
- Jika user input melebihi → auto set ke max length
- Show tooltip warning

---

## 🚀 WORKFLOW PENGGUNAAN

### **Setup Awal (Database):**
1. Buat tabel baru dengan 7 common fields wajib
2. Tambahkan columns sesuai kebutuhan
3. Set primary key auto increment
4. Set foreign key constraints (jika ada relasi)

### **Setup Menu Master:**
1. Buka Management Menu URL → Tambah
2. Pilih aplikasi
3. Pilih kategori "Master"
4. Input nama tabel → Cek Tabel
5. Customize field configs (label, type, validation, criteria)
6. Simpan

### **Setup Menu Sidebar:**
1. Buka Menu Management Global → Tambah
2. Pilih web menu URL yang baru dibuat
3. Set label, icon, parent (jika sub-menu)
4. Set order & kategori (menu/sub-menu)
5. Simpan

### **Setup Permission:**
1. Buka Management Menu → Set permission per role
2. Enable CRUD operations sesuai kebutuhan

### **Menu Siap Digunakan!**

---

## 🔧 TROUBLESHOOTING

| Problem | Solusi |
|---------|--------|
| Tabel tidak terdeteksi | Pastikan tabel punya 7 common fields |
| Duplikasi menu | Cek tabel sudah terdaftar via "Cek Tabel" |
| FK tidak muncul search | Pastikan ada FK constraint di database |
| Validation tidak jalan | Cek JSON format di wmfc_validation |
| Field tidak muncul | Cek wmfc_is_visible = 1 |
| Max length error | System auto-clamp ke column max length |

---

## 📌 KEY ADVANTAGES

✅ **Zero Code** - Tidak perlu coding controller/model/view  
✅ **Database-Driven** - 100% dari database schema  
✅ **Auto-Detect Changes** - Detect ALTER TABLE otomatis  
✅ **Soft Delete** - Update = archive old + create new  
✅ **Universal Template** - 1 controller handle semua menu master  
✅ **Consistent UI** - Semua menu master tampilan sama  
✅ **Fast Development** - 10-15 menit vs 4-6 jam manual coding  

---

**© PPID Polinema - Menu Master System v1.0**