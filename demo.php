<?php
/**
 * Demo Script - GitHub Contribution Automation
 * 
 * Script demo untuk menunjukkan semua fitur sistem automation
 */

echo "🎬 GitHub Contribution Automation - DEMO\n";
echo "=========================================\n\n";

// Include semua class yang diperlukan
require_once 'daily_commit.php';
require_once 'github_activity_tracker.php';
require_once 'contribution_utilities.php';

function waitForUser($message = "Tekan Enter untuk melanjutkan...") {
    echo "\n💡 $message";
    fgets(STDIN);
    echo "\n";
}

function separator($title) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🎯 $title\n";
    echo str_repeat("=", 50) . "\n\n";
}

// Demo 1: Daily Commit System
separator("DEMO 1: Daily Commit Automation");

echo "Sistem ini akan melakukan commit otomatis setiap hari dengan:\n";
echo "• Pesan commit yang bervariasi\n";
echo "• Perubahan yang bermakna pada file log\n";
echo "• Tracking progress dan streak\n\n";

$bot = new GitHubContributionBot();

echo "📊 Statistik saat ini:\n";
$bot->showStats();

waitForUser("Jalankan daily commit automation?");

echo "🚀 Menjalankan daily commit...\n";
$bot->performDailyCommit();

echo "\n📊 Statistik setelah commit:\n";
$bot->showStats();

waitForUser();

// Demo 2: Activity Tracking
separator("DEMO 2: Activity Tracking System");

echo "Sistem tracking dapat mencatat berbagai aktivitas GitHub:\n";
echo "• Commits\n";
echo "• Issues\n";
echo "• Pull Requests\n";
echo "• Repository creation\n";
echo "• Forks\n\n";

$tracker = new GitHubActivityTracker();

echo "📝 Mencatat beberapa aktivitas demo...\n";
$tracker->recordActivity('commit', 'Demo commit untuk testing sistem');
$tracker->recordActivity('issue', 'Buat issue untuk feature request baru');
$tracker->recordActivity('pull_request', 'Merge feature branch ke main');

echo "\n📊 Dashboard aktivitas:\n";
$tracker->showDashboard();

echo "\n🎯 Cek target harian:\n";
$tracker->checkDailyTarget();

waitForUser();

// Demo 3: Project Utilities
separator("DEMO 3: Project Generation Utilities");

echo "Utility ini dapat:\n";
echo "• Membuat project baru dari template\n";
echo "• Generate struktur file otomatis\n";
echo "• Menyediakan ide project\n";
echo "• Membuat commit bermakna\n\n";

$utils = new ContributionUtilities();

echo "💡 Beberapa ide project yang tersedia:\n";
$utils->showProjectIdeas('web_projects');

waitForUser("Buat project demo?");

echo "🏗️ Membuat project demo...\n";
$projectPath = $utils->createNewProject('Demo Calculator', 'web');

if ($projectPath) {
    echo "\n📁 Project berhasil dibuat di: $projectPath\n";
    
    waitForUser("Buat commit bermakna untuk project ini?");
    
    echo "📝 Membuat perubahan bermakna...\n";
    $changeDescription = $utils->createMeaningfulCommit($projectPath);
    
    if ($changeDescription) {
        echo "✅ Perubahan berhasil: $changeDescription\n";
    }
}

echo "\n📋 Daftar semua project:\n";
$utils->listProjects();

waitForUser();

// Demo 4: Integration Test
separator("DEMO 4: Integration Test");

echo "Test integrasi semua komponen:\n\n";

echo "1️⃣ Record aktivitas commit ke tracker...\n";
$tracker->recordActivity('commit', 'Integration test commit', 'autogit', null);

echo "\n2️⃣ Update progress di daily commit system...\n";
$bot->performDailyCommit();

echo "\n3️⃣ Buat project baru...\n";
$utils->createNewProject('Integration Test Project', 'php');

echo "\n4️⃣ Dashboard terintegrasi:\n";
$tracker->showDashboard();

waitForUser();

// Demo 5: Reporting
separator("DEMO 5: Reporting System");

echo "Sistem dapat generate berbagai laporan:\n\n";

echo "📋 Generate laporan mingguan...\n";
$reportFile = $tracker->generateWeeklyReport();

echo "\n📊 Statistik lengkap:\n";
$bot->showStats();

echo "\n🎯 Target dan pencapaian:\n";
$tracker->checkDailyTarget();

waitForUser();

// Demo 6: Automation Commands
separator("DEMO 6: Command Line Usage");

echo "Sistem dapat digunakan melalui command line:\n\n";

echo "📝 Contoh penggunaan daily commit:\n";
echo "   php daily_commit.php commit\n";
echo "   php daily_commit.php stats\n\n";

echo "📊 Contoh penggunaan activity tracker:\n";
echo "   php github_activity_tracker.php dashboard\n";
echo "   php github_activity_tracker.php record commit \"Fix bug\"\n";
echo "   php github_activity_tracker.php report\n\n";

echo "🛠️ Contoh penggunaan project utilities:\n";
echo "   php contribution_utilities.php create \"My App\" web\n";
echo "   php contribution_utilities.php list\n";
echo "   php contribution_utilities.php commit\n\n";

echo "⚡ PowerShell automation:\n";
echo "   .\\github_automation.ps1 -Action commit\n";
echo "   .\\github_automation.ps1 -Action stats\n";
echo "   .\\github_automation.ps1 -Action schedule\n\n";

waitForUser();

// Summary
separator("DEMO SELESAI - SUMMARY");

echo "🎉 Demo GitHub Contribution Automation selesai!\n\n";

echo "✅ Yang telah didemonstrasikan:\n";
echo "   • Daily commit automation dengan pesan bervariasi\n";
echo "   • Activity tracking untuk berbagai jenis kontribusi\n";
echo "   • Project generation dengan template otomatis\n";
echo "   • Integration antar komponen\n";
echo "   • Reporting dan monitoring\n";
echo "   • Command line interface\n\n";

echo "📁 File yang dibuat selama demo:\n";
echo "   • contribution_log.txt - Log commit harian\n";
echo "   • daily_progress.json - Progress tracking\n";
echo "   • github_activities.json - Data aktivitas\n";
echo "   • github_config.json - Konfigurasi sistem\n";
echo "   • projects/ - Direktori project yang dibuat\n";
echo "   • weekly_report_*.txt - Laporan mingguan\n\n";

echo "🚀 Langkah selanjutnya:\n";
echo "   1. Setup scheduled task untuk automation harian\n";
echo "   2. Konfigurasi repository GitHub\n";
echo "   3. Customize quotes dan project ideas\n";
echo "   4. Monitor contribution graph di GitHub\n\n";

echo "📚 Dokumentasi lengkap tersedia di:\n";
echo "   • README.md - Panduan utama\n";
echo "   • SETUP_GUIDE.md - Panduan setup detail\n\n";

echo "🎯 Sistem siap digunakan untuk menjaga kontribusi GitHub tetap hijau!\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 DEMO SELESAI\n";
echo str_repeat("=", 50) . "\n";
?>
