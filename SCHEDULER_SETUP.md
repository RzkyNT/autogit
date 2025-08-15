# 🕐 Setup Scheduler untuk Commit Otomatis Harian

## 1. Windows Task Scheduler (Recommended)

### Setup Otomatis (Termudah)

```powershell
# Jalankan sekali untuk setup
.\github_automation.ps1 -Action schedule
```

### Setup Manual

1. **Buka Task Scheduler:**
   - Tekan `Win + R`, ketik `taskschd.msc`
   - Atau search "Task Scheduler" di Start Menu

2. **Create Basic Task:**
   - Klik "Create Basic Task..." di panel kanan
   - Name: `GitHubDailyCommit`
   - Description: `Automated daily GitHub commit`

3. **Set Trigger:**
   - Pilih "Daily"
   - Start date: Hari ini
   - Start time: `09:00:00` (atau waktu yang diinginkan)
   - Recur every: `1 days`

4. **Set Action:**
   - Pilih "Start a program"
   - Program/script: `PowerShell.exe`
   - Arguments: `-ExecutionPolicy Bypass -File "C:\laragon\www\autogit\github_automation.ps1" -Action commit`
   - Start in: `C:\laragon\www\autogit`

5. **Advanced Settings:**
   - ✅ Run whether user is logged on or not
   - ✅ Run with highest privileges
   - ✅ Hidden (jika tidak ingin melihat window)
   - ✅ Allow task to be run on demand
   - ✅ If the running task does not end when requested, force it to stop

### Verifikasi Task Scheduler

```powershell
# Cek apakah task sudah dibuat
Get-ScheduledTask -TaskName "GitHubDailyCommit"

# Test run manual
Start-ScheduledTask -TaskName "GitHubDailyCommit"

# Lihat history
Get-ScheduledTaskInfo -TaskName "GitHubDailyCommit"
```

## 2. Cron Job (Jika menggunakan WSL/Linux)

### Setup Cron

```bash
# Edit crontab
crontab -e

# Tambahkan line ini untuk run setiap hari jam 9 pagi
0 9 * * * cd /mnt/c/laragon/www/autogit && php daily_commit.php commit

# Atau untuk dummy commit
0 9 * * * cd /mnt/c/laragon/www/autogit && php dummy_commit.php single

# Lihat crontab yang aktif
crontab -l
```

### Cron Syntax Explanation
```
# ┌───────────── minute (0 - 59)
# │ ┌───────────── hour (0 - 23)
# │ │ ┌───────────── day of month (1 - 31)
# │ │ │ ┌───────────── month (1 - 12)
# │ │ │ │ ┌───────────── day of week (0 - 6) (Sunday to Saturday)
# │ │ │ │ │
# * * * * * command

# Contoh:
0 9 * * *     # Setiap hari jam 9 pagi
0 */6 * * *   # Setiap 6 jam
0 9 * * 1-5   # Setiap hari kerja jam 9 pagi
```

## 3. PowerShell Scheduled Job

### Setup PowerShell Job

```powershell
# Buat scheduled job
$Trigger = New-JobTrigger -Daily -At "09:00"
$ScriptPath = "C:\laragon\www\autogit\github_automation.ps1"

Register-ScheduledJob -Name "GitHubDailyCommit" -Trigger $Trigger -ScriptBlock {
    Set-Location "C:\laragon\www\autogit"
    & powershell -ExecutionPolicy Bypass -File "github_automation.ps1" -Action commit
}

# Lihat scheduled jobs
Get-ScheduledJob

# Test run
Start-Job -DefinitionName "GitHubDailyCommit"
```

## 4. Startup Script (Jika PC selalu nyala)

### Setup Startup

1. **Buat script startup:**
   - Buat file `startup_github_automation.bat`
   - Isi dengan delay dan loop

2. **Tambahkan ke Startup folder:**
   - Tekan `Win + R`, ketik `shell:startup`
   - Copy script ke folder tersebut

### Script Startup Example

```batch
@echo off
REM Startup GitHub Automation

:LOOP
REM Tunggu sampai jam 9 pagi
timeout /t 3600 /nobreak > nul

REM Cek apakah sudah jam 9
for /f "tokens=1-2 delims=:" %%a in ('time /t') do (
    if "%%a"=="09" (
        cd /d "C:\laragon\www\autogit"
        php dummy_commit.php single
    )
)

REM Loop kembali
goto LOOP
```

## 5. GitHub Actions (Jika repo di GitHub)

### Setup GitHub Actions

Buat file `.github/workflows/daily-commit.yml`:

```yaml
name: Daily Commit Automation

on:
  schedule:
    # Runs at 09:00 UTC every day
    - cron: '0 9 * * *'
  workflow_dispatch: # Manual trigger

jobs:
  daily-commit:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout repository
      uses: actions/checkout@v3
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        
    - name: Run daily commit
      run: |
        php dummy_commit.php single
        
    - name: Commit and push changes
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"
        git add .
        git commit -m "Automated daily commit" || exit 0
        git push
```

## 🎯 Rekomendasi Setup

### Untuk Penggunaan Lokal (PC Windows):

1. **Gunakan Windows Task Scheduler** (Paling reliable)
   ```powershell
   .\github_automation.ps1 -Action schedule
   ```

2. **Verifikasi setup:**
   ```powershell
   # Cek task
   Get-ScheduledTask -TaskName "GitHubDailyCommit"
   
   # Test manual
   Start-ScheduledTask -TaskName "GitHubDailyCommit"
   ```

3. **Monitor hasil:**
   - Cek Task Scheduler History
   - Lihat log file: `automation.log`
   - Cek GitHub contribution graph

### Untuk Repository GitHub:

1. **Setup GitHub Actions** untuk automation cloud
2. **Kombinasi dengan local scheduler** untuk backup

## 🔧 Troubleshooting Scheduler

### Task Scheduler tidak jalan:

1. **Cek Task History:**
   - Buka Task Scheduler
   - Pilih task "GitHubDailyCommit"
   - Tab "History"

2. **Common Issues:**
   ```powershell
   # Execution Policy
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   
   # Path issues - gunakan full path
   # Wrong: php daily_commit.php
   # Right: C:\laragon\bin\php\php-8.x.x\php.exe C:\laragon\www\autogit\daily_commit.php
   ```

3. **Test manual:**
   ```powershell
   # Test script manual
   cd C:\laragon\www\autogit
   .\github_automation.ps1 -Action commit
   ```

### Cron Job tidak jalan:

```bash
# Cek cron service
sudo service cron status

# Cek cron logs
sudo tail -f /var/log/cron

# Test manual
cd /mnt/c/laragon/www/autogit && php dummy_commit.php single
```

## 📊 Monitoring Automation

### Cek apakah automation berjalan:

1. **GitHub Contribution Graph** - Lihat kotak hijau
2. **Log Files** - Cek `automation.log`
3. **Git History** - `git log --oneline -10`
4. **Task Scheduler History** - Event logs
5. **File Timestamps** - Cek kapan file terakhir dimodifikasi

### Setup Notification (Optional):

```powershell
# Tambahkan email notification di task scheduler
# Atau buat script yang kirim notification ke Discord/Slack
```

---

**Kesimpulan:** Windows Task Scheduler adalah pilihan terbaik untuk automation harian di Windows. Setup sekali dengan `.\github_automation.ps1 -Action schedule` dan sistem akan berjalan otomatis setiap hari!
