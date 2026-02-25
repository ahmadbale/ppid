### INSTRUCTION: UPDATE KONFIGURASI MENU (TAMBAH & UPDATE)

**1. PENAMBAHAN KOLOM DATABASE**
Tambahkan field berikut pada tabel konfigurasi:
- wmfc_label_keterangan (Type: Text/String, Nullable)
- wmfc_display_list (Type: Boolean/TinyInt, Default: 1)
- wmfc_ukuran_max (Type: Integer, Nullable)

**2. LOGIKA FIELD LABEL KETERANGAN (wmfc_label_keterangan)**
Fungsi: Menampilkan keterangan di bawah input field.
Mekanisme UI:
- Tampilkan Checkbox utama: [ ] Butuh (Default: Tercentang) / [ ] Tidak.
- Jika "Tidak" tercentang:
  * Disable semua sub-opsi di bawahnya.
  * Value yang dikirim ke database (wmfc_label_keterangan) adalah NULL.
- Jika "Butuh" tercentang:
  * Munculkan dua sub-checkbox: [ ] Default (Default: Tercentang) / [ ] Custom.
  * Jika "Custom" dipilih: Buka inputan text untuk mengisi manual.
  * Jika "Default" dipilih: Sistem otomatis men-generate teks dengan format:
    [Nama Label] + [Tipe Input] + [Kriteria] + [Validasi].

**3. STRUKTUR KALIMAT OTOMATIS (DEFAULT LABEL)**
Gunakan pola kalimat berikut untuk opsi "Default":
- Contoh A (Text + Uppercase + Validasi Kompleks):
  "tk kode bisa semua karakter dan bersifat auto huruf besar dengan harus diisi min 4 max 10 huruf dan harus unique"
- Contoh B (Number + Validasi Simple):
  "tk nim hanya bisa input angka dan harus diisi"
- Contoh C (Email + Unique):
  "tk email bisa semua karakter dengan harus diisi dan format email dengan value unique"

**4. LOGIKA LIST DISPLAY (wmfc_display_list)**
Fungsi: Menentukan apakah field label muncul di template 'list.blade.php'.
Mekanisme:
- Berupa Checkbox yang defaultnya TERCENTANG.
- Gunakan logika yang sama dengan kolom 'visible' yang sudah ada.

**5. LOGIKA SIZE DOKUMEN (wmfc_ukuran_max)**
Fungsi: Membatasi ukuran maksimal upload file dalam satuan MB.
Mekanisme:
- Berupa Inputan Angka (Numeric).
- Trigger: Default field ini adalah DISABLED.
- Field akan TERBUKA (Enabled) dan WAJIB DIISI (Required) hanya jika "Type Input" yang dipilih adalah 'file' atau 'gambar'.
- Contoh: Jika diinput 10, maka limit upload adalah 10MB.

**6. MAPPING TYPE INPUT (VARCHAR)**
Update pilihan pada dropdown "Type Input" jika tipe data kolom adalah VARCHAR:
- Tambahkan/Pastikan opsi berikut tersedia: [text, textarea, file, gambar].