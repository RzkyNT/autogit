<?php
/**
 * Simple Dummy Commit Script
 * 
 * Script kecil untuk commit file dummy setiap hari
 * Fokus pada kesederhanaan dan "gaming the system" untuk GitHub green
 */

class DummyCommitBot {
    private $dummyDir = 'dummy_files';
    
    public function __construct() {
        if (!is_dir($this->dummyDir)) {
            mkdir($this->dummyDir, 0755, true);
        }
    }
    
    /**
     * Buat commit dummy sederhana
     */
    public function createDummyCommit() {
        $today = date('Y-m-d');
        $timestamp = date('Y-m-d H:i:s');
        $filename = $this->dummyDir . '/dummy_' . date('Ymd_His') . '.txt';
        
        // Buat file dummy minimal
        $content = $this->generateDummyContent($timestamp);
        file_put_contents($filename, $content);
        
        echo "📄 File dummy dibuat: $filename\n";
        
        // Git add dan commit
        $this->performGitCommit($today);
        
        return $filename;
    }
    
    /**
     * Generate konten dummy minimal
     */
    private function generateDummyContent($timestamp) {
        $templates = [
            "Daily commit\n$timestamp\n",
            "Keep streak alive\n$timestamp\nRandom: " . rand(1000, 9999) . "\n",
            "Automated commit\n$timestamp\n",
            "Daily update\n$timestamp\nCounter: " . $this->getDayOfYear() . "\n",
            "Consistency\n$timestamp\n",
            "Green square\n$timestamp\nHash: " . substr(md5($timestamp), 0, 8) . "\n"
        ];
        
        return $templates[array_rand($templates)];
    }
    
    /**
     * Lakukan git commit
     */
    private function performGitCommit($date) {
        // Git add
        shell_exec('git add .');
        
        // Commit message sederhana
        $messages = [
            "Daily commit $date",
            "Update $date",
            "Automated commit $date",
            "Keep green $date",
            "Daily update $date",
            "Consistency $date"
        ];
        
        $message = $messages[array_rand($messages)];
        $commitCommand = 'git commit -m "' . addslashes($message) . '" 2>&1';
        $result = shell_exec($commitCommand);
        
        echo "✅ Commit berhasil: $message\n";
        
        return $result;
    }
    
    /**
     * Buat multiple dummy files (untuk commit yang lebih "substantial")
     */
    public function createMultipleDummyFiles($count = 3) {
        $files = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $filename = $this->dummyDir . '/batch_' . date('Ymd') . "_$i.txt";
            $content = "Batch file $i\n" . date('Y-m-d H:i:s') . "\nData: " . rand(10000, 99999) . "\n";
            
            file_put_contents($filename, $content);
            $files[] = $filename;
        }
        
        echo "📄 $count file dummy dibuat\n";
        
        // Commit semua sekaligus
        $this->performGitCommit(date('Y-m-d'));
        
        return $files;
    }
    
    /**
     * Update file dummy yang sudah ada
     */
    public function updateExistingDummy() {
        $dummyFiles = glob($this->dummyDir . '/*.txt');
        
        if (empty($dummyFiles)) {
            // Buat file baru jika belum ada
            return $this->createDummyCommit();
        }
        
        // Update file random
        $file = $dummyFiles[array_rand($dummyFiles)];
        $content = file_get_contents($file);
        $content .= "\nUpdated: " . date('Y-m-d H:i:s') . "\n";
        
        file_put_contents($file, $content);
        
        echo "📝 File dummy diupdate: $file\n";
        
        $this->performGitCommit(date('Y-m-d'));
        
        return $file;
    }
    
    /**
     * Cleanup file dummy lama (opsional)
     */
    public function cleanupOldDummies($daysOld = 30) {
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $dummyFiles = glob($this->dummyDir . '/*.txt');
        $deleted = 0;
        
        foreach ($dummyFiles as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }
        
        if ($deleted > 0) {
            echo "🗑️ $deleted file dummy lama dihapus\n";
            $this->performGitCommit(date('Y-m-d'));
        }
        
        return $deleted;
    }
    
    /**
     * Get day of year
     */
    private function getDayOfYear() {
        return date('z') + 1;
    }
    
    /**
     * Tampilkan statistik dummy files
     */
    public function showDummyStats() {
        $dummyFiles = glob($this->dummyDir . '/*.txt');
        $totalFiles = count($dummyFiles);
        
        echo "\n📊 Dummy Files Statistics\n";
        echo "========================\n";
        echo "Total dummy files: $totalFiles\n";
        echo "Directory: {$this->dummyDir}\n";
        
        if ($totalFiles > 0) {
            $totalSize = 0;
            $oldestFile = null;
            $newestFile = null;
            $oldestTime = PHP_INT_MAX;
            $newestTime = 0;
            
            foreach ($dummyFiles as $file) {
                $size = filesize($file);
                $time = filemtime($file);
                $totalSize += $size;
                
                if ($time < $oldestTime) {
                    $oldestTime = $time;
                    $oldestFile = $file;
                }
                
                if ($time > $newestTime) {
                    $newestTime = $time;
                    $newestFile = $file;
                }
            }
            
            echo "Total size: " . round($totalSize / 1024, 2) . " KB\n";
            echo "Oldest file: " . basename($oldestFile) . " (" . date('Y-m-d H:i:s', $oldestTime) . ")\n";
            echo "Newest file: " . basename($newestFile) . " (" . date('Y-m-d H:i:s', $newestTime) . ")\n";
        }
        
        echo "========================\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $bot = new DummyCommitBot();
    
    $action = $argv[1] ?? 'single';
    
    switch ($action) {
        case 'single':
        case 'commit':
            $bot->createDummyCommit();
            break;
            
        case 'multiple':
        case 'batch':
            $count = isset($argv[2]) ? (int)$argv[2] : 3;
            $bot->createMultipleDummyFiles($count);
            break;
            
        case 'update':
            $bot->updateExistingDummy();
            break;
            
        case 'cleanup':
            $days = isset($argv[2]) ? (int)$argv[2] : 30;
            $bot->cleanupOldDummies($days);
            break;
            
        case 'stats':
            $bot->showDummyStats();
            break;
            
        case 'help':
        default:
            echo "Simple Dummy Commit Script\n";
            echo "==========================\n";
            echo "Usage: php dummy_commit.php [command] [options]\n\n";
            echo "Commands:\n";
            echo "  single/commit     - Buat 1 file dummy dan commit\n";
            echo "  multiple [count]  - Buat multiple file dummy (default: 3)\n";
            echo "  update           - Update file dummy yang ada\n";
            echo "  cleanup [days]   - Hapus file dummy lama (default: 30 hari)\n";
            echo "  stats            - Tampilkan statistik dummy files\n";
            echo "  help             - Tampilkan bantuan\n\n";
            echo "Examples:\n";
            echo "  php dummy_commit.php single\n";
            echo "  php dummy_commit.php multiple 5\n";
            echo "  php dummy_commit.php update\n";
            echo "  php dummy_commit.php cleanup 7\n";
            break;
    }
} else {
    echo "Script ini harus dijalankan melalui command line\n";
}
?>
