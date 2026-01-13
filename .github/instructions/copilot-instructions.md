# PPID Polinema - Instruksi Pengkodean AI

## Gambaran Proyek
Ini adalah sistem Layanan Pejabat Pengelola Informasi dan Dokumentasi (PPID) berbasis **Laravel 10.48.28** untuk Politeknik Negeri Malang. Dibangun dengan arsitektur modular menggunakan `nwidart/laravel-modules`, menangani permohonan informasi publik, pengaduan, dan manajemen dokumen dengan notifikasi WhatsApp terintegrasi.

### Stack Teknologi
- **Backend**: Laravel 10.48.28 + PHP 8.2.12 + MySQL
- **Frontend**: AlpineJS 3.14.8 + Vue 3.5.13 + Bootstrap
- **Autentikasi**: JWT (`tymon/jwt-auth`) + Laravel Sanctum
- **Build**: Vite + layanan WhatsApp API Node.js
- **Paket Utama**: `yajra/laravel-datatables-oracle`, `nesbot/carbon`

## Arsitektur & Struktur

### Desain Modular
- **Modul Utama**: `Modules/Sisfo/` - Berisi logika aplikasi utama
- **Modul Sekunder**: `Modules/User/` - Fitur yang menghadap pengguna
- Controller diorganisir berdasarkan domain: `AdminWeb/`, `Api/`, `SistemInformasi/`
- Model mengikuti struktur domain-driven: `Website/Publikasi/`, `SistemInformasi/EForm/`

### Pola Kunci

#### Pola Controller
Semua controller memperluas fungsionalitas dasar melalui traits:
```php
use TraitsController; // Menyertakan BaseControllerFunction untuk respons terstandar
```

Metode respons standar:
- `jsonSuccess($data, $message)` - Respons sukses API
- `jsonError(\Exception $e, $prefix)` - Penanganan error API  
- `jsonValidationError(ValidationException $e)` - Error validasi
- `redirectSuccess($route, $message)` - Redirect sukses web

#### Pola Model
Model menggunakan `TraitsModel` yang menyertakan `BaseModelFunction`:
- Mengisi otomatis field `created_by`/`updated_by` dari session atau JWT
- Field umum: `isDeleted`, timestamp audit
- Pola soft delete melalui `isDeleted = 0/1`
- Paginasi standar melalui metode `paginateResults()`

#### Validasi & Alur Data
- Model menangani validasi sendiri: `ModelName::validasiData($request)`
- Operasi CRUD: `createData()`, `updateData()`, `deleteData()`, `selectData()`
- Pembungkusan transaksi dalam model untuk integritas data

## Alur Kerja Pengembangan

### Proses Build Frontend
```powershell
npm run dev          # Development dengan hot reload
npm run build        # Production build
```
Konfigurasi Vite meliputi: kompilasi CSS/JS, integrasi AdminLTE

### Perintah Backend
```powershell
php artisan serve                    # Mulai Laravel dev server
php artisan module:make ModuleName   # Buat modul baru
php artisan migrate                  # Jalankan migrasi
```

### Layanan WhatsApp API
```powershell
start-whatsapp.bat   # Mulai WhatsApp API server
stop-whatsapp.bat    # Hentikan layanan
test-whatsapp.bat    # Tes konektivitas
reset-whatsapp-session.bat  # Reset autentikasi
```
- **Layanan**: Node.js dengan whatsapp-web.js pada `localhost:3000`
- **Autentikasi**: Diperlukan pemindaian kode QR
- **Konfigurasi**: `services.whatsapp` - base_url, token, pengaturan timeout
- **Logging**: Pelacakan pengiriman pesan lengkap dalam `log_whatsapp`
- **Integrasi**: Panggilan HTTP API dari Laravel dengan verifikasi status

## Konvensi Khusus Proyek

### Penamaan File
- Controllers: `{Domain}Controller.php` (contoh: `PengumumanController.php`)
- Models: `{Entity}Model.php` (contoh: `PengumumanModel.php`)
- Views: `sisfo::{path}.{template}` (templating Blade)

### Konvensi Database
- **Prefiks Tabel**: `t_` (transaksi), `m_` (data master), `log_` (log audit)
- **Primary Keys**: `{table_name}_id` (contoh: `pengumuman_id`, `berita_id`)
- **Foreign Keys**: `fk_{referenced_table}` (contoh: `fk_m_pengumuman_dinamis`)
- **Field Audit**: `created_by`, `updated_by`, `deleted_by` (simpan alias pengguna, bukan ID)
- **Soft Deletes**: Gunakan flag `isDeleted = 0/1` (bukan Laravel's deleted_at)
- **Tabel Kunci**: `m_user`, `m_hak_akses`, `t_permohonan_informasi`, `t_pengaduan_masyarakat`, `log_whatsapp`

### Struktur API (720+ Routes)
- **Autentikasi**: Token JWT (`tymon/jwt-auth`) dengan TTL 60 menit
- **API Publik**: `/api/public/*` (tanpa auth) - pengiriman konten, data landing page
- **API Terproteksi**: `/api/auth/*` (JWT diperlukan) - pengiriman form, manajemen pengguna
- **API Sistem**: `/api/system/*` - manajemen token internal
- **Format Respons**: JSON terstandar dengan field `success`, `message`, `data`
- **Endpoint Kunci**: 
  - E-Forms: `/api/auth/{permohonan-informasi,pengaduan-masyarakat,wbs}/create`
  - Verifikasi: `/api/auth/daftar-{verifikasi,review}-pengajuan/*`
  - Data Publik: `/api/public/get{DataBerita,DataPengumuman,DataTimeline}`

## Titik Integrasi

### Layanan Eksternal
- **WhatsApp Web API**: `whatsapp-api/server.js` - Layanan notifikasi
- **File Storage**: Laravel Storage facade untuk upload dokumen
- **Summernote**: Rich text editor untuk manajemen konten
- **DataTables**: Plugin jQuery untuk grid data admin

### Komunikasi Modul
- Helper bersama: `MenuHelper.php`, `EFormActiveMenuHelper.php`
- Referensi model lintas modul melalui fully qualified namespaces
- Autentikasi terpadu di seluruh modul

### Area Pengembangan Kunci
- **E-Forms**: Permohonan informasi publik, pengaduan, keberatan
- **Manajemen Konten**: Berita, pengumuman, regulasi  
- **Manajemen Pengguna**: Kontrol akses berbasis peran
- **Manajemen Dokumen**: Upload PDF, akses dokumen publik
- **Sistem Notifikasi**: Integrasi WhatsApp untuk update status

## Pola Logika Bisnis

### Alur Kerja E-Form
1. **Pengajuan**: Form diajukan melalui `/api/auth/{form-type}/create`
2. **Verifikasi**: Admin meninjau di `/daftar-verifikasi-pengajuan`
3. **Review**: Persetujuan akhir di `/daftar-review-pengajuan`
4. **Notifikasi**: Alert WhatsApp dikirim otomatis
5. **Pelacakan**: Akses timeline publik melalui `/permohonan/lacak`

### Publikasi Konten
- **Kategori Dinamis**: Semua konten menggunakan struktur `m_{type}_dinamis`
- **Konten Kaya**: Summernote WYSIWYG dengan upload gambar melalui metode `uploadImage()`
- **URL SEO**: Slug otomatis menggunakan helper `Str::slug()`
- **Manajemen File**: Laravel Storage dengan konfigurasi disk publik

## Konfigurasi Sistem & Environment

### Kunci Konfigurasi Laravel
- **App**: `app.name` = "PPID-Polinema", `app.env` untuk deteksi environment
- **Layanan WhatsApp**: `services.whatsapp.{base_url,token,enabled,timeout}`
- **JWT**: `jwt.ttl` = 60 menit, `jwt.secret` untuk penandatanganan token
- **Database**: `database.default` = "mysql", koneksi via `database.connections.mysql`
- **Modules**: `modules.namespace` = "Modules\\", auto-discovery diaktifkan

### Manajemen Koneksi Database
```php
// Dukungan multiple connection tersedia
mcp_laravel-boost_database-connections // Cek koneksi yang tersedia
mcp_laravel-boost_database-query       // Eksekusi query read-only
```

## Debugging & Monitoring Tools

### Error Tracking
- **Last Error**: Use `mcp_laravel-boost_last-error` to check recent exceptions
- **Browser Logs**: `mcp_laravel-boost_browser-logs` for frontend debugging
- **Application Logs**: `mcp_laravel-boost_read-log-entries` for backend issues

### Performance Monitoring
- **Route Analysis**: 720+ routes mapped via `mcp_laravel-boost_list-routes`
- **Config Inspection**: `mcp_laravel-boost_get-config` for runtime values
- **Database Schema**: Live schema via `mcp_laravel-boost_database-schema`

## Pola Akses Data

### Model Relationships & Queries
```php
// Contoh: Pengumuman dengan kategori dinamis
PengumumanModel::with(['PengumumanDinamis', 'UploadPengumuman'])
    ->where('isDeleted', 0)
    ->orderBy('created_at', 'desc');

// Pola pencarian dengan relasi
$query->whereHas('PengumumanDinamis', function ($subq) use ($search) {
    $subq->where('pd_nama_submenu', 'like', "%{$search}%");
});
```

### Implementasi Audit Trail
- **Auto-population**: `created_by` dari session/JWT via `BaseModelFunction`
- **Soft Delete**: `isDeleted = 0/1` bukan Laravel's `deleted_at`
- **Transaction Logging**: Semua operasi CRUD dicatat di `log_transaction`
- **WhatsApp Tracking**: Status pengiriman pesan di `log_whatsapp`

## Implementasi Keamanan

### Alur Autentikasi
1. **Web Login**: Session Laravel tradisional + pemilihan peran
2. **API Authentication**: Token JWT dengan TTL 60 menit
3. **Role Switching**: Penugasan peran dinamis via `switch-role/{hakAksesId}`
4. **Permission Checks**: Kontrol akses berbasis menu melalui `m_hak_akses`

### Pola Validasi Data
```php
// Validasi berbasis model (pola standar)
ModelName::validasiData($request);  // Aturan validasi kustom
ModelName::createData($request);    // Pembuatan dengan pembungkus transaksi
```

## Development Environment Setup

### Layanan yang Diperlukan
1. **Aplikasi Laravel**: `php artisan serve` pada port default
2. **WhatsApp API**: Layanan Node.js pada `localhost:3000`
3. **MySQL Database**: Koneksi dikonfigurasi di `.env`
4. **Asset Compilation**: Vite dev server untuk hot reload

### Dependensi Layanan
- **Autentikasi WhatsApp**: Diperlukan scan QR code sebelum pengiriman pesan
- **File Storage**: `storage/app/public` terhubung ke `public/storage`
- **Image Processing**: Integrasi Summernote untuk rich content editing
- **PDF Generation**: Kemampuan ekspor dokumen built-in

## Integrasi MCP Boost

### Tool MCP yang Tersedia untuk Pengembangan
- **Application Info**: `mcp_laravel-boost_application-info` - Dapatkan versi, paket, model
- **Database Operations**: `mcp_laravel-boost_database-{schema,query,connections}`
- **Configuration**: `mcp_laravel-boost_{get-config,list-available-config-keys}`
- **Debugging**: `mcp_laravel-boost_{last-error,browser-logs,read-log-entries}`
- **Route Analysis**: `mcp_laravel-boost_list-routes` - Semua 720+ routes
- **Documentation Search**: `mcp_laravel-boost_search-docs` - Dokumentasi ekosistem Laravel
- **Code Execution**: `mcp_laravel-boost_tinker` - Jalankan PHP dalam konteks Laravel

### Menggunakan MCP untuk Pengembangan
```php
// Cek status aplikasi
mcp_laravel-boost_application-info();

// Debug masalah database  
mcp_laravel-boost_database-schema();
mcp_laravel-boost_last-error();

// Eksekusi kode Laravel
mcp_laravel-boost_tinker('User::count()');
```

## File Kritis untuk Referensi
- `Modules/Sisfo/App/Http/Controllers/TraitsController.php` - Pola respons
- `Modules/Sisfo/App/Models/BaseModelFunction.php` - Pola lapisan data  
- `Modules/Sisfo/routes/{web,api}.php` - Organisasi route (774 routes total)
- `whatsapp-api/{server.js,package.json}` - Implementasi layanan WhatsApp
- `composer.json` - Dependencies, autoloading, dan helper functions
- `Modules/Sisfo/App/Helpers/{MenuHelper.php,EFormActiveMenuHelper.php}` - Utilitas bersama
- `config/services.php` - Konfigurasi WhatsApp dan layanan eksternal
- `vite.config.js` - Konfigurasi build frontend dengan integrasi Laravel

## Quick Reference Commands

### Alur Kerja Pengembangan
```powershell
# Backend
php artisan serve                    # Start Laravel (biasanya :8000)
php artisan tinker                   # Interactive PHP shell
php artisan route:list --compact     # Lihat semua routes
php artisan config:cache             # Cache konfigurasi

# Frontend  
npm run dev                          # Development dengan HMR
npm run build                        # Production build

# WhatsApp Service
start-whatsapp.bat                   # Start Node.js WhatsApp API
./whatsapp-api/server.js             # Eksekusi Node.js langsung
```

### Tugas Umum
- **E-Form Baru**: Perluas controller dan model `SistemInformasi/EForm/`
- **Manajemen Konten**: Gunakan controller `AdminWeb/` dengan metode `uploadImage()`
- **Pengembangan API**: Ikuti pola route `/api/{public,auth}/`
- **Manajemen Pengguna**: Manfaatkan `m_hak_akses` dan sistem role switching
- **Notifikasi**: Integrasikan dengan WhatsApp API via HTTP calls ke `localhost:3000`
