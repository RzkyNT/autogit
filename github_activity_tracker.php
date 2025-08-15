<?php
/**
 * GitHub Activity Tracker
 * 
 * Sistem untuk melacak berbagai jenis aktivitas GitHub:
 * - Commits
 * - Issues
 * - Pull Requests
 * - Repository creation
 * - Forks
 */

class GitHubActivityTracker {
    private $dataFile = 'github_activities.json';
    private $configFile = 'github_config.json';
    
    public function __construct() {
        $this->initializeFiles();
    }
    
    /**
     * Inisialisasi file data dan konfigurasi
     */
    private function initializeFiles() {
        if (!file_exists($this->dataFile)) {
            $initialData = [
                'activities' => [],
                'statistics' => [
                    'total_commits' => 0,
                    'total_issues' => 0,
                    'total_prs' => 0,
                    'total_repos' => 0,
                    'total_forks' => 0,
                    'streak_days' => 0,
                    'last_activity_date' => null
                ],
                'created_at' => date('Y-m-d H:i:s')
            ];
            file_put_contents($this->dataFile, json_encode($initialData, JSON_PRETTY_PRINT));
        }
        
        if (!file_exists($this->configFile)) {
            $config = [
                'github_username' => '',
                'target_daily_commits' => 1,
                'target_weekly_issues' => 2,
                'target_monthly_prs' => 4,
                'notification_enabled' => true
            ];
            file_put_contents($this->configFile, json_encode($config, JSON_PRETTY_PRINT));
        }
    }
    
    /**
     * Mencatat aktivitas baru
     */
    public function recordActivity($type, $description, $repository = null, $url = null) {
        $data = $this->loadData();
        
        $activity = [
            'id' => uniqid(),
            'type' => $type,
            'description' => $description,
            'repository' => $repository,
            'url' => $url,
            'timestamp' => date('Y-m-d H:i:s'),
            'date' => date('Y-m-d')
        ];
        
        $data['activities'][] = $activity;
        
        // Update statistik
        $this->updateStatistics($data, $type);
        
        $this->saveData($data);
        
        echo "✅ Aktivitas tercatat: $type - $description\n";
        
        return $activity['id'];
    }
    
    /**
     * Update statistik berdasarkan aktivitas
     */
    private function updateStatistics(&$data, $type) {
        $stats = &$data['statistics'];
        
        switch ($type) {
            case 'commit':
                $stats['total_commits']++;
                break;
            case 'issue':
                $stats['total_issues']++;
                break;
            case 'pull_request':
                $stats['total_prs']++;
                break;
            case 'repository':
                $stats['total_repos']++;
                break;
            case 'fork':
                $stats['total_forks']++;
                break;
        }
        
        // Update streak
        $today = date('Y-m-d');
        $lastDate = $stats['last_activity_date'];
        
        if ($lastDate === null) {
            $stats['streak_days'] = 1;
        } elseif ($lastDate === date('Y-m-d', strtotime('-1 day'))) {
            $stats['streak_days']++;
        } elseif ($lastDate !== $today) {
            $stats['streak_days'] = 1;
        }
        
        $stats['last_activity_date'] = $today;
    }
    
    /**
     * Mendapatkan aktivitas hari ini
     */
    public function getTodayActivities() {
        $data = $this->loadData();
        $today = date('Y-m-d');
        
        return array_filter($data['activities'], function($activity) use ($today) {
            return $activity['date'] === $today;
        });
    }
    
    /**
     * Mendapatkan aktivitas minggu ini
     */
    public function getWeekActivities() {
        $data = $this->loadData();
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        
        return array_filter($data['activities'], function($activity) use ($weekStart) {
            return $activity['date'] >= $weekStart;
        });
    }
    
    /**
     * Mendapatkan aktivitas bulan ini
     */
    public function getMonthActivities() {
        $data = $this->loadData();
        $monthStart = date('Y-m-01');
        
        return array_filter($data['activities'], function($activity) use ($monthStart) {
            return $activity['date'] >= $monthStart;
        });
    }
    
    /**
     * Menampilkan dashboard aktivitas
     */
    public function showDashboard() {
        $data = $this->loadData();
        $stats = $data['statistics'];
        $todayActivities = $this->getTodayActivities();
        $weekActivities = $this->getWeekActivities();
        
        echo "\n🎯 GitHub Activity Dashboard\n";
        echo "============================\n";
        echo "📊 Total Statistics:\n";
        echo "   • Commits: {$stats['total_commits']}\n";
        echo "   • Issues: {$stats['total_issues']}\n";
        echo "   • Pull Requests: {$stats['total_prs']}\n";
        echo "   • Repositories: {$stats['total_repos']}\n";
        echo "   • Forks: {$stats['total_forks']}\n";
        echo "   • Current Streak: {$stats['streak_days']} hari\n\n";
        
        echo "📅 Hari Ini (" . date('Y-m-d') . "):\n";
        if (empty($todayActivities)) {
            echo "   Belum ada aktivitas hari ini\n";
        } else {
            foreach ($todayActivities as $activity) {
                echo "   • {$activity['type']}: {$activity['description']}\n";
            }
        }
        
        echo "\n📈 Minggu Ini:\n";
        echo "   Total aktivitas: " . count($weekActivities) . "\n";
        
        // Breakdown per hari
        $dailyCount = [];
        foreach ($weekActivities as $activity) {
            $date = $activity['date'];
            $dailyCount[$date] = ($dailyCount[$date] ?? 0) + 1;
        }
        
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("monday this week +$i days"));
            $dayName = date('D', strtotime($date));
            $count = $dailyCount[$date] ?? 0;
            $indicator = $count > 0 ? '🟢' : '⚪';
            echo "   $indicator $dayName ($date): $count aktivitas\n";
        }
        
        echo "\n============================\n";
    }
    
    /**
     * Cek apakah target harian tercapai
     */
    public function checkDailyTarget() {
        $config = $this->loadConfig();
        $todayActivities = $this->getTodayActivities();
        
        $todayCommits = count(array_filter($todayActivities, function($a) {
            return $a['type'] === 'commit';
        }));
        
        $target = $config['target_daily_commits'];
        $achieved = $todayCommits >= $target;
        
        echo "\n🎯 Target Harian:\n";
        echo "Target commits: $target\n";
        echo "Commits hari ini: $todayCommits\n";
        echo "Status: " . ($achieved ? "✅ Tercapai" : "❌ Belum tercapai") . "\n";
        
        return $achieved;
    }
    
    /**
     * Generate laporan mingguan
     */
    public function generateWeeklyReport() {
        $weekActivities = $this->getWeekActivities();
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        
        $report = "\n📋 Laporan Mingguan ($weekStart s/d $weekEnd)\n";
        $report .= "================================================\n";
        
        $typeCount = [];
        foreach ($weekActivities as $activity) {
            $type = $activity['type'];
            $typeCount[$type] = ($typeCount[$type] ?? 0) + 1;
        }
        
        $report .= "📊 Ringkasan Aktivitas:\n";
        foreach ($typeCount as $type => $count) {
            $report .= "   • " . ucfirst($type) . ": $count\n";
        }
        
        $report .= "\n📅 Detail Harian:\n";
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("monday this week +$i days"));
            $dayName = date('l', strtotime($date));
            $dayActivities = array_filter($weekActivities, function($a) use ($date) {
                return $a['date'] === $date;
            });
            
            $report .= "\n$dayName ($date):\n";
            if (empty($dayActivities)) {
                $report .= "   Tidak ada aktivitas\n";
            } else {
                foreach ($dayActivities as $activity) {
                    $time = date('H:i', strtotime($activity['timestamp']));
                    $report .= "   [$time] {$activity['type']}: {$activity['description']}\n";
                }
            }
        }
        
        $report .= "\n================================================\n";
        
        // Simpan laporan
        $reportFile = "weekly_report_" . date('Y_W') . ".txt";
        file_put_contents($reportFile, $report);
        
        echo $report;
        echo "💾 Laporan disimpan ke: $reportFile\n";
        
        return $reportFile;
    }
    
    /**
     * Load data dari file
     */
    private function loadData() {
        return json_decode(file_get_contents($this->dataFile), true);
    }
    
    /**
     * Simpan data ke file
     */
    private function saveData($data) {
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Load konfigurasi
     */
    private function loadConfig() {
        return json_decode(file_get_contents($this->configFile), true);
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $tracker = new GitHubActivityTracker();
    
    $action = $argv[1] ?? 'dashboard';
    
    switch ($action) {
        case 'record':
            $type = $argv[2] ?? '';
            $description = $argv[3] ?? '';
            $repository = $argv[4] ?? null;
            $url = $argv[5] ?? null;
            
            if (empty($type) || empty($description)) {
                echo "Usage: php github_activity_tracker.php record <type> <description> [repository] [url]\n";
                echo "Types: commit, issue, pull_request, repository, fork\n";
                exit(1);
            }
            
            $tracker->recordActivity($type, $description, $repository, $url);
            break;
            
        case 'dashboard':
            $tracker->showDashboard();
            break;
            
        case 'target':
            $tracker->checkDailyTarget();
            break;
            
        case 'report':
            $tracker->generateWeeklyReport();
            break;
            
        default:
            echo "Available commands: record, dashboard, target, report\n";
            break;
    }
}
?>
