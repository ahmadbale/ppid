# 📚 Sistem Informasi PPID Polinema

## 🚀 Project Pengembangan Layanan In## 🔧 Setup Instalasi

### Persyaratan Sistem
- PHP 8.2.12 atau lebih tinggi
- Node.js 18+ dan NPM
- MySQL 8.0+
- Composer

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/adeliashahahahaha/PPID-polinema.git
   cd PPID-polinema
   ```

2. **Jalankan script setup (Windows)**
   ```bash
   setup.bat
   ```

3. **Atau setup manual:**
   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   php artisan config:clear
   php artisan module:enable User
   php artisan module:enable Sisfo
   npm install
   npm run build
   php artisan migrate
   php artisan serve
   ```

4. **Setup WhatsApp Service**
   ```bash
   start-whatsapp.bat
   ```

---

## ⚠️ Troubleshooting

### Error "No hint path defined for [user]"

Jika terjadi error ini setelah download/clone project:

**Solusi Cepat:**
```bash
# Jalankan quick fix
quick-fix.bat
```

**Solusi Manual:**
```bash
php artisan module:enable User
php artisan module:enable Sisfo
php artisan config:clear
php artisan module:optimize
```

**Lihat panduan lengkap:** [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📁 Struktur Projectmasi Publik

Selamat datang di repository **Sistem Informasi PPID Polinema**, sebuah sistem informasi modern untuk mendukung layanan keterbukaan informasi publik di **Politeknik Negeri Malang**.  
Proyek ini bertujuan untuk memberikan akses informasi yang lebih **mudah**, **transparan**, dan **efisien** kepada masyarakat umum, sesuai dengan prinsip keterbukaan informasi publik.

---

## 👨‍💻 Tim Pengembang

| Nama | Role |
|:----|:-----|
| 🎨 Adelia Syaharani Hermawan | Frontend Developer |
| 🎨 Ahmad Iqbal Firmansyah | Frontend Developer |
| 🛠️ M. Isroqi Gelby Firmansyah | Backend Developer |
| 🛠️ Solikhin | Backend Developer |

---

## 🎯 Tujuan Proyek

Membangun sistem informasi yang mempermudah masyarakat dalam:

- Mengakses berbagai jenis informasi publik.
- Mengajukan permohonan informasi.
- Menyampaikan keberatan atau pengaduan terkait informasi publik.
- Memantau transparansi dan akuntabilitas layanan informasi publik di lingkungan Polinema.

---

## ✨ Fitur Utama

### 📄 Permohonan Informasi Publik
Masyarakat dapat dengan mudah mengajukan permintaan akses terhadap informasi yang dibutuhkan.

### 📝 Pengajuan Keberatan
Pengguna dapat mengajukan keberatan resmi jika permohonan informasi ditolak atau tidak ditindaklanjuti.

### 📢 Pengaduan WBS dan Masyarakat
Menyediakan kanal bagi masyarakat dan whistleblower untuk menyampaikan pengaduan terkait pelanggaran keterbukaan informasi.

### 📂 Download Dokumen Publik
Pengguna dapat mengakses dan mengunduh berbagai dokumen publik yang tersedia.

### 🔐 Login & Registrasi
Fitur login aman untuk pengguna umum maupun admin, dilengkapi dengan sistem registrasi mandiri.

### 📊 Dashboard Admin & Sistem Informasi
Admin dapat memonitor dan mengelola data permohonan, keberatan, pengaduan, serta informasi publik melalui dashboard.

### 🗂️ Kategori Informasi

- **Informasi Berkala**  
  Informasi yang wajib disediakan dan diperbaharui secara rutin.

- **Informasi Setiap Saat**  
  Informasi yang tersedia setiap saat dan dapat diakses berdasarkan permintaan.

- **Informasi Serta Merta**  
  Informasi yang harus diumumkan segera karena berkaitan dengan keselamatan atau kepentingan publik.

- **Informasi Dikecualikan**  
  Informasi yang tidak dapat diakses publik sesuai ketentuan perundang-undangan.

---

## 🛠️ Teknologi yang Digunakan

- **Laravel** — Backend development.
- **Bootstrap** — Frontend styling.
- **Alpine.js** — Frontend interaktivitas ringan.
- **MySQL** — Database management.

---

## 🔧 Tools Pendukung

- **Figma** — Desain dan prototyping UI/UX.
- **Postman** — API testing.
- **GitHub** — Version control dan kolaborasi.
- **Google Drive & Google Docs** — Dokumentasi dan penyimpanan cloud.
- **Notion** — Manajemen tugas dan koordinasi tim.

---

## 📬 Catatan

Sistem ini masih dalam tahap pengembangan aktif.  
Kontribusi, ide, dan masukan dari pengguna sangat kami apresiasi untuk pengembangan ke depannya! 🚀
