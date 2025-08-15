# Multi Commit Scheduler
# Setup multiple scheduled tasks untuk commit 15-40 kali per hari

param(
    [int]$MinCommits = 15,
    [int]$MaxCommits = 40,
    [string]$Mode = "distributed"
)

$ScriptPath = $PSScriptRoot
$TaskBaseName = "GitHubMultiCommit"

Write-Host "Setting up Multi Commit Scheduler..." -ForegroundColor Green
Write-Host "Target: $MinCommits-$MaxCommits commits per day" -ForegroundColor Yellow
Write-Host "Mode: $Mode" -ForegroundColor Yellow

# Remove existing multi commit tasks
$ExistingTasks = Get-ScheduledTask -TaskName "$TaskBaseName*" -ErrorAction SilentlyContinue
if ($ExistingTasks) {
    Write-Host "Removing existing multi commit tasks..." -ForegroundColor Yellow
    foreach ($task in $ExistingTasks) {
        Unregister-ScheduledTask -TaskName $task.TaskName -Confirm:$false
    }
}

if ($Mode -eq "distributed") {
    # Strategy 1: Distributed throughout the day
    Write-Host "Creating distributed schedule..." -ForegroundColor Cyan
    
    # Morning burst (7-9 AM): 5-8 commits
    $MorningAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
    $MorningTrigger = New-ScheduledTaskTrigger -Daily -At "07:30"
    $Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
    Register-ScheduledTask -TaskName "$TaskBaseName-Morning" -Action $MorningAction -Trigger $MorningTrigger -Settings $Settings -Description "Morning commit burst"
    
    # Midday activity (11 AM - 1 PM): 3-6 commits
    $MiddayAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
    $MiddayTrigger = New-ScheduledTaskTrigger -Daily -At "12:00"
    Register-ScheduledTask -TaskName "$TaskBaseName-Midday" -Action $MiddayAction -Trigger $MiddayTrigger -Settings $Settings -Description "Midday commit activity"
    
    # Afternoon work (3-5 PM): 4-8 commits
    $AfternoonAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
    $AfternoonTrigger = New-ScheduledTaskTrigger -Daily -At "15:30"
    Register-ScheduledTask -TaskName "$TaskBaseName-Afternoon" -Action $AfternoonAction -Trigger $AfternoonTrigger -Settings $Settings -Description "Afternoon commit work"
    
    # Evening session (7-9 PM): 3-8 commits
    $EveningAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
    $EveningTrigger = New-ScheduledTaskTrigger -Daily -At "19:30"
    Register-ScheduledTask -TaskName "$TaskBaseName-Evening" -Action $EveningAction -Trigger $EveningTrigger -Settings $Settings -Description "Evening commit session"
    
    Write-Host "Created 4 distributed tasks:" -ForegroundColor Green
    Write-Host "  - Morning (07:30): 5-8 commits" -ForegroundColor White
    Write-Host "  - Midday (12:00): 3-6 commits" -ForegroundColor White
    Write-Host "  - Afternoon (15:30): 4-8 commits" -ForegroundColor White
    Write-Host "  - Evening (19:30): 3-8 commits" -ForegroundColor White
    
} elseif ($Mode -eq "hourly") {
    # Strategy 2: Hourly commits during work hours
    Write-Host "Creating hourly schedule..." -ForegroundColor Cyan
    
    $WorkHours = @("09:00", "10:00", "11:00", "14:00", "15:00", "16:00", "17:00", "20:00")
    
    foreach ($hour in $WorkHours) {
        $TaskName = "$TaskBaseName-$($hour.Replace(':', ''))"
        $Action = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
        $Trigger = New-ScheduledTaskTrigger -Daily -At $hour
        $Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
        
        Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -Settings $Settings -Description "Hourly commit at $hour"
        Write-Host "  - Created task for $hour" -ForegroundColor White
    }
    
} elseif ($Mode -eq "burst") {
    # Strategy 3: Few large bursts
    Write-Host "Creating burst schedule..." -ForegroundColor Cyan
    
    # Morning mega burst (9 AM): 15-20 commits
    $MorningBurstAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
    $MorningBurstTrigger = New-ScheduledTaskTrigger -Daily -At "09:00"
    $Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
    Register-ScheduledTask -TaskName "$TaskBaseName-MorningBurst" -Action $MorningBurstAction -Trigger $MorningBurstTrigger -Settings $Settings -Description "Morning commit burst"
    
    # Evening burst (8 PM): 10-20 commits
    $EveningBurstAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory $ScriptPath
    $EveningBurstTrigger = New-ScheduledTaskTrigger -Daily -At "20:00"
    Register-ScheduledTask -TaskName "$TaskBaseName-EveningBurst" -Action $EveningBurstAction -Trigger $EveningBurstTrigger -Settings $Settings -Description "Evening commit burst"
    
    Write-Host "Created 2 burst tasks:" -ForegroundColor Green
    Write-Host "  - Morning Burst (09:00): 15-20 commits" -ForegroundColor White
    Write-Host "  - Evening Burst (20:00): 10-20 commits" -ForegroundColor White
}

# Add cleanup task (weekly)
$CleanupAction = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php cleanup" -WorkingDirectory $ScriptPath
$CleanupTrigger = New-ScheduledTaskTrigger -Weekly -DaysOfWeek Sunday -At "23:00"
$CleanupSettings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
Register-ScheduledTask -TaskName "$TaskBaseName-Cleanup" -Action $CleanupAction -Trigger $CleanupTrigger -Settings $CleanupSettings -Description "Weekly cleanup of old files"

Write-Host "Added weekly cleanup task (Sunday 23:00)" -ForegroundColor Green

# Test one task
Write-Host "Testing multi commit system..." -ForegroundColor Cyan
& php multi_commit_automation.php run

Write-Host ""
Write-Host "Multi Commit Scheduler setup complete!" -ForegroundColor Green
Write-Host "Expected daily commits: $MinCommits-$MaxCommits" -ForegroundColor Yellow
Write-Host ""
Write-Host "To view all tasks:" -ForegroundColor Cyan
Write-Host "  Get-ScheduledTask -TaskName 'GitHubMultiCommit*'" -ForegroundColor White
Write-Host ""
Write-Host "To test manually:" -ForegroundColor Cyan
Write-Host "  php multi_commit_automation.php run" -ForegroundColor White
Write-Host "  php multi_commit_automation.php stats" -ForegroundColor White
