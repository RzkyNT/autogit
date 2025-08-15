# 🤖 GitHub Contribution Automation

Sistem otomatis untuk menjaga kotak kontribusi GitHub tetap hijau dengan aktivitas yang bermakna dan konsisten.

## 🎯 Fitur Utama

- ✅ **Daily Commit Automation** - Commit otomatis setiap hari
- 📊 **Activity Tracking** - Pelacakan semua aktivitas GitHub
- 🛠️ **Project Generator** - Pembuat project otomatis dengan template
- 📈 **Statistics Dashboard** - Dashboard statistik kontribusi
- ⏰ **Scheduled Tasks** - Penjadwalan otomatis dengan Windows Task Scheduler
- 📋 **Weekly Reports** - Laporan mingguan aktivitas

## 🚀 Quick Start

### 1. Setup Awal

```bash
# Clone atau download repository ini
git clone <your-repo-url>
cd autogit

# Jalankan setup awal
powershell -ExecutionPolicy Bypass -File github_automation.ps1 -Action setup
```

### 2. Konfigurasi Git (jika belum)

```bash
git config --global user.name "Nama Anda"
git config --global user.email "email@anda.com"
```

### 3. Jalankan Daily Commit

```bash
# Manual
php daily_commit.php

# Atau menggunakan PowerShell
powershell -ExecutionPolicy Bypass -File github_automation.ps1 -Action commit
```

### 4. Setup Automation (Opsional)

```bash
# Setup scheduled task untuk automation harian
powershell -ExecutionPolicy Bypass -File github_automation.ps1 -Action schedule
```

## 📁 Struktur File

```
autogit/
├── daily_commit.php              # Script utama untuk daily commit
├── github_automation.ps1         # PowerShell automation script
├── github_activity_tracker.php   # Sistem tracking aktivitas
├── contribution_utilities.php    # Utility untuk membuat project
├── run_daily_commit.bat         # Batch script untuk Windows
├── README.md                    # Dokumentasi ini
├── projects/                    # Direktori untuk project yang dibuat
├── templates/                   # Template untuk project baru
└── logs/                       # File log dan data
```

## 🛠️ Penggunaan Detail

### Daily Commit Automation

```bash
# Jalankan daily commit
php daily_commit.php commit

# Lihat statistik
php daily_commit.php stats
```

### Activity Tracking

```bash
# Catat aktivitas manual
php github_activity_tracker.php record commit "Fix bug in authentication"
php github_activity_tracker.php record issue "Add new feature request"
php github_activity_tracker.php record pull_request "Merge feature branch"

# Lihat dashboard
php github_activity_tracker.php dashboard

# Cek target harian
php github_activity_tracker.php target

# Generate laporan mingguan
php github_activity_tracker.php report
```

### Project Utilities

```bash
# Buat project baru
php contribution_utilities.php create "Todo App" web
php contribution_utilities.php create "API Helper" php

# Lihat daftar project
php contribution_utilities.php list

# Buat commit bermakna di project yang ada
php contribution_utilities.php commit

# Lihat ide project
php contribution_utilities.php ideas
php contribution_utilities.php ideas web_projects
```

### PowerShell Automation

```powershell
# Berbagai perintah automation
.\github_automation.ps1 -Action commit    # Daily commit
.\github_automation.ps1 -Action stats     # Statistik
.\github_automation.ps1 -Action setup     # Setup awal
.\github_automation.ps1 -Action schedule  # Setup scheduled task
.\github_automation.ps1 -Action help      # Bantuan
```

## 📊 Dashboard dan Monitoring

### Melihat Statistik Kontribusi

```bash
php daily_commit.php stats
```

Output:
```
=== GitHub Contribution Stats ===
📅 Start Date: 2024-01-01
📊 Total Commits: 45
🔥 Current Streak: 15 hari
📆 Last Commit: 2024-01-15
📈 Consistency Rate: 95.5%
```

### Activity Dashboard

```bash
php github_activity_tracker.php dashboard
```

Output:
```
🎯 GitHub Activity Dashboard
============================
📊 Total Statistics:
   • Commits: 45
   • Issues: 8
   • Pull Requests: 12
   • Repositories: 3
   • Forks: 2
   • Current Streak: 15 hari

📅 Hari Ini (2024-01-15):
   • commit: Daily progress update
   • issue: Add feature request

📈 Minggu Ini:
   Total aktivitas: 28
   🟢 Mon (2024-01-10): 4 aktivitas
   🟢 Tue (2024-01-11): 3 aktivitas
   🟢 Wed (2024-01-12): 5 aktivitas
   ...
```

## ⏰ Automation Setup

### Windows Task Scheduler

Script akan otomatis membuat scheduled task yang berjalan setiap hari jam 09:00:

```powershell
.\github_automation.ps1 -Action schedule
```

### Manual Scheduling

Anda juga bisa setup manual melalui Task Scheduler Windows:

1. Buka Task Scheduler
2. Create Basic Task
3. Set trigger: Daily pada jam yang diinginkan
4. Set action: Start a program
5. Program: `PowerShell.exe`
6. Arguments: `-ExecutionPolicy Bypass -File "C:\path\to\github_automation.ps1" -Action commit`

## 🎨 Kustomisasi

### Mengubah Commit Messages

Edit file `daily_commit.php` pada bagian `generateCommitMessage()`:

```php
private function generateCommitMessage($quote, $progress) {
    $templates = [
        "📈 Daily progress update - {$quote}",
        "🔄 Day {$progress['streak_days']} - {$quote}",
        // Tambahkan template Anda sendiri
    ];
    
    return $templates[array_rand($templates)];
}
```

### Menambah Project Ideas

Edit file `project_ideas.json` atau gunakan:

```bash
php contribution_utilities.php ideas
```

### Mengubah Target Harian

Edit file `github_config.json`:

```json
{
    "target_daily_commits": 2,
    "target_weekly_issues": 3,
    "target_monthly_prs": 6
}
```

## 🔧 Troubleshooting

### Git tidak terdeteksi

```bash
# Pastikan git sudah terinstall dan ada di PATH
git --version

# Jika belum, install Git for Windows
# https://git-scm.com/download/win
```

### PHP tidak terdeteksi

```bash
# Pastikan PHP sudah terinstall (Laragon sudah include PHP)
php --version

# Jika menggunakan XAMPP/WAMP, tambahkan PHP ke PATH
```

### Permission Error di PowerShell

```powershell
# Set execution policy
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Repository belum terhubung ke GitHub

```bash
# Tambahkan remote repository
git remote add origin https://github.com/username/repository.git

# Push ke GitHub
git push -u origin main
```

## 📈 Tips untuk Kontribusi Maksimal

### 1. Konsistensi adalah Kunci

- Jalankan automation setiap hari
- Buat minimal 1 commit per hari
- Variasikan jenis aktivitas (commits, issues, PRs)

### 2. Kontribusi Bermakna

- Buat project nyata, bukan hanya dummy files
- Tulis commit message yang deskriptif
- Dokumentasikan perubahan dengan baik

### 3. Diversifikasi Aktivitas

- Tidak hanya commits, tapi juga issues dan PRs
- Kontribusi ke project open source
- Buat repository publik

### 4. Monitoring dan Evaluasi

- Cek dashboard secara rutin
- Review laporan mingguan
- Adjust strategi berdasarkan data

## 🤝 Contributing

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan MIT License. Lihat file `LICENSE` untuk detail.

## 🙏 Acknowledgments

- Terinspirasi dari kebutuhan untuk menjaga konsistensi kontribusi GitHub
- Menggunakan best practices untuk automation yang bertanggung jawab
- Fokus pada kontribusi yang bermakna, bukan sekedar "gaming the system"

---

**⚠️ Disclaimer**: Tool ini dibuat untuk membantu menjaga konsistensi kontribusi dengan cara yang bermakna. Gunakan dengan bijak dan pastikan kontribusi Anda memberikan nilai nyata.
