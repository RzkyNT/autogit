<?php
/**
 * GitHub Daily Commit Automation Script
 * 
 * Script ini akan melakukan commit otomatis setiap hari untuk menjaga
 * kotak kontribusi GitHub tetap hijau dengan aktivitas yang bermakna.
 */

class GitHubContributionBot {
    private $logFile = 'contribution_log.txt';
    private $progressFile = 'daily_progress.json';
    private $quotesFile = 'daily_quotes.txt';
    
    public function __construct() {
        $this->ensureFilesExist();
    }
    
    /**
     * Memastikan file-file yang diperlukan ada
     */
    private function ensureFilesExist() {
        if (!file_exists($this->logFile)) {
            file_put_contents($this->logFile, "=== GitHub Contribution Log ===\n");
        }
        
        if (!file_exists($this->progressFile)) {
            $initialData = [
                'start_date' => date('Y-m-d'),
                'total_commits' => 0,
                'streak_days' => 0,
                'last_commit_date' => null
            ];
            file_put_contents($this->progressFile, json_encode($initialData, JSON_PRETTY_PRINT));
        }
        
        if (!file_exists($this->quotesFile)) {
            $this->createQuotesFile();
        }
    }
    
    /**
     * Membuat file quotes untuk variasi commit message
     */
    private function createQuotesFile() {
        $quotes = [
            "Konsistensi adalah kunci kesuksesan",
            "Setiap hari adalah kesempatan untuk berkembang",
            "Progress kecil setiap hari menghasilkan perubahan besar",
            "Coding adalah seni, commit adalah karya",
            "Hari ini lebih baik dari kemarin",
            "Terus berkarya, terus berkontribusi",
            "Setiap commit adalah langkah maju",
            "Dedikasi menghasilkan prestasi",
            "Belajar, berkembang, berkontribusi",
            "Passion for coding never stops",
            "Innovation through daily practice",
            "Building the future one commit at a time",
            "Code with purpose, commit with pride",
            "Every line of code matters",
            "Continuous improvement is the goal"
        ];
        
        file_put_contents($this->quotesFile, implode("\n", $quotes));
    }
    
    /**
     * Mendapatkan quote random untuk commit message
     */
    private function getRandomQuote() {
        $quotes = file($this->quotesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $quotes[array_rand($quotes)];
    }
    
    /**
     * Update progress tracking
     */
    private function updateProgress() {
        $progress = json_decode(file_get_contents($this->progressFile), true);
        $today = date('Y-m-d');
        
        $progress['total_commits']++;
        
        // Update streak
        if ($progress['last_commit_date'] === date('Y-m-d', strtotime('-1 day'))) {
            $progress['streak_days']++;
        } elseif ($progress['last_commit_date'] !== $today) {
            $progress['streak_days'] = 1;
        }
        
        $progress['last_commit_date'] = $today;
        
        file_put_contents($this->progressFile, json_encode($progress, JSON_PRETTY_PRINT));
        
        return $progress;
    }
    
    /**
     * Membuat perubahan bermakna pada file log
     */
    private function createMeaningfulChange() {
        $timestamp = date('Y-m-d H:i:s');
        $dayOfYear = date('z') + 1;
        $quote = $this->getRandomQuote();
        
        $logEntry = "\n[$timestamp] Day $dayOfYear - $quote\n";
        $logEntry .= "- Commit otomatis untuk menjaga konsistensi kontribusi\n";
        $logEntry .= "- Status: Active development\n";
        $logEntry .= "- Focus: GitHub contribution automation\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        return $quote;
    }
    
    /**
     * Melakukan commit otomatis
     */
    public function performDailyCommit() {
        try {
            echo "🚀 Memulai daily commit automation...\n";
            
            // Buat perubahan bermakna
            $quote = $this->createMeaningfulChange();
            
            // Update progress
            $progress = $this->updateProgress();
            
            // Git add
            $addResult = shell_exec('git add . 2>&1');
            echo "📁 Files added to staging area\n";
            
            // Buat commit message yang bervariasi
            $commitMessage = $this->generateCommitMessage($quote, $progress);
            
            // Git commit
            $commitCommand = 'git commit -m "' . addslashes($commitMessage) . '" 2>&1';
            $commitResult = shell_exec($commitCommand);
            
            echo "✅ Commit berhasil: $commitMessage\n";
            echo "📊 Total commits: {$progress['total_commits']}\n";
            echo "🔥 Streak: {$progress['streak_days']} hari\n";
            
            return true;
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Generate commit message yang bervariasi
     */
    private function generateCommitMessage($quote, $progress) {
        $templates = [
            "📈 Daily progress update - {$quote}",
            "🔄 Day {$progress['streak_days']} - {$quote}",
            "✨ Continuous improvement - {$quote}",
            "🎯 Daily commitment - {$quote}",
            "🚀 Keep building - {$quote}",
            "💪 Consistency matters - {$quote}",
            "🌟 Daily contribution - {$quote}"
        ];
        
        return $templates[array_rand($templates)];
    }
    
    /**
     * Menampilkan statistik kontribusi
     */
    public function showStats() {
        $progress = json_decode(file_get_contents($this->progressFile), true);
        
        echo "\n=== GitHub Contribution Stats ===\n";
        echo "📅 Start Date: {$progress['start_date']}\n";
        echo "📊 Total Commits: {$progress['total_commits']}\n";
        echo "🔥 Current Streak: {$progress['streak_days']} hari\n";
        echo "📆 Last Commit: {$progress['last_commit_date']}\n";
        
        $startDate = new DateTime($progress['start_date']);
        $today = new DateTime();
        $totalDays = $startDate->diff($today)->days + 1;
        $percentage = round(($progress['total_commits'] / $totalDays) * 100, 1);
        
        echo "📈 Consistency Rate: $percentage%\n";
        echo "================================\n";
    }
}

// Eksekusi script
if (php_sapi_name() === 'cli') {
    $bot = new GitHubContributionBot();
    
    // Cek argumen command line
    $action = $argv[1] ?? 'commit';
    
    switch ($action) {
        case 'commit':
            $bot->performDailyCommit();
            break;
        case 'stats':
            $bot->showStats();
            break;
        default:
            echo "Usage: php daily_commit.php [commit|stats]\n";
            break;
    }
} else {
    echo "Script ini harus dijalankan melalui command line\n";
}
?>
