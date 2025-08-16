<?php
/**
 * Efficient Commit Bot
 * 
 * Sistem commit yang efisien - hanya menggunakan beberapa file yang diupdate
 * daripada membuat ribuan file baru
 */

class EfficientCommitBot {
    private $targetCommits = 1000;
    private $batchSize = 50;
    private $dataDir = 'efficient_data';
    
    // File utama yang akan diupdate
    private $mainFiles = [
        'commit_log.txt',
        'activity_data.csv', 
        'progress_tracker.md',
        'counter.json',
        'timestamps.log'
    ];
    
    public function __construct($targetCommits = 1000, $batchSize = 50) {
        $this->targetCommits = $targetCommits;
        $this->batchSize = $batchSize;
        
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        $this->initializeFiles();
    }
    
    /**
     * Initialize main files
     */
    private function initializeFiles() {
        // Initialize commit log
        $logFile = $this->dataDir . '/commit_log.txt';
        if (!file_exists($logFile)) {
            $header = "=== COMMIT LOG ===\n";
            $header .= "Started: " . date('Y-m-d H:i:s') . "\n";
            $header .= "Target: {$this->targetCommits} commits\n";
            $header .= "==================\n\n";
            file_put_contents($logFile, $header);
        }
        
        // Initialize CSV data
        $csvFile = $this->dataDir . '/activity_data.csv';
        if (!file_exists($csvFile)) {
            $header = "commit_number,timestamp,batch,random_value,status\n";
            file_put_contents($csvFile, $header);
        }
        
        // Initialize progress tracker
        $progressFile = $this->dataDir . '/progress_tracker.md';
        if (!file_exists($progressFile)) {
            $header = "# Commit Progress Tracker\n\n";
            $header .= "Started: " . date('Y-m-d H:i:s') . "\n";
            $header .= "Target: {$this->targetCommits} commits\n\n";
            $header .= "## Progress Log\n\n";
            file_put_contents($progressFile, $header);
        }
        
        // Initialize counter
        $counterFile = $this->dataDir . '/counter.json';
        if (!file_exists($counterFile)) {
            $counter = [
                'total_commits' => 0,
                'start_time' => date('Y-m-d H:i:s'),
                'target' => $this->targetCommits,
                'last_update' => null
            ];
            file_put_contents($counterFile, json_encode($counter, JSON_PRETTY_PRINT));
        }
        
        // Initialize timestamps
        $timestampFile = $this->dataDir . '/timestamps.log';
        if (!file_exists($timestampFile)) {
            $header = "=== TIMESTAMP LOG ===\n";
            $header .= "Format: [YYYY-MM-DD HH:MM:SS] Commit #N - Message\n";
            $header .= "=====================\n\n";
            file_put_contents($timestampFile, $header);
        }
    }
    
    /**
     * Run efficient commits
     */
    public function runEfficientCommits() {
        $startTime = time();
        
        echo "🚀 EFFICIENT COMMIT BOT STARTED!\n";
        echo "🎯 Target: {$this->targetCommits} commits\n";
        echo "📦 Batch size: {$this->batchSize} commits per push\n";
        echo "📁 Using only " . count($this->mainFiles) . " files (efficient!)\n";
        echo "⏰ Started: " . date('Y-m-d H:i:s') . "\n\n";
        
        $totalBatches = ceil($this->targetCommits / $this->batchSize);
        $completedCommits = 0;
        
        for ($batch = 1; $batch <= $totalBatches; $batch++) {
            $commitsInThisBatch = min($this->batchSize, $this->targetCommits - $completedCommits);
            
            echo "📦 BATCH $batch/$totalBatches - $commitsInThisBatch commits\n";
            
            for ($i = 1; $i <= $commitsInThisBatch; $i++) {
                $commitNumber = $completedCommits + $i;
                $this->updateFiles($commitNumber, $batch);
                $this->performCommit($commitNumber);
                
                // Progress indicator
                if ($commitNumber % 100 == 0 || $commitNumber == $this->targetCommits) {
                    $progress = round($commitNumber / $this->targetCommits * 100, 1);
                    $elapsed = time() - $startTime;
                    $rate = round($commitNumber / max($elapsed, 1), 1);
                    echo "📊 $commitNumber/{$this->targetCommits} ($progress%) - Rate: $rate commits/sec\n";
                }
            }
            
            $completedCommits += $commitsInThisBatch;
            
            // Push batch
            echo "📤 Pushing batch $batch...\n";
            $this->pushToGitHub();
            
            if ($batch < $totalBatches) {
                echo "⏸️ Batch delay 5s...\n\n";
                sleep(5);
            }
        }
        
        $endTime = time();
        $duration = $endTime - $startTime;
        $avgRate = round($this->targetCommits / max($duration, 1), 1);
        
        echo "\n🎉 EFFICIENT COMMIT COMPLETED!\n";
        echo "✅ Total commits: {$this->targetCommits}\n";
        echo "⏱️ Duration: " . gmdate('H:i:s', $duration) . "\n";
        echo "📈 Average rate: $avgRate commits/sec\n";
        echo "📁 Files used: " . count($this->mainFiles) . " (vs {$this->targetCommits} individual files)\n";
        echo "💾 Space saved: " . round((($this->targetCommits - count($this->mainFiles)) / $this->targetCommits) * 100, 1) . "%\n";
        
        return true;
    }
    
    /**
     * Update all main files with new data
     */
    private function updateFiles($commitNumber, $batch) {
        $timestamp = date('Y-m-d H:i:s');
        $randomValue = rand(10000, 99999);
        
        // Update commit log
        $logEntry = "[$timestamp] Commit #$commitNumber - Batch $batch - Random: $randomValue\n";
        file_put_contents($this->dataDir . '/commit_log.txt', $logEntry, FILE_APPEND);
        
        // Update CSV data
        $csvEntry = "$commitNumber,$timestamp,$batch,$randomValue,completed\n";
        file_put_contents($this->dataDir . '/activity_data.csv', $csvEntry, FILE_APPEND);
        
        // Update progress tracker
        $progressEntry = "- [x] Commit #$commitNumber completed at " . date('H:i:s') . " (Batch $batch)\n";
        file_put_contents($this->dataDir . '/progress_tracker.md', $progressEntry, FILE_APPEND);
        
        // Update counter
        $counterFile = $this->dataDir . '/counter.json';
        $counter = json_decode(file_get_contents($counterFile), true);
        $counter['total_commits'] = $commitNumber;
        $counter['last_update'] = $timestamp;
        $counter['current_batch'] = $batch;
        $counter['progress_percent'] = round($commitNumber / $this->targetCommits * 100, 2);
        file_put_contents($counterFile, json_encode($counter, JSON_PRETTY_PRINT));
        
        // Update timestamps
        $timestampEntry = "[$timestamp] Commit #$commitNumber - Efficient update (Batch $batch)\n";
        file_put_contents($this->dataDir . '/timestamps.log', $timestampEntry, FILE_APPEND);
    }
    
    /**
     * Perform git commit
     */
    private function performCommit($commitNumber) {
        shell_exec('git add . 2>nul');
        $message = "Efficient commit #$commitNumber - Updated " . count($this->mainFiles) . " files";
        shell_exec('git commit -m "' . addslashes($message) . '" 2>nul');
    }
    
    /**
     * Push to GitHub
     */
    private function pushToGitHub() {
        $branch = trim(shell_exec('git branch --show-current 2>nul'));
        if (empty($branch)) {
            $branch = 'master';
        }
        
        $result = shell_exec("git push origin $branch 2>&1");
        
        if (strpos($result, 'error') === false && strpos($result, 'fatal') === false) {
            echo "✅ Pushed to GitHub ($branch)\n";
        } else {
            echo "⚠️ Push warning: " . trim($result) . "\n";
        }
    }
    
    /**
     * Show file statistics
     */
    public function showFileStats() {
        echo "\n📊 File Statistics:\n";
        echo "==================\n";
        
        foreach ($this->mainFiles as $file) {
            $filePath = $this->dataDir . '/' . $file;
            if (file_exists($filePath)) {
                $size = filesize($filePath);
                $lines = count(file($filePath));
                echo "📄 $file: " . number_format($size) . " bytes, $lines lines\n";
            }
        }
        
        $totalSize = 0;
        foreach (glob($this->dataDir . '/*') as $file) {
            $totalSize += filesize($file);
        }
        
        echo "\n💾 Total size: " . number_format($totalSize) . " bytes (" . round($totalSize/1024, 1) . " KB)\n";
        echo "📁 Total files: " . count($this->mainFiles) . "\n";
        echo "🎯 Efficiency: Using " . count($this->mainFiles) . " files instead of {$this->targetCommits} individual files\n";
        echo "==================\n";
    }
    
    /**
     * Clean old data (optional)
     */
    public function cleanOldData() {
        echo "🧹 Cleaning old data...\n";
        
        foreach ($this->mainFiles as $file) {
            $filePath = $this->dataDir . '/' . $file;
            if (file_exists($filePath)) {
                unlink($filePath);
                echo "🗑️ Removed: $file\n";
            }
        }
        
        $this->initializeFiles();
        echo "✅ Data cleaned and reinitialized\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $targetCommits = isset($argv[1]) ? (int)$argv[1] : 1000;
    $batchSize = isset($argv[2]) ? (int)$argv[2] : 50;
    $action = isset($argv[3]) ? $argv[3] : 'run';
    
    if ($targetCommits <= 0) {
        echo "❌ Target commits must be greater than 0\n";
        echo "Usage: php efficient_commit_bot.php [commits] [batch_size] [action]\n";
        echo "Actions: run, stats, clean\n";
        echo "Examples:\n";
        echo "  php efficient_commit_bot.php 1000\n";
        echo "  php efficient_commit_bot.php 5000 100\n";
        echo "  php efficient_commit_bot.php 1000 50 stats\n";
        exit(1);
    }
    
    $bot = new EfficientCommitBot($targetCommits, $batchSize);
    
    switch ($action) {
        case 'run':
            if ($targetCommits > 5000) {
                echo "⚠️ WARNING: $targetCommits commits is a large number!\n";
                echo "Estimated time: ~" . round($targetCommits / 600, 1) . " minutes\n";
                echo "Continue? (y/N): ";
                $confirm = trim(fgets(STDIN));
                if (strtolower($confirm) !== 'y') {
                    echo "Cancelled.\n";
                    exit(0);
                }
            }
            
            $bot->runEfficientCommits();
            $bot->showFileStats();
            break;
            
        case 'stats':
            $bot->showFileStats();
            break;
            
        case 'clean':
            $bot->cleanOldData();
            break;
            
        default:
            echo "Unknown action: $action\n";
            echo "Available actions: run, stats, clean\n";
            break;
    }
}
?>
