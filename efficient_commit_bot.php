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
        $this->checkGitAvailability();
    }
    
    /**
     * Check if Git is available and repository is initialized
     */
    private function checkGitAvailability() {
        $gitVersion = shell_exec('git --version 2>&1');
        if ($gitVersion === null || strpos($gitVersion, 'git version') === false) {
            echo "❌ Error: Git is not installed or not found in PATH. Please ensure Git is installed and accessible.\n";
            exit(1);
        }

        // Check if inside a Git repository
        $gitDir = shell_exec('git rev-parse --is-inside-work-tree 2>&1');
        if ($gitDir === null || trim($gitDir) !== 'true') {
            echo "❌ Error: Current directory is not a Git repository. Please initialize a repository with 'git init' and set up the remote.\n";
            exit(1);
        }

        // Check remote configuration
        $remote = shell_exec('git remote -v 2>&1');
        if (empty($remote) || strpos($remote, 'origin') === false) {
            echo "❌ Error: No remote repository configured. Please set up a remote with 'git remote add origin <url>'.\n";
            exit(1);
        }

        // Check for 'nul' in Git index
        $indexFiles = shell_exec('git ls-files 2>&1');
        if ($indexFiles !== null && strpos($indexFiles, 'nul') !== false) {
            echo "❌ Error: File named 'nul' found in repository. Remove it with 'git rm nul' and commit.\n";
            exit(1);
        }
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
     * Pull latest changes from GitHub
     */
    private function pullFromGitHub() {
        $branch = shell_exec('git branch --show-current 2>&1');
        $branch = $branch !== null ? trim($branch) : '';
        if (empty($branch)) {
            $branch = 'master';
            echo "⚠️ Could not determine current branch. Defaulting to 'master'.\n";
        }
        
        echo "⬇️ Pulling latest changes from GitHub (branch: $branch)...\n";
        
        // Check git status
        $status = shell_exec('git status --porcelain 2>&1');
        if ($status === null || strpos($status, 'error') !== false || strpos($status, 'fatal') !== false) {
            echo "❌ Error: Failed to check git status: " . ($status !== null ? trim($status) : 'Command returned null') . "\n";
            return false;
        }
        if (!empty(trim($status))) {
            echo "📌 Committing local changes before pull...\n";
            $addResult = shell_exec('git add . 2>&1');
            if ($addResult !== null && strpos($addResult, 'error') === false && strpos($addResult, 'fatal') === false) {
                $commitResult = shell_exec('git commit -m "Auto-commit before pull" 2>&1');
                if (strpos($commitResult, 'nothing to commit') !== false) {
                    echo "⚠️ No changes to commit\n";
                } elseif (strpos($commitResult, 'error') !== false || strpos($commitResult, 'fatal') !== false) {
                    echo "⚠️ Commit failed: " . trim($commitResult) . "\n";
                    echo "📌 Stashing local changes instead...\n";
                    $stashResult = shell_exec('git stash push -m "Auto-stash before pull" 2>&1');
                    if (strpos($stashResult, 'error') !== false || strpos($stashResult, 'fatal') !== false) {
                        echo "⚠️ Stash failed: " . trim($stashResult) . "\n";
                    }
                }
            } else {
                echo "⚠️ Add failed: " . trim($addResult) . "\n";
                echo "📌 Stashing local changes instead...\n";
                $stashResult = shell_exec('git stash push -m "Auto-stash before pull" 2>&1');
                if (strpos($stashResult, 'error') !== false || strpos($stashResult, 'fatal') !== false) {
                    echo "⚠️ Stash failed: " . trim($stashResult) . "\n";
                }
            }
        }
        
        // Lakukan git pull
        $result = shell_exec("git pull --rebase origin $branch 2>&1");
        
        if (strpos($result, 'error') === false && strpos($result, 'fatal') === false) {
            echo "✅ Successfully pulled from GitHub\n";
            // Kembalikan perubahan yang di-stash jika ada
            if (!empty(trim($status))) {
                echo "📌 Popping stashed changes...\n";
                $stashResult = shell_exec('git stash pop 2>&1');
                if (strpos($stashResult, 'conflict') !== false) {
                    echo "⚠️ Conflict detected when popping stash. Resetting to remote state...\n";
                    $resetResult = shell_exec('git reset --hard origin/' . $branch . ' 2>&1');
                    if (strpos($resetResult, 'error') !== false || strpos($resetResult, 'fatal') !== false) {
                        echo "⚠️ Reset failed: " . trim($resetResult) . "\n";
                    }
                    shell_exec('git stash drop 2>&1');
                }
            }
            return true;
        } else {
            echo "⚠️ Pull warning: " . trim($result) . "\n";
            if (strpos($result, 'invalid path \'nul\'')) {
                echo "❌ Detected 'nul' error. Please check for problematic commits in the remote history with 'git log origin/master --name-only | findstr /i \"nul\"'.\n";
            }
            echo "🔄 Resetting to remote state...\n";
            $fetchResult = shell_exec('git fetch origin 2>&1');
            $resetResult = shell_exec('git reset --hard origin/' . $branch . ' 2>&1');
            if (strpos($resetResult, 'error') !== false || strpos($resetResult, 'fatal') !== false) {
                echo "⚠️ Reset failed: " . trim($resetResult) . "\n";
                if (strpos($resetResult, 'invalid path \'nul\'')) {
                    echo "❌ Detected 'nul' error. Please check for problematic commits in the remote history with 'git log origin/master --name-only | findstr /i \"nul\"'.\n";
                }
            }
            if (!empty(trim($status))) {
                echo "⚠️ Keeping stashed changes due to pull failure\n";
            }
            return false;
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
        
        // Pull from GitHub before starting commits
        $this->pullFromGitHub();
        
        $totalBatches = ceil($this->targetCommits / $this->batchSize);
        $completedCommits = 0;
        $successfulCommits = 0;
        
        for ($batch = 1; $batch <= $totalBatches; $batch++) {
            $commitsInThisBatch = min($this->batchSize, $this->targetCommits - $completedCommits);
            
            echo "📦 BATCH $batch/$totalBatches - $commitsInThisBatch commits\n";
            
            for ($i = 1; $i <= $commitsInThisBatch; $i++) {
                $commitNumber = $completedCommits + $i;
                $this->updateFiles($commitNumber, $batch);
                if ($this->performCommit($commitNumber)) {
                    $successfulCommits++;
                } else {
                    echo "⚠️ Skipping commit #$commitNumber due to Git error\n";
                }
                
                // Progress indicator
                if ($commitNumber % 100 == 0 || $commitNumber == $this->targetCommits) {
                    $progress = round($commitNumber / $this->targetCommits * 100, 1);
                    $elapsed = time() - $startTime;
                    $rate = round($successfulCommits / max($elapsed, 1), 1);
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
        $avgRate = round($successfulCommits / max($duration, 1), 1);
        
        echo "\n🎉 EFFICIENT COMMIT COMPLETED!\n";
        echo "✅ Total commits attempted: {$this->targetCommits}\n";
        echo "✅ Total commits successful: $successfulCommits\n";
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
        $addResult = shell_exec('git add . 2>&1');
        if ($addResult === null || strpos($addResult, 'error') !== false || strpos($addResult, 'fatal') !== false) {
            echo "❌ Error: Failed to execute 'git add' for commit #$commitNumber: " . ($addResult !== null ? trim($addResult) : 'Command returned null') . "\n";
            if ($addResult !== null && strpos($addResult, 'invalid path \'nul\'')) {
                echo "❌ Detected 'nul' error. Please check for problematic commits in the remote history with 'git log origin/master --name-only | findstr /i \"nul\"'.\n";
            }
            return false;
        }
        $message = "Efficient commit #$commitNumber - Updated " . count($this->mainFiles) . " files";
        $commitResult = shell_exec('git commit -m "' . addslashes($message) . '" 2>&1');
        if (strpos($commitResult, 'nothing to commit') !== false) {
            echo "⚠️ Warning: No changes to commit for #$commitNumber\n";
            return false;
        } elseif (strpos($commitResult, 'error') !== false || strpos($commitResult, 'fatal') !== false) {
            echo "❌ Error: Commit failed for #$commitNumber: " . trim($commitResult) . "\n";
            if (strpos($commitResult, 'invalid path \'nul\'')) {
                echo "❌ Detected 'nul' error. Please check for problematic commits in the remote history with 'git log origin/master --name-only | findstr /i \"nul\"'.\n";
            }
            return false;
        }
        echo "✅ Commit #$commitNumber successful\n";
        return true;
    }
    
    /**
     * Push to GitHub
     */
    private function pushToGitHub() {
        $branch = shell_exec('git branch --show-current 2>&1');
        $branch = $branch !== null ? trim($branch) : '';
        if (empty($branch)) {
            $branch = 'master';
            echo "⚠️ Could not determine current branch. Defaulting to 'master'.\n";
        }
        
        $result = shell_exec("git push origin $branch 2>&1");
        
        if (strpos($result, 'error') === false && strpos($result, 'fatal') === false) {
            echo "✅ Pushed to GitHub ($branch)\n";
        } else {
            echo "⚠️ Push warning: " . trim($result) . "\n";
            if (strpos($result, 'invalid path \'nul\'')) {
                echo "❌ Detected 'nul' error. Please check for problematic commits in the remote history with 'git log origin/master --name-only | findstr /i \"nul\"'.\n";
            }
            echo "🔄 Attempting to resolve non-fast-forward by pulling...\n";
            if ($this->pullFromGitHub()) {
                // Retry push after successful pull
                $retryResult = shell_exec("git push origin $branch 2>&1");
                if (strpos($retryResult, 'error') === false && strpos($retryResult, 'fatal') === false) {
                    echo "✅ Successfully pushed to GitHub after retry ($branch)\n";
                } else {
                    echo "⚠️ Push failed after retry: " . trim($retryResult) . ". Continuing with next batch...\n";
                    if (strpos($retryResult, 'invalid path \'nul\'')) {
                        echo "❌ Detected 'nul' error. Please check for problematic commits in the remote history with 'git log origin/master --name-only | findstr /i \"nul\"'.\n";
                    }
                }
            } else {
                echo "⚠️ Pull failed, skipping push for this batch.\n";
            }
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