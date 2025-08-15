# Simple Scheduler Setup for GitHub Automation

param([string]$Mode = "meaningful")

$TaskName = "GitHubDailyCommit"
$ScriptPath = $PSScriptRoot

Write-Host "Setting up Windows Task Scheduler..." -ForegroundColor Green

# Remove existing task if exists
$ExistingTask = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
if ($ExistingTask) {
    Write-Host "Removing existing task..." -ForegroundColor Yellow
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
}

# Determine command based on mode
if ($Mode -eq "dummy") {
    $Command = "php"
    $Arguments = "dummy_commit.php single"
    Write-Host "Mode: Dummy commit automation" -ForegroundColor Yellow
} else {
    $Command = "php"
    $Arguments = "daily_commit.php commit"
    Write-Host "Mode: Meaningful commit automation" -ForegroundColor Green
}

# Create task components
$Action = New-ScheduledTaskAction -Execute $Command -Argument $Arguments -WorkingDirectory $ScriptPath
$Trigger = New-ScheduledTaskTrigger -Daily -At "09:00"
$Principal = New-ScheduledTaskPrincipal -UserId $env:USERNAME -LogonType Interactive
$Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable

# Register the task
$Description = "GitHub Daily Commit Automation - $Mode mode"
Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -Principal $Principal -Settings $Settings -Description $Description

Write-Host "Task '$TaskName' created successfully!" -ForegroundColor Green
Write-Host "Will run daily at 09:00" -ForegroundColor Green
Write-Host "Mode: $Mode" -ForegroundColor Green

# Test the task
Write-Host "Testing task..." -ForegroundColor Cyan
Start-ScheduledTask -TaskName $TaskName

Write-Host "Setup complete! Task will run automatically every day." -ForegroundColor Green
Write-Host "To modify schedule, open Windows Task Scheduler" -ForegroundColor Yellow

Write-Host ""
Write-Host "Usage examples:" -ForegroundColor Cyan
Write-Host "  .\simple_scheduler.ps1                # Setup meaningful commit"
Write-Host "  .\simple_scheduler.ps1 -Mode dummy    # Setup dummy commit"
