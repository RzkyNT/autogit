# 🚀 Multi Commit Automation Guide - 15-40 Commits Per Day

Panduan lengkap untuk sistem automation yang dapat melakukan 15-40 commit per hari secara otomatis dengan pola yang realistis.

## 🎯 Sistem yang Tersedia

### 1. **Multi Commit Automation** (`multi_commit_automation.php`)
- Target: 15-40 commits per hari (random)
- Strategi: 6 jenis commit berbeda
- Batch system dengan delay realistis
- Auto-cleanup file lama

### 2. **Intensive Commit Bot** (`intensive_commit_bot.php`)
- Target: 15-40 commits dengan timing realistis
- Working hours vs off-hours strategy
- 10 jenis commit strategy
- Continuous mode untuk testing

### 3. **Multi Commit Scheduler** (`multi_commit_scheduler.ps1`)
- Setup multiple scheduled tasks
- 3 mode: distributed, hourly, burst
- Automatic scheduling sepanjang hari

## 🚀 Quick Start - 15-40 Commits Per Hari

### Method 1: Single Run (Manual)

```bash
# Jalankan sekali untuk 15-40 commits
php multi_commit_automation.php run

# Atau intensive mode
php intensive_commit_bot.php run
```

### Method 2: Distributed Schedule (Recommended)

```powershell
# Setup 4 sesi per hari (total 15-30 commits)
.\multi_commit_scheduler.ps1 -Mode distributed

# Setup hourly commits (total 20-40 commits)
.\multi_commit_scheduler.ps1 -Mode hourly

# Setup burst mode (total 25-40 commits)
.\multi_commit_scheduler.ps1 -Mode burst
```

### Method 3: Continuous Mode (Testing)

```bash
# Run continuous selama 2 jam
php intensive_commit_bot.php continuous 120

# Run continuous selama 6 jam
php intensive_commit_bot.php continuous 360
```

## 📅 Strategi Scheduling

### 🌅 **Distributed Mode (Recommended)**
Menyebar commits sepanjang hari seperti aktivitas developer nyata:

- **Morning (07:30)**: 5-8 commits
- **Midday (12:00)**: 3-6 commits  
- **Afternoon (15:30)**: 4-8 commits
- **Evening (19:30)**: 3-8 commits

**Total**: 15-30 commits per hari

```powershell
.\multi_commit_scheduler.ps1 -Mode distributed
```

### ⏰ **Hourly Mode**
Commits setiap jam selama jam kerja:

- **09:00, 10:00, 11:00**: Morning session
- **14:00, 15:00, 16:00, 17:00**: Afternoon session
- **20:00**: Evening session

**Total**: 20-40 commits per hari (2-5 commits per jam)

```powershell
.\multi_commit_scheduler.ps1 -Mode hourly
```

### 💥 **Burst Mode**
Beberapa sesi besar dengan banyak commits:

- **Morning Burst (09:00)**: 15-20 commits
- **Evening Burst (20:00)**: 10-20 commits

**Total**: 25-40 commits per hari

```powershell
.\multi_commit_scheduler.ps1 -Mode burst
```

## 🎨 Jenis Commits yang Dibuat

### Multi Commit Automation (6 Strategies):

1. **Dummy Files** - File sederhana dengan timestamp
2. **Update Files** - Modifikasi file yang sudah ada
3. **Log Entries** - Entry ke activity.log
4. **Config Updates** - Update file konfigurasi JSON
5. **Data Entries** - Entry ke file CSV
6. **Progress Updates** - Update progress.md

### Intensive Commit Bot (10 Strategies):

1. **Micro Feature** - Feature kecil di features.md
2. **Bug Fix** - Entry bug fix di bugfixes.log
3. **Refactor** - File PHP refactored
4. **Documentation** - Update docs.md
5. **Test** - File test JavaScript
6. **Config** - File konfigurasi JSON
7. **Data Update** - Entry data CSV
8. **Cleanup** - Log cleanup
9. **Optimization** - Performance optimization
10. **Style** - Update CSS styles

## 📊 Monitoring dan Statistik

### Cek Commits Hari Ini

```bash
# Multi commit stats
php multi_commit_automation.php stats

# Intensive bot stats
php intensive_commit_bot.php stats

# Git log hari ini
git log --since="$(date +%Y-%m-%d) 00:00:00" --oneline
```

### Cek Scheduled Tasks

```powershell
# Lihat semua multi commit tasks
Get-ScheduledTask -TaskName "GitHubMultiCommit*"

# Cek status task
Get-ScheduledTaskInfo -TaskName "GitHubMultiCommit-Morning"

# Test manual task
Start-ScheduledTask -TaskName "GitHubMultiCommit-Morning"
```

## 🔧 Kustomisasi Target Commits

### Ubah Target di Script

Edit `multi_commit_automation.php`:
```php
private $targetCommitsMin = 20;  // Minimum commits
private $targetCommitsMax = 50;  // Maximum commits
```

Edit `intensive_commit_bot.php`:
```php
private $targetMin = 20;
private $targetMax = 50;
```

### Setup Custom Schedule

```powershell
# Custom target dengan parameter
.\multi_commit_scheduler.ps1 -MinCommits 20 -MaxCommits 50 -Mode distributed
```

## ⚡ Extreme Mode - 40+ Commits

Untuk target yang sangat tinggi (40+ commits per hari):

### Setup 1: Multiple Distributed Sessions

```powershell
# Setup 6 sesi per hari
.\multi_commit_scheduler.ps1 -Mode distributed

# Tambah sesi extra
$Action = New-ScheduledTaskAction -Execute "php" -Argument "multi_commit_automation.php run" -WorkingDirectory "C:\laragon\www\autogit"
$Trigger1 = New-ScheduledTaskTrigger -Daily -At "10:30"
$Trigger2 = New-ScheduledTaskTrigger -Daily -At "16:30"
$Trigger3 = New-ScheduledTaskTrigger -Daily -At "21:30"

Register-ScheduledTask -TaskName "GitHubMultiCommit-Extra1" -Action $Action -Trigger $Trigger1
Register-ScheduledTask -TaskName "GitHubMultiCommit-Extra2" -Action $Action -Trigger $Trigger2
Register-ScheduledTask -TaskName "GitHubMultiCommit-Extra3" -Action $Action -Trigger $Trigger3
```

### Setup 2: Continuous Background

```bash
# Run continuous mode di background (Linux/WSL)
nohup php intensive_commit_bot.php continuous 480 > /dev/null 2>&1 &

# Atau dengan screen
screen -S github_commits
php intensive_commit_bot.php continuous 480
# Ctrl+A, D untuk detach
```

## 🧹 Maintenance dan Cleanup

### Auto Cleanup

```bash
# Cleanup file lama (otomatis setiap minggu)
php multi_commit_automation.php cleanup 7

# Manual cleanup
php intensive_commit_bot.php cleanup
```

### Reset Daily Counter

```bash
# Reset log untuk hari baru
rm multi_commit_log.json
php multi_commit_automation.php stats
```

## 📈 Expected Results

### GitHub Contribution Graph
- ✅ Kotak hijau setiap hari
- ✅ Intensitas tinggi (dark green)
- ✅ Pola yang realistis sepanjang hari

### Commit Pattern
- ✅ 15-40 commits per hari
- ✅ Variasi commit messages
- ✅ Berbagai jenis file dan perubahan
- ✅ Timing yang natural (jam kerja)

### Repository Growth
- ✅ Banyak file dengan variasi
- ✅ History yang rich dan detailed
- ✅ Aktivitas yang konsisten

## ⚠️ Important Notes

### Realistic Patterns
- Commits terdistribusi sepanjang hari
- Lebih banyak di jam kerja
- Variasi jenis commits
- Delay yang natural antar commits

### File Management
- Auto cleanup file lama
- Struktur folder yang terorganisir
- Ukuran file yang reasonable

### Performance
- Batch processing untuk efisiensi
- Delay untuk menghindari spam
- Resource usage yang minimal

## 🎯 Rekomendasi Setup

### Untuk 15-25 Commits/Hari:
```powershell
.\multi_commit_scheduler.ps1 -Mode distributed
```

### Untuk 25-35 Commits/Hari:
```powershell
.\multi_commit_scheduler.ps1 -Mode hourly
```

### Untuk 35-40+ Commits/Hari:
```powershell
.\multi_commit_scheduler.ps1 -Mode burst
# + tambah extra sessions manual
```

---

**🎉 Dengan setup ini, Anda akan mendapatkan 15-40 commits per hari secara otomatis dengan pola yang realistis dan natural!**
