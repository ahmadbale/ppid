# WhatsApp API Server untuk PPID Polinema

## Instalasi

1. Pastikan Node.js sudah terinstall (versi 16+)
2. Jalankan `npm install` di folder ini
3. Jalankan `npm start` untuk memulai server

## Penggunaan

### Jalankan Server
```bash
npm start
```

### Akses QR Code
Buka browser: http://localhost:3000/qr

### Test API
```bash
# Dari folder root Laravel
php artisan whatsapp:test 08123456789
```

## Endpoints

- `GET /` - Info server
- `GET /qr` - QR Code untuk scan
- `GET /api/status` - Status koneksi
- `POST /api/send-message` - Kirim pesan

## Konfigurasi

Edit file `.env`:
```
WA_PORT=3000
WA_TOKEN=ppid-polinema-2024
NODE_ENV=development
```