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

// Ensure session directory exists
if (!fs.existsSync(SESSION_PATH)) {
    fs.mkdirSync(SESSION_PATH, { recursive: true });
}

// Initialize WhatsApp client dengan konfigurasi yang disederhanakan
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
        ],
        timeout: 60000
    }
});

let isReady = false;
let isAuthenticated = false;
let qrCodeData = '';
let clientInitialized = false;
let initRetryCount = 0;
const maxRetries = 3;

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
    isAuthenticated = true;
    qrCodeData = '';
    initRetryCount = 0;
});

client.on('authenticated', () => {
    console.log('🔐 WhatsApp berhasil terautentikasi');
    isAuthenticated = true;
    qrCodeData = '';
});

client.on('auth_failure', (msg) => {
    console.error('❌ Gagal autentikasi WhatsApp:', msg);
    isAuthenticated = false;
    isReady = false;
    
    // Reset dan coba lagi
    setTimeout(() => {
        if (initRetryCount < maxRetries) {
            console.log('🔄 Mencoba autentikasi ulang...');
            restartClient();
        }
    }, 5000);
});

client.on('disconnected', (reason) => {
    console.log('📱 WhatsApp terputus:', reason);
    isReady = false;
    isAuthenticated = false;
    qrCodeData = '';
    clientInitialized = false;
    
    // Try to reinitialize after disconnection
    setTimeout(() => {
        if (initRetryCount < maxRetries) {
            console.log('🔄 Mencoba koneksi ulang setelah disconnect...');
            restartClient();
        }
    }, 5000);
});

client.on('loading_screen', (percent, message) => {
    console.log('🔄 Loading WhatsApp:', percent + '%', message);
});

// Function to restart client
async function restartClient() {
    try {
        initRetryCount++;
        console.log(`🔄 Restart attempt ${initRetryCount}/${maxRetries}`);
        
        if (clientInitialized) {
            await client.destroy();
            clientInitialized = false;
        }
        
        // Tunggu sebentar sebelum restart
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        initializeClient();
    } catch (error) {
        console.error('❌ Error saat restart client:', error);
        clientInitialized = false;
    }
}

// Function to initialize client
function initializeClient() {
    if (clientInitialized) {
        console.log('⚠️ Client sudah diinisialisasi');
        return;
    }
    
    clientInitialized = true;
    console.log('🔄 Menginisialisasi WhatsApp client...');
    
    client.initialize().catch(err => {
        console.error('❌ Gagal inisialisasi WhatsApp client:', err);
        clientInitialized = false;
        
        // Retry dengan delay yang lebih lama
        if (initRetryCount < maxRetries) {
            setTimeout(() => {
                console.log(`🔄 Retry ${initRetryCount + 1}/${maxRetries} dalam 15 detik...`);
                restartClient();
            }, 15000);
        } else {
            console.error('❌ Gagal inisialisasi setelah beberapa kali percobaan');
            console.error('💡 Coba restart server atau reset session');
        }
    });
}

// Routes
app.get('/', (req, res) => {
    res.json({
        service: 'PPID Polinema WhatsApp API',
        status: isReady ? 'siap' : 'belum_siap',
        authenticated: isAuthenticated,
        version: '1.0.0',
        qr_available: !!qrCodeData,
        client_initialized: clientInitialized,
        retry_count: initRetryCount,
        max_retries: maxRetries,
        endpoints: [
            'GET /api/status - Cek status koneksi',
            'POST /api/send-message - Kirim pesan',
            'GET /qr - Tampilkan QR Code'
        ]
    });
});

// API endpoint untuk mendapatkan QR code sebagai base64
app.get('/api/qr-image', async (req, res) => {
    try {
        if (!qrCodeData) {
            return res.status(404).json({
                success: false,
                message: 'QR Code belum tersedia'
            });
        }

        // Import qrcode library untuk generate image
        const QRCode = require('qrcode');
        
        // Generate QR code sebagai base64
        const qrImage = await QRCode.toDataURL(qrCodeData, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        });

        res.json({
            success: true,
            qr_image: qrImage,
            qr_text: qrCodeData
        });

    } catch (error) {
        console.error('Error generating QR image:', error);
        res.status(500).json({
            success: false,
            message: 'Gagal generate QR code image'
        });
    }
});

app.get('/qr', (req, res) => {
    if (qrCodeData) {
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp QR Code - PPID Polinema</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                        text-align: center; 
                        padding: 30px; 
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        min-height: 100vh;
                        margin: 0;
                        color: white;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 0 auto; 
                        background: white;
                        border-radius: 15px;
                        padding: 30px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        color: #333;
                    }
                    .header {
                        margin-bottom: 20px;
                    }
                    .logo {
                        font-size: 2em;
                        margin-bottom: 10px;
                    }
                    .qr-code { 
                        margin: 20px 0; 
                        padding: 20px;
                        background: #f8f9fa;
                        border-radius: 10px;
                        border: 2px dashed #007bff;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 350px;
                        flex-direction: column;
                    }
                    .instructions {
                        background: #e3f2fd;
                        padding: 15px;
                        border-radius: 8px;
                        margin: 20px 0;
                        text-align: left;
                    }
                    .instructions ol {
                        margin: 10px 0;
                        padding-left: 20px;
                    }
                    .refresh-info {
                        font-size: 0.9em;
                        color: #666;
                        margin-top: 15px;
                    }
                    .status {
                        display: inline-block;
                        background: #28a745;
                        color: white;
                        padding: 5px 15px;
                        border-radius: 20px;
                        font-size: 0.9em;
                        margin-bottom: 15px;
                    }
                    .qr-image {
                        border: 2px solid #ddd;
                        border-radius: 8px;
                        padding: 10px;
                        background: white;
                        max-width: 300px;
                        max-height: 300px;
                        margin: 10px auto;
                    }
                    .qr-text {
                        font-family: monospace;
                        font-size: 10px;
                        line-height: 1.2;
                        color: #333;
                        background: white;
                        padding: 10px;
                        border-radius: 5px;
                        word-break: break-all;
                        max-width: 300px;
                        margin: 10px auto;
                        max-height: 100px;
                        overflow-y: auto;
                        border: 1px solid #ddd;
                    }
                    .btn {
                        display: inline-block;
                        background: #007bff;
                        color: white;
                        padding: 8px 16px;
                        text-decoration: none;
                        border-radius: 5px;
                        margin: 5px;
                        font-size: 14px;
                        cursor: pointer;
                        border: none;
                    }
                    .btn:hover {
                        background: #0056b3;
                    }
                    .loading {
                        color: #666;
                        font-style: italic;
                    }
                    .error {
                        color: #dc3545;
                        font-weight: bold;
                    }
                    .success {
                        color: #28a745;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <div class="logo">🏛️ PPID Polinema</div>
                        <h2>WhatsApp QR Code</h2>
                        <div class="status">✅ QR Code Tersedia</div>
                    </div>
                    
                    <div class="instructions">
                        <strong>📱 Langkah-langkah Scan:</strong>
                        <ol>
                            <li>Buka WhatsApp di HP Anda</li>
                            <li>Tap menu (3 titik) → <strong>WhatsApp Web</strong></li>
                            <li>Tap <strong>"Scan QR Code"</strong></li>
                            <li>Arahkan kamera ke QR code di bawah</li>
                        </ol>
                    </div>
                    
                    <p><strong>Scan QR Code berikut dengan WhatsApp Anda:</strong></p>
                    <div class="qr-code">
                        <div id="qr-status" class="loading">Memuat QR Code...</div>
                        <img id="qr-image" class="qr-image" style="display: none;" />
                        <div id="qr-text-container" style="display: none;">
                            <p style="color: #666; font-size: 14px;">Jika QR Code tidak muncul, gunakan text di bawah ini:</p>
                            <div class="qr-text">${qrCodeData}</div>
                            <button id="copy-qr" class="btn">📋 Copy QR Text</button>
                        </div>
                    </div>
                    
                    <div class="refresh-info">
                        <small>⏱️ Halaman ini akan otomatis refresh setiap 15 detik</small><br>
                        <a href="/qr" class="btn">🔄 Manual Refresh</a>
                        <a href="/api/status" class="btn">📊 Cek Status</a>
                        <button id="force-refresh" class="btn">🔄 Force Refresh QR</button>
                    </div>
                </div>
                
                <script>
                    let qrLoaded = false;
                    
                    // Function to load QR code image
                    async function loadQRImage() {
                        try {
                            const response = await fetch('/api/qr-image');
                            const data = await response.json();
                            
                            if (data.success && data.qr_image) {
                                const qrImg = document.getElementById('qr-image');
                                const statusDiv = document.getElementById('qr-status');
                                
                                qrImg.src = data.qr_image;
                                qrImg.onload = function() {
                                    statusDiv.style.display = 'none';
                                    qrImg.style.display = 'block';
                                    qrLoaded = true;
                                    console.log('✅ QR Code berhasil dimuat');
                                };
                                
                                qrImg.onerror = function() {
                                    showFallback('Gagal memuat gambar QR Code');
                                };
                            } else {
                                showFallback(data.message || 'QR Code tidak tersedia');
                            }
                        } catch (error) {
                            console.error('Error loading QR image:', error);
                            showFallback('Error loading QR Code');
                        }
                    }
                    
                    // Function to show text fallback
                    function showFallback(message) {
                        const statusDiv = document.getElementById('qr-status');
                        const textContainer = document.getElementById('qr-text-container');
                        
                        statusDiv.innerHTML = '<span class="error">' + message + '</span>';
                        textContainer.style.display = 'block';
                        
                        // Setup copy functionality
                        document.getElementById('copy-qr').onclick = function() {
                            const qrText = '${qrCodeData}';
                            navigator.clipboard.writeText(qrText).then(function() {
                                alert('QR code text berhasil dicopy ke clipboard!');
                            }).catch(function(err) {
                                console.error('Copy failed:', err);
                                // Fallback selection
                                const textDiv = document.querySelector('.qr-text');
                                const range = document.createRange();
                                range.selectNode(textDiv);
                                window.getSelection().removeAllRanges();
                                window.getSelection().addRange(range);
                                alert('Silakan copy manual text yang sudah diselect');
                            });
                        };
                    }
                    
                    // Force refresh functionality
                    document.getElementById('force-refresh').onclick = function() {
                        location.reload();
                    };
                    
                    // Start loading QR image
                    loadQRImage();
                    
                    // Fallback if image doesn't load in 5 seconds
                    setTimeout(function() {
                        if (!qrLoaded) {
                            showFallback('QR Code membutuhkan waktu lama untuk dimuat');
                        }
                    }, 5000);
                    
                    // Auto refresh setiap 15 detik
                    setTimeout(() => {
                        console.log('🔄 Auto refreshing page...');
                        location.reload();
                    }, 15000);
                </script>
            </body>
            </html>
        `);
    } else if (isReady && isAuthenticated) {
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp Connected - PPID Polinema</title>
                <meta charset="UTF-8">
                <style>
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                        text-align: center; 
                        padding: 50px; 
                        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                        min-height: 100vh;
                        margin: 0;
                        color: white;
                    }
                    .container {
                        max-width: 500px;
                        margin: 0 auto;
                        background: white;
                        border-radius: 15px;
                        padding: 40px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        color: #333;
                    }
                    .success-icon {
                        font-size: 4em;
                        margin-bottom: 20px;
                    }
                    .btn {
                        display: inline-block;
                        background: #007bff;
                        color: white;
                        padding: 10px 20px;
                        text-decoration: none;
                        border-radius: 5px;
                        margin: 10px;
                        transition: background 0.3s;
                    }
                    .btn:hover {
                        background: #0056b3;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="success-icon">✅</div>
                    <h1>WhatsApp Sudah Terhubung!</h1>
                    <p>WhatsApp API PPID Polinema siap digunakan!</p>
                    <div>
                        <a href="/api/status" class="btn">Cek Status</a>
                        <a href="/" class="btn">Kembali ke Home</a>
                    </div>
                </div>
            </body>
            </html>
        `);
    } else {
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp Loading - PPID Polinema</title>
                <meta charset="UTF-8">
                <style>
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                        text-align: center; 
                        padding: 50px; 
                        background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
                        min-height: 100vh;
                        margin: 0;
                        color: white;
                    }
                    .container {
                        max-width: 500px;
                        margin: 0 auto;
                        background: white;
                        border-radius: 15px;
                        padding: 40px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        color: #333;
                    }
                    .loading-icon {
                        font-size: 4em;
                        margin-bottom: 20px;
                        animation: spin 2s linear infinite;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                    .debug-info {
                        background: #f8f9fa;
                        padding: 10px;
                        border-radius: 5px;
                        margin-top: 20px;
                        font-size: 12px;
                        text-align: left;
                    }
                    .btn {
                        display: inline-block;
                        background: #007bff;
                        color: white;
                        padding: 8px 16px;
                        text-decoration: none;
                        border-radius: 5px;
                        margin: 5px;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="loading-icon">🔄</div>
                    <h1>Menunggu Koneksi WhatsApp...</h1>
                    <p>Server sedang memulai, silakan tunggu beberapa saat</p>
                    
                    <div class="debug-info">
                        <strong>Debug Info:</strong><br>
                        Client Initialized: ${clientInitialized ? 'Ya' : 'Tidak'}<br>
                        Ready: ${isReady ? 'Ya' : 'Tidak'}<br>
                        Authenticated: ${isAuthenticated ? 'Ya' : 'Tidak'}<br>
                        QR Available: ${!!qrCodeData ? 'Ya' : 'Tidak'}<br>
                        Retry: ${initRetryCount}/${maxRetries}
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <a href="/qr" class="btn">🔄 Refresh</a>
                        <a href="/api/restart" class="btn" onclick="return confirm('Restart WhatsApp client?')">🔄 Force Restart</a>
                    </div>
                    
                    <p><small>Jika masalah berlanjut lebih dari 2 menit, coba restart server</small></p>
                </div>
                <script>
                    setTimeout(() => location.reload(), 10000);
                </script>
            </body>
            </html>
        `);
    }
});

// API Status
app.get('/api/status', (req, res) => {
    res.json({
        status: isReady ? 'siap' : 'belum_siap',
        authenticated: isAuthenticated,
        message: isReady ? 'WhatsApp client siap digunakan' : 'WhatsApp client belum siap',
        timestamp: new Date().toISOString(),
        qr_available: !!qrCodeData,
        client_initialized: clientInitialized,
        retry_count: initRetryCount,
        max_retries: maxRetries,
        server_info: {
            uptime: process.uptime(),
            memory_usage: process.memoryUsage(),
            node_version: process.version
        }
    });
});

// Send Message endpoint
app.post('/api/send-message', async (req, res) => {
    try {
        // Autentikasi bearer token
        const token = req.headers.authorization?.replace('Bearer ', '');
        if (token !== AUTH_TOKEN) {
            return res.status(401).json({ 
                error: 'Token tidak valid',
                message: 'Authorization header diperlukan untuk mengirim pesan' 
            });
        }

        if (!isReady || !isAuthenticated) {
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
            isAuthenticated = false;
        }

        res.status(500).json({
            error: errorMessage,
            message: error.message,
            timestamp: new Date().toISOString()
        });
    }
});

// Force restart endpoint untuk debugging
app.post('/api/restart', (req, res) => {
    console.log('🔄 Manual restart diminta...');
    restartClient();
    res.json({
        success: true,
        message: 'Client restart dimulai',
        timestamp: new Date().toISOString()
    });
});

// Debug endpoint untuk troubleshooting
app.get('/debug', (req, res) => {
    const sessionExists = fs.existsSync(SESSION_PATH);
    const sessionFiles = sessionExists ? fs.readdirSync(SESSION_PATH) : [];
    
    res.json({
        timestamp: new Date().toISOString(),
        server_status: 'running',
        whatsapp_status: {
            isReady,
            isAuthenticated,
            clientInitialized,
            qrCodeAvailable: !!qrCodeData,
            qrCodeLength: qrCodeData ? qrCodeData.length : 0,
            initRetryCount,
            maxRetries
        },
        system_info: {
            uptime: process.uptime(),
            memory: process.memoryUsage(),
            node_version: process.version,
            platform: process.platform,
            arch: process.arch,
            pid: process.pid
        },
        directories: {
            session_path: SESSION_PATH,
            session_exists: sessionExists,
            session_files_count: sessionFiles.length,
            current_dir: __dirname,
            working_dir: process.cwd()
        },
        environment: {
            port: PORT,
            auth_token_set: !!AUTH_TOKEN,
            node_env: process.env.NODE_ENV || 'development'
        },
        client_info: {
            puppeteer_config: {
                headless: true,
                args_count: 7
            },
            events_registered: [
                'qr', 'ready', 'authenticated', 
                'auth_failure', 'disconnected', 'loading_screen'
            ]
        }
    });
});

// Force QR regeneration endpoint
app.post('/api/force-qr', (req, res) => {
    console.log('🔄 Force QR regeneration diminta...');

    // Reset QR data untuk memaksa generate ulang
    qrCodeData = '';
    isReady = false;
    isAuthenticated = false;
    
    // Restart client untuk generate QR baru
    restartClient();
    
    res.json({
        success: true,
        message: 'Force QR regeneration dimulai',
        timestamp: new Date().toISOString(),
        note: 'Check /qr endpoint in 30-60 seconds'
    });
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        service: 'PPID WhatsApp API',
        uptime: process.uptime(),
        whatsapp_ready: isReady,
        whatsapp_authenticated: isAuthenticated,
        client_initialized: clientInitialized,
        retry_count: initRetryCount
    });
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error('Server error:', err);
    res.status(500).json({
        error: 'Internal server error',
        message: process.env.NODE_ENV === 'development' ? err.message : 'Terjadi kesalahan server'
    });
});

// 404 handler
app.use((req, res) => {
    res.status(404).json({
        error: 'Endpoint tidak ditemukan',
        message: 'API endpoint yang diminta tidak ditemukan'
    });
});

// Start server
const server = app.listen(PORT, () => {
    console.log('\n🚀========================================');
    console.log('🏛️  PPID POLINEMA WHATSAPP API SERVER');
    console.log('========================================');
    console.log(`🌐 Server: http://localhost:${PORT}`);
    console.log(`📱 QR Code: http://localhost:${PORT}/qr`);
    console.log(`📋 Status: http://localhost:${PORT}/api/status`);
    console.log('========================================');
    console.log('🔄 Akan menginisialisasi WhatsApp client dalam 3 detik...\n');
    
    // Delay initialization untuk memastikan server sudah siap
    setTimeout(() => {
        initializeClient();
    }, 3000);
});

// Error handling untuk port
server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
        console.error('\n❌ ERROR: Port 3000 sudah digunakan!');
        console.error('📋 Jalankan: stop-whatsapp.bat lalu start-whatsapp.bat');
        process.exit(1);
    } else {
        console.error('❌ Server error:', err);
        process.exit(1);
    }
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\n🔄 Menutup WhatsApp API server...');
    try {
        if (clientInitialized) {
            await client.destroy();
            console.log('✅ WhatsApp client berhasil ditutup');
        }
    } catch (error) {
        console.error('❌ Error saat menutup client:', error);
    }
    server.close(() => {
        console.log('✅ Server berhasil ditutup');
        process.exit(0);
    });
});

process.on('SIGTERM', async () => {
    console.log('\n🔄 Menerima signal SIGTERM...');
    try {
        if (clientInitialized) {
            await client.destroy();
            console.log('✅ WhatsApp client berhasil ditutup');
        }
    } catch (error) {
        console.error('❌ Error saat menutup client:', error);
    }
    server.close(() => {
        console.log('✅ Server berhasil ditutup');
        process.exit(0);
    });
});

process.on('uncaughtException', (error) => {
    console.error('❌ Uncaught Exception:', error);
    process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('❌ Unhandled Rejection at:', promise, 'reason:', reason);
    process.exit(1);
});