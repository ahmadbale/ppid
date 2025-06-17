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

### Pesan Teks
- `POST /api/send-message` - Kirim pesan teks biasa
  ```json
  {
    "number": "628123456789",
    "message": "Hello World"
  }
  ```

### Pesan dengan Media  
- `POST /api/send-message-with-media` - Kirim pesan dengan file attachment
  ```json
  {
    "number": "628123456789", 
    "message": "Dokumen jawaban terlampir",
    "media": {
      "data": "base64-encoded-file-data",
      "mimetype": "application/pdf",
      "filename": "dokumen.pdf"
    }
  }
  ```

### Status & Info
- `GET /api/status` - Status koneksi WhatsApp
- `GET /qr` - QR Code untuk scan
- `GET /api/connected-phone` - Info nomor terhubung

## Konfigurasi

Edit file `.env`:
```
WA_PORT=3000
WA_TOKEN=ppid-polinema-2024
NODE_ENV=development
```