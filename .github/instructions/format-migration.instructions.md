# Instruksi Umum: Buat Laravel Migration dari SQL DDL

Gunakan file `.sql` yang disertakan sebagai referensi utama.
Buat file migration Laravel yang menghasilkan struktur tabel **identik** dengan DDL tersebut.

---

## 1. Nama File Migration

Format: `YYYY_MM_DD_HHMMSS_create_table_{nama_tabel}.php`

Contoh: `2025_01_01_000000_create_table_t_permohonan_informasi.php`

---

## 2. Ketentuan Umum Tabel

- Engine: **InnoDB**
- Tambahkan di dalam `Schema::create`:
  ```php
  $table->charset = 'utf8mb4';
  $table->collation = 'utf8mb4_unicode_ci';
  ```

---

## 3. Primary Key

- Kolom ID menggunakan `integer` + `autoIncrement()` + **`unsigned()`**
- Gunakan nama kolom sesuai DDL (bukan `id`)

```php
$table->integer('nama_tabel_id')->unsigned()->autoIncrement();
```

---

## 4. Kolom Foreign Key

- Tipe: `integer`, **nullable**, dan wajib **`unsigned()`**
- Jangan gunakan `->unsignedInteger()`, gunakan `->integer()->unsigned()->nullable()`

```php
$table->integer('fk_nama_kolom')->unsigned()->nullable();
```

---

## 5. Kolom ENUM

- Gunakan nilai enum **persis** sesuai DDL (perhatikan huruf besar/kecil dan spasi)
- Tambahkan `->default()` jika ada nilai default di DDL

```php
$table->enum('nama_kolom', ['Nilai1', 'Nilai2', 'Nilai3']);
$table->enum('nama_kolom', ['a', 'b', 'c'])->default('a');
```

---

## 6. Kolom VARCHAR / STRING

- Gunakan `$table->string('nama_kolom', panjang)`
- Jika nullable: tambahkan `->nullable()`
- Jika NOT NULL tanpa default: tidak perlu tambahan apa-apa

---

## 7. Kolom TIMESTAMP

- **Jangan** gunakan `$table->timestamps()` — definisikan setiap kolom secara manual
- Untuk kolom `created_at` dengan default `CURRENT_TIMESTAMP`:
  ```php
  $table->timestamp('created_at')->useCurrent();
  ```
- Untuk kolom timestamp nullable:
  ```php
  $table->timestamp('nama_kolom')->nullable();
  ```

---

## 8. Kolom TINYINT

```php
$table->tinyInteger('nama_kolom')->default(0);          // NOT NULL, default 0
$table->tinyInteger('nama_kolom')->nullable()->default(0); // nullable, default 0
```

---

## 9. Index

Buat index sesuai DDL dengan ketentuan nama berikut:

### Format Nama Index
```
{nama_tabel}_{nama_kolom}_idx
```

Contoh:
```php
$table->index('nama_kolom', 'nama_tabel_nama_kolom_idx');
```

### INVISIBLE Index
Laravel tidak mendukung `INVISIBLE` index secara native. Tetap buat indexnya, tambahkan komentar:

```php
// Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
$table->index('nama_kolom', 'nama_tabel_nama_kolom_idx');
```

---

## 10. Foreign Key Constraints

Format nama constraint:
```
{nama_tabel}_{nama_kolom_fk}
```

Contoh lengkap:
```php
$table->foreign('nama_kolom_fk', 'nama_tabel_nama_kolom_fk')
    ->references('kolom_referensi_id')->on('tabel_referensi')
    ->onDelete('RESTRICT')->onUpdate('CASCADE');
```

> Gunakan nama constraint, tabel referensi, dan kolom referensi **persis** sesuai DDL.

---

## 11. Metode `down()`

Urutan wajib:
1. Drop semua **foreign key constraints** (gunakan nama constraint)
2. Drop tabel

```php
public function down(): void
{
    Schema::table('nama_tabel', function (Blueprint $table) {
        $table->dropForeign('nama_constraint_1');
        $table->dropForeign('nama_constraint_2');
    });

    Schema::dropIfExists('nama_tabel');
}
```

---

## 12. Larangan

| ❌ Jangan                         | ✅ Gantinya                                      |
|-----------------------------------|--------------------------------------------------|
| `$table->timestamps()`            | Definisikan `created_at`, `updated_at` manual    |
| `->unsignedInteger()`             | `->integer()->unsigned()`                        |
| Tambah kolom tidak ada di DDL     | Ikuti DDL secara ketat                           |
| Asumsikan urutan kolom sendiri    | Ikuti urutan kolom di DDL                        |