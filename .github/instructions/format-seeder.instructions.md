---
applyTo: 'database/seeders/**'
---

# Format Seeder Instructions

Instruksi ini mengatur standar pembuatan dan format file seeder di dalam project PPID-polinema.

## 1. Penamaan File Seeder

### Format: `{PrefixNomor}_{TableName}Seeder.php`

**Aturan:**
- **PrefixNomor**: Gunakan nomor urut 6 digit dengan format `000001`, `000002`, `000003`, dst.
- **Nomor harus sama dengan nomor migration** yang membuat tabel tersebut
  - Jika migration: `2025_03_02_000001_create_m_hak_akses_table.php` → Seeder: `000001_m_hak_akses_seeder.php`
  - Jika migration: `2025_03_02_000002_create_m_user_table.php` → Seeder: `000002_m_user_seeder.php`
- **TableName**: Nama tabel dalam format PascalCase tanpa underscore
  - Tabel `m_hak_akses` → `MHakAkses` atau gunakan format yang sudah terbentuk dari migration
- **Akhiran**: Selalu gunakan suffix `Seeder`
- **Extension**: `.php`

**Contoh Valid:**
```
000001_m_hak_akses_seeder.php
000002_m_user_seeder.php
000006_set_hak_akses_seeder.php
000007_set_user_hak_akses_seeder.php
```

## 2. Namespace dan Class

**Format:**
```php
namespace Database\Seeders;

class Seeder{PrefixNomor}{TableName} extends Seeder
{
    public function run(): void
    {
        // ...
    }
}
```

**Contoh:**
```php
namespace Database\Seeders;

class Seeder000001MHakAkses extends Seeder
{
    public function run(): void
    {
        // ...
    }
}
```

## 3. Struktur Data Array

### 3.1 Audit Fields (Mandatory pada semua seeder)

Setiap row data harus memiliki audit fields berikut:

| Field | Value | Aturan |
|-------|-------|--------|
| `created_by` | `'System'` or `'Seeder'` | Selalu `'System'` untuk data seeder pertama kali |
| `created_at` | Timestamp | Format: `'YYYY-MM-DD HH:MM:SS'` atau `now()` saat seeding |
| `updated_by` | `null` | Hanya ada value jika ada update setelah create |
| `updated_at` | `null` | Hanya ada value jika ada update setelah create |
| `deleted_by` | `null` atau nama user | Hanya ada value jika `isDeleted = 1` sesuai DDL |
| `deleted_at` | `null` atau timestamp | Hanya ada value jika `isDeleted = 1` sesuai DDL |

**Rule Penting:**
- `created_by` → `'System'` (konsisten untuk seeder)
- `created_at` → Use actual datetime seeder dibuat
- Jika `isDeleted = 0` → `updated_by`, `updated_at`, `deleted_by`, `deleted_at` = `null`
- Jika `isDeleted = 1` → `deleted_by` dan `deleted_at` harus memiliki value sesuai DDL

### 3.2 Password/Hash Fields

**PENTING:** Gunakan `Hash::make()` untuk hashing password saat runtime, bukan menyimpan hash value langsung di seeder.

**SALAH (❌):**
```php
'password' => '$2y$12$LDaYqnIoRpSEp0jkOwobNeLJaPvr9PC7Pd5WL58dyXC7OzV863Aiy',
```

**BENAR (✅):**
```php
'password' => Hash::make('password_asli_disini'),
```

**Atau jika ingin hash di script terpisah:**
```php
'password' => bcrypt('password_asli'),
```

### 3.3 Data Sensitive

Untuk field yang berisi data sensitive (password, token, API key):
- Gunakan hashing function (`Hash::make()`, `bcrypt()`)
- Jangan store plain text di seeder
- Dokumentasikan password plain text di file terpisah atau comment dengan aman

## 4. Use Statement & Import

**Minimal imports untuk seeder:**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;  // Jika ada password hashing
```

## 5. Method: insertOrIgnore()

**Wajib gunakan `insertOrIgnore()`** bukan `insert()`:

**Alasan:**
- Menghindari duplicate key error jika seeder dijalankan berkali-kali
- Idempotent: aman untuk di-run ulang tanpa side effects
- Lebih robust untuk development dan testing

**Format:**
```php
public function run(): void
{
    $data = [
        // array data
    ];

    DB::table('table_name')->insertOrIgnore($data);
}
```

## 6. Struktur File Lengkap

**Template:**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Seeder000002MUser extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'user_id' => 1,
                'nama_pengguna' => 'Gelby Firmansyah',
                'email_pengguna' => 'gelby@example.com',
                'password' => Hash::make('password123'),
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => '2025-03-02 13:11:03',
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
            // row lainnya...
        ];

        DB::table('m_user')->insertOrIgnore($data);
    }
}
```

## 7. Catatan Penting

### 7.1 Ordering Seeder
Jika ada dependency antar seeder (foreign key), run order:
1. Seeder tanpa FK terlebih dahulu (master data)
2. Seeder dengan FK kemudian (detail/mapping data)

Contoh untuk PPID-polinema:
```
1. 000001_m_hak_akses_seeder.php
2. 000002_m_user_seeder.php
3. 000006_set_hak_akses_seeder.php (has FK ke m_hak_akses)
4. 000007_set_user_hak_akses_seeder.php (has FK ke m_user, m_hak_akses)
```

### 7.2 Data Consistency
- Pastikan timestamp konsisten dengan timezone project (config/app.php)
- Pastikan FK values valid terhadap table yang direferensikan
- Validate data sebelum run seeder

### 7.3 Documentation
Comment data yang penting atau memiliki special meaning:
```php
$data = [
    [
        // Super Administrator - Full Access
        'hak_akses_id' => 1,
        'hak_akses_kode' => 'SAR',
        'hak_akses_nama' => 'Super Administrator',
        // ...
    ],
];
```

## 8. Contoh Implementasi Lengkap

### Seeder tanpa FK (Master Data)
```php
// 000001_m_hak_akses_seeder.php
class Seeder000001MHakAkses extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'hak_akses_id' => 1,
                'hak_akses_kode' => 'SAR',
                'hak_akses_nama' => 'Super Administrator',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => '2025-03-02 13:06:18',
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('m_hak_akses')->insertOrIgnore($data);
    }
}
```

### Seeder dengan Password Hashing
```php
// 000002_m_user_seeder.php
class Seeder000002MUser extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'user_id' => 1,
                'password' => Hash::make('gelby123'),  // ✅ Hashed at runtime
                'nama_pengguna' => 'Gelby Firmansyah',
                'email_pengguna' => 'gelby@example.com',
                'isDeleted' => 0,
                'created_by' => 'System',
                'created_at' => '2025-03-02 13:11:03',
                'updated_by' => null,
                'updated_at' => null,
                'deleted_by' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('m_user')->insertOrIgnore($data);
    }
}
```

### Seeder dengan Soft Delete (isDeleted = 1)
```php
// 000001_m_hak_akses_seeder.php
$data = [
    [
        'hak_akses_id' => 6,
        'hak_akses_kode' => 'OPT',
        'hak_akses_nama' => 'Operator Senior',
        'isDeleted' => 1,  // Soft deleted
        'created_by' => 'System',
        'created_at' => '2025-04-28 21:35:04',
        'updated_by' => 'Gelby F.',
        'updated_at' => '2025-04-28 21:35:54',
        'deleted_by' => 'Gelby F.',  // ✅ Must have value when isDeleted = 1
        'deleted_at' => '2025-04-28 21:35:54',  // ✅ Must have value when isDeleted = 1
    ],
];
```

## 9. Validation Checklist

Sebelum commit seeder, pastikan:

- [ ] Nama file mengikuti format `{000001}_{table_name}_seeder.php`
- [ ] Prefix nomor sama dengan nomor migration
- [ ] Class name format `Seeder{Nomor}{TableName}`
- [ ] Namespace correct: `Database\Seeders`
- [ ] Menggunakan `insertOrIgnore()` bukan `insert()`
- [ ] `created_by` = `'System'`
- [ ] Password/hash fields menggunakan `Hash::make()` atau `bcrypt()`
- [ ] Audit fields lengkap: `created_at`, `updated_by`, `updated_at`, `deleted_by`, `deleted_at`
- [ ] Jika `isDeleted = 1`, pastikan `deleted_by` dan `deleted_at` memiliki value
- [ ] Jika `isDeleted = 0`, pastikan `deleted_by` dan `deleted_at` = `null`
- [ ] FK values valid dan exist di parent table
- [ ] Timestamp format konsisten: `'YYYY-MM-DD HH:MM:SS'`
- [ ] Comment penting atau special meaning
- [ ] No plain text password atau sensitive data tanpa hashing

---

**Last Updated:** 2025-03-17
**Status:** Active
