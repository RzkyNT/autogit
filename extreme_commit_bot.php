<?php
/**
 * Extreme Commit Bot
 * 
 * Bot untuk membuat commit dalam jumlah yang sangat besar (100-10000+ commits per hari)
 * dengan customizable target dan auto-push batch
 */

class ExtremeCommitBot {
    private $targetCommits = 1000;
    private $batchSize = 50; // Push setiap 50 commits
    private $delayBetweenCommits = 1; // 1 detik antar commit
    private $delayBetweenBatches = 30; // 30 detik antar batch
    private $extremeDir = 'extreme_commits';
    
    public function __construct($targetCommits = 1000, $batchSize = 50) {
        $this->targetCommits = $targetCommits;
        $this->batchSize = $batchSize;
        
        if (!is_dir($this->extremeDir)) {
            mkdir($this->extremeDir, 0755, true);
        }
    }
    
    /**
     * Jalankan extreme commit automation
     */
    public function runExtremeCommits() {
        $startTime = time();
        $today = date('Y-m-d');
        
        echo "🚀 EXTREME COMMIT BOT ACTIVATED!\n";
        echo "🎯 Target: {$this->targetCommits} commits\n";
        echo "📦 Batch size: {$this->batchSize} commits per push\n";
        echo "⏱️ Delay: {$this->delayBetweenCommits}s between commits\n";
        echo "📅 Date: $today\n\n";
        
        $totalBatches = ceil($this->targetCommits / $this->batchSize);
        $completedCommits = 0;
        
        for ($batch = 1; $batch <= $totalBatches; $batch++) {
            $commitsInThisBatch = min($this->batchSize, $this->targetCommits - $completedCommits);
            
            echo "📦 BATCH $batch/$totalBatches - $commitsInThisBatch commits\n";
            echo str_repeat("=", 50) . "\n";
            
            for ($i = 1; $i <= $commitsInThisBatch; $i++) {
                $commitNumber = $completedCommits + $i;
                $this->createExtremeCommit($batch, $i, $commitNumber);
                
                // Progress indicator
                if ($i % 10 == 0 || $i == $commitsInThisBatch) {
                    $progress = round(($completedCommits + $i) / $this->targetCommits * 100, 1);
                    echo "📊 Progress: $progress% ($commitNumber/{$this->targetCommits})\n";
                }
                
                // Delay between commits (kecuali commit terakhir dalam batch)
                if ($i < $commitsInThisBatch) {
                    sleep($this->delayBetweenCommits);
                }
            }
            
            $completedCommits += $commitsInThisBatch;
            
            // Push batch ke GitHub
            echo "📤 Pushing batch $batch to GitHub...\n";
            $this->pushToGitHub();
            
            // Delay between batches (kecuali batch terakhir)
            if ($batch < $totalBatches) {
                echo "⏸️ Batch delay {$this->delayBetweenBatches}s...\n\n";
                sleep($this->delayBetweenBatches);
            }
        }
        
        $endTime = time();
        $duration = $endTime - $startTime;
        $commitsPerMinute = round($this->targetCommits / ($duration / 60), 1);
        
        echo "\n🎉 EXTREME COMMIT COMPLETED!\n";
        echo "✅ Total commits: {$this->targetCommits}\n";
        echo "⏱️ Duration: " . gmdate('H:i:s', $duration) . "\n";
        echo "📈 Speed: $commitsPerMinute commits/minute\n";
        echo "📤 All commits pushed to GitHub!\n";
        
        return true;
    }
    
    /**
     * Buat single extreme commit
     */
    private function createExtremeCommit($batch, $commitInBatch, $totalCommitNumber) {
        $timestamp = date('Y-m-d H:i:s');
        $strategy = rand(1, 8);
        
        switch ($strategy) {
            case 1:
                $this->createMicroFile($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 2:
                $this->createDataEntry($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 3:
                $this->createLogEntry($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 4:
                $this->createConfigUpdate($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 5:
                $this->createProgressUpdate($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 6:
                $this->createCounterFile($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 7:
                $this->createTimestampFile($batch, $commitInBatch, $totalCommitNumber);
                break;
            case 8:
                $this->createRandomData($batch, $commitInBatch, $totalCommitNumber);
                break;
        }
    }
    
    /**
     * Strategy 1: Micro file
     */
    private function createMicroFile($batch, $commit, $total) {
        $filename = $this->extremeDir . "/micro_$total.txt";
        $content = "Commit #$total\nBatch: $batch\nTime: " . date('H:i:s') . "\n";
        file_put_contents($filename, $content);
        
        $this->gitCommit("Add micro file #$total");
    }
    
    /**
     * Strategy 2: Data entry
     */
    private function createDataEntry($batch, $commit, $total) {
        $filename = $this->extremeDir . "/data.csv";
        $entry = "$total,$batch,$commit," . date('Y-m-d H:i:s') . "," . rand(1000, 9999) . "\n";
        file_put_contents($filename, $entry, FILE_APPEND);
        
        $this->gitCommit("Data entry #$total");
    }
    
    /**
     * Strategy 3: Log entry
     */
    private function createLogEntry($batch, $commit, $total) {
        $filename = $this->extremeDir . "/activity.log";
        $entry = "[" . date('Y-m-d H:i:s') . "] Commit #$total - Batch $batch - Activity logged\n";
        file_put_contents($filename, $entry, FILE_APPEND);
        
        $this->gitCommit("Log entry #$total");
    }
    
    /**
     * Strategy 4: Config update
     */
    private function createConfigUpdate($batch, $commit, $total) {
        $filename = $this->extremeDir . "/config_$total.json";
        $config = [
            'commit_number' => $total,
            'batch' => $batch,
            'timestamp' => date('Y-m-d H:i:s'),
            'random' => rand(10000, 99999)
        ];
        file_put_contents($filename, json_encode($config, JSON_PRETTY_PRINT));
        
        $this->gitCommit("Config update #$total");
    }
    
    /**
     * Strategy 5: Progress update
     */
    private function createProgressUpdate($batch, $commit, $total) {
        $filename = $this->extremeDir . "/progress.md";
        $entry = "- [x] Commit #$total completed at " . date('H:i:s') . " (Batch $batch)\n";
        file_put_contents($filename, $entry, FILE_APPEND);
        
        $this->gitCommit("Progress #$total");
    }
    
    /**
     * Strategy 6: Counter file
     */
    private function createCounterFile($batch, $commit, $total) {
        $filename = $this->extremeDir . "/counter.txt";
        file_put_contents($filename, "Count: $total\nLast update: " . date('Y-m-d H:i:s') . "\n");
        
        $this->gitCommit("Counter update #$total");
    }
    
    /**
     * Strategy 7: Timestamp file
     */
    private function createTimestampFile($batch, $commit, $total) {
        $filename = $this->extremeDir . "/timestamps.txt";
        $entry = "Commit #$total: " . date('Y-m-d H:i:s') . "\n";
        file_put_contents($filename, $entry, FILE_APPEND);
        
        $this->gitCommit("Timestamp #$total");
    }
    
    /**
     * Strategy 8: Random data
     */
    private function createRandomData($batch, $commit, $total) {
        $filename = $this->extremeDir . "/random_$total.dat";
        $data = "ID:$total|BATCH:$batch|TIME:" . time() . "|RAND:" . rand(100000, 999999) . "\n";
        file_put_contents($filename, $data);
        
        $this->gitCommit("Random data #$total");
    }
    
    /**
     * Perform git commit (optimized for speed)
     */
    private function gitCommit($message) {
        // Fast git operations
        shell_exec('git add . 2>/dev/null');
        shell_exec('git commit -m "' . addslashes($message) . '" 2>/dev/null');
    }
    
    /**
     * Push to GitHub
     */
    private function pushToGitHub() {
        $currentBranch = trim(shell_exec('git branch --show-current 2>/dev/null'));
        if (empty($currentBranch)) {
            $currentBranch = 'master';
        }
        
        $result = shell_exec("git push origin $currentBranch 2>&1");
        
        if (strpos($result, 'error') === false && strpos($result, 'fatal') === false) {
            echo "✅ Pushed to GitHub ($currentBranch)\n";
        } else {
            echo "⚠️ Push warning: " . trim($result) . "\n";
        }
    }
    
    /**
     * Estimate time untuk completion
     */
    public function estimateTime() {
        $totalTime = ($this->targetCommits * $this->delayBetweenCommits) + 
                    (ceil($this->targetCommits / $this->batchSize) * $this->delayBetweenBatches);
        
        echo "⏱️ Estimated completion time: " . gmdate('H:i:s', $totalTime) . "\n";
        echo "📊 Commits per hour: " . round($this->targetCommits / ($totalTime / 3600), 0) . "\n";
        
        return $totalTime;
    }
    
    /**
     * Set custom parameters
     */
    public function setParameters($targetCommits, $batchSize = 50, $commitDelay = 1, $batchDelay = 30) {
        $this->targetCommits = $targetCommits;
        $this->batchSize = $batchSize;
        $this->delayBetweenCommits = $commitDelay;
        $this->delayBetweenBatches = $batchDelay;
        
        echo "⚙️ Parameters updated:\n";
        echo "   Target commits: $targetCommits\n";
        echo "   Batch size: $batchSize\n";
        echo "   Commit delay: {$commitDelay}s\n";
        echo "   Batch delay: {$batchDelay}s\n";
    }
    
    /**
     * Quick mode (minimal delays)
     */
    public function quickMode() {
        $this->delayBetweenCommits = 0;
        $this->delayBetweenBatches = 5;
        $this->batchSize = 100;
        
        echo "⚡ QUICK MODE ACTIVATED!\n";
        echo "   Commit delay: 0s\n";
        echo "   Batch delay: 5s\n";
        echo "   Batch size: 100 commits\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $targetCommits = isset($argv[1]) ? (int)$argv[1] : 1000;
    $batchSize = isset($argv[2]) ? (int)$argv[2] : 50;
    $mode = isset($argv[3]) ? $argv[3] : 'normal';
    
    if ($targetCommits <= 0) {
        echo "❌ Target commits harus lebih dari 0\n";
        exit(1);
    }
    
    $bot = new ExtremeCommitBot($targetCommits, $batchSize);
    
    if ($mode === 'quick') {
        $bot->quickMode();
    }
    
    echo "🎯 EXTREME COMMIT BOT\n";
    echo "====================\n";
    echo "Target: $targetCommits commits\n";
    echo "Batch size: $batchSize commits per push\n";
    echo "Mode: $mode\n\n";
    
    // Show estimate
    $bot->estimateTime();
    
    echo "\nPress Enter to start or Ctrl+C to cancel...";
    fgets(STDIN);
    
    // Start extreme commits
    $bot->runExtremeCommits();
}
?>
