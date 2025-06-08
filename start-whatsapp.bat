@echo off
title PPID Polinema - WhatsApp API Server
color 0A
cls

echo.
echo ========================================
echo    PPID POLINEMA WHATSAPP API SERVER
echo ========================================
echo.
echo [INFO] Starting WhatsApp API Server...
echo [INFO] Server akan berjalan di: http://localhost:3000
echo [INFO] QR Code tersedia di: http://localhost:3000/qr
echo [INFO] Tekan Ctrl+C untuk menghentikan server
echo.

REM Pindah ke folder whatsapp-api
cd /d "%~dp0whatsapp-api"

REM Cek apakah node_modules sudah ada
if not exist node_modules (
    echo [INSTALL] Installing dependencies...
    echo.
    call npm install
    if errorlevel 1 (
        echo.
        echo [ERROR] Gagal menginstall dependencies!
        echo [ERROR] Pastikan Node.js sudah terinstall
        pause
        exit /b 1
    )
    echo.
    echo [SUCCESS] Dependencies berhasil diinstall!
    echo.
)

REM Cek apakah Node.js terinstall
node --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Node.js tidak ditemukan!
    echo [ERROR] Silakan install Node.js terlebih dahulu
    echo [ERROR] Download di: https://nodejs.org
    pause
    exit /b 1
)

REM Jalankan server
echo [START] Memulai WhatsApp API Server...
echo.
call npm start

REM Jika server berhenti
echo.
echo [INFO] WhatsApp API Server telah dihentikan
pause