<?php
/**
 * Multi Commit Automation System
 * 
 * Sistem untuk melakukan 15-40 commit per hari secara otomatis
 * dengan berbagai strategi dan variasi
 */

class MultiCommitAutomation {
    private $targetCommitsMin = 15;
    private $targetCommitsMax = 40;
    private $commitLogFile = 'multi_commit_log.json';
    private $dummyDir = 'multi_dummy';
    
    public function __construct() {
        if (!is_dir($this->dummyDir)) {
            mkdir($this->dummyDir, 0755, true);
        }
        $this->initializeLog();
    }
    
    /**
     * Inisialisasi log file
     */
    private function initializeLog() {
        if (!file_exists($this->commitLogFile)) {
            $initialData = [
                'start_date' => date('Y-m-d'),
                'daily_commits' => [],
                'total_commits' => 0,
                'last_run' => null
            ];
            file_put_contents($this->commitLogFile, json_encode($initialData, JSON_PRETTY_PRINT));
        }
    }
    
    /**
     * Jalankan automation untuk hari ini
     */
    public function runDailyAutomation($autoPush = false, $pushInterval = 5) {
        $today = date('Y-m-d');
        $todayCommits = $this->getTodayCommitCount();
        $targetCommits = rand($this->targetCommitsMin, $this->targetCommitsMax);

        echo "🎯 Target commits hari ini: $targetCommits\n";
        echo "📊 Commits yang sudah ada: $todayCommits\n";
        echo "📤 Auto-push: " . ($autoPush ? "Enabled (every $pushInterval commits)" : "Disabled") . "\n";

        $remainingCommits = $targetCommits - $todayCommits;

        if ($remainingCommits <= 0) {
            echo "✅ Target sudah tercapai untuk hari ini!\n";
            if ($autoPush) {
                $this->performGitPush();
            }
            return true;
        }

        echo "🚀 Perlu $remainingCommits commit lagi\n";

        // Bagi commits ke dalam beberapa batch dengan interval
        $batches = $this->distributeBatches($remainingCommits);
        $commitCounter = 0;

        foreach ($batches as $batchIndex => $batchSize) {
            echo "\n📦 Batch " . ($batchIndex + 1) . ": $batchSize commits\n";

            for ($i = 0; $i < $batchSize; $i++) {
                $this->createSingleCommit($batchIndex + 1, $i + 1, $autoPush && (++$commitCounter % $pushInterval == 0));

                // Delay antar commit dalam batch (1-5 detik)
                if ($i < $batchSize - 1) {
                    $delay = rand(1, 5);
                    echo "⏳ Delay $delay detik...\n";
                    sleep($delay);
                }
            }

            // Push setelah setiap batch jika auto-push enabled
            if ($autoPush && $batchIndex < count($batches) - 1) {
                $this->performGitPush();
            }

            // Delay antar batch (30-120 detik)
            if ($batchIndex < count($batches) - 1) {
                $batchDelay = rand(30, 120);
                echo "⏸️ Delay batch $batchDelay detik...\n";
                sleep($batchDelay);
            }
        }

        // Final push jika belum di-push
        if ($autoPush) {
            $this->performGitPush();
        }

        $this->updateDailyLog($today, $remainingCommits);

        echo "\n🎉 Automation selesai! Total commits hari ini: " . $this->getTodayCommitCount() . "\n";

        return true;
    }
    
    /**
     * Distribusi commits ke dalam batch
     */
    private function distributeBatches($totalCommits) {
        $maxBatchSize = 8; // Maksimal 8 commits per batch
        $batches = [];
        
        while ($totalCommits > 0) {
            $batchSize = min($totalCommits, rand(3, $maxBatchSize));
            $batches[] = $batchSize;
            $totalCommits -= $batchSize;
        }
        
        return $batches;
    }
    
    /**
     * Buat single commit dengan variasi
     */
    private function createSingleCommit($batchNum, $commitNum, $shouldPush = false) {
        $strategy = rand(1, 6);

        switch ($strategy) {
            case 1:
                $this->createDummyFileCommit($batchNum, $commitNum, $shouldPush);
                break;
            case 2:
                $this->createUpdateCommit($batchNum, $commitNum, $shouldPush);
                break;
            case 3:
                $this->createLogCommit($batchNum, $commitNum, $shouldPush);
                break;
            case 4:
                $this->createConfigCommit($batchNum, $commitNum, $shouldPush);
                break;
            case 5:
                $this->createDataCommit($batchNum, $commitNum, $shouldPush);
                break;
            case 6:
                $this->createProgressCommit($batchNum, $commitNum, $shouldPush);
                break;
        }
    }
    
    /**
     * Strategy 1: Dummy file commit
     */
    private function createDummyFileCommit($batch, $commit, $shouldPush = false) {
        $filename = $this->dummyDir . '/dummy_' . date('Ymd_His') . '_' . $batch . '_' . $commit . '.txt';
        $content = "Dummy commit\nBatch: $batch\nCommit: $commit\nTime: " . date('Y-m-d H:i:s') . "\nRandom: " . rand(10000, 99999) . "\n";

        file_put_contents($filename, $content);

        $messages = [
            "Add dummy file batch $batch commit $commit",
            "Create temp file $batch-$commit",
            "Update batch $batch item $commit",
            "Add data file $batch.$commit",
            "Create dummy $batch-$commit"
        ];

        $this->performGitCommit($messages[array_rand($messages)], $shouldPush);
        echo "📄 Dummy file commit: $filename\n";
    }

    /**
     * Strategy 2: Update existing file
     */
    private function createUpdateCommit($batch, $commit, $shouldPush = false) {
        $files = glob($this->dummyDir . '/*.txt');

        if (!empty($files)) {
            $file = $files[array_rand($files)];
            $content = file_get_contents($file);
            $content .= "\nUpdate: " . date('Y-m-d H:i:s') . " Batch $batch Commit $commit\n";
            file_put_contents($file, $content);

            $messages = [
                "Update file batch $batch commit $commit",
                "Modify data $batch-$commit",
                "Update content $batch.$commit",
                "Revise file $batch-$commit",
                "Edit data batch $batch"
            ];

            $this->performGitCommit($messages[array_rand($messages)], $shouldPush);
            echo "📝 Update commit: " . basename($file) . "\n";
        } else {
            $this->createDummyFileCommit($batch, $commit, $shouldPush);
        }
    }
    
    /**
     * Strategy 3: Log commit
     */
    private function createLogCommit($batch, $commit) {
        $logFile = $this->dummyDir . '/activity.log';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] Batch $batch Commit $commit - Activity logged\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        $messages = [
            "Log activity batch $batch commit $commit",
            "Add log entry $batch-$commit",
            "Update activity log $batch.$commit",
            "Record activity $batch-$commit",
            "Log batch $batch commit $commit"
        ];
        
        $this->performGitCommit($messages[array_rand($messages)]);
        echo "📋 Log commit: activity.log\n";
    }
    
    /**
     * Strategy 4: Config commit
     */
    private function createConfigCommit($batch, $commit) {
        $configFile = $this->dummyDir . '/config.json';
        $config = [
            'batch' => $batch,
            'commit' => $commit,
            'timestamp' => date('Y-m-d H:i:s'),
            'random_value' => rand(1000, 9999),
            'status' => 'active'
        ];
        
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
        
        $messages = [
            "Update config batch $batch commit $commit",
            "Modify settings $batch-$commit",
            "Config update $batch.$commit",
            "Settings change $batch-$commit",
            "Update configuration $batch"
        ];
        
        $this->performGitCommit($messages[array_rand($messages)]);
        echo "⚙️ Config commit: config.json\n";
    }
    
    /**
     * Strategy 5: Data commit
     */
    private function createDataCommit($batch, $commit) {
        $dataFile = $this->dummyDir . '/data_' . date('Ymd') . '.csv';
        $dataEntry = "$batch,$commit," . date('Y-m-d H:i:s') . "," . rand(100, 999) . "\n";
        
        file_put_contents($dataFile, $dataEntry, FILE_APPEND);
        
        $messages = [
            "Add data entry batch $batch commit $commit",
            "Data update $batch-$commit",
            "Insert data $batch.$commit",
            "Add record $batch-$commit",
            "Data batch $batch commit $commit"
        ];
        
        $this->performGitCommit($messages[array_rand($messages)]);
        echo "📊 Data commit: " . basename($dataFile) . "\n";
    }
    
    /**
     * Strategy 6: Progress commit
     */
    private function createProgressCommit($batch, $commit) {
        $progressFile = $this->dummyDir . '/progress.md';
        $progressEntry = "- [x] Batch $batch Commit $commit completed at " . date('H:i:s') . "\n";
        
        file_put_contents($progressFile, $progressEntry, FILE_APPEND);
        
        $messages = [
            "Progress update batch $batch commit $commit",
            "Mark progress $batch-$commit",
            "Update progress $batch.$commit",
            "Progress batch $batch commit $commit",
            "Complete task $batch-$commit"
        ];
        
        $this->performGitCommit($messages[array_rand($messages)]);
        echo "📈 Progress commit: progress.md\n";
    }
    
    /**
     * Perform git commit with lock handling
     */
    private function performGitCommit($message, $autoPush = false) {
        // Wait for any existing git operations to complete
        $this->waitForGitLock();

        $addResult = shell_exec('git add . 2>&1');
        if (strpos($addResult, 'fatal') !== false) {
            echo "⚠️ Git add failed: $addResult\n";
            return false;
        }

        $commitCommand = 'git commit -m "' . addslashes($message) . '" 2>&1';
        $commitResult = shell_exec($commitCommand);

        if (strpos($commitResult, 'fatal') !== false && strpos($commitResult, 'nothing to commit') === false) {
            echo "⚠️ Git commit failed: $commitResult\n";
            return false;
        }

        if ($autoPush) {
            $this->performGitPush();
        }

        return true;
    }

    /**
     * Wait for git lock to be released
     */
    private function waitForGitLock($maxWait = 30) {
        $lockFile = '.git/index.lock';
        $waited = 0;

        while (file_exists($lockFile) && $waited < $maxWait) {
            echo "⏳ Waiting for git lock to be released...\n";
            sleep(1);
            $waited++;
        }

        // Force remove lock if it's been too long
        if (file_exists($lockFile) && $waited >= $maxWait) {
            echo "🔓 Force removing git lock file...\n";
            unlink($lockFile);
        }
    }

    /**
     * Perform git push with auto-detection
     */
    private function performGitPush() {
        // Auto-detect current branch
        $currentBranch = trim(shell_exec('git branch --show-current 2>/dev/null'));
        if (empty($currentBranch)) {
            $currentBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD 2>/dev/null'));
        }
        if (empty($currentBranch)) {
            $currentBranch = 'master'; // Default fallback
        }

        $pushResult = shell_exec("git push origin $currentBranch 2>&1");
        if (strpos($pushResult, 'error') === false && strpos($pushResult, 'fatal') === false) {
            echo "📤 Pushed to GitHub ($currentBranch)\n";
        } else {
            echo "⚠️ Push failed: $pushResult\n";
        }
    }
    
    /**
     * Get today's commit count
     */
    private function getTodayCommitCount() {
        $today = date('Y-m-d');
        $gitLog = shell_exec("git log --since=\"$today 00:00:00\" --until=\"$today 23:59:59\" --oneline 2>/dev/null");
        
        if (empty($gitLog)) {
            return 0;
        }
        
        return count(explode("\n", trim($gitLog)));
    }
    
    /**
     * Update daily log
     */
    private function updateDailyLog($date, $commitsAdded) {
        $log = json_decode(file_get_contents($this->commitLogFile), true);
        
        if (!isset($log['daily_commits'][$date])) {
            $log['daily_commits'][$date] = 0;
        }
        
        $log['daily_commits'][$date] += $commitsAdded;
        $log['total_commits'] += $commitsAdded;
        $log['last_run'] = date('Y-m-d H:i:s');
        
        file_put_contents($this->commitLogFile, json_encode($log, JSON_PRETTY_PRINT));
    }
    
    /**
     * Show statistics
     */
    public function showStats() {
        $log = json_decode(file_get_contents($this->commitLogFile), true);
        $today = date('Y-m-d');
        $todayCommits = $this->getTodayCommitCount();
        
        echo "\n📊 Multi Commit Statistics\n";
        echo "==========================\n";
        echo "Total commits tracked: {$log['total_commits']}\n";
        echo "Commits today: $todayCommits\n";
        echo "Last run: {$log['last_run']}\n";
        
        echo "\nRecent daily commits:\n";
        $recentDays = array_slice($log['daily_commits'], -7, 7, true);
        foreach ($recentDays as $date => $count) {
            echo "  $date: $count commits\n";
        }
        
        echo "==========================\n";
    }
    
    /**
     * Cleanup old files
     */
    public function cleanup($daysOld = 7) {
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $files = glob($this->dummyDir . '/*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }
        
        if ($deleted > 0) {
            echo "🗑️ $deleted file lama dihapus\n";
            $this->performGitCommit("Cleanup old files - removed $deleted files");
        }
        
        return $deleted;
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $automation = new MultiCommitAutomation();

    $action = $argv[1] ?? 'run';

    switch ($action) {
        case 'run':
        case 'auto':
            $autoPush = isset($argv[2]) && $argv[2] === 'push';
            $pushInterval = isset($argv[3]) ? (int)$argv[3] : 5;
            $automation->runDailyAutomation($autoPush, $pushInterval);
            break;

        case 'push':
            $pushInterval = isset($argv[2]) ? (int)$argv[2] : 5;
            $automation->runDailyAutomation(true, $pushInterval);
            break;

        case 'stats':
            $automation->showStats();
            break;

        case 'cleanup':
            $days = isset($argv[2]) ? (int)$argv[2] : 7;
            $automation->cleanup($days);
            break;

        case 'help':
        default:
            echo "Multi Commit Automation System\n";
            echo "==============================\n";
            echo "Usage: php multi_commit_automation.php [command] [options]\n\n";
            echo "Commands:\n";
            echo "  run [push] [interval]  - Jalankan automation (15-40 commits)\n";
            echo "  push [interval]        - Jalankan dengan auto-push (default: every 5 commits)\n";
            echo "  stats                  - Tampilkan statistik\n";
            echo "  cleanup [days]         - Hapus file lama (default: 7 hari)\n";
            echo "  help                   - Tampilkan bantuan\n\n";
            echo "Examples:\n";
            echo "  php multi_commit_automation.php run\n";
            echo "  php multi_commit_automation.php run push\n";
            echo "  php multi_commit_automation.php run push 3\n";
            echo "  php multi_commit_automation.php push\n";
            echo "  php multi_commit_automation.php push 10\n";
            echo "  php multi_commit_automation.php stats\n";
            echo "\nAuto-Push Options:\n";
            echo "  push           - Enable auto-push to GitHub\n";
            echo "  [interval]     - Push every N commits (default: 5)\n";
            break;
    }
}
?>
