@echo off
REM Simple Dummy Commit Script
REM Script kecil untuk commit file dummy setiap hari

echo ========================================
echo   GitHub Dummy Commit Automation
echo ========================================
echo.

REM Pindah ke direktori script
cd /d "%~dp0"

REM Tampilkan tanggal dan waktu
echo Tanggal: %date%
echo Waktu: %time%
echo.

REM Jalankan dummy commit
echo Membuat dummy commit...
php dummy_commit.php single

echo.
echo ========================================
echo   Dummy Commit Selesai
echo ========================================

REM Pause untuk melihat hasil (hapus jika dijalankan otomatis)
pause
