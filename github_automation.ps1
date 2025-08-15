# GitHub Contribution Automation PowerShell Script
# Script ini menyediakan berbagai fungsi untuk menjaga kontribusi GitHub

param(
    [Parameter(Mandatory=$false)]
    [ValidateSet("commit", "dummy", "stats", "setup", "schedule", "help")]
    [string]$Action = "commit",

    [Parameter(Mandatory=$false)]
    [ValidateSet("meaningful", "dummy")]
    [string]$Mode = "meaningful"
)

# Konfigurasi
$ScriptPath = $PSScriptRoot
$LogFile = Join-Path $ScriptPath "automation.log"

# Fungsi logging
function Write-Log {
    param([string]$Message)
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry
}

# Fungsi untuk melakukan daily commit
function Invoke-DailyCommit {
    param([string]$CommitMode = "meaningful")

    Write-Log "🚀 Memulai daily commit automation (mode: $CommitMode)..."

    try {
        # Pindah ke direktori script
        Set-Location $ScriptPath

        # Jalankan PHP script berdasarkan mode
        if ($CommitMode -eq "dummy") {
            Write-Log "📄 Menggunakan dummy commit mode..."
            & php dummy_commit.php single
        } else {
            Write-Log "📝 Menggunakan meaningful commit mode..."
            & php daily_commit.php commit meaningful
        }

        if ($LASTEXITCODE -eq 0) {
            Write-Log "✅ Daily commit berhasil dilakukan"
            return $true
        } else {
            Write-Log "❌ Daily commit gagal"
            return $false
        }
    }
    catch {
        Write-Log "❌ Error: $($_.Exception.Message)"
        return $false
    }
}

# Fungsi untuk melakukan dummy commit
function Invoke-DummyCommit {
    Write-Log "🤖 Memulai dummy commit automation..."

    try {
        Set-Location $ScriptPath
        & php dummy_commit.php single

        if ($LASTEXITCODE -eq 0) {
            Write-Log "✅ Dummy commit berhasil dilakukan"
            return $true
        } else {
            Write-Log "❌ Dummy commit gagal"
            return $false
        }
    }
    catch {
        Write-Log "❌ Error: $($_.Exception.Message)"
        return $false
    }
}

# Fungsi untuk menampilkan statistik
function Show-Statistics {
    Write-Log "📊 Menampilkan statistik kontribusi..."
    
    try {
        Set-Location $ScriptPath
        & php daily_commit.php stats
    }
    catch {
        Write-Log "❌ Error menampilkan statistik: $($_.Exception.Message)"
    }
}

# Fungsi setup awal
function Initialize-Setup {
    Write-Log "🔧 Melakukan setup awal..."
    
    # Cek apakah git sudah dikonfigurasi
    $GitUser = git config user.name
    $GitEmail = git config user.email
    
    if (-not $GitUser -or -not $GitEmail) {
        Write-Host "⚠️  Git belum dikonfigurasi. Silakan konfigurasi terlebih dahulu:"
        Write-Host "git config --global user.name 'Nama Anda'"
        Write-Host "git config --global user.email 'email@anda.com'"
        return
    }
    
    Write-Log "✅ Git sudah dikonfigurasi untuk: $GitUser ($GitEmail)"
    
    # Buat initial commit jika belum ada
    $CommitCount = git rev-list --count HEAD 2>$null
    if (-not $CommitCount -or $CommitCount -eq "0") {
        Write-Log "📝 Membuat initial commit..."
        git add .
        git commit -m "🎉 Initial commit - GitHub contribution automation setup"
        Write-Log "✅ Initial commit berhasil dibuat"
    }
    
    Write-Log "🎯 Setup selesai! Anda dapat mulai menggunakan automation."
}

# Fungsi untuk setup scheduled task
function Set-ScheduledTask {
    Write-Log "⏰ Setting up scheduled task..."
    
    $TaskName = "GitHubDailyCommit"
    $ScriptFullPath = Join-Path $ScriptPath "github_automation.ps1"
    
    # Hapus task yang sudah ada jika ada
    $ExistingTask = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
    if ($ExistingTask) {
        Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
        Write-Log "🗑️  Task lama dihapus"
    }
    
    # Buat action untuk menjalankan script
    $Action = New-ScheduledTaskAction -Execute "PowerShell.exe" -Argument "-ExecutionPolicy Bypass -File `"$ScriptFullPath`" -Action commit"
    
    # Buat trigger untuk setiap hari jam 9 pagi
    $Trigger = New-ScheduledTaskTrigger -Daily -At "09:00"
    
    # Buat principal untuk menjalankan dengan user saat ini
    $Principal = New-ScheduledTaskPrincipal -UserId $env:USERNAME -LogonType Interactive
    
    # Buat settings
    $Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
    
    # Register task
    Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -Principal $Principal -Settings $Settings -Description "GitHub Daily Commit Automation"
    
    Write-Log "✅ Scheduled task '$TaskName' berhasil dibuat"
    Write-Log "📅 Task akan berjalan setiap hari jam 09:00"
    Write-Log "🔧 Anda dapat mengubah jadwal melalui Task Scheduler Windows"
}

# Fungsi help
function Show-Help {
    Write-Host @"
🤖 GitHub Contribution Automation Tool

USAGE:
    .\github_automation.ps1 [ACTION] [OPTIONS]

ACTIONS:
    commit      Lakukan daily commit (default)
    dummy       Lakukan dummy commit sederhana
    stats       Tampilkan statistik kontribusi
    setup       Setup awal repository dan konfigurasi
    schedule    Setup scheduled task untuk automation
    help        Tampilkan bantuan ini

COMMIT MODES:
    -Mode meaningful    Commit dengan perubahan bermakna (default)
    -Mode dummy        Commit file dummy sederhana

EXAMPLES:
    .\github_automation.ps1 commit
    .\github_automation.ps1 commit -Mode meaningful
    .\github_automation.ps1 commit -Mode dummy
    .\github_automation.ps1 dummy
    .\github_automation.ps1 stats
    .\github_automation.ps1 setup
    .\github_automation.ps1 schedule

DUMMY COMMIT USAGE:
    php dummy_commit.php single        # 1 file dummy
    php dummy_commit.php multiple 5    # 5 file dummy
    php dummy_commit.php update        # Update file yang ada
    php dummy_commit.php cleanup       # Hapus file lama

TIPS:
    - Jalankan 'setup' terlebih dahulu untuk konfigurasi awal
    - Gunakan 'schedule' untuk automation harian
    - Mode 'dummy' untuk "gaming the system" GitHub green
    - Mode 'meaningful' untuk kontribusi yang bermakna
    - Gunakan 'stats' untuk melihat progress kontribusi

📚 Untuk informasi lebih lanjut, baca dokumentasi README.md
"@
}

# Main execution
Write-Host "🤖 GitHub Contribution Automation Tool" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan

switch ($Action.ToLower()) {
    "commit" {
        Invoke-DailyCommit -CommitMode $Mode
    }
    "dummy" {
        Invoke-DummyCommit
    }
    "stats" {
        Show-Statistics
    }
    "setup" {
        Initialize-Setup
    }
    "schedule" {
        Set-ScheduledTask
    }
    "help" {
        Show-Help
    }
    default {
        Write-Host "❌ Action tidak dikenal: $Action" -ForegroundColor Red
        Show-Help
    }
}

Write-Host "`n✨ Selesai!" -ForegroundColor Green
