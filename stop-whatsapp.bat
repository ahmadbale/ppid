@echo off
title PPID Polinema - Stop WhatsApp Server
color 0C

echo.
echo ========================================
echo    MENGHENTIKAN WHATSAPP API SERVER
echo ========================================
echo.

REM Hentikan proses Node.js yang menggunakan port 3000
echo [INFO] Mencari proses WhatsApp API Server...

for /f "tokens=5" %%a in ('netstat -aon ^| find ":3000" ^| find "LISTENING"') do (
    echo [INFO] Menghentikan proses dengan PID: %%a
    taskkill /f /pid %%a >nul 2>&1
    if not errorlevel 1 (
        echo [SUCCESS] WhatsApp API Server berhasil dihentikan
    ) else (
        echo [WARNING] Tidak dapat menghentikan proses %%a
    )
)

echo.
echo [INFO] Selesai
timeout /t 3 >nul