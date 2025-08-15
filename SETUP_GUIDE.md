# 🚀 Setup Guide - GitHub Contribution Automation

Panduan lengkap untuk setup dan konfigurasi sistem automation GitHub contribution.

## 📋 Prerequisites

### 1. Software yang Diperlukan

- **Git** - Version control system
  - Download: https://git-scm.com/download/win
  - Verifikasi: `git --version`

- **PHP** - Untuk menjalankan script automation
  - Jika menggunakan Laragon: PHP sudah terinstall
  - Verifikasi: `php --version`

- **PowerShell** - Untuk automation script (sudah ada di Windows)
  - Verifikasi: `powershell --version`

### 2. Akun GitHub

- Pastikan Anda memiliki akun GitHub aktif
- Repository target sudah dibuat (bisa private atau public)
- SSH key atau HTTPS authentication sudah dikonfigurasi

## 🔧 Langkah Setup

### Step 1: Download dan Extract

1. Download atau clone repository ini:
```bash
git clone https://github.com/username/autogit.git
cd autogit
```

2. Atau extract file ZIP ke direktori `c:\laragon\www\autogit`

### Step 2: Konfigurasi Git

```bash
# Set global git configuration
git config --global user.name "Nama Lengkap Anda"
git config --global user.email "email@anda.com"

# Verifikasi konfigurasi
git config --list
```

### Step 3: Inisialisasi Repository

```bash
# Jika belum ada .git folder
git init

# Tambahkan remote repository
git remote add origin https://github.com/username/repository-anda.git

# Atau menggunakan SSH
git remote add origin git@github.com:username/repository-anda.git
```

### Step 4: Setup PowerShell Execution Policy

```powershell
# Buka PowerShell sebagai Administrator
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Verifikasi
Get-ExecutionPolicy
```

### Step 5: Jalankan Setup Awal

```powershell
# Jalankan setup automation
.\github_automation.ps1 -Action setup
```

Script akan:
- Membuat file konfigurasi yang diperlukan
- Membuat initial commit jika belum ada
- Memverifikasi semua dependencies

### Step 6: Test Manual Run

```bash
# Test daily commit script
php daily_commit.php

# Test PowerShell automation
.\github_automation.ps1 -Action commit

# Lihat hasil
git log --oneline -5
```

### Step 7: Setup Automation (Opsional)

```powershell
# Setup scheduled task untuk automation harian
.\github_automation.ps1 -Action schedule
```

## ⚙️ Konfigurasi Lanjutan

### 1. Kustomisasi Target Harian

Edit file `github_config.json`:

```json
{
    "github_username": "username-anda",
    "target_daily_commits": 1,
    "target_weekly_issues": 2,
    "target_monthly_prs": 4,
    "notification_enabled": true
}
```

### 2. Kustomisasi Commit Messages

Edit `daily_commit.php` pada bagian quotes:

```php
private function createQuotesFile() {
    $quotes = [
        "Konsistensi adalah kunci kesuksesan",
        "Tambahkan quote Anda sendiri",
        // ... quotes lainnya
    ];
    
    file_put_contents($this->quotesFile, implode("\n", $quotes));
}
```

### 3. Kustomisasi Project Ideas

Edit `project_ideas.json` atau tambahkan kategori baru:

```json
{
    "web_projects": [
        "Simple Calculator",
        "Todo List App"
    ],
    "custom_category": [
        "Your Custom Project 1",
        "Your Custom Project 2"
    ]
}
```

## 🔍 Verifikasi Setup

### 1. Cek File yang Dibuat

Pastikan file-file berikut sudah ada:

```
autogit/
├── daily_commit.php ✅
├── github_automation.ps1 ✅
├── github_activity_tracker.php ✅
├── contribution_utilities.php ✅
├── contribution_log.txt ✅
├── daily_progress.json ✅
├── daily_quotes.txt ✅
├── github_activities.json ✅
├── github_config.json ✅
└── projects/ ✅
```

### 2. Test Semua Fungsi

```bash
# Test daily commit
php daily_commit.php stats

# Test activity tracker
php github_activity_tracker.php dashboard

# Test project utilities
php contribution_utilities.php list

# Test PowerShell automation
.\github_automation.ps1 -Action help
```

### 3. Verifikasi Git Integration

```bash
# Cek status git
git status

# Cek remote repository
git remote -v

# Test push (jika sudah ada commits)
git push origin main
```

## 🚨 Troubleshooting

### Problem: "git command not found"

**Solution:**
```bash
# Tambahkan Git ke PATH environment variable
# Atau reinstall Git dengan opsi "Add to PATH"
```

### Problem: "php command not found"

**Solution:**
```bash
# Jika menggunakan Laragon:
# 1. Buka Laragon
# 2. Menu > Tools > Path > Add Laragon to Path

# Atau tambahkan manual ke PATH:
# C:\laragon\bin\php\php-8.x.x
```

### Problem: PowerShell execution policy error

**Solution:**
```powershell
# Jalankan sebagai Administrator
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope CurrentUser

# Atau untuk satu kali eksekusi
powershell -ExecutionPolicy Bypass -File github_automation.ps1
```

### Problem: Git authentication error

**Solution:**
```bash
# Untuk HTTPS (akan diminta username/password atau token)
git remote set-url origin https://github.com/username/repo.git

# Untuk SSH (perlu setup SSH key)
git remote set-url origin git@github.com:username/repo.git

# Generate SSH key jika belum ada
ssh-keygen -t rsa -b 4096 -C "email@anda.com"
```

### Problem: Scheduled task tidak berjalan

**Solution:**
1. Buka Task Scheduler Windows
2. Cari task "GitHubDailyCommit"
3. Klik kanan > Properties
4. Tab "General": Pastikan "Run whether user is logged on or not" dicentang
5. Tab "Conditions": Uncheck "Start the task only if the computer is on AC power"

## 📊 Monitoring Setup

### 1. Cek Log Files

```bash
# Lihat log automation
type automation.log

# Lihat progress harian
type daily_progress.json

# Lihat aktivitas GitHub
type github_activities.json
```

### 2. Dashboard Monitoring

```bash
# Dashboard utama
php github_activity_tracker.php dashboard

# Statistik detail
php daily_commit.php stats

# Laporan mingguan
php github_activity_tracker.php report
```

### 3. GitHub Verification

1. Buka profil GitHub Anda
2. Cek contribution graph
3. Pastikan kotak hijau muncul setelah automation berjalan
4. Verifikasi commits muncul di repository

## 🎯 Best Practices

### 1. Setup Backup

```bash
# Backup konfigurasi penting
copy github_config.json github_config.backup.json
copy daily_progress.json daily_progress.backup.json
```

### 2. Regular Maintenance

- Cek log files seminggu sekali
- Update quotes dan project ideas secara berkala
- Monitor GitHub contribution graph
- Backup data penting

### 3. Security Considerations

- Jangan commit credentials ke repository
- Gunakan environment variables untuk sensitive data
- Pastikan repository private jika berisi data sensitif

## 🔄 Update dan Maintenance

### Update Script

```bash
# Backup current setup
xcopy /E /I autogit autogit_backup

# Download update terbaru
# Replace files yang diperlukan

# Restore konfigurasi
copy autogit_backup\github_config.json autogit\
copy autogit_backup\daily_progress.json autogit\
```

### Maintenance Rutin

1. **Harian**: Cek apakah automation berjalan
2. **Mingguan**: Review laporan dan statistik
3. **Bulanan**: Update project ideas dan quotes
4. **Quarterly**: Backup semua data dan konfigurasi

---

🎉 **Setup Complete!** Anda sekarang siap untuk menjaga kontribusi GitHub tetap hijau dengan automation yang bermakna.

Untuk bantuan lebih lanjut, lihat `README.md` atau buat issue di repository ini.
