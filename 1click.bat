@echo off
REM === Jalankan PowerShell script di background ===
start powershell -ExecutionPolicy Bypass -File ".\tools\spam.ps1"

REM === Jalankan PHP script di background ===
start php efficient_commit_bot.php 10000

REM === Biarkan jendela tetap terbuka ===
pause
