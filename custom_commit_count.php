<?php
/**
 * Custom Commit Count
 * 
 * Script sederhana untuk membuat jumlah commit yang bisa di-custom
 * Usage: php custom_commit_count.php 1000
 */

function createCustomCommits($count, $pushEvery = 50) {
    $startTime = time();
    $dir = 'custom_commits';
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    echo "🚀 Creating $count commits...\n";
    echo "📤 Push every $pushEvery commits\n";
    echo "⏰ Started at: " . date('Y-m-d H:i:s') . "\n\n";
    
    for ($i = 1; $i <= $count; $i++) {
        // Create simple file
        $filename = "$dir/commit_$i.txt";
        $content = "Commit #$i\nTimestamp: " . date('Y-m-d H:i:s') . "\nRandom: " . rand(10000, 99999) . "\n";
        file_put_contents($filename, $content);
        
        // Git commit
        shell_exec('git add . 2>/dev/null');
        shell_exec('git commit -m "Commit #' . $i . '" 2>/dev/null');
        
        // Progress indicator
        if ($i % 100 == 0 || $i == $count) {
            $progress = round($i / $count * 100, 1);
            $elapsed = time() - $startTime;
            $rate = round($i / max($elapsed, 1), 1);
            echo "📊 $i/$count ($progress%) - Rate: $rate commits/sec\n";
        }
        
        // Push to GitHub
        if ($i % $pushEvery == 0 || $i == $count) {
            echo "📤 Pushing commits $i...\n";
            $branch = trim(shell_exec('git branch --show-current 2>/dev/null')) ?: 'master';
            shell_exec("git push origin $branch 2>/dev/null");
        }
        
        // Micro delay to prevent overwhelming
        if ($count > 1000) {
            usleep(100000); // 0.1 second for very large counts
        }
    }
    
    $totalTime = time() - $startTime;
    $avgRate = round($count / max($totalTime, 1), 1);
    
    echo "\n🎉 COMPLETED!\n";
    echo "✅ Total commits: $count\n";
    echo "⏱️ Total time: " . gmdate('H:i:s', $totalTime) . "\n";
    echo "📈 Average rate: $avgRate commits/sec\n";
    echo "📤 All commits pushed to GitHub!\n";
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $count = isset($argv[1]) ? (int)$argv[1] : 100;
    $pushEvery = isset($argv[2]) ? (int)$argv[2] : 50;
    
    if ($count <= 0) {
        echo "Usage: php custom_commit_count.php <count> [push_every]\n";
        echo "Examples:\n";
        echo "  php custom_commit_count.php 1000        # 1000 commits, push every 50\n";
        echo "  php custom_commit_count.php 5000 100    # 5000 commits, push every 100\n";
        echo "  php custom_commit_count.php 10000 200   # 10000 commits, push every 200\n";
        exit(1);
    }
    
    if ($count > 10000) {
        echo "⚠️ WARNING: $count commits is a very large number!\n";
        echo "This will take approximately " . round($count / 10 / 60, 1) . " minutes.\n";
        echo "Continue? (y/N): ";
        $confirm = trim(fgets(STDIN));
        if (strtolower($confirm) !== 'y') {
            echo "Cancelled.\n";
            exit(0);
        }
    }
    
    createCustomCommits($count, $pushEvery);
} else {
    echo "This script must be run from command line.\n";
}
?>
