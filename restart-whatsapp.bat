@echo off
title PPID Polinema - Restart WhatsApp Server
color 0E

echo.
echo ========================================
echo    RESTART WHATSAPP API SERVER
echo ========================================
echo.

echo [INFO] Menghentikan server yang sedang berjalan...
call "%~dp0stop-whatsapp.bat"

echo.
echo [INFO] Menunggu 3 detik...
timeout /t 3 >nul

echo.
echo [INFO] Memulai ulang server...
call "%~dp0start-whatsapp.bat"