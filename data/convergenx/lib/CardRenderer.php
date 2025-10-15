<?php

class CardRenderer {
    private $configPath;
    private $pluginsPath;
    
    public function __construct($configPath = null, $pluginsPath = null) {
        $webRoot = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__ . '/..';
        $this->configPath = $configPath ?? $webRoot . '/config/dashboard.json';
        $this->pluginsPath = $pluginsPath ?? $webRoot . '/plugins';
    }
    
    public function renderDashboard() {
        try {
            if (!file_exists($this->configPath)) {
                throw new Exception("Dashboard configuration not found: " . $this->configPath);
            }
            
            $configJson = file_get_contents($this->configPath);
            $dashboardConfig = json_decode($configJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON in dashboard config: " . json_last_error_msg());
            }
            
            return $this->renderFromConfig($dashboardConfig);
            
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }
    
    private function renderFromConfig($config) {
        $html = '<!DOCTYPE html>';
        $html .= '<html lang="en">';
        $html .= $this->renderHead($config['dashboard'] ?? []);
        $html .= '<body>';
        $html .= $this->renderHeader($config['dashboard'] ?? []);
        $html .= $this->renderGrid($config['panels'] ?? []);
        $html .= $this->renderScripts();
        $html .= '</body>';
        $html .= '</html>';
        
        return $html;
    }
    
    private function renderHead($dashboardConfig) {
        $title = htmlspecialchars($dashboardConfig['title'] ?? 'Convergenx');
        
        return <<<HTML
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <link href="https://cdn.jsdelivr.net/npm/gridstack/dist/gridstack.min.css" rel="stylesheet"/>
    <style>
        body { 
            margin: 0; 
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #e0e0e0;
        }
        .grid-stack { 
            background: #2d2d2d; 
            min-height: 100vh;
            padding: 20px;
        }
        .grid-stack-item-content {
            background: #3d3d3d;
            border: 1px solid #555;
            border-radius: 8px;
            padding: 15px;
            color: #e0e0e0;
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .panel-title {
            font-weight: bold;
            font-size: 1.1em;
        }
        .card {
            background: #4d4d4d;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .card-content {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .error { 
            color: #ff6b6b; 
            padding: 20px;
            text-align: center;
        }
        .success { color: #00ff00; }
        .warning { color: #ffff00; }
        .info { color: #00ffff; }
    </style>
</head>
HTML;
    }
    
    private function renderHeader($dashboardConfig) {
        $title = htmlspecialchars($dashboardConfig['title'] ?? 'Convergenx');
        
        return <<<HTML
<header style="background: #333; padding: 15px 20px; border-bottom: 1px solid #555;">
    <h1 style="margin: 0; color: #00ff00;">$title</h1>
    <div style="font-size: 0.8em; opacity: 0.7;">Plugin-Driven Dashboard</div>
</header>
HTML;
    }
    
    private function renderGrid($panels) {
        $html = '<div class="grid-stack" data-gs-width="' . ($panels['gridColumns'] ?? 12) . '">';
        
        foreach ($panels as $panel) {
            if (isset($panel['id'])) {
                $html .= $this->renderPanel($panel);
            }
        }
        
        $html .= '</div>';
        return $html;
    }
    
    private function renderPanel($panel) {
        $panelId = htmlspecialchars($panel['id']);
        $title = htmlspecialchars($panel['title'] ?? 'Untitled Panel');
        $width = $panel['width'] ?? 6;
        $height = $panel['height'] ?? 2;
        $x = $panel['x'] ?? 0;
        $y = $panel['y'] ?? 0;
        
        $html = <<<HTML
        <div class="grid-stack-item" 
             data-gs-width="$width" 
             data-gs-height="$height" 
             data-gs-x="$x" 
             data-gs-y="$y"
             data-gs-id="$panelId">
            <div class="grid-stack-item-content">
                <div class="panel-header">
                    <div class="panel-title">$title</div>
                </div>
HTML;

        foreach ($panel['cards'] ?? [] as $card) {
            $html .= $this->renderCard($card);
        }
        
        $html .= '</div></div>';
        return $html;
    }
    
    private function renderCard($card) {
        $cardType = $card['type'] ?? 'unknown';
        $title = htmlspecialchars($card['title'] ?? $cardType);
        
        // NEW: Try to load plugin content, fall back to placeholder
        $content = $this->loadPluginContent($cardType, $card);
        
        return <<<HTML
        <div class="card" data-card-type="$cardType">
            <div style="font-size: 0.9em; opacity: 0.8;">$title</div>
            <div class="card-content">
                $content
            </div>
        </div>
HTML;
    }
    
    // NEW: Plugin Loader System!
    private function loadPluginContent($pluginType, $cardConfig) {
        $pluginDir = $this->pluginsPath . '/' . $pluginType;
        
        // Check if plugin exists
        if (!is_dir($pluginDir)) {
            return "<span style='color: #ff6b6b;'>Plugin not found: $pluginType</span>";
        }
        
        $manifestPath = $pluginDir . '/manifest.json';
        if (!file_exists($manifestPath)) {
            return "<span style='color: #ff6b6b;'>Missing manifest.json for: $pluginType</span>";
        }
        
        try {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            
            if (isset($manifest['handler']) && $manifest['handler'] === 'php') {
                // PHP-based plugin
                return $this->executePhpPlugin($pluginDir, $manifest, $cardConfig);
            } else {
                // Default: shell command plugin
                return $this->executeShellPlugin($pluginDir, $manifest, $cardConfig);
            }
            
        } catch (Exception $e) {
            return "<span style='color: #ff6b6b;'>Plugin error: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
    }
    
    private function executeShellPlugin($pluginDir, $manifest, $cardConfig) {
        if (!isset($manifest['command'])) {
            return "<span style='color: #ff6b6b;'>No command defined in manifest</span>";
        }
        
        $command = $pluginDir . '/' . $manifest['command'];
        if (!file_exists($command)) {
            return "<span style='color: #ff6b6b;'>Command not found: $command</span>";
        }
        
        // REMOVED: chmod call that was causing permission errors
        // The script should already be executable on the host
        
        $output = shell_exec($command . ' 2>&1');
        
        return nl2br(htmlspecialchars(trim($output ?? 'No output')));
    }
    
    private function executePhpPlugin($pluginDir, $manifest, $cardConfig) {
        if (!isset($manifest['entrypoint'])) {
            return "<span style='color: #ff6b6b;'>No PHP entrypoint defined</span>";
        }
        
        $entrypoint = $pluginDir . '/' . $manifest['entrypoint'];
        if (!file_exists($entrypoint)) {
            return "<span style='color: #ff6b6b;'>PHP entrypoint not found: $entrypoint</span>";
        }
        
        // Capture PHP output
        ob_start();
        include $entrypoint;
        $output = ob_get_clean();
        
        return $output;
    }
    
    private function renderScripts() {
        return <<<HTML
<script src="https://cdn.jsdelivr.net/npm/gridstack/dist/gridstack-all.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var grid = GridStack.init({
            column: 12,
            cellHeight: 80,
            minRow: 1,
            removable: false,
            acceptWidgets: false
        });
        
        console.log('Convergenx grid with plugin system initialized!');
    });
</script>
HTML;
    }
    
    private function renderError($message) {
        return "<div class='error'>Configuration Error: " . htmlspecialchars($message) . "</div>";
    }
}
?>
