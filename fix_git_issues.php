<?php
/**
 * Git Issues Fixer
 * 
 * Script untuk memperbaiki masalah git yang umum terjadi
 */

class GitIssuesFixer {
    
    /**
     * Fix git lock issues
     */
    public function fixGitLock() {
        echo "🔧 Fixing git lock issues...\n";
        
        $lockFiles = [
            '.git/index.lock',
            '.git/refs/heads/master.lock',
            '.git/refs/heads/main.lock',
            '.git/config.lock'
        ];
        
        $removed = 0;
        foreach ($lockFiles as $lockFile) {
            if (file_exists($lockFile)) {
                if (unlink($lockFile)) {
                    echo "🗑️ Removed: $lockFile\n";
                    $removed++;
                } else {
                    echo "❌ Failed to remove: $lockFile\n";
                }
            }
        }
        
        if ($removed > 0) {
            echo "✅ Removed $removed lock files\n";
        } else {
            echo "✅ No lock files found\n";
        }
        
        return $removed;
    }
    
    /**
     * Check and fix branch issues
     */
    public function fixBranchIssues() {
        echo "🌿 Checking branch issues...\n";
        
        // Get current branch
        $currentBranch = trim(shell_exec('git branch --show-current 2>/dev/null'));
        if (empty($currentBranch)) {
            $currentBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD 2>/dev/null'));
        }
        
        echo "Current branch: $currentBranch\n";
        
        // Check remote branches
        $remoteBranches = shell_exec('git branch -r 2>/dev/null');
        echo "Remote branches:\n$remoteBranches\n";
        
        // Check if remote exists
        $remotes = shell_exec('git remote -v 2>/dev/null');
        echo "Remotes:\n$remotes\n";
        
        return $currentBranch;
    }
    
    /**
     * Test git operations
     */
    public function testGitOperations() {
        echo "🧪 Testing git operations...\n";
        
        // Test git status
        echo "Testing git status...\n";
        $status = shell_exec('git status --porcelain 2>&1');
        if (strpos($status, 'fatal') !== false) {
            echo "❌ Git status failed: $status\n";
            return false;
        }
        echo "✅ Git status OK\n";
        
        // Test git add
        echo "Testing git add...\n";
        $addResult = shell_exec('git add . 2>&1');
        if (strpos($addResult, 'fatal') !== false) {
            echo "❌ Git add failed: $addResult\n";
            return false;
        }
        echo "✅ Git add OK\n";
        
        // Test git commit (if there are changes)
        $statusCheck = trim(shell_exec('git status --porcelain 2>/dev/null'));
        if (!empty($statusCheck)) {
            echo "Testing git commit...\n";
            $commitResult = shell_exec('git commit -m "Test commit - fix git issues" 2>&1');
            if (strpos($commitResult, 'fatal') !== false) {
                echo "❌ Git commit failed: $commitResult\n";
                return false;
            }
            echo "✅ Git commit OK\n";
        } else {
            echo "ℹ️ No changes to commit\n";
        }
        
        return true;
    }
    
    /**
     * Test git push
     */
    public function testGitPush() {
        echo "📤 Testing git push...\n";
        
        // Get current branch
        $currentBranch = trim(shell_exec('git branch --show-current 2>/dev/null'));
        if (empty($currentBranch)) {
            $currentBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD 2>/dev/null'));
        }
        if (empty($currentBranch)) {
            $currentBranch = 'master';
        }
        
        echo "Pushing to branch: $currentBranch\n";
        
        $pushResult = shell_exec("git push origin $currentBranch 2>&1");
        
        if (strpos($pushResult, 'error') === false && strpos($pushResult, 'fatal') === false) {
            echo "✅ Git push successful\n";
            echo "Result: $pushResult\n";
            return true;
        } else {
            echo "❌ Git push failed: $pushResult\n";
            return false;
        }
    }
    
    /**
     * Kill all git processes
     */
    public function killGitProcesses() {
        echo "🔪 Killing git processes...\n";
        
        // Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $result = shell_exec('taskkill /F /IM git.exe 2>&1');
            echo "Windows git processes: $result\n";
        } else {
            // Linux/Mac
            $result = shell_exec('pkill -f git 2>&1');
            echo "Unix git processes: $result\n";
        }
        
        sleep(2); // Wait for processes to be killed
        
        return true;
    }
    
    /**
     * Complete git fix
     */
    public function fixAll() {
        echo "🚀 Running complete git fix...\n\n";
        
        // Step 1: Kill git processes
        $this->killGitProcesses();
        
        // Step 2: Remove lock files
        $this->fixGitLock();
        
        // Step 3: Check branch issues
        $currentBranch = $this->fixBranchIssues();
        
        // Step 4: Test git operations
        if ($this->testGitOperations()) {
            echo "✅ Git operations working\n";
            
            // Step 5: Test push
            if ($this->testGitPush()) {
                echo "🎉 All git issues fixed successfully!\n";
                return true;
            } else {
                echo "⚠️ Push still has issues, but basic git operations work\n";
                return false;
            }
        } else {
            echo "❌ Git operations still have issues\n";
            return false;
        }
    }
    
    /**
     * Show git status
     */
    public function showStatus() {
        echo "📊 Git Status Report\n";
        echo "===================\n";
        
        // Current branch
        $currentBranch = trim(shell_exec('git branch --show-current 2>/dev/null'));
        echo "Current branch: $currentBranch\n";
        
        // Uncommitted changes
        $status = shell_exec('git status --porcelain 2>/dev/null');
        $changeCount = empty(trim($status)) ? 0 : count(explode("\n", trim($status)));
        echo "Uncommitted changes: $changeCount files\n";
        
        // Unpushed commits
        $unpushed = shell_exec("git rev-list --count origin/$currentBranch..HEAD 2>/dev/null");
        echo "Unpushed commits: " . trim($unpushed) . "\n";
        
        // Lock files
        $lockFiles = glob('.git/*.lock') ?: [];
        echo "Lock files: " . count($lockFiles) . "\n";
        
        echo "===================\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $fixer = new GitIssuesFixer();
    
    $action = $argv[1] ?? 'fix';
    
    switch ($action) {
        case 'fix':
        case 'all':
            $fixer->fixAll();
            break;
            
        case 'lock':
            $fixer->fixGitLock();
            break;
            
        case 'branch':
            $fixer->fixBranchIssues();
            break;
            
        case 'test':
            $fixer->testGitOperations();
            break;
            
        case 'push':
            $fixer->testGitPush();
            break;
            
        case 'kill':
            $fixer->killGitProcesses();
            break;
            
        case 'status':
            $fixer->showStatus();
            break;
            
        case 'help':
        default:
            echo "Git Issues Fixer\n";
            echo "================\n";
            echo "Usage: php fix_git_issues.php [command]\n\n";
            echo "Commands:\n";
            echo "  fix/all    - Run complete git fix\n";
            echo "  lock       - Remove git lock files\n";
            echo "  branch     - Check branch issues\n";
            echo "  test       - Test git operations\n";
            echo "  push       - Test git push\n";
            echo "  kill       - Kill git processes\n";
            echo "  status     - Show git status\n";
            echo "  help       - Show this help\n";
            break;
    }
}
?>
