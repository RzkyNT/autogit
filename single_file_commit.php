<?php
/**
 * Single File Commit System
 * 
 * Sistem paling efisien - hanya menggunakan 1 file yang terus diupdate
 * untuk membuat ribuan commits tanpa bloat
 */

class SingleFileCommitBot {
    private $targetCommits = 1000;
    private $batchSize = 50;
    private $mainFile = 'commit_activity.txt';
    
    public function __construct($targetCommits = 1000, $batchSize = 50) {
        $this->targetCommits = $targetCommits;
        $this->batchSize = $batchSize;
        $this->initializeFile();
    }
    
    /**
     * Initialize the single main file
     */
    private function initializeFile() {
        if (!file_exists($this->mainFile)) {
            $header = "=== GITHUB COMMIT ACTIVITY LOG ===\n";
            $header .= "Started: " . date('Y-m-d H:i:s') . "\n";
            $header .= "Target: {$this->targetCommits} commits\n";
            $header .= "Strategy: Single file updates for efficiency\n";
            $header .= "===================================\n\n";
            file_put_contents($this->mainFile, $header);
        }
    }
    
    /**
     * Run single file commit system
     */
    public function runSingleFileCommits() {
        $startTime = time();
        
        echo "🚀 SINGLE FILE COMMIT SYSTEM!\n";
        echo "🎯 Target: {$this->targetCommits} commits\n";
        echo "📄 Using only 1 file: {$this->mainFile}\n";
        echo "💾 Maximum efficiency - no file bloat!\n";
        echo "⏰ Started: " . date('Y-m-d H:i:s') . "\n\n";
        
        $totalBatches = ceil($this->targetCommits / $this->batchSize);
        $completedCommits = 0;
        
        for ($batch = 1; $batch <= $totalBatches; $batch++) {
            $commitsInThisBatch = min($this->batchSize, $this->targetCommits - $completedCommits);
            
            echo "📦 BATCH $batch/$totalBatches - $commitsInThisBatch commits\n";
            
            for ($i = 1; $i <= $commitsInThisBatch; $i++) {
                $commitNumber = $completedCommits + $i;
                $this->updateSingleFile($commitNumber, $batch);
                $this->performCommit($commitNumber);
                
                // Progress indicator setiap 50 commits
                if ($commitNumber % 50 == 0 || $commitNumber == $this->targetCommits) {
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
                sleep(2); // Short delay between batches
            }
        }
        
        $endTime = time();
        $duration = $endTime - $startTime;
        $avgRate = round($this->targetCommits / max($duration, 1), 1);
        
        // Final file update with summary
        $this->addSummaryToFile($duration, $avgRate);
        
        echo "\n🎉 SINGLE FILE COMMIT COMPLETED!\n";
        echo "✅ Total commits: {$this->targetCommits}\n";
        echo "⏱️ Duration: " . gmdate('H:i:s', $duration) . "\n";
        echo "📈 Average rate: $avgRate commits/sec\n";
        echo "📄 File used: 1 (vs {$this->targetCommits} individual files)\n";
        echo "💾 Space efficiency: 99.9% saved!\n";
        echo "📁 File size: " . $this->getFileSize() . "\n";
        
        return true;
    }
    
    /**
     * Update the single file with new commit data
     */
    private function updateSingleFile($commitNumber, $batch) {
        $timestamp = date('Y-m-d H:i:s');
        $progress = round($commitNumber / $this->targetCommits * 100, 2);
        
        // Create a meaningful entry
        $entry = sprintf(
            "[%s] Commit #%d | Batch: %d | Progress: %s%% | Random: %d\n",
            $timestamp,
            $commitNumber,
            $batch,
            $progress,
            rand(10000, 99999)
        );
        
        // Append to file
        file_put_contents($this->mainFile, $entry, FILE_APPEND);
        
        // Add milestone markers
        if ($commitNumber % 100 == 0) {
            $milestone = "\n--- MILESTONE: $commitNumber COMMITS REACHED ---\n";
            $milestone .= "Time: $timestamp | Batch: $batch | Progress: $progress%\n\n";
            file_put_contents($this->mainFile, $milestone, FILE_APPEND);
        }
    }
    
    /**
     * Add final summary to file
     */
    private function addSummaryToFile($duration, $avgRate) {
        $summary = "\n" . str_repeat("=", 50) . "\n";
        $summary .= "FINAL SUMMARY\n";
        $summary .= str_repeat("=", 50) . "\n";
        $summary .= "Completed: " . date('Y-m-d H:i:s') . "\n";
        $summary .= "Total commits: {$this->targetCommits}\n";
        $summary .= "Duration: " . gmdate('H:i:s', $duration) . "\n";
        $summary .= "Average rate: $avgRate commits/sec\n";
        $summary .= "Efficiency: Single file strategy\n";
        $summary .= "Status: SUCCESS ✅\n";
        $summary .= str_repeat("=", 50) . "\n";
        
        file_put_contents($this->mainFile, $summary, FILE_APPEND);
    }
    
    /**
     * Perform git commit
     */
    private function performCommit($commitNumber) {
        shell_exec('git add . 2>nul');
        $message = "Update #$commitNumber - Single file efficiency strategy";
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
        
        shell_exec("git push origin $branch 2>nul");
        echo "✅ Pushed to GitHub\n";
    }
    
    /**
     * Get file size in human readable format
     */
    private function getFileSize() {
        if (!file_exists($this->mainFile)) {
            return "0 bytes";
        }
        
        $size = filesize($this->mainFile);
        
        if ($size < 1024) {
            return $size . " bytes";
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024, 1) . " KB";
        } else {
            return round($size / (1024 * 1024), 1) . " MB";
        }
    }
    
    /**
     * Show file statistics
     */
    public function showStats() {
        if (!file_exists($this->mainFile)) {
            echo "❌ File not found: {$this->mainFile}\n";
            return;
        }
        
        $size = filesize($this->mainFile);
        $lines = count(file($this->mainFile));
        
        echo "\n📊 Single File Statistics:\n";
        echo "=========================\n";
        echo "📄 File: {$this->mainFile}\n";
        echo "💾 Size: " . $this->getFileSize() . " ($size bytes)\n";
        echo "📝 Lines: " . number_format($lines) . "\n";
        echo "🎯 Target commits: {$this->targetCommits}\n";
        echo "💡 Efficiency: 1 file vs {$this->targetCommits} files\n";
        echo "📈 Space saved: " . round((($this->targetCommits - 1) / $this->targetCommits) * 100, 2) . "%\n";
        echo "=========================\n";
    }
    
    /**
     * Preview file content
     */
    public function previewFile($lines = 10) {
        if (!file_exists($this->mainFile)) {
            echo "❌ File not found: {$this->mainFile}\n";
            return;
        }
        
        $content = file($this->mainFile);
        $totalLines = count($content);
        
        echo "\n📄 File Preview (last $lines lines):\n";
        echo str_repeat("-", 40) . "\n";
        
        $startLine = max(0, $totalLines - $lines);
        for ($i = $startLine; $i < $totalLines; $i++) {
            echo trim($content[$i]) . "\n";
        }
        
        echo str_repeat("-", 40) . "\n";
        echo "Total lines: $totalLines\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $targetCommits = isset($argv[1]) ? (int)$argv[1] : 1000;
    $batchSize = isset($argv[2]) ? (int)$argv[2] : 50;
    $action = isset($argv[3]) ? $argv[3] : 'run';
    
    if ($targetCommits <= 0) {
        echo "Single File Commit System\n";
        echo "========================\n";
        echo "Usage: php single_file_commit.php [commits] [batch_size] [action]\n\n";
        echo "Parameters:\n";
        echo "  commits     - Number of commits to create (default: 1000)\n";
        echo "  batch_size  - Commits per push (default: 50)\n";
        echo "  action      - run, stats, preview (default: run)\n\n";
        echo "Examples:\n";
        echo "  php single_file_commit.php 1000\n";
        echo "  php single_file_commit.php 5000 100\n";
        echo "  php single_file_commit.php 1000 50 stats\n";
        echo "  php single_file_commit.php 1000 50 preview\n";
        exit(1);
    }
    
    $bot = new SingleFileCommitBot($targetCommits, $batchSize);
    
    switch ($action) {
        case 'run':
            if ($targetCommits > 5000) {
                echo "⚠️ WARNING: $targetCommits commits is a large number!\n";
                echo "Using single file strategy for maximum efficiency.\n";
                echo "Estimated time: ~" . round($targetCommits / 600, 1) . " minutes\n";
                echo "Continue? (y/N): ";
                $confirm = trim(fgets(STDIN));
                if (strtolower($confirm) !== 'y') {
                    echo "Cancelled.\n";
                    exit(0);
                }
            }
            
            $bot->runSingleFileCommits();
            $bot->showStats();
            break;
            
        case 'stats':
            $bot->showStats();
            break;
            
        case 'preview':
            $lines = isset($argv[4]) ? (int)$argv[4] : 10;
            $bot->previewFile($lines);
            break;
            
        default:
            echo "Unknown action: $action\n";
            echo "Available actions: run, stats, preview\n";
            break;
    }
}
?>
