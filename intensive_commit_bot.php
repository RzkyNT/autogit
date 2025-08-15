<?php
/**
 * Intensive Commit Bot
 * 
 * Bot untuk melakukan commit intensif 15-40 kali per hari
 * dengan berbagai strategi dan timing yang realistis
 */

class IntensiveCommitBot {
    private $targetMin = 15;
    private $targetMax = 40;
    private $workingHours = [
        'morning' => ['start' => 7, 'end' => 11],
        'afternoon' => ['start' => 13, 'end' => 17],
        'evening' => ['start' => 19, 'end' => 22]
    ];
    
    public function __construct() {
        if (!is_dir('intensive_commits')) {
            mkdir('intensive_commits', 0755, true);
        }
    }
    
    /**
     * Jalankan intensive commit untuk hari ini
     */
    public function runIntensiveCommits() {
        $targetCommits = rand($this->targetMin, $this->targetMax);
        $currentHour = (int)date('H');
        
        echo "🎯 Target commits hari ini: $targetCommits\n";
        echo "🕐 Waktu sekarang: " . date('H:i') . "\n";
        
        // Tentukan strategi berdasarkan waktu
        if ($this->isWorkingHours($currentHour)) {
            $this->runWorkingHoursStrategy($targetCommits);
        } else {
            $this->runOffHoursStrategy($targetCommits);
        }
    }
    
    /**
     * Cek apakah sedang jam kerja
     */
    private function isWorkingHours($hour) {
        foreach ($this->workingHours as $period) {
            if ($hour >= $period['start'] && $hour <= $period['end']) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Strategi untuk jam kerja (lebih banyak commits)
     */
    private function runWorkingHoursStrategy($targetCommits) {
        echo "💼 Mode: Working Hours - Intensive commits\n";
        
        // Bagi menjadi beberapa sesi dengan interval pendek
        $sessions = $this->createWorkingSessions($targetCommits);
        
        foreach ($sessions as $sessionIndex => $sessionCommits) {
            echo "\n🔥 Sesi " . ($sessionIndex + 1) . ": $sessionCommits commits\n";
            
            for ($i = 0; $i < $sessionCommits; $i++) {
                $this->createVariedCommit($sessionIndex + 1, $i + 1);
                
                // Interval pendek antar commit (10-60 detik)
                if ($i < $sessionCommits - 1) {
                    $delay = rand(10, 60);
                    echo "⏳ Delay $delay detik...\n";
                    sleep($delay);
                }
            }
            
            // Interval antar sesi (2-10 menit)
            if ($sessionIndex < count($sessions) - 1) {
                $sessionDelay = rand(120, 600);
                echo "⏸️ Break " . round($sessionDelay/60, 1) . " menit...\n";
                sleep($sessionDelay);
            }
        }
    }
    
    /**
     * Strategi untuk di luar jam kerja (commits lebih sedikit)
     */
    private function runOffHoursStrategy($targetCommits) {
        echo "🌙 Mode: Off Hours - Moderate commits\n";
        
        // Commits lebih sedikit dengan interval lebih panjang
        $reducedCommits = min($targetCommits, rand(5, 15));
        
        for ($i = 0; $i < $reducedCommits; $i++) {
            $this->createVariedCommit(1, $i + 1);
            
            if ($i < $reducedCommits - 1) {
                $delay = rand(300, 1800); // 5-30 menit
                echo "⏳ Delay " . round($delay/60, 1) . " menit...\n";
                sleep($delay);
            }
        }
    }
    
    /**
     * Buat sesi kerja untuk jam kerja
     */
    private function createWorkingSessions($totalCommits) {
        $sessions = [];
        $remaining = $totalCommits;
        
        // Bagi menjadi 3-6 sesi
        $sessionCount = rand(3, 6);
        
        for ($i = 0; $i < $sessionCount; $i++) {
            if ($i == $sessionCount - 1) {
                // Sesi terakhir, ambil sisa commits
                $sessions[] = $remaining;
            } else {
                // Sesi biasa, ambil 20-40% dari sisa
                $sessionSize = min($remaining, rand(2, max(2, round($remaining * 0.4))));
                $sessions[] = $sessionSize;
                $remaining -= $sessionSize;
            }
        }
        
        return $sessions;
    }
    
    /**
     * Buat commit dengan variasi tinggi
     */
    private function createVariedCommit($session, $commit) {
        $strategies = [
            'micro_feature',
            'bug_fix',
            'refactor',
            'documentation',
            'test',
            'config',
            'data_update',
            'cleanup',
            'optimization',
            'style'
        ];
        
        $strategy = $strategies[array_rand($strategies)];
        $this->executeStrategy($strategy, $session, $commit);
    }
    
    /**
     * Execute specific commit strategy
     */
    private function executeStrategy($strategy, $session, $commit) {
        $timestamp = date('Y-m-d H:i:s');
        $dir = 'intensive_commits';
        
        switch ($strategy) {
            case 'micro_feature':
                $file = "$dir/features.md";
                $content = "## Feature $session.$commit\n\nAdded at $timestamp\n\n";
                file_put_contents($file, $content, FILE_APPEND);
                $this->gitCommit("Add micro feature $session.$commit");
                echo "✨ Micro feature commit\n";
                break;
                
            case 'bug_fix':
                $file = "$dir/bugfixes.log";
                $content = "[$timestamp] Fixed bug #$session$commit - Minor issue resolved\n";
                file_put_contents($file, $content, FILE_APPEND);
                $this->gitCommit("Fix bug #$session$commit");
                echo "🐛 Bug fix commit\n";
                break;
                
            case 'refactor':
                $file = "$dir/refactor_$session" . "_$commit.php";
                $content = "<?php\n// Refactored code $session.$commit\n// Timestamp: $timestamp\nclass Refactor$session$commit {\n    // Improved implementation\n}\n";
                file_put_contents($file, $content);
                $this->gitCommit("Refactor code $session.$commit");
                echo "♻️ Refactor commit\n";
                break;
                
            case 'documentation':
                $file = "$dir/docs.md";
                $content = "### Documentation Update $session.$commit\n\nUpdated at $timestamp\n\n";
                file_put_contents($file, $content, FILE_APPEND);
                $this->gitCommit("Update documentation $session.$commit");
                echo "📚 Documentation commit\n";
                break;
                
            case 'test':
                $file = "$dir/test_$session" . "_$commit.js";
                $content = "// Test case $session.$commit\n// Created: $timestamp\ndescribe('Test $session.$commit', () => {\n  it('should work', () => {\n    expect(true).toBe(true);\n  });\n});\n";
                file_put_contents($file, $content);
                $this->gitCommit("Add test case $session.$commit");
                echo "🧪 Test commit\n";
                break;
                
            case 'config':
                $file = "$dir/config_$session$commit.json";
                $config = [
                    'session' => $session,
                    'commit' => $commit,
                    'timestamp' => $timestamp,
                    'environment' => 'development',
                    'version' => "1.$session.$commit"
                ];
                file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT));
                $this->gitCommit("Update config $session.$commit");
                echo "⚙️ Config commit\n";
                break;
                
            case 'data_update':
                $file = "$dir/data.csv";
                $data = "$session,$commit,$timestamp," . rand(100, 999) . "\n";
                file_put_contents($file, $data, FILE_APPEND);
                $this->gitCommit("Update data $session.$commit");
                echo "📊 Data commit\n";
                break;
                
            case 'cleanup':
                $file = "$dir/cleanup.log";
                $content = "[$timestamp] Cleanup task $session.$commit completed\n";
                file_put_contents($file, $content, FILE_APPEND);
                $this->gitCommit("Cleanup $session.$commit");
                echo "🧹 Cleanup commit\n";
                break;
                
            case 'optimization':
                $file = "$dir/performance.md";
                $content = "## Performance Optimization $session.$commit\n\nOptimized at $timestamp\n\n";
                file_put_contents($file, $content, FILE_APPEND);
                $this->gitCommit("Optimize performance $session.$commit");
                echo "⚡ Optimization commit\n";
                break;
                
            case 'style':
                $file = "$dir/styles.css";
                $content = "/* Style update $session.$commit */\n.update-$session-$commit {\n  /* Added $timestamp */\n}\n\n";
                file_put_contents($file, $content, FILE_APPEND);
                $this->gitCommit("Update styles $session.$commit");
                echo "🎨 Style commit\n";
                break;
        }
    }
    
    /**
     * Perform git commit
     */
    private function gitCommit($message) {
        shell_exec('git add .');
        $commitCommand = 'git commit -m "' . addslashes($message) . '" 2>&1';
        shell_exec($commitCommand);
    }
    
    /**
     * Run continuous mode (untuk testing)
     */
    public function runContinuous($duration = 3600) {
        echo "🔄 Running continuous mode for " . ($duration/60) . " minutes\n";
        
        $startTime = time();
        $commitCount = 0;
        
        while ((time() - $startTime) < $duration) {
            $this->createVariedCommit(1, ++$commitCount);
            
            // Random interval 30-300 seconds (0.5-5 minutes)
            $interval = rand(30, 300);
            echo "⏳ Next commit in " . round($interval/60, 1) . " minutes...\n";
            sleep($interval);
        }
        
        echo "✅ Continuous mode completed. Total commits: $commitCount\n";
    }
    
    /**
     * Show today's commit count
     */
    public function showTodayStats() {
        $today = date('Y-m-d');
        $gitLog = shell_exec("git log --since=\"$today 00:00:00\" --until=\"$today 23:59:59\" --oneline 2>/dev/null");
        
        $count = 0;
        if (!empty($gitLog)) {
            $count = count(explode("\n", trim($gitLog)));
        }
        
        echo "\n📊 Commits hari ini: $count\n";
        echo "🎯 Target: {$this->targetMin}-{$this->targetMax}\n";
        
        if ($count >= $this->targetMin) {
            echo "✅ Target minimum tercapai!\n";
        } else {
            echo "⚠️ Perlu " . ($this->targetMin - $count) . " commits lagi untuk target minimum\n";
        }
        
        return $count;
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $bot = new IntensiveCommitBot();
    
    $action = $argv[1] ?? 'run';
    
    switch ($action) {
        case 'run':
        case 'intensive':
            $bot->runIntensiveCommits();
            break;
            
        case 'continuous':
            $duration = isset($argv[2]) ? (int)$argv[2] * 60 : 3600; // default 1 hour
            $bot->runContinuous($duration);
            break;
            
        case 'stats':
            $bot->showTodayStats();
            break;
            
        case 'help':
        default:
            echo "Intensive Commit Bot\n";
            echo "===================\n";
            echo "Usage: php intensive_commit_bot.php [command] [options]\n\n";
            echo "Commands:\n";
            echo "  run/intensive        - Jalankan intensive commits (15-40)\n";
            echo "  continuous [minutes] - Mode continuous (default: 60 menit)\n";
            echo "  stats               - Tampilkan statistik hari ini\n";
            echo "  help                - Tampilkan bantuan\n\n";
            echo "Examples:\n";
            echo "  php intensive_commit_bot.php run\n";
            echo "  php intensive_commit_bot.php continuous 120\n";
            echo "  php intensive_commit_bot.php stats\n";
            break;
    }
}
?>
