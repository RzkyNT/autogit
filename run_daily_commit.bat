@echo off
REM GitHub Daily Commit Automation Batch Script
REM Jalankan script ini setiap hari untuk menjaga kontribusi GitHub

echo ========================================
echo   GitHub Contribution Automation
echo ========================================
echo.

REM Pindah ke direktori script
cd /d "%~dp0"

REM Tampilkan tanggal dan waktu
echo Tanggal: %date%
echo Waktu: %time%
echo.

REM Jalankan PHP script
echo Menjalankan daily commit automation...
php daily_commit.php commit

echo.
echo ========================================
echo   Automation Selesai
echo ========================================

REM Pause untuk melihat hasil (hapus jika dijalankan otomatis)
pause
