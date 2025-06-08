const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const cors = require('cors');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.WA_PORT || 3000;
const AUTH_TOKEN = process.env.WA_TOKEN || 'ppid-polinema-2024';

// Middleware
app.use(express.json());
app.use(cors());

// Session storage path
const SESSION_PATH = path.join(__dirname, '.wwebjs_auth');

// Initialize WhatsApp client dengan session storage
const client = new Client({
    authStrategy: new LocalAuth({
        dataPath: SESSION_PATH
    }),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    }
});

let isReady = false;
let qrCodeData = '';

// WhatsApp client events
client.on('qr', (qr) => {
    qrCodeData = qr;
    console.log('\n===========================================');
    console.log('📱 SCAN QR CODE DENGAN WHATSAPP ANDA');
    console.log('===========================================');
    qrcode.generate(qr, { small: true });
    console.log('===========================================');
    console.log('🌐 Atau akses: http://localhost:' + PORT + '/qr');
    console.log('===========================================\n');
});

client.on('ready', () => {
    console.log('\n✅ WhatsApp Client SIAP DIGUNAKAN!');
    console.log('🚀 Server berjalan di: http://localhost:' + PORT);
    console.log('📋 Status: http://localhost:' + PORT + '/api/status\n');
    isReady = true;
    qrCodeData = '';
});

client.on('authenticated', () => {
    console.log('🔐 WhatsApp berhasil terautentikasi');
});

client.on('auth_failure', () => {
    console.error('❌ Gagal autentikasi WhatsApp');
});

client.on('disconnected', (reason) => {
    console.log('📱 WhatsApp terputus:', reason);
    isReady = false;
});

// Routes
app.get('/', (req, res) => {
    res.json({
        service: 'PPID Polinema WhatsApp API',
        status: isReady ? 'ready' : 'not_ready',
        version: '1.0.0',
        endpoints: [
            'GET /api/status - Cek status koneksi',
            'POST /api/send-message - Kirim pesan',
            'GET /qr - Tampilkan QR Code'
        ]
    });
});

app.get('/qr', (req, res) => {
    if (qrCodeData) {
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp QR Code - PPID Polinema</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .container { max-width: 500px; margin: 0 auto; }
                    .qr-code { margin: 20px 0; }
                </style>
                <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
            </head>
            <body>
                <div class="container">
                    <h1>🏛️ PPID Polinema</h1>
                    <h2>WhatsApp QR Code</h2>
                    <p>Scan QR Code berikut dengan WhatsApp Anda:</p>
                    <div id="qrcode" class="qr-code"></div>
                    <p><small>Halaman ini akan otomatis refresh setiap 10 detik</small></p>
                </div>
                <script>
                    QRCode.toCanvas(document.getElementById('qrcode'), '${qrCodeData}', function (error) {
                        if (error) console.error(error);
                    });
                    setTimeout(() => location.reload(), 10000);
                </script>
            </body>
            </html>
        `);
    } else if (isReady) {
        res.send(`
            <div style="text-align:center; padding:50px; font-family:Arial;">
                <h1>✅ WhatsApp Sudah Terhubung</h1>
                <p>WhatsApp API siap digunakan!</p>
                <a href="/api/status">Cek Status</a>
            </div>
        `);
    } else {
        res.send(`
            <div style="text-align:center; padding:50px; font-family:Arial;">
                <h1>🔄 Menunggu Koneksi WhatsApp...</h1>
                <p>Silakan tunggu beberapa saat</p>
                <script>setTimeout(() => location.reload(), 5000);</script>
            </div>
        `);
    }
});

app.get('/api/status', (req, res) => {
    // Autentikasi bearer token
    const token = req.headers.authorization?.replace('Bearer ', '');
    if (token !== AUTH_TOKEN) {
        return res.status(401).json({ 
            error: 'Token tidak valid',
            message: 'Authorization header diperlukan' 
        });
    }

    res.json({
        status: isReady ? 'ready' : 'not_ready',
        message: isReady ? 'WhatsApp client siap digunakan' : 'WhatsApp client belum siap',
        timestamp: new Date().toISOString(),
        qr_available: !!qrCodeData
    });
});

app.post('/api/send-message', async (req, res) => {
    try {
        // Autentikasi bearer token
        const token = req.headers.authorization?.replace('Bearer ', '');
        if (token !== AUTH_TOKEN) {
            return res.status(401).json({ 
                error: 'Token tidak valid',
                message: 'Authorization header diperlukan' 
            });
        }

        if (!isReady) {
            return res.status(503).json({ 
                error: 'WhatsApp client belum siap',
                message: 'Silakan scan QR code terlebih dahulu',
                qr_url: qrCodeData ? `http://localhost:${PORT}/qr` : null
            });
        }

        const { number, message } = req.body;

        if (!number || !message) {
            return res.status(400).json({ 
                error: 'Parameter tidak lengkap',
                message: 'Nomor dan pesan harus diisi',
                required: ['number', 'message']
            });
        }

        // Validasi format nomor
        const cleanNumber = number.replace(/[^0-9]/g, '');
        if (cleanNumber.length < 10) {
            return res.status(400).json({
                error: 'Format nomor tidak valid',
                message: 'Nomor telepon minimal 10 digit'
            });
        }

        // Format nomor dengan @c.us untuk chat
        const chatId = cleanNumber + '@c.us';
        
        // Cek apakah nomor terdaftar di WhatsApp
        const isRegistered = await client.isRegisteredUser(chatId);
        if (!isRegistered) {
            return res.status(404).json({
                error: 'Nomor tidak terdaftar',
                message: 'Nomor WhatsApp tidak ditemukan atau tidak aktif'
            });
        }
        
        // Kirim pesan
        const sentMessage = await client.sendMessage(chatId, message);

        console.log(`✅ Pesan berhasil dikirim ke ${number}`);
        console.log(`📝 Pesan: ${message.substring(0, 50)}${message.length > 50 ? '...' : ''}`);

        res.json({
            success: true,
            message: 'Pesan berhasil dikirim',
            to: number,
            message_id: sentMessage.id._serialized,
            timestamp: new Date().toISOString()
        });

    } catch (error) {
        console.error('❌ Error mengirim pesan:', error);
        
        let errorMessage = 'Gagal mengirim pesan';
        if (error.message.includes('Rate limit')) {
            errorMessage = 'Terlalu banyak pesan dikirim, coba lagi nanti';
        } else if (error.message.includes('disconnected')) {
            errorMessage = 'WhatsApp terputus, silakan restart server';
            isReady = false;
        }

        res.status(500).json({
            error: errorMessage,
            message: error.message,
            timestamp: new Date().toISOString()
        });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        service: 'PPID WhatsApp API',
        uptime: process.uptime(),
        whatsapp_ready: isReady
    });
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error('Server error:', err);
    res.status(500).json({
        error: 'Internal server error',
        message: process.env.NODE_ENV === 'development' ? err.message : 'Something went wrong'
    });
});

// 404 handler
app.use((req, res) => {
    res.status(404).json({
        error: 'Endpoint not found',
        message: 'API endpoint yang diminta tidak ditemukan'
    });
});

// Start server
app.listen(PORT, () => {
    console.log('\n🚀========================================');
    console.log('🏛️  PPID POLINEMA WHATSAPP API SERVER');
    console.log('========================================');
    console.log(`🌐 Server: http://localhost:${PORT}`);
    console.log(`📱 QR Code: http://localhost:${PORT}/qr`);
    console.log(`📋 Status: http://localhost:${PORT}/api/status`);
    console.log('========================================');
    console.log('🔄 Menginisialisasi WhatsApp client...\n');
    
    client.initialize().catch(err => {
        console.error('❌ Gagal inisialisasi WhatsApp client:', err);
    });
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\n🔄 Menutup WhatsApp API server...');
    try {
        await client.destroy();
        console.log('✅ WhatsApp client berhasil ditutup');
    } catch (error) {
        console.error('❌ Error saat menutup client:', error);
    }
    process.exit(0);
});

process.on('uncaughtException', (error) => {
    console.error('❌ Uncaught Exception:', error);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('❌ Unhandled Rejection at:', promise, 'reason:', reason);
});