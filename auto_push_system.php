<?php
/**
 * Auto Push System
 * 
 * Sistem untuk otomatis push commits ke GitHub dengan berbagai strategi
 */

class AutoPushSystem {
    private $logFile = 'auto_push_log.json';
    private $configFile = 'auto_push_config.json';
    
    public function __construct() {
        $this->initializeConfig();
    }
    
    /**
     * Initialize configuration
     */
    private function initializeConfig() {
        if (!file_exists($this->configFile)) {
            $config = [
                'auto_push_enabled' => true,
                'push_interval_commits' => 5,
                'push_interval_minutes' => 30,
                'max_unpushed_commits' => 20,
                'push_schedule' => [
                    'morning' => '09:00',
                    'afternoon' => '15:00',
                    'evening' => '21:00'
                ],
                'remote_name' => 'origin',
                'branch_name' => 'master'
            ];
            file_put_contents($this->configFile, json_encode($config, JSON_PRETTY_PRINT));
        }
        
        if (!file_exists($this->logFile)) {
            $log = [
                'last_push' => null,
                'total_pushes' => 0,
                'push_history' => []
            ];
            file_put_contents($this->logFile, json_encode($log, JSON_PRETTY_PRINT));
        }
    }
    
    /**
     * Check if auto push is needed
     */
    public function checkAndPush() {
        $config = $this->loadConfig();
        
        if (!$config['auto_push_enabled']) {
            echo "⚠️ Auto-push disabled in config\n";
            return false;
        }
        
        $unpushedCount = $this->getUnpushedCommitCount();
        $timeSinceLastPush = $this->getTimeSinceLastPush();
        
        echo "📊 Unpushed commits: $unpushedCount\n";
        echo "⏰ Minutes since last push: $timeSinceLastPush\n";
        
        $shouldPush = false;
        $reason = '';
        
        // Check commit count threshold
        if ($unpushedCount >= $config['push_interval_commits']) {
            $shouldPush = true;
            $reason = "Reached commit threshold ($unpushedCount >= {$config['push_interval_commits']})";
        }
        
        // Check time threshold
        if ($timeSinceLastPush >= $config['push_interval_minutes']) {
            $shouldPush = true;
            $reason = "Reached time threshold ($timeSinceLastPush >= {$config['push_interval_minutes']} minutes)";
        }
        
        // Check max unpushed limit
        if ($unpushedCount >= $config['max_unpushed_commits']) {
            $shouldPush = true;
            $reason = "Max unpushed limit reached ($unpushedCount >= {$config['max_unpushed_commits']})";
        }
        
        if ($shouldPush && $unpushedCount > 0) {
            echo "🚀 Pushing to GitHub: $reason\n";
            return $this->performPush();
        } else {
            echo "✅ No push needed at this time\n";
            return false;
        }
    }
    
    /**
     * Force push all unpushed commits
     */
    public function forcePush() {
        $unpushedCount = $this->getUnpushedCommitCount();
        
        if ($unpushedCount > 0) {
            echo "🚀 Force pushing $unpushedCount commits to GitHub...\n";
            return $this->performPush();
        } else {
            echo "✅ No commits to push\n";
            return true;
        }
    }
    
    /**
     * Perform the actual git push
     */
    private function performPush() {
        $config = $this->loadConfig();
        $remote = $config['remote_name'];
        $branch = $config['branch_name'];
        
        // Try main branch first, then master
        $pushCommand = "git push $remote $branch 2>&1";
        $result = shell_exec($pushCommand);
        
        if (strpos($result, 'error') !== false || strpos($result, 'fatal') !== false) {
            // Try master branch if main fails
            if ($branch === 'main') {
                echo "⚠️ Main branch failed, trying master...\n";
                $pushCommand = "git push $remote master 2>&1";
                $result = shell_exec($pushCommand);
            }
        }
        
        if (strpos($result, 'error') === false && strpos($result, 'fatal') === false) {
            echo "✅ Successfully pushed to GitHub\n";
            $this->logPush(true, $result);
            return true;
        } else {
            echo "❌ Push failed: $result\n";
            $this->logPush(false, $result);
            return false;
        }
    }
    
    /**
     * Get count of unpushed commits
     */
    private function getUnpushedCommitCount() {
        $config = $this->loadConfig();
        $remote = $config['remote_name'];
        $branch = $config['branch_name'];
        
        // Check if remote exists
        $remoteCheck = shell_exec("git remote -v 2>&1");
        if (strpos($remoteCheck, $remote) === false) {
            echo "⚠️ Remote '$remote' not found\n";
            return 0;
        }
        
        // Fetch latest from remote
        shell_exec("git fetch $remote 2>/dev/null");
        
        // Count unpushed commits
        $unpushedCommand = "git rev-list --count $remote/$branch..HEAD 2>/dev/null";
        $unpushedCount = (int)trim(shell_exec($unpushedCommand));
        
        // If that fails, try master
        if ($unpushedCount === 0 && $branch === 'main') {
            $unpushedCommand = "git rev-list --count $remote/master..HEAD 2>/dev/null";
            $unpushedCount = (int)trim(shell_exec($unpushedCommand));
        }
        
        return $unpushedCount;
    }
    
    /**
     * Get minutes since last push
     */
    private function getTimeSinceLastPush() {
        $log = $this->loadLog();
        
        if (!$log['last_push']) {
            return 999; // Very high number to trigger push
        }
        
        $lastPushTime = strtotime($log['last_push']);
        $currentTime = time();
        
        return round(($currentTime - $lastPushTime) / 60);
    }
    
    /**
     * Log push attempt
     */
    private function logPush($success, $output) {
        $log = $this->loadLog();
        
        $pushEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => $success,
            'output' => trim($output),
            'unpushed_count' => $this->getUnpushedCommitCount()
        ];
        
        $log['push_history'][] = $pushEntry;
        $log['total_pushes']++;
        
        if ($success) {
            $log['last_push'] = date('Y-m-d H:i:s');
        }
        
        // Keep only last 50 entries
        if (count($log['push_history']) > 50) {
            $log['push_history'] = array_slice($log['push_history'], -50);
        }
        
        file_put_contents($this->logFile, json_encode($log, JSON_PRETTY_PRINT));
    }
    
    /**
     * Show push statistics
     */
    public function showStats() {
        $log = $this->loadLog();
        $config = $this->loadConfig();
        $unpushedCount = $this->getUnpushedCommitCount();
        $timeSinceLastPush = $this->getTimeSinceLastPush();
        
        echo "\n📊 Auto Push Statistics\n";
        echo "=======================\n";
        echo "Auto-push enabled: " . ($config['auto_push_enabled'] ? 'Yes' : 'No') . "\n";
        echo "Unpushed commits: $unpushedCount\n";
        echo "Minutes since last push: $timeSinceLastPush\n";
        echo "Total pushes: {$log['total_pushes']}\n";
        echo "Last push: " . ($log['last_push'] ?? 'Never') . "\n";
        echo "Push interval: {$config['push_interval_commits']} commits or {$config['push_interval_minutes']} minutes\n";
        echo "Max unpushed: {$config['max_unpushed_commits']} commits\n";
        
        echo "\nRecent push history:\n";
        $recentPushes = array_slice($log['push_history'], -5);
        foreach ($recentPushes as $push) {
            $status = $push['success'] ? '✅' : '❌';
            echo "  $status {$push['timestamp']} - " . substr($push['output'], 0, 50) . "\n";
        }
        
        echo "=======================\n";
    }
    
    /**
     * Setup scheduled push
     */
    public function setupScheduledPush() {
        $config = $this->loadConfig();
        
        echo "🕐 Setting up scheduled auto-push...\n";
        
        foreach ($config['push_schedule'] as $period => $time) {
            $taskName = "GitHubAutoPush-" . ucfirst($period);
            
            // Remove existing task
            $existingTask = shell_exec("schtasks /query /tn \"$taskName\" 2>nul");
            if ($existingTask) {
                shell_exec("schtasks /delete /tn \"$taskName\" /f 2>nul");
            }
            
            // Create new task
            $scriptPath = __DIR__ . '/auto_push_system.php';
            $createCommand = "schtasks /create /tn \"$taskName\" /tr \"php \\\"$scriptPath\\\" check\" /sc daily /st $time /f";
            
            $result = shell_exec($createCommand . " 2>&1");
            
            if (strpos($result, 'SUCCESS') !== false) {
                echo "✅ Created $period push task at $time\n";
            } else {
                echo "❌ Failed to create $period task: $result\n";
            }
        }
        
        echo "🎯 Auto-push scheduled for: " . implode(', ', $config['push_schedule']) . "\n";
    }
    
    /**
     * Load configuration
     */
    private function loadConfig() {
        return json_decode(file_get_contents($this->configFile), true);
    }
    
    /**
     * Load log
     */
    private function loadLog() {
        return json_decode(file_get_contents($this->logFile), true);
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $pushSystem = new AutoPushSystem();
    
    $action = $argv[1] ?? 'check';
    
    switch ($action) {
        case 'check':
            $pushSystem->checkAndPush();
            break;
            
        case 'force':
        case 'push':
            $pushSystem->forcePush();
            break;
            
        case 'stats':
            $pushSystem->showStats();
            break;
            
        case 'setup':
            $pushSystem->setupScheduledPush();
            break;
            
        case 'help':
        default:
            echo "Auto Push System\n";
            echo "================\n";
            echo "Usage: php auto_push_system.php [command]\n\n";
            echo "Commands:\n";
            echo "  check    - Check if push is needed and push if so\n";
            echo "  force    - Force push all unpushed commits\n";
            echo "  stats    - Show push statistics\n";
            echo "  setup    - Setup scheduled auto-push tasks\n";
            echo "  help     - Show this help\n\n";
            echo "Examples:\n";
            echo "  php auto_push_system.php check\n";
            echo "  php auto_push_system.php force\n";
            echo "  php auto_push_system.php stats\n";
            echo "  php auto_push_system.php setup\n";
            break;
    }
}
?>
