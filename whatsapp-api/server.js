const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const cors = require('cors');
const fs = require('fs');
const path = require('path');
const axios = require('axios'); // Tambahkan axios untuk HTTP request

const app = express();
const PORT = process.env.WA_PORT || 3000;
const AUTH_TOKEN = process.env.WA_TOKEN || 'ppid-polinema-2024';

// Laravel API configuration
const LARAVEL_BASE_URL = process.env.LARAVEL_URL || 'http://localhost:8000';
const LARAVEL_API_TOKEN = process.env.LARAVEL_TOKEN || 'your-api-token';

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
let connectedPhoneNumber = null;

// Perbaiki function sendScanLogToLaravel dengan retry mechanism
async function sendScanLogToLaravel(phoneNumber, retryCount = 0) {
    try {
        console.log(`📤 Sending scan log to Laravel (attempt ${retryCount + 1})`);
        console.log(`📞 Phone number: ${phoneNumber}`);
        
        const requestData = {
            nomor_pengirim: phoneNumber,
            scan_source: 'auto_detected',
            timestamp: new Date().toISOString()
        };

        console.log('📤 Request data:', JSON.stringify(requestData));

        const response = await axios.post(
            `${LARAVEL_BASE_URL}/whatsapp-management/auto-save-qrcode-log`,
            requestData,
            {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                timeout: 15000
            }
        );

        console.log('📥 Laravel response status:', response.status);
        console.log('📥 Laravel response data:', JSON.stringify(response.data));

        if (response.data && response.data.success) {
            console.log('✅ Log scan berhasil dikirim ke Laravel');
            console.log('📝 Message:', response.data.message);
            return true;
        } else {
            console.log('⚠️ Laravel response tidak success:', response.data);
            
            if (retryCount < 3) {
                console.log(`🔄 Retrying in 3 seconds... (${retryCount + 1}/3)`);
                await new Promise(resolve => setTimeout(resolve, 3000));
                return await sendScanLogToLaravel(phoneNumber, retryCount + 1);
            }
            return false;
        }

    } catch (error) {
        console.error('❌ Error sending scan log to Laravel:', error.message);
        
        if (error.response) {
            console.error('📋 Response status:', error.response.status);
            console.error('📋 Response data:', JSON.stringify(error.response.data));
        }
        
        if (retryCount < 3) {
            console.log(`🔄 Retrying in 5 seconds... (${retryCount + 1}/3)`);
            await new Promise(resolve => setTimeout(resolve, 5000));
            return await sendScanLogToLaravel(phoneNumber, retryCount + 1);
        }
        return false;
    }
}

// Tambahkan event listener untuk message masuk sebagai fallback
client.on('message', async (message) => {
    // Jika belum ada nomor yang terdeteksi, ambil dari message
    if (!connectedPhoneNumber && message.from) {
        try {
            const fromNumber = message.from.replace('@c.us', '').replace('@g.us', '');
            if (fromNumber && fromNumber.length >= 10) {
                console.log('📱 Detected phone from incoming message:', fromNumber);
                connectedPhoneNumber = fromNumber;
                await sendScanLogToLaravel(connectedPhoneNumber);
            }
        } catch (error) {
            console.error('Error processing message for phone detection:', error);
        }
    }
});

// Perbaiki function extractPhoneNumber untuk lebih komprehensif
function extractPhoneNumber(info) {
    try {
        console.log('🔍 Extracting phone from:', typeof info);
        
        // Jika info null atau undefined
        if (!info) {
            console.log('❌ Info is null or undefined');
            return null;
        }

        // Method 1: Direct string check
        if (typeof info === 'string') {
            const numbers = info.replace(/[^0-9]/g, '');
            if (numbers.length >= 10) {
                console.log('📱 Found phone in string:', numbers);
                return numbers;
            }
        }

        // Method 2: Object properties check
        if (typeof info === 'object') {
            // Check common WhatsApp ID patterns
            const possibleFields = [
                'user', 'wid', 'id', 'me', '_serialized', 
                'phoneNumber', 'phone', 'number'
            ];

            for (const field of possibleFields) {
                if (info[field]) {
                    const extracted = extractPhoneNumber(info[field]);
                    if (extracted) {
                        console.log(`📱 Found phone in ${field}:`, extracted);
                        return extracted;
                    }
                }
            }

            // Check nested objects
            for (const key in info) {
                if (typeof info[key] === 'object' && info[key] !== null) {
                    const nested = extractPhoneNumber(info[key]);
                    if (nested) {
                        console.log(`📱 Found phone in nested ${key}:`, nested);
                        return nested;
                    }
                }
                
                // Check string values for phone patterns
                if (typeof info[key] === 'string') {
                    const numbers = info[key].replace(/[^0-9]/g, '');
                    if (numbers.length >= 10 && numbers.length <= 15) {
                        console.log(`📱 Found phone in property ${key}:`, numbers);
                        return numbers;
                    }
                }
            }
        }
        
        console.log('❌ No phone number found');
        return null;
        
    } catch (error) {
        console.error('❌ Error extracting phone number:', error);
        return null;
    }
}

// WhatsApp client events
client.on('qr', (qr) => {
    qrCodeData = qr;
    connectedPhoneNumber = null;
    console.log('\n===========================================');
    console.log('📱 SCAN QR CODE DENGAN WHATSAPP ANDA');
    console.log('===========================================');
    qrcode.generate(qr, { small: true });
    console.log('===========================================');
    console.log('🌐 Atau akses: http://localhost:' + PORT + '/qr');
    console.log('===========================================\n');
});

client.on('ready', async () => {
    console.log('\n✅ WhatsApp Client SIAP DIGUNAKAN!');
    console.log('🚀 Server berjalan di: http://localhost:' + PORT);
    console.log('📋 Status: http://localhost:' + PORT + '/api/status\n');
    
    isReady = true;
    isAuthenticated = true;
    qrCodeData = '';
    initRetryCount = 0;

    // Tunggu 3 detik untuk memastikan client info tersedia
    setTimeout(async () => {
        await detectAndSendPhoneNumber();
    }, 3000);
});

// Function untuk detect dan kirim nomor telepon
async function detectAndSendPhoneNumber() {
    try {
        console.log('🔍 Detecting phone number...');
        
        // Method 1: Dari client.info
        let phoneNumber = null;
        try {
            const info = client.info;
            console.log('📱 Client info:', JSON.stringify(info, null, 2));
            phoneNumber = extractPhoneNumber(info);
        } catch (error) {
            console.log('⚠️ Error getting client.info:', error.message);
        }

        // Method 2: Dari client.getState()
        if (!phoneNumber) {
            try {
                const state = await client.getState();
                console.log('📱 Client state:', state);
                phoneNumber = extractPhoneNumber(state);
            } catch (error) {
                console.log('⚠️ Error getting client state:', error.message);
            }
        }

        // Method 3: Dari client.pupPage (fallback)
        if (!phoneNumber) {
            try {
                // Last resort - coba extract dari page context
                const page = client.pupPage;
                if (page) {
                    const waInfo = await page.evaluate(() => {
                        return window.Store?.Conn?.wid || window.Store?.Me?.wid || null;
                    });
                    if (waInfo) {
                        phoneNumber = extractPhoneNumber(waInfo);
                    }
                }
            } catch (error) {
                console.log('⚠️ Error getting phone from page:', error.message);
            }
        }

        if (phoneNumber) {
            connectedPhoneNumber = phoneNumber;
            console.log('📞 Successfully detected phone number:', phoneNumber);
            await sendScanLogToLaravel(phoneNumber);
        } else {
            console.log('⚠️ Could not detect phone number, will retry...');
            // Retry dengan delay
            setTimeout(async () => {
                await retryPhoneDetection();
            }, 5000);
        }

    } catch (error) {
        console.error('❌ Error in detectAndSendPhoneNumber:', error);
        setTimeout(async () => {
            await retryPhoneDetection();
        }, 5000);
    }
}

// Function untuk retry detection
async function retryPhoneDetection(attempt = 1) {
    const maxAttempts = 5;
    
    if (attempt > maxAttempts) {
        console.log('❌ Max retry attempts reached for phone detection');
        // Gunakan fallback nomor atau skip
        const fallbackNumber = 'auto_detected_' + Date.now();
        await sendScanLogToLaravel(fallbackNumber);
        return;
    }

    console.log(`🔄 Retry phone detection attempt ${attempt}/${maxAttempts}`);
    
    try {
        await detectAndSendPhoneNumber();
    } catch (error) {
        console.error(`❌ Retry ${attempt} failed:`, error.message);
        setTimeout(async () => {
            await retryPhoneDetection(attempt + 1);
        }, 3000);
    }
}

// Function untuk mencoba auto log berulang kali
async function attemptAutoLog() {
    try {
        const info = client.info;
        if (info && !connectedPhoneNumber) {
            connectedPhoneNumber = extractPhoneNumber(info);
            
            if (connectedPhoneNumber) {
                console.log('📞 Berhasil detect nomor di attempt kedua:', connectedPhoneNumber);
                await sendScanLogToLaravel(connectedPhoneNumber);
            } else {
                console.log('⚠️ Masih belum bisa detect nomor, akan menggunakan fallback...');
                // Gunakan nomor default atau skip
                await sendScanLogToLaravel('auto_detected');
            }
        }
    } catch (error) {
        console.error('❌ Error in attemptAutoLog:', error);
    }
}

client.on('authenticated', async (session) => {
    console.log('🔐 WhatsApp berhasil terautentikasi');
    isAuthenticated = true;
    qrCodeData = '';

    // Try to get phone number from session data
    try {
        console.log('📱 Session data:', JSON.stringify(session, null, 2));
        
        if (session && !connectedPhoneNumber) {
            connectedPhoneNumber = extractPhoneNumber(session);
            console.log('📞 Phone number from session:', connectedPhoneNumber);
        }
    } catch (error) {
        console.error('❌ Error processing session data:', error);
    }
});

client.on('auth_failure', (msg) => {
    console.error('❌ Gagal autentikasi WhatsApp:', msg);
    isAuthenticated = false;
    isReady = false;
    connectedPhoneNumber = null;
    
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
    connectedPhoneNumber = null;
    
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

// Event listener untuk mendeteksi saat client siap dan mendapatkan info
client.on('change_state', async (state) => {
    console.log('🔄 WhatsApp state changed:', state);
    
    if (state === 'CONNECTED' && !connectedPhoneNumber) {
        try {
            // Tunggu sebentar untuk memastikan client info tersedia
            setTimeout(async () => {
                const info = client.info;
                if (info && !connectedPhoneNumber) {
                    connectedPhoneNumber = extractPhoneNumber(info);
                    console.log('📞 Phone number from state change:', connectedPhoneNumber);
                    
                    if (connectedPhoneNumber) {
                        await sendScanLogToLaravel(connectedPhoneNumber);
                    }
                }
            }, 2000);
        } catch (error) {
            console.error('❌ Error in state change handler:', error);
        }
    }
});

// Tambahkan endpoint untuk get connected phone number
app.get('/api/connected-phone', (req, res) => {
    res.json({
        success: true,
        connected_phone: connectedPhoneNumber,
        is_authenticated: isAuthenticated,
        is_ready: isReady,
        timestamp: new Date().toISOString()
    });
});

// Tambahkan endpoint untuk manual trigger log scan
app.post('/api/trigger-scan-log', async (req, res) => {
    try {
        if (!isAuthenticated || !isReady) {
            return res.status(503).json({
                success: false,
                message: 'WhatsApp client belum siap'
            });
        }

        let phoneNumber = connectedPhoneNumber;
        
        // Jika nomor belum terdeteksi, coba ambil dari client info
        if (!phoneNumber) {
            try {
                const info = client.info;
                phoneNumber = extractPhoneNumber(info);
                connectedPhoneNumber = phoneNumber;
            } catch (error) {
                console.error('Error getting phone from client info:', error);
            }
        }

        if (phoneNumber) {
            await sendScanLogToLaravel(phoneNumber);
            res.json({
                success: true,
                message: 'Log scan berhasil dikirim',
                phone_number: phoneNumber
            });
        } else {
            res.status(404).json({
                success: false,
                message: 'Nomor telefon tidak dapat dideteksi'
            });
        }

    } catch (error) {
        console.error('Error triggering scan log:', error);
        res.status(500).json({
            success: false,
            message: 'Error: ' + error.message
        });
    }
});

// Function to restart client
async function restartClient() {
    try {
        initRetryCount++;
        console.log(`🔄 Restart attempt ${initRetryCount}/${maxRetries}`);
        
        connectedPhoneNumber = null;
        
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
        connected_phone: connectedPhoneNumber,
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
    console.log(`🔗 Laravel URL: ${LARAVEL_BASE_URL}`);
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