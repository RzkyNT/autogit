<?php
/**
 * GitHub Contribution Utilities
 * 
 * Kumpulan utility untuk membantu melakukan berbagai aktivitas GitHub
 * secara konsisten dan bermakna.
 */

class ContributionUtilities {
    private $projectsDir = 'projects';
    private $templatesDir = 'templates';
    private $ideasFile = 'project_ideas.json';
    
    public function __construct() {
        $this->initializeDirectories();
        $this->initializeIdeas();
    }
    
    /**
     * Inisialisasi direktori yang diperlukan
     */
    private function initializeDirectories() {
        if (!is_dir($this->projectsDir)) {
            mkdir($this->projectsDir, 0755, true);
        }
        
        if (!is_dir($this->templatesDir)) {
            mkdir($this->templatesDir, 0755, true);
            $this->createTemplates();
        }
    }
    
    /**
     * Inisialisasi file ide project
     */
    private function initializeIdeas() {
        if (!file_exists($this->ideasFile)) {
            $ideas = [
                'web_projects' => [
                    'Simple Calculator',
                    'Todo List App',
                    'Weather App',
                    'Quote Generator',
                    'Color Palette Generator',
                    'Password Generator',
                    'QR Code Generator',
                    'Unit Converter',
                    'Expense Tracker',
                    'Recipe Finder'
                ],
                'php_projects' => [
                    'Contact Form Handler',
                    'File Upload System',
                    'Simple CMS',
                    'User Authentication System',
                    'API Rate Limiter',
                    'Log Analyzer',
                    'Database Backup Tool',
                    'Email Newsletter System',
                    'Image Resizer',
                    'CSV Data Processor'
                ],
                'utility_scripts' => [
                    'File Organizer',
                    'Duplicate File Finder',
                    'System Monitor',
                    'Backup Automation',
                    'Text File Merger',
                    'Image Optimizer',
                    'Git Helper Scripts',
                    'Development Environment Setup',
                    'Code Formatter',
                    'Documentation Generator'
                ],
                'learning_projects' => [
                    'Algorithm Implementations',
                    'Design Pattern Examples',
                    'Code Challenges Solutions',
                    'Tutorial Follow-alongs',
                    'Concept Demonstrations',
                    'Best Practices Examples',
                    'Performance Comparisons',
                    'Security Implementations',
                    'Testing Examples',
                    'Refactoring Exercises'
                ]
            ];
            
            file_put_contents($this->ideasFile, json_encode($ideas, JSON_PRETTY_PRINT));
        }
    }
    
    /**
     * Membuat template file
     */
    private function createTemplates() {
        // Template HTML
        $htmlTemplate = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{TITLE}}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{TITLE}}</h1>
        <p>{{DESCRIPTION}}</p>
        <!-- Your content here -->
    </div>
</body>
</html>';
        
        file_put_contents($this->templatesDir . '/template.html', $htmlTemplate);
        
        // Template PHP
        $phpTemplate = '<?php
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
?>';
        
        file_put_contents($this->templatesDir . '/template.php', $phpTemplate);
        
        // Template README
        $readmeTemplate = '# {{TITLE}}

{{DESCRIPTION}}

## Features

- Feature 1
- Feature 2
- Feature 3

## Installation

```bash
git clone <repository-url>
cd {{PROJECT_DIR}}
```

## Usage

```bash
php {{MAIN_FILE}}
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).
';
        
        file_put_contents($this->templatesDir . '/README.md', $readmeTemplate);
    }
    
    /**
     * Membuat project baru
     */
    public function createNewProject($name = null, $type = 'php') {
        if (!$name) {
            $name = $this->getRandomProjectIdea($type);
        }
        
        $projectDir = $this->projectsDir . '/' . $this->sanitizeProjectName($name);
        
        if (is_dir($projectDir)) {
            echo "❌ Project '$name' sudah ada!\n";
            return false;
        }
        
        mkdir($projectDir, 0755, true);
        
        // Copy template berdasarkan type
        $this->setupProjectFromTemplate($projectDir, $name, $type);
        
        echo "✅ Project '$name' berhasil dibuat di: $projectDir\n";
        echo "📁 Struktur project:\n";
        $this->listDirectory($projectDir, '   ');
        
        return $projectDir;
    }
    
    /**
     * Setup project dari template
     */
    private function setupProjectFromTemplate($projectDir, $name, $type) {
        $className = $this->generateClassName($name);
        $projectDirName = basename($projectDir);
        
        $replacements = [
            '{{TITLE}}' => $name,
            '{{DESCRIPTION}}' => "A simple $type project: $name",
            '{{AUTHOR}}' => get_current_user(),
            '{{DATE}}' => date('Y-m-d'),
            '{{CLASS_NAME}}' => $className,
            '{{PROJECT_DIR}}' => $projectDirName,
            '{{MAIN_FILE}}' => 'index.php'
        ];
        
        // Copy dan replace template files
        $templates = ['template.php', 'template.html', 'README.md'];
        
        foreach ($templates as $template) {
            $templatePath = $this->templatesDir . '/' . $template;
            if (file_exists($templatePath)) {
                $content = file_get_contents($templatePath);
                
                foreach ($replacements as $placeholder => $value) {
                    $content = str_replace($placeholder, $value, $content);
                }
                
                $newFileName = str_replace('template.', '', $template);
                if ($newFileName === 'php') $newFileName = 'index.php';
                if ($newFileName === 'html') $newFileName = 'index.html';
                
                file_put_contents($projectDir . '/' . $newFileName, $content);
            }
        }
        
        // Buat file tambahan
        file_put_contents($projectDir . '/.gitignore', "*.log\n*.tmp\nvendor/\n");
        file_put_contents($projectDir . '/CHANGELOG.md', "# Changelog\n\n## [1.0.0] - " . date('Y-m-d') . "\n- Initial release\n");
    }
    
    /**
     * Mendapatkan ide project random
     */
    private function getRandomProjectIdea($category = null) {
        $ideas = json_decode(file_get_contents($this->ideasFile), true);
        
        if ($category && isset($ideas[$category])) {
            return $ideas[$category][array_rand($ideas[$category])];
        }
        
        // Random dari semua kategori
        $allIdeas = [];
        foreach ($ideas as $categoryIdeas) {
            $allIdeas = array_merge($allIdeas, $categoryIdeas);
        }
        
        return $allIdeas[array_rand($allIdeas)];
    }
    
    /**
     * Sanitize nama project untuk dijadikan nama direktori
     */
    private function sanitizeProjectName($name) {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name));
    }
    
    /**
     * Generate nama class dari nama project
     */
    private function generateClassName($name) {
        return str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $name)));
    }
    
    /**
     * List isi direktori
     */
    private function listDirectory($dir, $prefix = '') {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                echo $prefix . $item . "\n";
            }
        }
    }
    
    /**
     * Membuat commit dengan perubahan bermakna
     */
    public function createMeaningfulCommit($projectPath = null) {
        if (!$projectPath) {
            // Pilih project random yang ada
            $projects = glob($this->projectsDir . '/*', GLOB_ONLYDIR);
            if (empty($projects)) {
                echo "❌ Tidak ada project yang tersedia. Buat project baru terlebih dahulu.\n";
                return false;
            }
            $projectPath = $projects[array_rand($projects)];
        }
        
        $projectName = basename($projectPath);
        
        // Buat perubahan bermakna
        $changes = [
            'update_readme' => function($path) {
                $readmePath = $path . '/README.md';
                if (file_exists($readmePath)) {
                    $content = file_get_contents($readmePath);
                    $content .= "\n## Update " . date('Y-m-d H:i:s') . "\n- Minor improvements and bug fixes\n";
                    file_put_contents($readmePath, $content);
                    return "Updated README with latest changes";
                }
                return null;
            },
            'update_changelog' => function($path) {
                $changelogPath = $path . '/CHANGELOG.md';
                if (file_exists($changelogPath)) {
                    $content = file_get_contents($changelogPath);
                    $version = '1.0.' . rand(1, 99);
                    $newEntry = "\n## [$version] - " . date('Y-m-d') . "\n- Performance improvements\n- Code optimization\n";
                    $content = str_replace("# Changelog\n", "# Changelog\n" . $newEntry, $content);
                    file_put_contents($changelogPath, $content);
                    return "Updated changelog to version $version";
                }
                return null;
            },
            'add_comment' => function($path) {
                $phpFiles = glob($path . '/*.php');
                if (!empty($phpFiles)) {
                    $file = $phpFiles[0];
                    $content = file_get_contents($file);
                    $comment = "\n// Updated on " . date('Y-m-d H:i:s') . " - Code review and optimization\n";
                    $content = str_replace('<?php', '<?php' . $comment, $content);
                    file_put_contents($file, $content);
                    return "Added code comments and documentation";
                }
                return null;
            }
        ];
        
        // Pilih perubahan random
        $changeType = array_rand($changes);
        $changeDescription = $changes[$changeType]($projectPath);
        
        if ($changeDescription) {
            echo "✅ Perubahan dibuat: $changeDescription\n";
            echo "📁 Project: $projectName\n";
            return $changeDescription;
        }
        
        return false;
    }
    
    /**
     * Menampilkan daftar project yang ada
     */
    public function listProjects() {
        $projects = glob($this->projectsDir . '/*', GLOB_ONLYDIR);
        
        echo "\n📁 Daftar Project:\n";
        echo "==================\n";
        
        if (empty($projects)) {
            echo "Belum ada project. Gunakan 'create' untuk membuat project baru.\n";
            return;
        }
        
        foreach ($projects as $project) {
            $name = basename($project);
            $files = glob($project . '/*');
            $fileCount = count($files);
            $lastModified = date('Y-m-d H:i:s', filemtime($project));
            
            echo "• $name ($fileCount files) - Last modified: $lastModified\n";
        }
        
        echo "==================\n";
    }
    
    /**
     * Menampilkan ide project
     */
    public function showProjectIdeas($category = null) {
        $ideas = json_decode(file_get_contents($this->ideasFile), true);
        
        echo "\n💡 Ide Project:\n";
        echo "===============\n";
        
        if ($category && isset($ideas[$category])) {
            echo "Kategori: " . ucwords(str_replace('_', ' ', $category)) . "\n";
            foreach ($ideas[$category] as $idea) {
                echo "• $idea\n";
            }
        } else {
            foreach ($ideas as $cat => $categoryIdeas) {
                echo "\n" . ucwords(str_replace('_', ' ', $cat)) . ":\n";
                foreach ($categoryIdeas as $idea) {
                    echo "  • $idea\n";
                }
            }
        }
        
        echo "===============\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $utils = new ContributionUtilities();
    
    $action = $argv[1] ?? 'help';
    
    switch ($action) {
        case 'create':
            $name = $argv[2] ?? null;
            $type = $argv[3] ?? 'php';
            $utils->createNewProject($name, $type);
            break;
            
        case 'commit':
            $project = $argv[2] ?? null;
            $utils->createMeaningfulCommit($project);
            break;
            
        case 'list':
            $utils->listProjects();
            break;
            
        case 'ideas':
            $category = $argv[2] ?? null;
            $utils->showProjectIdeas($category);
            break;
            
        case 'help':
        default:
            echo "GitHub Contribution Utilities\n";
            echo "=============================\n";
            echo "Usage: php contribution_utilities.php [command] [options]\n\n";
            echo "Commands:\n";
            echo "  create [name] [type]  - Buat project baru\n";
            echo "  commit [project]      - Buat commit bermakna\n";
            echo "  list                  - Tampilkan daftar project\n";
            echo "  ideas [category]      - Tampilkan ide project\n";
            echo "  help                  - Tampilkan bantuan\n\n";
            echo "Examples:\n";
            echo "  php contribution_utilities.php create \"Todo App\" web\n";
            echo "  php contribution_utilities.php commit\n";
            echo "  php contribution_utilities.php ideas web_projects\n";
            break;
    }
}
?>
