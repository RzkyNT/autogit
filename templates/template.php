<?php
/**
 * {{TITLE}}
 * 
 * {{DESCRIPTION}}
 * 
 * @author {{AUTHOR}}
 * @date {{DATE}}
 */

class {{CLASS_NAME}} {
    public function __construct() {
        // Constructor
    }
    
    public function run() {
        echo "{{TITLE}} is running...\n";
    }
}

// Usage
if (php_sapi_name() === "cli") {
    $app = new {{CLASS_NAME}}();
    $app->run();
}
?>