<?php
/**
 * Test Project
 *
 * A simple php project: Test Project
 *
 * @author RIZQI AHSAN SETIAWAN
 * @date 2025-08-15
 */

class TestProject {
    public function __construct() {
        // Constructor
    }

    public function run() {
        echo "Test Project is running...\n";
    }
}

// Usage
if (php_sapi_name() === "cli") {
    $app = new TestProject();
    $app->run();
}