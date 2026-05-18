<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\NotificationService;
use App\Services\BusinessService;
use App\Services\EventService;
use App\Services\EmergencyService;

/**
 * Scheduled Tasks Command
 * Handles automated tasks like sending reminders, notifications, etc.
 * 
 * Usage:
 * php spark tasks:run
 * php spark tasks:notifications
 * php spark tasks:reminders
 * php spark tasks:cleanup
 */
class ScheduledTasks extends BaseCommand
{
    protected $group = 'Tasks';
    protected $name = 'tasks:run';
    protected $description = 'Run all scheduled tasks';

    protected NotificationService $notificationService;
    protected BusinessService $businessService;
    protected EventService $eventService;
    protected EmergencyService $emergencyService;

    public function __construct(\Psr\Log\LoggerInterface $logger, \CodeIgniter\CLI\Commands $commands)
    {
        parent::__construct($logger, $commands);
    }

    public function initializeServices()
    {
        $this->notificationService = new NotificationService();
        $this->businessService = new BusinessService();
        $this->eventService = new EventService();
        $this->emergencyService = new EmergencyService();
    }

    public function run(array $params)
    {
        CLI::write('Starting scheduled tasks...', 'green');
        
        $this->initializeServices();
        
        $this->processPendingNotifications();
        $this->sendHearingReminders();
        $this->sendBirthdayGreetings();
        $this->sendBusinessRenewalReminders();
        $this->sendEventReminders();
        $this->checkEmergencySupplies();
        $this->cleanupOldData();
        
        CLI::write('All scheduled tasks completed!', 'green');
    }

    /**
     * Process pending notifications
     */
    protected function processPendingNotifications(): void
    {
        CLI::write('Processing pending notifications...', 'yellow');
        
        try {
            $this->notificationService->processPendingNotifications();
            CLI::write('✓ Pending notifications processed', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Error processing notifications: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Send hearing reminders
     */
    protected function sendHearingReminders(): void
    {
        CLI::write('Sending hearing reminders...', 'yellow');
        
        try {
            $this->notificationService->sendHearingReminders();
            CLI::write('✓ Hearing reminders sent', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Error sending hearing reminders: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Send birthday greetings
     */
    protected function sendBirthdayGreetings(): void
    {
        CLI::write('Sending birthday greetings...', 'yellow');
        
        try {
            $this->notificationService->sendBirthdayGreetings();
            CLI::write('✓ Birthday greetings sent', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Error sending birthday greetings: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Send business renewal reminders
     */
    protected function sendBusinessRenewalReminders(): void
    {
        CLI::write('Sending business renewal reminders...', 'yellow');
        
        try {
            $this->businessService->sendRenewalReminders();
            CLI::write('✓ Business renewal reminders sent', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Error sending business reminders: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Send event reminders
     */
    protected function sendEventReminders(): void
    {
        CLI::write('Sending event reminders...', 'yellow');
        
        try {
            $this->eventService->sendEventReminders();
            CLI::write('✓ Event reminders sent', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Error sending event reminders: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Check emergency supplies
     */
    protected function checkEmergencySupplies(): void
    {
        CLI::write('Checking emergency supplies...', 'yellow');
        
        try {
            $lowSupplies = $this->emergencyService->checkLowSupplies();
            
            if (!empty($lowSupplies)) {
                CLI::write('⚠ Warning: ' . count($lowSupplies) . ' emergency supplies are running low', 'yellow');
                
                // Send notification to administrators
                foreach ($lowSupplies as $supply) {
                    CLI::write("  - {$supply['item_name']}: {$supply['quantity']} {$supply['unit']} remaining", 'yellow');
                }
            } else {
                CLI::write('✓ Emergency supplies OK', 'green');
            }
        } catch (\Exception $e) {
            CLI::write('✗ Error checking emergency supplies: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Clean up old data
     */
    protected function cleanupOldData(): void
    {
        CLI::write('Cleaning up old data...', 'yellow');
        
        try {
            // Clean old notifications (90 days)
            $notificationModel = new \App\Models\NotificationModel();
            $deletedNotifications = $notificationModel->cleanOldNotifications(90);
            CLI::write("✓ Cleaned {$deletedNotifications} old notifications", 'green');
            
            // Clean orphaned files
            $documentModel = new \App\Models\DocumentModel();
            $cleanedFiles = $documentModel->cleanupOrphanedFiles();
            CLI::write('✓ Cleaned ' . count($cleanedFiles) . ' orphaned files', 'green');
            
        } catch (\Exception $e) {
            CLI::write('✗ Error during cleanup: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Run only notifications
     */
    public function notifications(array $params)
    {
        CLI::write('Processing notifications only...', 'green');
        $this->processPendingNotifications();
    }

    /**
     * Run only reminders
     */
    public function reminders(array $params)
    {
        CLI::write('Sending reminders...', 'green');
        $this->sendHearingReminders();
        $this->sendBirthdayGreetings();
        $this->sendBusinessRenewalReminders();
        $this->sendEventReminders();
    }

    /**
     * Run only cleanup
     */
    public function cleanup(array $params)
    {
        CLI::write('Running cleanup tasks...', 'green');
        $this->cleanupOldData();
    }

    /**
     * Generate system reports
     */
    public function reports(array $params)
    {
        CLI::write('Generating system reports...', 'green');
        
        try {
            $analyticsService = new \App\Services\AnalyticsService();
            
            // Generate daily report
            $report = $analyticsService->getDashboardAnalytics();
            
            CLI::write('Daily System Report - ' . date('Y-m-d'), 'cyan');
            CLI::write('================================', 'cyan');
            CLI::write('Total Population: ' . $report['population']['total_population'], 'white');
            CLI::write('Certificates This Month: ' . $report['certificates']['this_month'], 'white');
            CLI::write('Active Businesses: ' . $report['business']['active_businesses'] ?? 0, 'white');
            CLI::write('Emergency Incidents: ' . count($report['emergency']['active_incidents'] ?? []), 'white');
            
            CLI::write('✓ System report generated', 'green');
            
        } catch (\Exception $e) {
            CLI::write('✗ Error generating reports: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Test all services
     */
    public function test(array $params)
    {
        CLI::write('Testing all services...', 'green');
        
        // Test database connection
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            CLI::write('✓ Database connection OK', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Database connection failed: ' . $e->getMessage(), 'red');
        }

        // Test SMS service
        $smsApiKey = env('SMS_API_KEY');
        if ($smsApiKey) {
            CLI::write('✓ SMS service configured', 'green');
        } else {
            CLI::write('⚠ SMS service not configured', 'yellow');
        }

        // Test email service
        $smtpHost = env('SMTP_HOST');
        if ($smtpHost) {
            CLI::write('✓ Email service configured', 'green');
        } else {
            CLI::write('⚠ Email service not configured', 'yellow');
        }

        // Test file permissions
        $uploadPath = WRITEPATH . 'uploads/';
        if (is_writable($uploadPath)) {
            CLI::write('✓ Upload directory writable', 'green');
        } else {
            CLI::write('✗ Upload directory not writable', 'red');
        }

        CLI::write('Service test completed', 'cyan');
    }

    /**
     * Initialize system data
     */
    public function init(array $params)
    {
        CLI::write('Initializing system data...', 'green');
        
        try {
            // Run migrations
            CLI::write('Running database migrations...', 'yellow');
            $migrate = \Config\Services::migrations();
            $migrate->latest();
            CLI::write('✓ Migrations completed', 'green');
            
            // Create default directories
            $directories = [
                WRITEPATH . 'uploads/documents/',
                WRITEPATH . 'uploads/qr_temp/',
                WRITEPATH . 'uploads/exports/',
                WRITEPATH . 'uploads/profiles/',
            ];
            
            foreach ($directories as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                    CLI::write("✓ Created directory: {$dir}", 'green');
                }
            }
            
            CLI::write('✓ System initialization completed', 'green');
            
        } catch (\Exception $e) {
            CLI::write('✗ Initialization failed: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Backup system data
     */
    public function backup(array $params)
    {
        CLI::write('Creating system backup...', 'green');
        
        try {
            $backupDir = WRITEPATH . 'backups/';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupDir . "backup_{$timestamp}.sql";
            
            // Database backup (MySQL)
            $db = \Config\Database::connect();
            $dbConfig = $db->getDatabase();
            
            $command = "mysqldump -h localhost -u root {$dbConfig} > {$backupFile}";
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                CLI::write("✓ Database backup created: {$backupFile}", 'green');
            } else {
                CLI::write('✗ Database backup failed', 'red');
            }
            
            // Compress backup
            $zipFile = $backupDir . "backup_{$timestamp}.zip";
            $zip = new \ZipArchive();
            
            if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($backupFile, "database_{$timestamp}.sql");
                
                // Add upload files
                $uploadDir = WRITEPATH . 'uploads/';
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($uploadDir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'uploads/' . substr($filePath, strlen($uploadDir));
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                
                $zip->close();
                CLI::write("✓ Complete backup created: {$zipFile}", 'green');
                
                // Remove SQL file (now in ZIP)
                unlink($backupFile);
            }
            
        } catch (\Exception $e) {
            CLI::write('✗ Backup failed: ' . $e->getMessage(), 'red');
        }
    }
}