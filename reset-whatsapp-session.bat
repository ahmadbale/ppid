@echo off
title PPID Polinema - Reset WhatsApp Session
color 0E

echo.
echo ========================================
echo    RESET WHATSAPP SESSION
echo ========================================
echo.
echo [WARNING] Ini akan menghapus session WhatsApp yang aktif!
echo [WARNING] Anda perlu scan QR code lagi dengan nomor baru
echo [WARNING] Nomor WhatsApp yang sedang terhubung akan terputus
echo.
set /p confirm="Apakah Anda yakin ingin reset session? (Y/N): "

if /i not "%confirm%"=="Y" (
    echo.
    echo [CANCEL] Reset session dibatalkan
    echo [INFO] Session WhatsApp tetap aktif
    pause
    exit /b 0
)

echo.
echo [INFO] Memulai proses reset session...
echo.

REM Step 1: Stop WhatsApp server
echo [STEP 1] Menghentikan WhatsApp server...
call "%~dp0stop-whatsapp.bat"

echo.
echo [STEP 2] Menunggu server benar-benar berhenti...
timeout /t 5 >nul

REM Step 2: Hapus semua file session WhatsApp
echo [STEP 3] Menghapus session files...
cd /d "%~dp0whatsapp-api"

REM Hapus folder .wwebjs_auth (session utama)
if exist .wwebjs_auth (
    echo [INFO] Menghapus folder .wwebjs_auth...
    rmdir /s /q .wwebjs_auth
    echo [SUCCESS] ✅ Session authentication dihapus
) else (
    echo [INFO] Folder .wwebjs_auth tidak ditemukan
)

REM Hapus folder .wwebjs_cache (cache session)
if exist .wwebjs_cache (
    echo [INFO] Menghapus folder .wwebjs_cache...
    rmdir /s /q .wwebjs_cache
    echo [SUCCESS] ✅ Session cache dihapus
) else (
    echo [INFO] Folder .wwebjs_cache tidak ditemukan
)

REM Hapus file session.json
if exist session.json (
    echo [INFO] Menghapus file session.json...
    del /q session.json
    echo [SUCCESS] ✅ Session JSON dihapus
) else (
    echo [INFO] File session.json tidak ditemukan
)

REM Hapus file chrome debug log
if exist chrome_debug.log (
    echo [INFO] Menghapus file chrome_debug.log...
    del /q chrome_debug.log
    echo [SUCCESS] ✅ Chrome debug log dihapus
) else (
    echo [INFO] File chrome_debug.log tidak ditemukan
)

REM Hapus semua file log WhatsApp
for %%f in (*.log) do (
    if exist "%%f" (
        echo [INFO] Menghapus file log: %%f
        del /q "%%f"
        echo [SUCCESS] ✅ Log file %%f dihapus
    )
)

REM Hapus folder DevToolsActivePort jika ada
if exist DevToolsActivePort (
    echo [INFO] Menghapus DevToolsActivePort...
    del /q DevToolsActivePort
    echo [SUCCESS] ✅ DevToolsActivePort dihapus
)

echo.
echo [SUCCESS] 🎉 Session WhatsApp berhasil direset!
echo [INFO] Semua data autentikasi telah dihapus
echo [INFO] Server akan menampilkan QR code baru saat dijalankan
echo.

set /p restart="Apakah Anda ingin memulai server sekarang? (Y/N): "

if /i "%restart%"=="Y" (
    echo.
    echo [STEP 4] Memulai WhatsApp server...
    echo [INFO] QR code akan muncul dalam beberapa detik
    echo [INFO] Scan QR code dengan nomor WhatsApp baru
    echo.
    pause
    call "%~dp0start-whatsapp.bat"
) else (
    echo.
    echo [INFO] Server tidak dijalankan
    echo [INFO] Jalankan start-whatsapp.bat untuk memulai server
    echo [INFO] QR code akan muncul saat server dijalankan
    echo.
    pause
)