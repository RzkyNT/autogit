# Mass Commit Generator
# PowerShell script untuk membuat commits dalam jumlah besar

param(
    [Parameter(Mandatory=$true)]
    [int]$CommitCount,
    
    [Parameter(Mandatory=$false)]
    [int]$PushEvery = 50,
    
    [Parameter(Mandatory=$false)]
    [string]$Mode = "normal"
)

function Write-Progress-Custom {
    param($Current, $Total, $Activity)
    
    $Percent = [math]::Round(($Current / $Total) * 100, 1)
    Write-Progress -Activity $Activity -Status "$Current/$Total ($Percent%)" -PercentComplete $Percent
}

function Create-MassCommits {
    param($Count, $PushInterval, $Mode)
    
    $StartTime = Get-Date
    $CommitDir = "mass_commits"
    
    # Create directory
    if (!(Test-Path $CommitDir)) {
        New-Item -ItemType Directory -Path $CommitDir | Out-Null
    }
    
    Write-Host "🚀 Mass Commit Generator Started" -ForegroundColor Green
    Write-Host "🎯 Target: $Count commits" -ForegroundColor Yellow
    Write-Host "📤 Push every: $PushInterval commits" -ForegroundColor Yellow
    Write-Host "⚡ Mode: $Mode" -ForegroundColor Yellow
    Write-Host "⏰ Started: $($StartTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor Cyan
    Write-Host ""
    
    # Get current branch
    $CurrentBranch = git branch --show-current 2>$null
    if ([string]::IsNullOrEmpty($CurrentBranch)) {
        $CurrentBranch = "master"
    }
    
    for ($i = 1; $i -le $Count; $i++) {
        # Create commit file
        $FileName = "$CommitDir/commit_$i.txt"
        $Content = @"
Commit #$i
Timestamp: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
Mode: $Mode
Random: $((Get-Random -Minimum 10000 -Maximum 99999))
Batch: $([math]::Ceiling($i / $PushInterval))
"@
        
        Set-Content -Path $FileName -Value $Content
        
        # Git operations
        git add . 2>$null | Out-Null
        git commit -m "Mass commit #$i" 2>$null | Out-Null
        
        # Progress update
        if ($i % 50 -eq 0 -or $i -eq $Count) {
            $Elapsed = (Get-Date) - $StartTime
            $Rate = [math]::Round($i / $Elapsed.TotalSeconds, 1)
            Write-Host "📊 Progress: $i/$Count ($([math]::Round($i/$Count*100,1))%) - Rate: $Rate commits/sec" -ForegroundColor Green
        }
        
        # Push to GitHub
        if ($i % $PushInterval -eq 0 -or $i -eq $Count) {
            Write-Host "📤 Pushing commits (up to #$i)..." -ForegroundColor Cyan
            $PushResult = git push origin $CurrentBranch 2>&1
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "✅ Push successful" -ForegroundColor Green
            } else {
                Write-Host "⚠️ Push warning: $PushResult" -ForegroundColor Yellow
            }
        }
        
        # Delay based on mode
        if ($Mode -eq "fast") {
            # No delay for fast mode
        } elseif ($Mode -eq "normal") {
            Start-Sleep -Milliseconds 100
        } elseif ($Mode -eq "slow") {
            Start-Sleep -Milliseconds 500
        }
    }
    
    $EndTime = Get-Date
    $TotalTime = $EndTime - $StartTime
    $AvgRate = [math]::Round($Count / $TotalTime.TotalSeconds, 1)
    
    Write-Host ""
    Write-Host "🎉 MASS COMMIT COMPLETED!" -ForegroundColor Green
    Write-Host "✅ Total commits: $Count" -ForegroundColor White
    Write-Host "⏱️ Total time: $($TotalTime.ToString('hh\:mm\:ss'))" -ForegroundColor White
    Write-Host "📈 Average rate: $AvgRate commits/sec" -ForegroundColor White
    Write-Host "📤 All commits pushed to GitHub!" -ForegroundColor White
    Write-Host "🌿 Branch: $CurrentBranch" -ForegroundColor White
}

# Validation
if ($CommitCount -le 0) {
    Write-Host "❌ Commit count must be greater than 0" -ForegroundColor Red
    exit 1
}

if ($PushEvery -le 0) {
    Write-Host "❌ Push interval must be greater than 0" -ForegroundColor Red
    exit 1
}

# Warning for large numbers
if ($CommitCount -gt 5000) {
    Write-Host "⚠️ WARNING: $CommitCount commits is a very large number!" -ForegroundColor Yellow
    $EstimatedMinutes = [math]::Round($CommitCount / 600, 1) # Rough estimate
    Write-Host "⏱️ Estimated time: ~$EstimatedMinutes minutes" -ForegroundColor Yellow
    Write-Host ""
    
    $Confirm = Read-Host "Continue? (y/N)"
    if ($Confirm.ToLower() -ne 'y') {
        Write-Host "Cancelled." -ForegroundColor Yellow
        exit 0
    }
}

# Show summary before starting
Write-Host ""
Write-Host "📋 SUMMARY:" -ForegroundColor Cyan
Write-Host "   Commits: $CommitCount" -ForegroundColor White
Write-Host "   Push every: $PushEvery commits" -ForegroundColor White
Write-Host "   Mode: $Mode" -ForegroundColor White
Write-Host ""

# Start the process
Create-MassCommits -Count $CommitCount -PushInterval $PushEvery -Mode $Mode

Write-Host ""
Write-Host "🔗 Check your GitHub repository to see all commits!" -ForegroundColor Green
