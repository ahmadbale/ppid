# 🎯 QUICK REFERENCE: Route Dinamis dengan Query Parameter

## 📌 Pattern URL Baru

### Menu Utama (Default)
```
GET  /editData/{id}           → edit menu utama
POST /updateData/{id}         → update menu utama
GET  /detailData/{id}         → detail menu utama
GET  /deleteData/{id}         → konfirmasi delete menu utama
DELETE /deleteData/{id}       → execute delete menu utama
```

### Sub Menu Utama (Query: `?type=submenu_utama`)
```
GET  /editData/{id}?type=submenu_utama     → edit sub menu utama
POST /updateData/{id}?type=submenu_utama   → update sub menu utama
GET  /detailData/{id}?type=submenu_utama   → detail sub menu utama
DELETE /deleteData/{id}?type=submenu_utama → delete sub menu utama
```

### Sub Menu (Query: `?type=submenu`)
```
GET  /editData/{id}?type=submenu     → edit sub menu
POST /updateData/{id}?type=submenu   → update sub menu
GET  /detailData/{id}?type=submenu   → detail sub menu
DELETE /deleteData/{id}?type=submenu → delete sub menu
```

---

## 🔧 JavaScript Usage

### Edit Functions
```javascript
// Menu Utama
modalAction(url + '/editData/123');

// Sub Menu Utama
modalAction(url + '/editData/123?type=submenu_utama');
// atau
editSubMenuUtama(123);

// Sub Menu
modalAction(url + '/editData/123?type=submenu');
// atau
editSubMenu(123);
```

### Delete Functions
```javascript
// Menu Utama
modalAction(url + '/deleteData/123');

// Sub Menu Utama
deleteSubMenuUtama(123);

// Sub Menu
deleteSubMenu(123);
```

---

## 📝 Blade Template Usage

### Form Action URLs

**Menu Utama:**
```blade
<form action="{{ url($url . '/updateData/' . $id) }}" method="POST">
```

**Sub Menu Utama:**
```blade
<form action="{{ url($url . '/updateData/' . $id . '?type=submenu_utama') }}" method="POST">
```

**Sub Menu:**
```blade
<form action="{{ url($url . '/updateData/' . $id . '?type=submenu') }}" method="POST">
```

### AJAX Delete

**Sub Menu Utama:**
```javascript
$.ajax({
    url: '{{ url($url . "/deleteData/" . $id . "?type=submenu_utama") }}',
    type: 'DELETE',
    data: { _token: $('meta[name="csrf-token"]').attr('content') }
});
```

**Sub Menu:**
```javascript
$.ajax({
    url: '{{ url($url . "/deleteData/" . $id . "?type=submenu") }}',
    type: 'DELETE',
    data: { _token: $('meta[name="csrf-token"]').attr('content') }
});
```

---

## 🎯 Controller Method Flow

```
┌────────────────────────────────────┐
│ REQUEST: /editData/123?type=X      │
└─────────────┬──────────────────────┘
              │
              ▼
┌────────────────────────────────────┐
│ public function editData($id)      │
│ {                                  │
│   $type = request()->query('type', 'menu');│
│   switch ($type) {                 │
│     case 'submenu_utama':          │
│       return $this->               │
│         editSubMenuUtamaInternal($id);│
│     case 'submenu':                │
│       return $this->               │
│         editSubMenuInternal($id);  │
│     default:                       │
│       return $this->               │
│         editMenuUtamaInternal($id);│
│   }                                │
│ }                                  │
└────────────────────────────────────┘
```

---

## 🚀 Quick Commands

### Test Routes
```bash
# List all routes untuk menu ini
php artisan route:list | grep set-informasi-publik-dinamis-tabel

# Expected output (8 routes only):
# GET     /set-informasi-publik-dinamis-tabel
# GET     /set-informasi-publik-dinamis-tabel/getData
# GET     /set-informasi-publik-dinamis-tabel/addData
# POST    /set-informasi-publik-dinamis-tabel/createData
# GET     /set-informasi-publik-dinamis-tabel/editData/{id}
# POST    /set-informasi-publik-dinamis-tabel/updateData/{id}
# GET     /set-informasi-publik-dinamis-tabel/detailData/{id}
# GET|DELETE /set-informasi-publik-dinamis-tabel/deleteData/{id}
```

### Clear Cache
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## ✅ Migration Checklist

Jika ada controller lain yang perlu direfaktor:

1. **Controller:**
   - [ ] Ubah 4 public methods jadi universal (edit, update, detail, delete)
   - [ ] Tambah switch case dengan `request()->query('type', 'default')`
   - [ ] Pindahkan logic ke private methods
   - [ ] Tambahkan docblock

2. **Routes:**
   - [ ] Hapus routes untuk sub-operations
   - [ ] Tinggalkan 8 routes standar
   - [ ] Tambah komentar dokumentasi

3. **Views:**
   - [ ] Update form action URLs
   - [ ] Tambah query parameter
   - [ ] Update JavaScript functions

4. **Testing:**
   - [ ] Test semua fungsi dengan query parameter
   - [ ] Test backward compatibility
   - [ ] Test permission middleware

---

## 📚 References

- 📄 **Full Documentation:** `REFAKTOR-SetIpDinamisTabel.md`
- ✅ **Testing Checklist:** `TESTING-CHECKLIST-SetIpDinamisTabel.md`
- 📖 **Route Instructions:** `.github/instructions/route-dinamis.instructions.md`

---

## 🆘 Troubleshooting

### Issue: Route tidak ditemukan
```
Solution: php artisan route:clear && php artisan cache:clear
```

### Issue: Query parameter tidak ter-detect
```php
// Debug di controller:
dd(request()->query('type'));
dd(request()->all());
```

### Issue: Modal tidak load
```javascript
// Check di console:
console.log('URL:', url + '/editData/' + id + '?type=submenu');
```

### Issue: Form submit error
```javascript
// Check form action:
console.log($('#formID').attr('action'));
```

---

**Last Updated:** 4 Februari 2026  
**Version:** 1.0  
**Status:** ✅ Production Ready
