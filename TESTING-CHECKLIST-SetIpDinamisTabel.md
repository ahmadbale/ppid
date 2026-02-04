# ✅ Testing Checklist - SetIpDinamisTabel Refactor

## 📋 Overview
Testing untuk memastikan refaktor dari 20 routes → 8 routes dengan query parameter berfungsi dengan baik.

---

## 🧪 Test Environment Setup

### 1. Prerequisites
- [ ] Database sudah ter-migrate
- [ ] Seeder sudah dijalankan (ada data test)
- [ ] User test dengan role admin sudah ada
- [ ] Browser console terbuka untuk debugging

### 2. Test Data Required
```sql
-- Pastikan ada data test:
SELECT * FROM ip_dinamis_tabel;              -- Kategori IP
SELECT * FROM ip_menu_utama;                 -- Menu Utama
SELECT * FROM ip_sub_menu_utama;             -- Sub Menu Utama
SELECT * FROM ip_sub_menu;                   -- Sub Menu
```

---

## 🔍 TESTING: Menu Utama

### Test 1.1: View List Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel`
- **Method:** GET
- **Expected:**
  - [ ] Menampilkan list menu utama
  - [ ] Tombol "Tambah Data" muncul
  - [ ] Filter kategori berfungsi
  - [ ] Search berfungsi
  - [ ] Pagination berfungsi (jika data > 10)

### Test 1.2: Tambah Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel/addData`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol "Tambah Data"
  2. [ ] Modal terbuka dengan form
  3. [ ] Pilih kategori
  4. [ ] Isi jumlah menu utama (1-5)
  5. [ ] Isi detail untuk setiap menu
  6. [ ] Submit form
- **Expected:**
  - [ ] Form validation berfungsi
  - [ ] Data tersimpan ke database
  - [ ] Redirect ke list dengan success message
  - [ ] Data baru muncul di list

### Test 1.3: Edit Menu Utama (DEFAULT - tanpa query parameter)
- **URL:** `/set-informasi-publik-dinamis-tabel/editData/1`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol edit (kuning) pada menu utama
  2. [ ] Modal terbuka dengan form edit
  3. [ ] Data menu utama ter-load dengan benar
  4. [ ] Ubah nama menu utama
  5. [ ] Submit form
- **Expected:**
  - [ ] Form ter-populate dengan data existing
  - [ ] Update berhasil
  - [ ] Success message muncul
  - [ ] Data ter-update di list

### Test 1.4: Edit Menu Utama (EXPLICIT type=menu)
- **URL:** `/set-informasi-publik-dinamis-tabel/editData/1?type=menu`
- **Method:** GET (modal)
- **Expected:**
  - [ ] Behavior sama dengan test 1.3
  - [ ] Tidak ada error di console

### Test 1.5: Update Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel/updateData/1`
- **Method:** POST
- **Steps:**
  1. [ ] Dari form edit (test 1.3), ubah data
  2. [ ] Submit form
- **Expected:**
  - [ ] Validation berfungsi
  - [ ] Data ter-update
  - [ ] JSON response success
  - [ ] Modal close
  - [ ] List ter-refresh

### Test 1.6: Detail Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel/detailData/1`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol detail (biru) pada menu utama
  2. [ ] Modal terbuka dengan detail data
- **Expected:**
  - [ ] Semua data ditampilkan dengan benar
  - [ ] Hierarki (sub menu utama & sub menu) ditampilkan
  - [ ] Statistik ditampilkan
  - [ ] Dokumen link berfungsi (jika ada)

### Test 1.7: Delete Menu Utama (GET confirmation)
- **URL:** `/set-informasi-publik-dinamis-tabel/deleteData/1`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol delete (merah) pada menu utama
  2. [ ] Modal konfirmasi terbuka
- **Expected:**
  - [ ] Menampilkan detail yang akan dihapus
  - [ ] Menampilkan warning jika ada sub menu
  - [ ] Statistik total data yang akan dihapus

### Test 1.8: Delete Menu Utama (DELETE action)
- **URL:** `/set-informasi-publik-dinamis-tabel/deleteData/1`
- **Method:** DELETE
- **Steps:**
  1. [ ] Dari modal konfirmasi (test 1.7)
  2. [ ] Klik tombol "Ya, Hapus"
- **Expected:**
  - [ ] Soft delete berhasil (isDeleted = 1)
  - [ ] Success message muncul
  - [ ] Data hilang dari list
  - [ ] Sub menu & sub menu utama ikut terhapus

---

## 🔍 TESTING: Sub Menu Utama

### Test 2.1: Edit Sub Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel/editData/1?type=submenu_utama`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Di tree view, expand menu utama
  2. [ ] Klik tombol edit (kuning) pada sub menu utama
  3. [ ] Verify URL menggunakan `?type=submenu_utama`
  4. [ ] Modal terbuka dengan form edit sub menu utama
  5. [ ] Ubah nama sub menu utama
  6. [ ] Submit form
- **Expected:**
  - [ ] URL correct: `/editData/[id]?type=submenu_utama`
  - [ ] Form ter-populate dengan data sub menu utama
  - [ ] Controller memanggil `editSubMenuUtamaInternal()`
  - [ ] Update berhasil
  - [ ] Success message muncul

### Test 2.2: Update Sub Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel/updateData/1?type=submenu_utama`
- **Method:** POST
- **Steps:**
  1. [ ] Dari form edit sub menu utama
  2. [ ] Ubah data (nama, dokumen, dll)
  3. [ ] Submit form
- **Expected:**
  - [ ] Validation berfungsi
  - [ ] Controller memanggil `updateSubMenuUtamaInternal()`
  - [ ] Data ter-update di database
  - [ ] JSON response success
  - [ ] Modal close
  - [ ] Tree view ter-refresh

### Test 2.3: Detail Sub Menu Utama
- **URL:** `/set-informasi-publik-dinamis-tabel/detailData/1?type=submenu_utama`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol detail (biru) pada sub menu utama
  2. [ ] Verify URL menggunakan `?type=submenu_utama`
- **Expected:**
  - [ ] Modal menampilkan detail sub menu utama
  - [ ] Hierarki menu ditampilkan (breadcrumb)
  - [ ] List sub menu (children) ditampilkan
  - [ ] Dokumen link berfungsi

### Test 2.4: Delete Sub Menu Utama (GET confirmation)
- **URL:** `/set-informasi-publik-dinamis-tabel/deleteData/1?type=submenu_utama`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol delete (merah) pada sub menu utama
  2. [ ] Verify URL menggunakan `?type=submenu_utama`
- **Expected:**
  - [ ] Modal konfirmasi terbuka
  - [ ] Menampilkan detail sub menu utama
  - [ ] Warning jika ada sub menu (children)
  - [ ] Tombol hapus disabled jika ada children

### Test 2.5: Delete Sub Menu Utama (DELETE action)
- **URL:** `/set-informasi-publik-dinamis-tabel/deleteData/1?type=submenu_utama`
- **Method:** DELETE
- **Steps:**
  1. [ ] Pilih sub menu utama yang TIDAK punya sub menu
  2. [ ] Klik delete → konfirmasi → hapus
- **Expected:**
  - [ ] AJAX URL correct: `/deleteData/[id]?type=submenu_utama`
  - [ ] Controller memanggil `deleteSubMenuUtamaInternal()`
  - [ ] Soft delete berhasil
  - [ ] Success message muncul
  - [ ] Data hilang dari tree view

---

## 🔍 TESTING: Sub Menu

### Test 3.1: Edit Sub Menu
- **URL:** `/set-informasi-publik-dinamis-tabel/editData/1?type=submenu`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Di tree view, expand hingga level sub menu
  2. [ ] Klik tombol edit (kuning) pada sub menu
  3. [ ] Verify URL menggunakan `?type=submenu`
  4. [ ] Modal terbuka dengan form edit sub menu
  5. [ ] Ubah nama & dokumen
  6. [ ] Submit form
- **Expected:**
  - [ ] URL correct: `/editData/[id]?type=submenu`
  - [ ] Form ter-populate dengan data sub menu
  - [ ] Controller memanggil `editSubMenuInternal()`
  - [ ] Breadcrumb hierarki ditampilkan
  - [ ] Update berhasil

### Test 3.2: Update Sub Menu
- **URL:** `/set-informasi-publik-dinamis-tabel/updateData/1?type=submenu`
- **Method:** POST
- **Steps:**
  1. [ ] Dari form edit sub menu
  2. [ ] Ubah nama dan/atau upload dokumen baru
  3. [ ] Submit form
- **Expected:**
  - [ ] Form action URL correct: `/updateData/[id]?type=submenu`
  - [ ] Controller memanggil `updateSubMenuInternal()`
  - [ ] Validation berfungsi (max file size, PDF only)
  - [ ] Data ter-update
  - [ ] File lama ter-replace (jika upload baru)

### Test 3.3: Detail Sub Menu
- **URL:** `/set-informasi-publik-dinamis-tabel/detailData/1?type=submenu`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol detail (biru) pada sub menu
  2. [ ] Verify URL menggunakan `?type=submenu`
- **Expected:**
  - [ ] Modal menampilkan detail sub menu
  - [ ] Hierarki lengkap (Menu Utama > Sub Menu Utama > Sub Menu)
  - [ ] Breadcrumb navigation ditampilkan
  - [ ] Dokumen link berfungsi

### Test 3.4: Delete Sub Menu (GET confirmation)
- **URL:** `/set-informasi-publik-dinamis-tabel/deleteData/1?type=submenu`
- **Method:** GET (modal)
- **Steps:**
  1. [ ] Klik tombol delete (merah) pada sub menu
  2. [ ] Verify URL menggunakan `?type=submenu`
- **Expected:**
  - [ ] Modal konfirmasi terbuka
  - [ ] Menampilkan detail sub menu
  - [ ] Menampilkan hierarki parent
  - [ ] Warning message muncul

### Test 3.5: Delete Sub Menu (DELETE action)
- **URL:** `/set-informasi-publik-dinamis-tabel/deleteData/1?type=submenu`
- **Method:** DELETE
- **Steps:**
  1. [ ] Dari modal konfirmasi
  2. [ ] Klik tombol "Ya, Hapus Sub Menu"
- **Expected:**
  - [ ] AJAX URL correct: `/deleteData/[id]?type=submenu`
  - [ ] Controller memanggil `deleteSubMenuInternal()`
  - [ ] Soft delete berhasil
  - [ ] File dokumen tetap ada (soft delete)
  - [ ] Success message muncul
  - [ ] Data hilang dari tree view

---

## 🔍 EDGE CASES & ERROR HANDLING

### Test 4.1: Invalid Query Parameter
- **URL:** `/set-informasi-publik-dinamis-tabel/editData/1?type=invalid`
- **Expected:**
  - [ ] Default ke type='menu' (karena switch default)
  - [ ] Form edit menu utama yang muncul
  - [ ] Tidak ada error 500

### Test 4.2: Missing ID
- **URL:** `/set-informasi-publik-dinamis-tabel/editData/999999`
- **Expected:**
  - [ ] Error 404 atau "Data tidak ditemukan"
  - [ ] Tidak crash

### Test 4.3: Permission Check
- **Test dengan user non-admin:**
  1. [ ] Login sebagai user biasa
  2. [ ] Akses edit/delete
- **Expected:**
  - [ ] Permission middleware block request
  - [ ] 403 Forbidden atau redirect
  - [ ] Tombol edit/delete tidak muncul di UI

### Test 4.4: Concurrent Updates
- **Steps:**
  1. [ ] Buka 2 tab browser
  2. [ ] Edit data yang sama di kedua tab
  3. [ ] Submit di tab pertama
  4. [ ] Submit di tab kedua
- **Expected:**
  - [ ] Data dari submission terakhir yang tersimpan
  - [ ] Tidak ada data corruption

### Test 4.5: File Upload Validation
- **Test upload file non-PDF:**
  1. [ ] Edit sub menu utama/sub menu
  2. [ ] Upload file .jpg atau .txt
  3. [ ] Submit
- **Expected:**
  - [ ] Validation error: "File harus PDF"
  - [ ] Form tidak ter-submit

### Test 4.6: Browser Backward/Forward
- **Steps:**
  1. [ ] Edit menu utama → modal buka
  2. [ ] Klik browser back button
- **Expected:**
  - [ ] Modal close
  - [ ] Tidak ada memory leak
  - [ ] Page still functional

---

## 🔍 TESTING: JavaScript Functions

### Test 5.1: modalAction() Function
```javascript
// Test di browser console:
modalAction(setIpDinamisTabelUrl + '/editData/1?type=submenu_utama');
```
- **Expected:**
  - [ ] Modal terbuka
  - [ ] Loading spinner muncul
  - [ ] Form ter-load dengan benar
  - [ ] Tidak ada error di console

### Test 5.2: editSubMenuUtama() Function
```javascript
// Test di browser console:
editSubMenuUtama(1);
```
- **Expected:**
  - [ ] Call modalAction dengan URL correct
  - [ ] Query parameter `?type=submenu_utama` ada
  - [ ] Modal terbuka

### Test 5.3: editSubMenu() Function
```javascript
// Test di browser console:
editSubMenu(1);
```
- **Expected:**
  - [ ] Call modalAction dengan URL correct
  - [ ] Query parameter `?type=submenu` ada
  - [ ] Modal terbuka

### Test 5.4: deleteSubMenuUtama() Function
```javascript
// Test di browser console:
deleteSubMenuUtama(1);
```
- **Expected:**
  - [ ] AJAX request ke URL correct
  - [ ] Query parameter ada
  - [ ] Confirmation modal muncul

### Test 5.5: Tree View Toggle
- **Steps:**
  1. [ ] Klik icon chevron pada menu utama
  2. [ ] Sub menu utama expand/collapse
  3. [ ] Klik icon chevron pada sub menu utama
  4. [ ] Sub menu expand/collapse
- **Expected:**
  - [ ] Animation smooth
  - [ ] Icon berubah (right ↔ down)
  - [ ] Tidak ada error di console

---

## 🔍 REGRESSION TESTING

### Test 6.1: Existing Features Still Work
- [ ] Filter by kategori berfungsi
- [ ] Search berfungsi
- [ ] Pagination berfungsi
- [ ] Sort order berfungsi
- [ ] Expand all / Collapse all berfungsi

### Test 6.2: Route Compatibility
- **Test route lama (harusnya tidak ada):**
  ```
  /set-informasi-publik-dinamis-tabel/editSubMenuUtama/1    → 404
  /set-informasi-publik-dinamis-tabel/updateSubMenuUtama/1  → 404
  /set-informasi-publik-dinamis-tabel/editSubMenu/1         → 404
  /set-informasi-publik-dinamis-tabel/updateSubMenu/1       → 404
  ```
- **Expected:**
  - [ ] Route tidak ditemukan (404)
  - [ ] Error handling berfungsi

### Test 6.3: Database Integrity
```sql
-- After all tests, check:
SELECT * FROM ip_menu_utama WHERE isDeleted = 0;
SELECT * FROM ip_sub_menu_utama WHERE isDeleted = 0;
SELECT * FROM ip_sub_menu WHERE isDeleted = 0;
```
- **Expected:**
  - [ ] Tidak ada data orphan
  - [ ] Relasi foreign key intact
  - [ ] Soft delete flag consistent

---

## 📊 PERFORMANCE TESTING

### Test 7.1: Page Load Time
- [ ] Halaman list load < 2 detik
- [ ] Modal edit load < 1 detik
- [ ] Tree view render < 1 detik (untuk 100 items)

### Test 7.2: AJAX Response Time
- [ ] GET request < 500ms
- [ ] POST request < 1 detik
- [ ] DELETE request < 500ms

### Test 7.3: Memory Usage
- [ ] Tidak ada memory leak setelah buka/tutup modal 10x
- [ ] Browser memory usage stabil

---

## 🐛 BUG TRACKING

| Bug ID | Description | Severity | Status | Fixed |
|--------|-------------|----------|--------|-------|
| BUG-001 | ... | High | Open | ❌ |
| BUG-002 | ... | Medium | Fixed | ✅ |

---

## ✅ FINAL CHECKLIST

### Pre-Deployment
- [ ] Semua test case passed
- [ ] Tidak ada error di console
- [ ] Tidak ada warning di console
- [ ] Permission check berfungsi
- [ ] Database integrity terjaga

### Post-Deployment
- [ ] Monitoring error logs
- [ ] User feedback
- [ ] Performance metrics

---

## 📝 NOTES

### Browser Compatibility
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Edge (latest)
- [ ] Safari (latest) - jika applicable

### Testing Environment
- **OS:** Windows/Linux/Mac
- **PHP Version:** 8.x
- **Laravel Version:** 10.x
- **Database:** MySQL/PostgreSQL
- **Browser:** Chrome/Firefox

---

**Testing Status:** 🟡 IN PROGRESS / 🟢 PASSED / 🔴 FAILED

**Tested By:** _________________  
**Date:** _________________  
**Sign Off:** _________________
