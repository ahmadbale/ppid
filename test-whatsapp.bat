@echo off
title PPID Polinema - Test WhatsApp
color 0B

echo.
echo ========================================
echo    TEST WHATSAPP API
echo ========================================
echo.

if "%1"=="" (
    set /p nomor="Masukkan nomor WhatsApp (08xxxxxxxxx): "
) else (
    set nomor=%1
)

if "%nomor%"=="" (
    echo [ERROR] Nomor WhatsApp harus diisi!
    pause
    exit /b 1
)

echo.
echo [INFO] Testing WhatsApp ke nomor: %nomor%
echo.

php artisan whatsapp:test %nomor%

echo.
pause