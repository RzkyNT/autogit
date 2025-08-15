<?php
/**
 * Example Project
 * 
 * A simple php project: Example Project
 * 
 * @author Developer
 * @date 2025-08-15
 */

class ExampleProject {
    public function __construct() {
        // Constructor
    }
    
    public function run() {
        echo "Example Project is running...\n";
    }
}

// Usage
if (php_sapi_name() === "cli") {
    $app = new ExampleProject();
    $app->run();
}
