<?php
/**
 * System Testing Script
 * 
 * Script untuk menguji semua komponen sistem GitHub contribution automation
 */

class SystemTester {
    private $testResults = [];
    private $totalTests = 0;
    private $passedTests = 0;
    
    public function runAllTests() {
        echo "🧪 Memulai System Testing...\n";
        echo "============================\n\n";
        
        // Test basic requirements
        $this->testPHPVersion();
        $this->testGitInstallation();
        $this->testFilePermissions();
        
        // Test core components
        $this->testDailyCommitScript();
        $this->testActivityTracker();
        $this->testContributionUtilities();
        
        // Test file creation and data integrity
        $this->testFileCreation();
        $this->testDataIntegrity();
        
        // Test git operations
        $this->testGitOperations();
        
        // Display results
        $this->displayResults();
        
        return $this->passedTests === $this->totalTests;
    }
    
    private function test($name, $condition, $message = '') {
        $this->totalTests++;
        $status = $condition ? '✅ PASS' : '❌ FAIL';
        $result = [
            'name' => $name,
            'status' => $condition,
            'message' => $message
        ];
        
        $this->testResults[] = $result;
        
        if ($condition) {
            $this->passedTests++;
        }
        
        echo "$status - $name";
        if ($message) {
            echo " ($message)";
        }
        echo "\n";
        
        return $condition;
    }
    
    private function testPHPVersion() {
        echo "📋 Testing PHP Requirements...\n";
        
        $phpVersion = PHP_VERSION;
        $this->test(
            'PHP Version >= 7.4',
            version_compare($phpVersion, '7.4.0', '>='),
            "Current: $phpVersion"
        );
        
        $this->test(
            'JSON Extension',
            extension_loaded('json'),
            'Required for data storage'
        );
        
        echo "\n";
    }
    
    private function testGitInstallation() {
        echo "📋 Testing Git Installation...\n";
        
        $gitVersion = shell_exec('git --version 2>&1');
        $this->test(
            'Git Installation',
            strpos($gitVersion, 'git version') !== false,
            trim($gitVersion)
        );
        
        $gitUser = trim(shell_exec('git config user.name 2>&1'));
        $this->test(
            'Git User Configuration',
            !empty($gitUser) && strpos($gitUser, 'fatal') === false,
            "User: $gitUser"
        );
        
        $gitEmail = trim(shell_exec('git config user.email 2>&1'));
        $this->test(
            'Git Email Configuration',
            !empty($gitEmail) && strpos($gitEmail, 'fatal') === false,
            "Email: $gitEmail"
        );
        
        echo "\n";
    }
    
    private function testFilePermissions() {
        echo "📋 Testing File Permissions...\n";
        
        $this->test(
            'Current Directory Writable',
            is_writable('.'),
            'Required for creating files'
        );
        
        $this->test(
            'Can Create Test File',
            file_put_contents('test_write.tmp', 'test') !== false,
            'Testing write permissions'
        );
        
        if (file_exists('test_write.tmp')) {
            unlink('test_write.tmp');
        }
        
        echo "\n";
    }
    
    private function testDailyCommitScript() {
        echo "📋 Testing Daily Commit Script...\n";
        
        $this->test(
            'daily_commit.php exists',
            file_exists('daily_commit.php'),
            'Main automation script'
        );
        
        if (file_exists('daily_commit.php')) {
            $syntax = shell_exec('php -l daily_commit.php 2>&1');
            $this->test(
                'daily_commit.php syntax valid',
                strpos($syntax, 'No syntax errors') !== false,
                'PHP syntax check'
            );
            
            // Test class instantiation
            try {
                include_once 'daily_commit.php';
                $bot = new GitHubContributionBot();
                $this->test(
                    'GitHubContributionBot class instantiation',
                    $bot instanceof GitHubContributionBot,
                    'Class can be instantiated'
                );
            } catch (Exception $e) {
                $this->test(
                    'GitHubContributionBot class instantiation',
                    false,
                    'Error: ' . $e->getMessage()
                );
            }
        }
        
        echo "\n";
    }
    
    private function testActivityTracker() {
        echo "📋 Testing Activity Tracker...\n";
        
        $this->test(
            'github_activity_tracker.php exists',
            file_exists('github_activity_tracker.php'),
            'Activity tracking script'
        );
        
        if (file_exists('github_activity_tracker.php')) {
            $syntax = shell_exec('php -l github_activity_tracker.php 2>&1');
            $this->test(
                'github_activity_tracker.php syntax valid',
                strpos($syntax, 'No syntax errors') !== false,
                'PHP syntax check'
            );
            
            // Test class instantiation
            try {
                include_once 'github_activity_tracker.php';
                $tracker = new GitHubActivityTracker();
                $this->test(
                    'GitHubActivityTracker class instantiation',
                    $tracker instanceof GitHubActivityTracker,
                    'Class can be instantiated'
                );
            } catch (Exception $e) {
                $this->test(
                    'GitHubActivityTracker class instantiation',
                    false,
                    'Error: ' . $e->getMessage()
                );
            }
        }
        
        echo "\n";
    }
    
    private function testContributionUtilities() {
        echo "📋 Testing Contribution Utilities...\n";
        
        $this->test(
            'contribution_utilities.php exists',
            file_exists('contribution_utilities.php'),
            'Project utilities script'
        );
        
        if (file_exists('contribution_utilities.php')) {
            $syntax = shell_exec('php -l contribution_utilities.php 2>&1');
            $this->test(
                'contribution_utilities.php syntax valid',
                strpos($syntax, 'No syntax errors') !== false,
                'PHP syntax check'
            );
            
            // Test class instantiation
            try {
                include_once 'contribution_utilities.php';
                $utils = new ContributionUtilities();
                $this->test(
                    'ContributionUtilities class instantiation',
                    $utils instanceof ContributionUtilities,
                    'Class can be instantiated'
                );
            } catch (Exception $e) {
                $this->test(
                    'ContributionUtilities class instantiation',
                    false,
                    'Error: ' . $e->getMessage()
                );
            }
        }
        
        echo "\n";
    }
    
    private function testFileCreation() {
        echo "📋 Testing File Creation...\n";
        
        // Test if required files are created after instantiation
        if (class_exists('GitHubContributionBot')) {
            $bot = new GitHubContributionBot();
            
            $this->test(
                'contribution_log.txt created',
                file_exists('contribution_log.txt'),
                'Log file for commits'
            );
            
            $this->test(
                'daily_progress.json created',
                file_exists('daily_progress.json'),
                'Progress tracking file'
            );
            
            $this->test(
                'daily_quotes.txt created',
                file_exists('daily_quotes.txt'),
                'Quotes for commit messages'
            );
        }
        
        if (class_exists('GitHubActivityTracker')) {
            $tracker = new GitHubActivityTracker();
            
            $this->test(
                'github_activities.json created',
                file_exists('github_activities.json'),
                'Activity tracking data'
            );
            
            $this->test(
                'github_config.json created',
                file_exists('github_config.json'),
                'Configuration file'
            );
        }
        
        if (class_exists('ContributionUtilities')) {
            $utils = new ContributionUtilities();
            
            $this->test(
                'projects directory created',
                is_dir('projects'),
                'Directory for generated projects'
            );
            
            $this->test(
                'templates directory created',
                is_dir('templates'),
                'Directory for project templates'
            );
            
            $this->test(
                'project_ideas.json created',
                file_exists('project_ideas.json'),
                'Project ideas database'
            );
        }
        
        echo "\n";
    }
    
    private function testDataIntegrity() {
        echo "📋 Testing Data Integrity...\n";
        
        if (file_exists('daily_progress.json')) {
            $progress = json_decode(file_get_contents('daily_progress.json'), true);
            $this->test(
                'daily_progress.json valid JSON',
                $progress !== null,
                'JSON format validation'
            );
            
            if ($progress) {
                $this->test(
                    'daily_progress.json has required fields',
                    isset($progress['start_date']) && isset($progress['total_commits']),
                    'Required fields present'
                );
            }
        }
        
        if (file_exists('github_activities.json')) {
            $activities = json_decode(file_get_contents('github_activities.json'), true);
            $this->test(
                'github_activities.json valid JSON',
                $activities !== null,
                'JSON format validation'
            );
        }
        
        if (file_exists('github_config.json')) {
            $config = json_decode(file_get_contents('github_config.json'), true);
            $this->test(
                'github_config.json valid JSON',
                $config !== null,
                'JSON format validation'
            );
        }
        
        echo "\n";
    }
    
    private function testGitOperations() {
        echo "📋 Testing Git Operations...\n";
        
        $gitStatus = shell_exec('git status 2>&1');
        $this->test(
            'Git repository initialized',
            strpos($gitStatus, 'fatal: not a git repository') === false,
            'Git repository status'
        );
        
        if (strpos($gitStatus, 'fatal') === false) {
            $this->test(
                'Git working directory clean or has changes',
                strpos($gitStatus, 'working tree clean') !== false || 
                strpos($gitStatus, 'Changes') !== false ||
                strpos($gitStatus, 'Untracked') !== false,
                'Git status check'
            );
        }
        
        echo "\n";
    }
    
    private function displayResults() {
        echo "🏁 Test Results Summary\n";
        echo "======================\n";
        echo "Total Tests: {$this->totalTests}\n";
        echo "Passed: {$this->passedTests}\n";
        echo "Failed: " . ($this->totalTests - $this->passedTests) . "\n";
        echo "Success Rate: " . round(($this->passedTests / $this->totalTests) * 100, 1) . "%\n\n";
        
        if ($this->passedTests === $this->totalTests) {
            echo "🎉 All tests passed! System is ready to use.\n";
        } else {
            echo "⚠️  Some tests failed. Please check the issues above.\n\n";
            echo "Failed Tests:\n";
            foreach ($this->testResults as $result) {
                if (!$result['status']) {
                    echo "❌ {$result['name']} - {$result['message']}\n";
                }
            }
        }
        
        echo "\n";
    }
}

// Run tests if called directly
if (php_sapi_name() === 'cli') {
    $tester = new SystemTester();
    $success = $tester->runAllTests();
    
    exit($success ? 0 : 1);
}
?>
