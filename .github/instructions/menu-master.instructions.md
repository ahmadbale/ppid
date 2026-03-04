# 📚 MENU MASTER - ZERO CODE CRUD SYSTEM

**Sistem untuk membuat menu CRUD otomatis tanpa coding, cukup konfigurasi tabel database.**

---

## 🎯 1. GAMBARAN UMUM

### Apa itu Menu Master?
- Sistem template-based CRUD yang memungkinkan pembuatan menu baru **tanpa menulis kode**
- Cukup mendaftarkan nama tabel → sistem otomatis membaca struktur → generate konfigurasi field → menu CRUD siap pakai
- Menggunakan 1 controller universal (`Template\MasterController`), 6 template Blade universal, dan dynamic routing

### 3 Kategori Menu URL
| Kategori | Keterangan |
|----------|-----------|
| **master** | CRUD otomatis dari tabel, tanpa coding |
| **pengajuan** | Template approval workflow (belum diimplementasi) |
| **custom** | Menu manual dengan controller sendiri |

---

## 🗄️ 2. STRUKTUR DATABASE

### Hierarki 4 Tabel Utama

```
web_menu_url (pendaftaran URL + tabel akses)
  ├── web_menu_field_config (konfigurasi field per kolom tabel)
  ├── web_menu_global (struktur sidebar menu)
  │     └── web_menu (assignment menu per role/hak akses)
```

### 2.1 Tabel `web_menu_url`
Menyimpan registrasi URL menu dan mapping ke controller.

| Kolom Penting | Keterangan |
|--------------|-----------|
| `web_menu_url_id` | Primary key (auto increment) |
| `fk_m_application` | FK ke tabel aplikasi |
| `wmu_parent_id` | ID parent jika sub-menu (null = parent) |
| `wmu_nama` | Slug URL menu (contoh: `m-testing`) |
| `controller_name` | Nama controller. Master otomatis = `Template\MasterController` |
| `module_type` | Enum: `sisfo` / `user` |
| `wmu_kategori_menu` | Enum: `master` / `custom` / `pengajuan`. Default = `custom` |
| `wmu_akses_tabel` | Nama tabel yang diakses (wajib untuk master, null untuk custom) |
| `wmu_keterangan` | Deskripsi menu |
| + 7 common fields | isDeleted, created_at/by, updated_at/by, deleted_at/by |

### 2.2 Tabel `web_menu_field_config`
Menyimpan konfigurasi setiap kolom/field dari tabel yang didaftarkan sebagai menu master.

| Kolom | Keterangan |
|-------|-----------|
| `web_menu_field_config_id` | Primary key (auto increment) |
| `fk_web_menu_url` | FK ke web_menu_url |
| `wmfc_column_name` | Nama kolom asli di database |
| `wmfc_column_type` | Tipe data MySQL (varchar, int, enum, dll) |
| `wmfc_field_label` | Label yang ditampilkan di form/tabel |
| `wmfc_field_type` | Tipe inputan di form (lihat Bagian 4) |
| `wmfc_criteria` | Kriteria field dalam format JSON |
| `wmfc_validation` | Validasi field dalam format JSON |
| `wmfc_fk_table` | Nama tabel referensi FK (jika kolom FK) |
| `wmfc_fk_pk_column` | Nama kolom PK di tabel FK |
| `wmfc_fk_display_columns` | JSON array kolom yang ditampilkan di modal pencarian FK |
| `wmfc_fk_label_columns` | JSON object alias label untuk header modal FK |
| `wmfc_fk_priority_display` | Kolom FK yang ditampilkan sebagai nilai di index/detail/edit/delete |
| `wmfc_max_length` | Panjang maksimal karakter (dari skema tabel) |
| `wmfc_label_keterangan` | Teks bantuan yang muncul di bawah input form |
| `wmfc_ukuran_max` | Ukuran maksimal file upload dalam MB |
| `wmfc_display_list` | Boolean: tampilkan kolom ini di tabel index atau tidak |
| `wmfc_order` | Urutan tampil kolom |
| `wmfc_is_primary_key` | Boolean: apakah kolom ini PK |
| `wmfc_is_auto_increment` | Boolean: apakah PK auto increment |
| `wmfc_is_visible` | Boolean: apakah kolom ditampilkan di form |
| `wmfc_is_foreign_key` | Boolean: apakah kolom ini FK |
| + 7 common fields | isDeleted, created_at/by, updated_at/by, deleted_at/by |

### 2.3 Tabel `web_menu_global`
Menyimpan struktur menu di sidebar untuk semua role.

| Kolom Penting | Keterangan |
|--------------|-----------|
| `web_menu_global_id` | Primary key (auto increment) |
| `fk_web_menu_url` | FK ke web_menu_url (link ke URL menu) |
| `wmg_parent_id` | ID parent menu (untuk sub-menu) |
| `wmg_icon` | Icon Font Awesome (contoh: `fa-book`) |
| `wmg_type` | `general` (semua role) / `special` (role tertentu) |
| `wmg_kategori_menu` | `Menu Biasa` / `Group Menu` / `Sub Menu` |
| `wmg_urutan_menu` | Urutan tampil di sidebar |
| `wmg_nama_default` | Nama default menu |
| `wmg_badge_method` | Method untuk badge/notifikasi (null jika tidak ada) |
| `wmg_status_menu` | `aktif` / `nonaktif` |

### 2.4 Tabel `web_menu`
Assignment menu per role/hak akses (copy dari web_menu_global per role).

| Kolom Penting | Keterangan |
|--------------|-----------|
| `web_menu_id` | Primary key (auto increment) |
| `fk_web_menu_global` | FK ke web_menu_global |
| `fk_m_hak_akses` | FK ke tabel hak akses (role) |
| `wm_parent_id` | ID parent dalam konteks role |
| `wm_urutan_menu` | Urutan menu untuk role tertentu |
| `wm_menu_nama` | Nama menu yang ditampilkan ke role |
| `wm_status_menu` | Status aktif/nonaktif per role |

---

## 🔀 3. DYNAMIC ROUTING

### Alur Request
```
User Request → Laravel Router → CheckDynamicRoute (middleware)
→ RouteHelper (validasi URL) → PageController (resolve controller)
→ Template\MasterController → execute method → return response
```

### 3 Pattern Route Universal
| Pattern | Contoh URL | Method Terpanggil |
|---------|-----------|-------------------|
| `/{page}` | GET `/m-testing` | `index()` |
| `/{page}` | POST `/m-testing` | `createData()` |
| `/{page}/{action}` | GET `/m-testing/getData` | `getData()` |
| `/{page}/{action}` | GET `/m-testing/addData` | `addData()` |
| `/{page}/{action}/{id}` | GET `/m-testing/editData/5` | `editData(5)` |
| `/{page}/{action}/{id}` | POST `/m-testing/updateData/5` | `updateData($request, 5)` |
| `/{page}/{action}/{id}` | GET `/m-testing/detailData/5` | `detailData(5)` |
| `/{page}/{action}/{id}` | GET `/m-testing/deleteData/5` | `deleteData(5)` (konfirmasi) |
| `/{page}/{action}/{id}` | DELETE `/m-testing/deleteData/5` | `deleteData($request, 5)` (proses) |

### 9 Standard Method (Maksimal)
1. `index()` — Halaman daftar data (DataTable)
2. `getData()` — AJAX response JSON untuk DataTable
3. `addData()` — Tampilkan form tambah data
4. `createData()` — Proses simpan data baru
5. `editData($id)` — Tampilkan form edit data
6. `updateData($request, $id)` — Proses update data
7. `detailData($id)` — Tampilkan detail data
8. `deleteData($request, $id)` — Konfirmasi (GET) atau proses hapus (DELETE)
9. `getFkData()` — Endpoint AJAX untuk data modal pencarian FK

### Aturan Penting
- Controller menu master **tidak boleh** memiliki method selain 9 di atas
- URL yang memiliki route manual (non-standar) didaftarkan di `RouteHelper::$nonStandardSisfoUrls`
- Middleware `CheckDynamicRoute` memvalidasi URL sebelum masuk ke PageController

---

## 🎨 4. TIPE FIELD & MAPPING

### Mapping Otomatis Tipe Data MySQL → Opsi Tipe Inputan

| Tipe Data MySQL | Opsi Field Type | Keterangan |
|----------------|----------------|-----------|
| `varchar`, `char` | `text`, `textarea`, `date2`, `datetime2`, `time2`, `year2`, `media` | String / rentang waktu berbagai jenis |
| `text`, `mediumtext`, `longtext` | `textarea` | String panjang |
| `int`, `bigint`, `decimal`, `float`, `double` | `number` | Angka |
| `date` | `date` | Hanya tanggal tunggal |
| `datetime`, `timestamp` | `datetime` | Tanggal + waktu tunggal |
| `time` | `time` | Waktu (jam:menit) tunggal |
| `year` | `year` | Tahun 4-digit tunggal |
| `json` | `date2` *(default, bisa diganti ke datetime2/time2/year2)* | Rentang waktu disimpan JSON |
| `enum` | `dropdown`, `radio` | Pilihan tetap dari ENUM values |
| Kolom FK (terdeteksi constraint) | `search` | Pencarian data dari tabel referensi |

### Tipe Field Lengkap
| Tipe | Input HTML | DB Column | Keterangan |
|------|-----------|-----------|-----------|
| `text` | `<input type="text">` | varchar | Input teks satu baris |
| `textarea` | `<textarea>` | text/varchar | Input teks multi-baris |
| `number` | `<input type="number">` | int/decimal/dll | Input angka |
| `date` | `<input type="date">` | DATE | Date picker tunggal |
| `datetime` | `<input type="datetime-local">` | DATETIME/TIMESTAMP | Tanggal + waktu tunggal |
| `time` | `<input type="time">` | TIME | Waktu (jam:menit) tunggal |
| `year` | `<input type="number" min="1901" max="2155">` | YEAR | Tahun 4-digit |
| `date2` | 2x `<input type="date">` (_start/_end) | VARCHAR/JSON | Rentang tanggal → disimpan JSON `{"start":"...","end":"..."}` |
| `datetime2` | 2x `<input type="datetime-local">` (_start/_end) | VARCHAR/JSON | Rentang tanggal+waktu → JSON |
| `time2` | 2x `<input type="time">` (_start/_end) | VARCHAR/JSON | Rentang waktu → JSON |
| `year2` | 2x `<input type="number">` (_start/_end) | VARCHAR/JSON | Rentang tahun → JSON |
| `dropdown` | `<select>` | enum/FK | Pilihan dropdown |
| `radio` | `<input type="radio">` | enum/FK | Radio button |
| `search` | Modal table | FK integer | Modal pencarian FK |
| `media` | `<input type="file">` | varchar | Upload file/gambar |

### Penyimpanan Tipe Rentang (*2)
Tipe `date2`, `datetime2`, `time2`, `year2` menyimpan data sebagai JSON di kolom varchar/json:
```json
{"start": "2024-01-01", "end": "2024-12-31"}
```
- **Encode**: Controller (`MasterController`) encode `_start`+`_end` → JSON sebelum simpan ke `$data`
- **Decode**: `buildFormFields()` di `MasterMenuService` decode JSON → `value_start`/`value_end` untuk form edit
- **Validasi backend**: Rule `string` (karena sudah JSON-encoded)
- **Input suffix**: `{column_name}_start` dan `{column_name}_end`

---

## ⚙️ 5. KONFIGURASI FOREIGN KEY (FK)

### Komponen Konfigurasi FK
Setiap kolom FK memiliki 5 pengaturan tambahan:

| Pengaturan | Kolom DB | Keterangan |
|-----------|---------|-----------|
| Tabel Referensi | `wmfc_fk_table` | Nama tabel yang direferensikan (otomatis terdeteksi) |
| Kolom PK Referensi | `wmfc_fk_pk_column` | Kolom PK di tabel referensi (otomatis terdeteksi) |
| Kolom Tampilan | `wmfc_fk_display_columns` | Kolom-kolom yang muncul di modal pencarian FK |
| Label Kolom | `wmfc_fk_label_columns` | Alias/nama custom untuk header kolom di modal FK |
| Prioritas Tampilan | `wmfc_fk_priority_display` | Satu kolom yang ditampilkan sebagai nilai FK di tabel index, detail, edit, dan delete |

### Mekanisme FK Display Columns
- User memilih kolom mana saja yang ditampilkan di modal pencarian menggunakan **checkbox**
- Kolom PK dan 7 common fields otomatis dikecualikan dari pilihan
- Minimal **1 kolom** harus dicentang (guard di frontend)
- Jika kolom di-uncheck → data label kolom tersebut juga dihapus dari database

### Mekanisme FK Label Columns
- Setiap kolom yang dipilih (display columns) bisa diberi alias label custom
- Jika label = `"default"` → menggunakan nama kolom asli sebagai header
- Contoh: kolom `tk_nama` bisa diberi label `"Nama Kategori"`

### Mekanisme FK Priority Display
- Menentukan **satu kolom** dari display columns yang nilainya ditampilkan sebagai representasi FK
- Ditampilkan di semua view: index (tabel), detail, edit, dan delete
- Contoh: Kolom FK `fk_m_kategori` menampilkan nilai `tk_kode` (seperti "WEB") bukan ID (seperti "5")
- Default otomatis = kolom display pertama saat auto-generate

### Alur Pencarian FK (Modal Search)
1. User klik input FK → modal terbuka
2. Modal menampilkan tabel dengan kolom sesuai `wmfc_fk_display_columns`
3. Header kolom menggunakan label dari `wmfc_fk_label_columns`
4. User pilih baris → nilai PK FK masuk ke form input

---

## ✅ 6. VALIDASI FIELD

### Opsi Validasi (Tersimpan sebagai JSON di `wmfc_validation`)

| Validasi | Keterangan |
|----------|-----------|
| `required` | Field wajib diisi |
| `unique` | Nilai harus unik di tabel (per kolom) |
| `email` | Format email valid |
| `max` | Maksimal karakter (angka yang diinput user) |
| `min` | Minimal karakter (angka yang diinput user) |
| `mimes` | Tipe file yang diizinkan (contoh: `png,jpg,pdf`) — khusus tipe media |

### Aturan Otomatis
- Kolom PK non-auto-increment → otomatis `required` + `unique`
- Kolom PK auto-increment → tidak memerlukan validasi (hidden dari form)
- `max` di-clamp otomatis: tidak boleh melebihi `wmfc_max_length` (dari skema tabel)

### Validasi Khusus File/Media
- Jika tipe field = `media` / `file` / `gambar`:
  - Validasi menggunakan `mimetypes` (BUKAN `mimes`) — karena Windows mendeteksi MIME type dokumen sebagai `application/octet-stream`
  - Extension di-mapping ke MIME types lengkap (contoh: `pdf` → `application/pdf,application/x-pdf,application/octet-stream`)
  - Validasi `max` menggunakan `wmfc_ukuran_max` (dalam MB, dikonversi ke KB saat proses: `ukuran_max * 1024`)
  - Pesan error menampilkan satuan MB (bukan karakter), contoh: "Testing Bukti maksimal 1 MB"

---

## 🔤 7. KRITERIA FIELD

### Opsi Kriteria (Tersimpan sebagai JSON di `wmfc_criteria`)

| Kriteria | Keterangan |
|----------|-----------|
| `uppercase` | Otomatis konversi input ke huruf kapital saat simpan |
| `lowercase` | Otomatis konversi input ke huruf kecil saat simpan |

### Aturan
- Hanya berlaku untuk field bertipe teks (`text`, `textarea`)
- `uppercase` dan `lowercase` saling eksklusif (hanya satu yang aktif)
- Konversi dilakukan di backend oleh `MasterMenuService::applyFieldCriteria()`

---

## 📋 8. FITUR DISPLAY & LABEL

### Display List (`wmfc_display_list`)
- Menentukan apakah kolom muncul di tabel index (halaman daftar data)
- Boolean: `true` = tampil, `false` = tersembunyi di index tapi tetap ada di form/detail
- Default = `true` untuk semua kolom yang visible

### Label Keterangan (`wmfc_label_keterangan`)
- Teks bantuan yang muncul di bawah input form
- Auto-generate berdasarkan konfigurasi field: tipe, kriteria, dan validasi
- Untuk field non-media: format `"tk [label] [deskripsi tipe] [kriteria] [validasi]"`
- Untuk field media/file/gambar: format `"tk [label] [required] [format mimes] [ukuran max]"`
  - Contoh: `"tk testing bukti wajib diisi dengan format pdf dengan max 1 mb"`
  - Contoh tanpa mimes: `"tk testing bukti wajib diisi bisa untuk semua type tanpa batas ukuran"`
- Bisa di-override dengan teks custom

### Visible (`wmfc_is_visible`)
- Menentukan apakah kolom ditampilkan di form (create/edit)
- Kolom PK auto-increment otomatis `visible = false`
- 7 common fields otomatis `visible = false`

### Order (`wmfc_order`)
- Urutan tampil kolom di form dan tabel
- Bisa diatur ulang oleh user

---

## 📁 9. MEDIA STORAGE (FILE UPLOAD)

### Konsep Penyimpanan
- File disimpan secara fisik di: `storage/app/public/menu_master/{nama_tabel}/{hash}.ext`
- Diakses melalui symlink Laravel: `public/storage` → `storage/app/public`
- URL publik: `asset('storage/menu_master/{nama_tabel}/{hash}.ext')`
- Database hanya menyimpan **path relatif hash**: `menu_master/{nama_tabel}/{hash}.ext`
- Hash = 40 karakter random (`Str::random(40)`) + extension asli

### Struktur Folder
```
storage/app/public/
  └── menu_master/
      ├── m_testing/
      │   ├── aB3xK9mN2pQr7sT...40char...pdf
      │   └── Zk8wL1vM4nOp6qR...40char...jpg
      └── m_dokumen/
          └── cD5eF2gH8iJk3lM...40char...docx
```

### Alur Upload (Create & Update)
1. User submit form dengan file → `MasterController` terima `Request`
2. Loop semua field config bertipe `media`/`file`/`gambar`
3. Jika ada file → panggil `MediaStorageService::uploadFile($file, $tableName)`
4. Service generate hash, buat folder otomatis jika belum ada, simpan file
5. Return hash path → disimpan ke kolom database
6. **Saat Update**: File lama dihapus dulu (`deleteFile`) sebelum upload baru
7. **Saat Delete**: Semua file terkait record dihapus sebelum soft delete

### Validasi Upload (Windows-Safe)
- **TIDAK** menggunakan Laravel `mimes` rule (unreliable di Windows karena MIME detection gagal)
- **HANYA** menggunakan `mimetypes` rule dengan whitelist komprehensif
- Setiap extension di-mapping ke multiple MIME types (termasuk `application/octet-stream` sebagai fallback)
- Contoh mapping: `pdf` → `application/pdf, application/x-pdf, application/octet-stream`
- Ukuran max dikonversi: `wmfc_ukuran_max` (MB) × 1024 = KB untuk Laravel rule

---

## 🔄 10. ALUR KERJA LENGKAP

### A. Pembuatan Menu Master Baru

**Prasyarat:**
- Tabel sudah dibuat di database
- Tabel WAJIB memiliki 7 common fields: `isDeleted`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`

**Langkah di Management Menu URL:**
1. Klik "Tambah Menu URL"
2. Pilih aplikasi (fk_m_application)
3. Pilih kategori = **Master**
4. Input nama tabel di `wmu_akses_tabel`
5. Klik **Cek Tabel** → sistem melakukan:
   - Cek tabel ada di database
   - Cek 7 common fields lengkap
   - Cek duplikasi (tabel sudah terdaftar atau belum)
   - Baca seluruh kolom tabel (DESCRIBE)
   - Deteksi PK, FK (via constraint), tipe data
   - Auto-generate field config untuk setiap kolom
6. Tabel konfigurasi field muncul → user customize:
   - Label field, tipe inputan, kriteria, validasi
   - FK: pilih display columns, atur label, pilih priority display
   - Toggle display list dan visible
7. Input URL menu (`wmu_nama`), keterangan (`wmu_keterangan`)
8. Klik **Simpan** → sistem membuat record di `web_menu_url` + semua `web_menu_field_config`

**Otomatisasi Backend saat Simpan (Kategori Master):**
- `controller_name` = `Template\MasterController`
- `module_type` = `sisfo`
- `wmu_parent_id` = `null` (master selalu parent)

### B. Update Menu Master

**Skenario 1 — Edit konfigurasi tanpa perubahan struktur tabel:**
- Buka Management Menu URL → Edit
- Ubah label, validasi, kriteria, FK config, dll
- Simpan → sistem update setiap field config yang berubah (true UPDATE, bukan delete-create)

**Skenario 2 — Struktur tabel berubah (ALTER TABLE):**
- Buka Management Menu URL → Edit
- Klik **Re-Check Tabel** → sistem panggil `validateTable()` dengan `menuUrlId`
- Sistem panggil `detectTableChanges()` → bandingkan struktur DB vs config tersimpan
- Hasil deteksi: kolom `added`, `removed`, `modified`
- **Merge config**: config lama (existing) + config baru (generated) digabung
  - Kolom yang masih ada → pakai data existing (label, validasi, kriteria tetap)
  - Kolom baru → pakai generated config (default values)
- Frontend: Row yang berubah/bertambah ditandai **background kuning terang** (`row-changed` CSS class)
- Alert kuning muncul dengan detail perubahan (kolom ditambah/dihapus/dimodifikasi)
- User re-configure → Simpan
- **Saat Simpan**: Kolom yang sudah dihapus dari tabel → soft-delete config-nya (`isDeleted = 1`)

**Mekanisme Link Redirect (Create → Edit):**
- Saat **Create** (Cek Tabel), jika tabel sudah terdaftar (`isDuplicate = true`) dan ada perubahan struktur (`hasChanges = true`):
  - Tombol Submit di-disable
  - Field configurator disembunyikan
  - Muncul badge kuning clickable: "Klik di sini untuk membuka halaman Edit"
  - Badge memanggil `modalAction(editUrl)` untuk membuka modal Edit langsung
- Jika tabel sudah terdaftar tanpa perubahan (`isDuplicate = true`, `hasChanges = false`):
  - Error merah: tabel sudah terdaftar, arahkan edit menu yang ada
  - Tombol Submit di-disable

### C. Hapus Menu Master
- Sistem cek apakah URL digunakan oleh `web_menu_global`
- Jika ya → tidak bisa dihapus (harus hapus dari menu global dulu)
- Jika tidak → soft delete URL + soft delete semua field config terkait

### D. Penggunaan Menu Master (End User)

**Alur Akses:**
1. User klik menu di sidebar → request ke URL (contoh: `/m-testing`)
2. Dynamic routing: `CheckDynamicRoute` → `PageController` → resolve `Template\MasterController`
3. `MasterController` constructor:
   - Query `web_menu_url` berdasarkan URL slug
   - Ambil semua `web_menu_field_config` aktif (isDeleted = 0)
   - Tentukan nama tabel dan kolom PK
4. Method CRUD dipanggil sesuai action

**Alur CRUD di MasterController:**

| Operasi | Proses |
|---------|--------|
| **Index** | Build query SELECT dengan LEFT JOIN FK → tampilkan di DataTable |
| **getData** | Query data + pagination → return JSON untuk AJAX DataTable |
| **addData** | Build form fields dari field config → tampilkan form create |
| **createData** | Validasi → apply criteria → upload file (jika media) → INSERT → redirect |
| **editData** | Query data by ID → build form fields + isi nilai → tampilkan form edit |
| **updateData** | Validasi → apply criteria → hapus file lama + upload baru (jika media berubah) → UPDATE → redirect |
| **detailData** | Query data by ID dengan JOIN FK → tampilkan detail (file ditampilkan sebagai link/preview) |
| **deleteData** | GET: tampilkan konfirmasi. DELETE: hapus file terkait → soft delete (isDeleted = 1) |
| **getFkData** | Query tabel FK → return data untuk modal pencarian |

---

## 📂 11. KOMPONEN FILE SISTEM

### Backend

| Komponen | File | Fungsi |
|----------|------|--------|
| **Model** | `WebMenuUrlModel.php` | CRUD URL menu, validateTable, autoGenerateFieldConfigs, detectTableChanges, parseCriteria/ValidationFromRequest |
| **Model** | `WebMenuFieldConfigModel.php` | CRUD field config, generateLabelKeterangan (auto-text bantuan), reorder, applyFieldCriteria |
| **Model** | `WebMenuGlobalModel.php` | CRUD menu sidebar, relasi ke URL dan menu per role |
| **Controller** | `Template\MasterController.php` | Controller universal CRUD semua menu master + handle file upload/delete otomatis |
| **Controller** | `WebMenuUrlController.php` | Management menu URL (konfigurasi), handle() dispatcher untuk AJAX validateTable & autoGenerateFields |
| **Controller** | `PageController.php` | Entry point dynamic routing, resolve controller + method dari database |
| **Service** | `MasterMenuService.php` | Build query dinamis (SELECT+JOIN), build form fields, build validation rules (termasuk MIME mapping untuk media), apply criteria, insert/update data |
| **Service** | `DatabaseSchemaService.php` | Inspeksi struktur database: DESCRIBE tabel, deteksi PK/FK via constraint, suggestFieldType, generateFieldLabel |
| **Service** | `MediaStorageService.php` | Upload file dengan hash (40 karakter random), hapus file, get URL, cek file exists. Folder per tabel otomatis |
| **Helper** | `ValidationHelper.php` | Build custom validation messages (Bahasa Indonesia), build custom attributes, validasi format JSON criteria & validation |
| **Helper** | `RouteHelper.php` | Daftar URL yang dikecualikan dari dynamic routing (`$nonStandardSisfoUrls`) |
| **Middleware** | `CheckDynamicRoute.php` | Validasi URL eligible untuk dynamic routing sebelum masuk PageController |

### Detail Kegunaan Setiap Service & Helper

**`MasterMenuService`** — Otak logika bisnis menu master:
- `buildValidationRules()`: Baca field configs → generate Laravel validation rules. Untuk media: mapping extension ke MIME types (hanya pakai `mimetypes`, bukan `mimes` — karena Windows issue)
- `buildSelectQuery()`: Build query SELECT dinamis dengan LEFT JOIN untuk FK, menggunakan priority display sebagai alias
- `buildFormFields()`: Susun array field untuk render di view, termasuk enum options dan FK display values
- `applyFieldCriteria()`: Terapkan uppercase/lowercase sebelum simpan
- `insertData()` / `updateData()` / `deleteData()`: Operasi CRUD universal

**`DatabaseSchemaService`** — Pembaca struktur database:
- `getTableStructure()`: DESCRIBE tabel → array kolom lengkap (nama, tipe, nullable, PK, FK, extra)
- `getPrimaryKey()`: Deteksi kolom PK dari tabel
- `getForeignKeys()`: Baca FK constraints dari `INFORMATION_SCHEMA`
- `getFKDisplayableColumns()`: Ambil kolom dari FK table yang bisa ditampilkan (exclude PK & common fields)
- `suggestFieldType()`: Mapping otomatis tipe MySQL → tipe input form

**`MediaStorageService`** — Pengelola file upload dengan hash:
- `uploadFile()`: Generate hash 40 karakter → simpan di `storage/app/public/menu_master/{table}/` → return path relatif untuk DB
- `deleteFile()`: Hapus file fisik berdasarkan hash path
- `getFileUrl()`: Convert hash path → URL publik via `asset('storage/...')`
- `fileExists()`: Cek keberadaan file fisik

**`ValidationHelper`** — Builder pesan validasi:
- `buildCustomMessages()`: Generate pesan error Bahasa Indonesia per field. Untuk file/media: pesan ukuran dalam MB (bukan karakter)
- `buildRulesArray()`: Build validation rules dari field config array
- `isValidCriteriaJson()` / `isValidValidationJson()`: Validasi format JSON config

### Frontend — Views Management (AdminWeb/WebMenuUrl/)

| View | Fungsi |
|------|--------|
| `index.blade.php` | Daftar semua menu URL |
| `create.blade.php` | Form konfigurasi menu baru + tabel field config |
| `update.blade.php` | Form edit menu + re-check tabel |
| `detail.blade.php` | Detail lengkap + field configs + audit trail |
| `delete.blade.php` | Konfirmasi hapus + warning dampak |
| `data.blade.php` | Partial DataTable rows |

### Frontend — Views Template (Template/Master/)

| View | Fungsi |
|------|--------|
| `index.blade.php` | Daftar data universal (DataTable) |
| `create.blade.php` | Form create universal (semua tipe field) |
| `update.blade.php` | Form update universal |
| `detail.blade.php` | Detail view universal |
| `delete.blade.php` | Konfirmasi hapus universal |
| `data.blade.php` | Partial DataTable rows universal |

---

## 🔄 12. MEKANISME AUTO-GENERATE FIELD CONFIG

### Proses `validateTable()` (Saat Cek Tabel)

**Mode CREATE (tabel belum terdaftar):**
1. Cek tabel exists
2. Cek 7 common fields
3. Cek duplikasi nama tabel → jika sudah ada:
   - Tanpa perubahan → block create, tampilkan pesan "tabel sudah terdaftar"
   - Ada perubahan → block create, tampilkan badge link redirect ke halaman Edit
4. Panggil `autoGenerateFieldConfigs()` → return fields untuk frontend

**Mode UPDATE (tabel sudah terdaftar, `menuUrlId` dikirim):**
1. Cek tabel exists
2. Cek menu masih aktif (belum soft-delete)
3. Panggil `detectTableChanges()` → bandingkan struktur DB vs config tersimpan
4. **Merge**: Loop generated fields, jika kolom sudah ada → pakai config existing (label, validasi, kriteria tetap), jika kolom baru → pakai generated default
5. Return: `hasChanges`, `changes` (added/removed/modified), `fields` (merged)

### Proses `detectTableChanges()`
- Bandingkan kolom tabel saat ini (`DESCRIBE`) vs field config tersimpan di database
- **Added**: kolom ada di DB tapi tidak di config → kolom baru
- **Removed**: kolom ada di config tapi tidak di DB → kolom dihapus
- **Modified**: kolom ada di keduanya tapi tipe data berubah (setelah normalisasi)
- Normalisasi tipe: ENUM values diurutkan agar perbandingan konsisten

### Mekanisme Highlight Row (Update Mode)
- Frontend kumpulkan nama kolom dari `changes.added` dan `changes.modified`
- Setiap row field config yang kolom-nya berubah/baru → ditambah CSS class `row-changed`
- CSS `row-changed`: background kuning terang (`#fff9c4`) sebagai visual indicator
- User bisa langsung melihat kolom mana yang perlu di-review

### Proses `autoGenerateFieldConfigs()`
- Membaca semua kolom tabel via `DESCRIBE`
- Untuk setiap kolom (kecuali 7 common fields):
  - Deteksi apakah PK → set `wmfc_is_primary_key`, `wmfc_is_auto_increment`
  - Deteksi apakah FK → set `wmfc_is_foreign_key`, `wmfc_fk_table`, `wmfc_fk_pk_column`
  - Mapping tipe data → opsi field type (lihat Bagian 4)
  - Set default label = nama kolom
  - Set default visible = true (kecuali PK auto-increment dan common fields)
  - Jika FK: set display columns awal, priority display = kolom display pertama

---

## 🔧 13. MEKANISME QUERY DINAMIS

### `buildSelectQuery()` — Query untuk Index dan Detail
- Membuat query SELECT dinamis berdasarkan field config
- Untuk kolom biasa → SELECT langsung dari tabel utama
- Untuk kolom FK → LEFT JOIN ke tabel referensi
  - Menggunakan `wmfc_fk_pk_column` sebagai join condition
  - Menambahkan kolom priority display ke SELECT (sebagai alias `{column}_display`)
  - Jika priority display tidak termasuk di display columns → tetap ditambahkan sebagai kolom extra
  - Fallback: jika `wmfc_fk_pk_column` null → gunakan `DatabaseSchemaService::getPrimaryKey()`

### `buildFormFields()` — Data untuk Form Create/Edit
- Menyusun array field untuk di-render di view
- Untuk kolom FK dengan tipe `search`:
  - Menghitung `display_value` dari data JOIN atau query langsung ke tabel FK
  - Menampilkan nilai priority display di input form (read-only)
- Untuk kolom ENUM dengan tipe `dropdown`:
  - Parse ENUM values dari `SHOW COLUMNS` → jadikan options

---

## 📌 14. SETUP MENU LENGKAP (STEP BY STEP)

### Step 1 — Persiapan Tabel
1. Buat tabel baru di database
2. Pastikan ada 7 common fields wajib
3. Set PK (sebaiknya auto increment)
4. Set FK constraint jika ada relasi ke tabel lain
5. Pastikan `php artisan storage:link` sudah dijalankan (untuk media upload)

### Step 2 — Daftarkan di Management Menu URL
1. Buka Management Menu URL → Tambah
2. Pilih aplikasi, pilih kategori **Master**
3. Input nama tabel → Cek Tabel
4. Konfigurasi field: label, tipe, validasi, kriteria, FK config
5. Input URL dan keterangan → Simpan

### Step 3 — Tambahkan ke Menu Sidebar
1. Buka Menu Management Global → Tambah
2. Pilih URL menu yang baru dibuat
3. Input: nama default, icon (Font Awesome), tipe (general/special)
4. Pilih kategori: Menu Biasa / Sub Menu / Group Menu
5. Atur urutan → Simpan

### Step 4 — Berikan Akses ke Role
1. Buka Menu Management
2. Assign menu ke role yang membutuhkan
3. Set status aktif/nonaktif per role

### E. Mekanisme AJAX Dispatcher (`handle()`)
- `WebMenuUrlController` memiliki method `handle()` yang dipanggil di awal `createData()`, `editData()`, `updateData()`
- Cek parameter `action` dari request:
  - `action = 'validateTable'` → panggil `validateTable()` (AJAX response)
  - `action = 'autoGenerateFields'` → panggil `autoGenerateFields()` (AJAX response)
- Jika tidak ada `action` → lanjut ke normal form submission (create/update)
- Ini memungkinkan satu endpoint URL digunakan untuk multiple AJAX operations

### ✅ Menu Master Siap Digunakan

---

## 🔧 15. TROUBLESHOOTING

| Masalah | Solusi |
|---------|--------|
| Tabel tidak terdeteksi saat Cek Tabel | Pastikan tabel memiliki 7 common fields wajib |
| Duplikasi menu saat tambah | Tabel sudah terdaftar → edit menu yang sudah ada |
| Tabel berubah tapi tidak bisa create ulang | Klik badge kuning redirect ke halaman Edit untuk update config |
| FK tidak terdeteksi otomatis | Pastikan ada FK constraint di database |
| Nilai FK tampil ID bukan nama | Cek `wmfc_fk_priority_display` sudah diisi kolom yang benar |
| Error 500 saat edit menu master | Cek `wmfc_fk_pk_column` terisi benar di field config |
| Kolom tidak muncul di tabel index | Cek `wmfc_display_list` = true untuk kolom tersebut |
| Kolom tidak muncul di form | Cek `wmfc_is_visible` = true |
| Validasi tidak berjalan | Pastikan format JSON `wmfc_validation` benar |
| Max length error | Sistem auto-clamp ke `wmfc_max_length` dari skema tabel |
| File upload gagal validasi MIME | Sistem menggunakan `mimetypes` bukan `mimes`. Cek mapping extension di `MasterMenuService` |
| PDF terdeteksi `application/octet-stream` | Normal di Windows — sudah di-handle dengan whitelist MIME types |
| File tersimpan tapi tidak bisa diakses | Pastikan `php artisan storage:link` sudah dijalankan (symlink `public/storage`) |
| Pesan validasi file menunjukkan "karakter" | Cek `wmfc_field_type` = media/file/gambar di field config |
| Baris kuning tidak muncul saat Re-Check | Cek response `changes.added` dan `changes.modified` dari backend |
| Label keterangan tidak otomatis | Pastikan `wmfc_label_keterangan` = 'auto' saat simpan, atau generate via `generateLabelKeteranganPublic()` |

---

## 📌 16. KEUNGGULAN SISTEM

- **Zero Code** — Tidak perlu coding controller, model, atau view
- **Database-Driven** — 100% konfigurasi dari database schema
- **Auto-Detect FK** — FK constraint otomatis terdeteksi dan dikonfigurasi
- **Auto-Detect Changes** — Perubahan ALTER TABLE terdeteksi otomatis dengan highlight visual
- **Smart Redirect** — Duplikasi tabel di Create otomatis arahkan ke Edit dengan link clickable
- **Priority Display** — Kolom FK menampilkan nilai bermakna, bukan ID
- **Flexible Validation** — Kombinasi validasi dan kriteria per field
- **Media Support** — Upload file dengan hash, folder per tabel, validasi MIME Windows-safe
- **Universal Template** — 1 controller + 6 view handle semua menu master
- **Dynamic Routing** — Tidak perlu menambah kode di file route
- **Consistent UI** — Semua menu master memiliki tampilan yang seragam
- **True Update** — Update config menggunakan UPDATE (bukan delete-create), data konfigurasi aman

---

**© PPID Polinema - Menu Master System v3.0**
