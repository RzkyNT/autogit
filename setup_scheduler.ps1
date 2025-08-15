# Setup Windows Task Scheduler untuk GitHub Automation
# Script sederhana untuk membuat scheduled task

param(
    [string]$Mode = "meaningful"
)

Write-Host "Setup Windows Task Scheduler untuk GitHub Automation" -ForegroundColor Cyan
Write-Host "=======================================================" -ForegroundColor Cyan

$ScriptPath = $PSScriptRoot
$TaskName = "GitHubDailyCommit"

try {
    # Hapus task yang sudah ada jika ada
    $ExistingTask = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
    if ($ExistingTask) {
        Write-Host "🗑️  Menghapus task lama..." -ForegroundColor Yellow
        Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
    }

    # Tentukan command berdasarkan mode
    if ($Mode -eq "dummy") {
        $Command = "php"
        $Arguments = "dummy_commit.php single"
        Write-Host "🤖 Mode: Dummy commit automation" -ForegroundColor Yellow
    } else {
        $Command = "php"
        $Arguments = "daily_commit.php commit"
        Write-Host "📝 Mode: Meaningful commit automation" -ForegroundColor Green
    }

    # Buat action untuk menjalankan script
    $Action = New-ScheduledTaskAction -Execute $Command -Argument $Arguments -WorkingDirectory $ScriptPath

    # Buat trigger untuk setiap hari jam 9 pagi
    $Trigger = New-ScheduledTaskTrigger -Daily -At "09:00"

    # Buat principal untuk menjalankan dengan user saat ini
    $Principal = New-ScheduledTaskPrincipal -UserId $env:USERNAME -LogonType Interactive

    # Buat settings
    $Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable

    # Register task
    $Description = "GitHub Daily Commit Automation - $Mode mode"
    Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -Principal $Principal -Settings $Settings -Description $Description

    Write-Host "Scheduled task '$TaskName' berhasil dibuat!" -ForegroundColor Green
    Write-Host "Task akan berjalan setiap hari jam 09:00" -ForegroundColor Green
    Write-Host "Mode: $Mode" -ForegroundColor Green

    # Test task
    Write-Host "Testing task..." -ForegroundColor Cyan
    Start-ScheduledTask -TaskName $TaskName
    Start-Sleep -Seconds 3

    $TaskInfo = Get-ScheduledTaskInfo -TaskName $TaskName
    Write-Host "Last Run Time: $($TaskInfo.LastRunTime)" -ForegroundColor Green
    Write-Host "Last Task Result: $($TaskInfo.LastTaskResult)" -ForegroundColor Green

    Write-Host "Setup selesai! Task akan berjalan otomatis setiap hari." -ForegroundColor Green
    Write-Host "Untuk mengubah jadwal, buka Task Scheduler Windows" -ForegroundColor Yellow
    
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Coba jalankan PowerShell sebagai Administrator" -ForegroundColor Yellow
}

Write-Host "Cara menggunakan:" -ForegroundColor Cyan
Write-Host "   .\setup_scheduler.ps1                # Setup meaningful commit"
Write-Host "   .\setup_scheduler.ps1 -Mode dummy    # Setup dummy commit"
Write-Host "   .\setup_scheduler.ps1 -Mode meaningful # Setup meaningful commit"
