@echo off
REM Custom Commit Generator
REM Usage: make_commits.bat 1000

echo ========================================
echo   Custom GitHub Commit Generator
echo ========================================
echo.

if "%1"=="" (
    echo Usage: make_commits.bat [number_of_commits]
    echo.
    echo Examples:
    echo   make_commits.bat 100     ^(100 commits^)
    echo   make_commits.bat 1000    ^(1000 commits^)
    echo   make_commits.bat 5000    ^(5000 commits^)
    echo.
    pause
    exit /b 1
)

set COMMIT_COUNT=%1
set PUSH_EVERY=50

echo Target commits: %COMMIT_COUNT%
echo Push every: %PUSH_EVERY% commits
echo.

REM Pindah ke direktori script
cd /d "%~dp0"

REM Konfirmasi untuk jumlah besar
if %COMMIT_COUNT% GTR 1000 (
    echo WARNING: %COMMIT_COUNT% commits is a large number!
    echo This may take a while to complete.
    echo.
    set /p CONFIRM=Continue? (y/N): 
    if /i not "!CONFIRM!"=="y" (
        echo Cancelled.
        pause
        exit /b 0
    )
)

echo.
echo Starting commit generation...
echo.

REM Jalankan PHP script
php custom_commit_count.php %COMMIT_COUNT% %PUSH_EVERY%

echo.
echo ========================================
echo   Commit Generation Complete!
echo ========================================
echo.
echo Check your GitHub repository to see the commits.
echo.
pause
