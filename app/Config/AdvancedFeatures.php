<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Advanced Features Configuration
 * Configuration settings for all new advanced features
 */
class AdvancedFeatures extends BaseConfig
{
    /**
     * SMS Configuration
     */
    public array $sms = [
        'provider' => 'semaphore', // semaphore, twilio, etc.
        'api_key' => '', // Set in .env as SMS_API_KEY
        'sender_name' => 'BARANGAY',
        'rate_limit' => 100, // messages per hour
    ];

    /**
     * Email Configuration
     */
    public array $email = [
        'from_email' => 'noreply@barangay.gov.ph',
        'from_name' => 'Barangay Management System',
        'reply_to' => 'admin@barangay.gov.ph',
    ];

    /**
     * QR Code Configuration
     */
    public array $qrCode = [
        'size' => 300, // pixels
        'error_correction' => 'M', // L, M, Q, H
        'margin' => 2,
        'expiry_days' => [
            'certificate' => 365,
            'resident' => 180,
        ],
    ];

    /**
     * Document Management Configuration
     */
    public array $documents = [
        'max_file_size' => 10485760, // 10MB in bytes
        'allowed_types' => [
            'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'
        ],
        'storage_path' => WRITEPATH . 'uploads/documents/',
        'versions_to_keep' => 5,
        'cleanup_after_days' => 30,
    ];

    /**
     * Notification Configuration
     */
    public array $notifications = [
        'batch_size' => 50, // notifications to process at once
        'retry_attempts' => 3,
        'retry_delay' => 300, // seconds
        'cleanup_after_days' => 90,
        'rate_limits' => [
            'sms' => 100, // per hour
            'email' => 500, // per hour
        ],
    ];

    /**
     * Analytics Configuration
     */
    public array $analytics = [
        'cache_duration' => 3600, // 1 hour in seconds
        'export_formats' => ['csv', 'excel', 'pdf'],
        'max_export_records' => 10000,
        'report_retention_days' => 365,
    ];

    /**
     * Emergency Response Configuration
     */
    public array $emergency = [
        'auto_dispatch_severity' => ['critical', 'high'],
        'response_time_targets' => [
            'critical' => 5, // minutes
            'high' => 15,
            'medium' => 30,
            'low' => 60,
        ],
        'evacuation_centers' => [
            [
                'name' => 'Barangay Hall',
                'capacity' => 200,
                'facilities' => ['First Aid', 'Generator', 'Water'],
                'coordinates' => ['lat' => 10.1234, 'lng' => 123.5678],
            ],
            [
                'name' => 'Elementary School',
                'capacity' => 500,
                'facilities' => ['Classrooms', 'Kitchen', 'Restrooms'],
                'coordinates' => ['lat' => 10.1244, 'lng' => 123.5688],
            ],
        ],
    ];

    /**
     * Business Management Configuration
     */
    public array $business = [
        'permit_validity_years' => 1,
        'renewal_reminder_days' => 30,
        'fee_structure' => [
            'base_fee' => 500.00,
            'capital_tiers' => [
                10000 => 100.00,
                50000 => 300.00,
                100000 => 500.00,
                'above' => 1000.00,
            ],
            'type_fees' => [
                'Retail Store' => 200.00,
                'Restaurant' => 500.00,
                'Sari-sari Store' => 100.00,
                'Beauty Salon' => 300.00,
                'Internet Cafe' => 400.00,
                'Repair Shop' => 250.00,
                'Bakery' => 350.00,
                'Pharmacy' => 600.00,
            ],
        ],
    ];

    /**
     * Event Management Configuration
     */
    public array $events = [
        'max_participants_default' => 100,
        'registration_deadline_days' => 7,
        'reminder_hours_before' => 24,
        'feedback_rating_scale' => 5,
        'event_types' => [
            'Community Meeting',
            'Health Program',
            'Sports Event',
            'Cultural Activity',
            'Training/Seminar',
            'Emergency Drill',
            'Cleanup Drive',
            'Festival',
        ],
    ];

    /**
     * Health Records Configuration
     */
    public array $health = [
        'blood_types' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'],
        'vaccination_schedule' => [
            'COVID-19' => 365, // days between doses
            'Flu' => 365,
            'Hepatitis B' => 1825, // 5 years
        ],
        'privacy_level' => 'restricted', // public, internal, restricted, confidential
    ];

    /**
     * Security Configuration
     */
    public array $security = [
        'session_timeout' => 3600, // 1 hour
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'password_requirements' => [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_symbols' => false,
        ],
        'audit_log_retention' => 365, // days
    ];

    /**
     * System Maintenance Configuration
     */
    public array $maintenance = [
        'backup_schedule' => 'daily', // daily, weekly, monthly
        'backup_retention_days' => 30,
        'cleanup_schedule' => 'weekly',
        'health_check_interval' => 300, // 5 minutes
        'log_rotation_days' => 7,
    ];

    /**
     * API Configuration
     */
    public array $api = [
        'rate_limit' => 1000, // requests per hour
        'version' => 'v1',
        'authentication' => 'jwt', // jwt, api_key, oauth
        'cors_origins' => ['*'], // allowed origins
    ];

    /**
     * Integration Configuration
     */
    public array $integrations = [
        'philsys' => [
            'enabled' => false,
            'api_endpoint' => '',
            'api_key' => '',
        ],
        'comelec' => [
            'enabled' => false,
            'api_endpoint' => '',
            'api_key' => '',
        ],
        'psa' => [
            'enabled' => false,
            'api_endpoint' => '',
            'api_key' => '',
        ],
    ];

    /**
     * Feature Flags
     */
    public array $features = [
        'sms_notifications' => true,
        'email_notifications' => true,
        'qr_verification' => true,
        'document_management' => true,
        'health_records' => true,
        'business_management' => true,
        'event_management' => true,
        'emergency_response' => true,
        'advanced_analytics' => true,
        'mobile_app_api' => false,
        'blockchain_verification' => false,
        'ai_predictions' => false,
    ];

    /**
     * Performance Configuration
     */
    public array $performance = [
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 hour
        'database_pool_size' => 10,
        'max_concurrent_requests' => 100,
        'image_optimization' => true,
        'compression_enabled' => true,
    ];

    /**
     * Localization Configuration
     */
    public array $localization = [
        'default_locale' => 'en',
        'supported_locales' => ['en', 'fil', 'ceb'],
        'timezone' => 'Asia/Manila',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
        'currency' => 'PHP',
    ];

    /**
     * Get configuration value
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } elseif (is_object($value) && property_exists($value, $k)) {
                $value = $value->$k;
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Check if feature is enabled
     */
    public function isFeatureEnabled(string $feature): bool
    {
        return $this->features[$feature] ?? false;
    }

    /**
     * Get SMS configuration
     */
    public function getSmsConfig(): array
    {
        return array_merge($this->sms, [
            'api_key' => env('SMS_API_KEY', ''),
        ]);
    }

    /**
     * Get email configuration
     */
    public function getEmailConfig(): array
    {
        return array_merge($this->email, [
            'smtp_host' => env('SMTP_HOST', ''),
            'smtp_user' => env('SMTP_USER', ''),
            'smtp_pass' => env('SMTP_PASS', ''),
            'smtp_port' => env('SMTP_PORT', 587),
        ]);
    }

    /**
     * Get database configuration for specific feature
     */
    public function getDatabaseConfig(string $feature): array
    {
        $configs = [
            'analytics' => [
                'cache_queries' => true,
                'optimize_indexes' => true,
            ],
            'documents' => [
                'enable_versioning' => true,
                'compress_files' => true,
            ],
            'notifications' => [
                'batch_processing' => true,
                'queue_enabled' => true,
            ],
        ];

        return $configs[$feature] ?? [];
    }
}