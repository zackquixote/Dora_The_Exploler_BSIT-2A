<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * System Performance Monitor Controller
 */
class SystemMonitor extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'System Performance Monitor',
            'system_info' => $this->getSystemInfo(),
            'php_info' => $this->getPHPInfo(),
            'database_info' => $this->getDatabaseInfo(),
            'performance_metrics' => $this->getPerformanceMetrics(),
        ];

        return view('system/monitor', $data);
    }

    /**
     * Get system information
     */
    private function getSystemInfo(): array
    {
        return [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'current_memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'peak_memory_usage' => $this->formatBytes(memory_get_peak_usage(true)),
        ];
    }

    /**
     * Get PHP configuration info
     */
    private function getPHPInfo(): array
    {
        return [
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
            'extensions' => get_loaded_extensions(),
            'error_reporting' => error_reporting(),
            'display_errors' => ini_get('display_errors'),
            'log_errors' => ini_get('log_errors'),
        ];
    }

    /**
     * Get database information
     */
    private function getDatabaseInfo(): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get database version
            $version = $db->query("SELECT VERSION() as version")->getRow();
            
            // Get database size
            $dbName = $db->getDatabase();
            $sizeQuery = $db->query("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);
            $size = $sizeQuery->getRow();

            // Get table count
            $tableCount = $db->query("
                SELECT COUNT(*) as count 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName])->getRow();

            // Get connection info
            $processlist = $db->query("SHOW PROCESSLIST")->getResult();

            return [
                'version' => $version->version ?? 'Unknown',
                'database_name' => $dbName,
                'size_mb' => $size->size_mb ?? 0,
                'table_count' => $tableCount->count ?? 0,
                'active_connections' => count($processlist),
                'connection_status' => 'Connected',
            ];
        } catch (\Exception $e) {
            return [
                'version' => 'Unknown',
                'database_name' => 'Unknown',
                'size_mb' => 0,
                'table_count' => 0,
                'active_connections' => 0,
                'connection_status' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        $startTime = microtime(true);
        
        // Test database query performance
        $db = \Config\Database::connect();
        $dbStartTime = microtime(true);
        $db->query("SELECT 1")->getResult();
        $dbQueryTime = (microtime(true) - $dbStartTime) * 1000;

        // Test file system performance
        $fsStartTime = microtime(true);
        $testFile = WRITEPATH . 'test_performance.tmp';
        file_put_contents($testFile, 'test');
        $content = file_get_contents($testFile);
        unlink($testFile);
        $fsTime = (microtime(true) - $fsStartTime) * 1000;

        $totalTime = (microtime(true) - $startTime) * 1000;

        return [
            'page_load_time' => round($totalTime, 2),
            'database_query_time' => round($dbQueryTime, 2),
            'filesystem_time' => round($fsTime, 2),
            'memory_usage_percent' => $this->getMemoryUsagePercent(),
            'disk_usage' => $this->getDiskUsage(),
        ];
    }

    /**
     * Get memory usage percentage
     */
    private function getMemoryUsagePercent(): float
    {
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $currentUsage = memory_get_usage(true);
        
        if ($memoryLimit > 0) {
            return round(($currentUsage / $memoryLimit) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit)-1]);
        $value = (int) $limit;
        
        switch($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    /**
     * Get disk usage information
     */
    private function getDiskUsage(): array
    {
        $path = ROOTPATH;
        $totalBytes = disk_total_space($path);
        $freeBytes = disk_free_space($path);
        $usedBytes = $totalBytes - $freeBytes;
        
        return [
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($usedBytes),
            'free' => $this->formatBytes($freeBytes),
            'usage_percent' => round(($usedBytes / $totalBytes) * 100, 2),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * AJAX endpoint for real-time metrics
     */
    public function getMetrics()
    {
        $metrics = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => $this->parseMemoryLimit(ini_get('memory_limit')),
            'timestamp' => time(),
        ];

        return $this->response->setJSON($metrics);
    }

    /**
     * Clear system cache
     */
    public function clearCache()
    {
        try {
            // Clear application cache
            $cacheDir = WRITEPATH . 'cache';
            if (is_dir($cacheDir)) {
                $files = glob($cacheDir . '/*');
                $deletedCount = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $deletedCount++;
                    }
                }
            }

            // Clear OPcache if available
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Cache cleared successfully. Deleted {$deletedCount} files.",
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Optimize database tables
     */
    public function optimizeDatabase()
    {
        try {
            $db = \Config\Database::connect();
            $dbName = $db->getDatabase();
            
            // Get all tables
            $tables = $db->query("SHOW TABLES")->getResult();
            $optimizedTables = [];
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                $db->query("OPTIMIZE TABLE `{$tableName}`");
                $optimizedTables[] = $tableName;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database optimized successfully.',
                'tables' => $optimizedTables,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error optimizing database: ' . $e->getMessage(),
            ]);
        }
    }
}