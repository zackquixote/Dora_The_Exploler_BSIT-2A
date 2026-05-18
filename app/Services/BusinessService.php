<?php

namespace App\Services;

use App\Models\BusinessPermitModel;
use App\Models\ResidentModel;
use App\Models\PaymentModel;
use App\Services\NotificationService;
use CodeIgniter\I18n\Time;

/**
 * Business Management Service
 * Handles business registration, permits, and monitoring
 */
class BusinessService
{
    protected BusinessPermitModel $businessModel;
    protected ResidentModel $residentModel;
    protected PaymentModel $paymentModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->businessModel = new BusinessPermitModel();
        $this->residentModel = new ResidentModel();
        $this->paymentModel = new PaymentModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Register new business
     */
    public function registerBusiness(array $businessData): array
    {
        // Generate permit number
        $permitNumber = $this->generatePermitNumber();
        
        // Calculate permit fee based on business type and capital
        $permitFee = $this->calculatePermitFee($businessData);
        
        $business = array_merge($businessData, [
            'business_permit_number' => $permitNumber,
            'permit_fee' => $permitFee,
            'issue_date' => date('Y-m-d'),
            'expiry_date' => date('Y-m-d', strtotime('+1 year')),
            'status' => 'active',
            'created_by' => session()->get('user_id'),
        ]);

        $businessId = $this->businessModel->insert($business);
        
        // Create payment record
        $this->createPermitPayment($businessId, $permitFee);
        
        // Send notification to business owner
        $this->notifyBusinessOwner($businessId, 'registration');
        
        return $this->businessModel->find($businessId);
    }

    /**
     * Generate business permit number
     */
    protected function generatePermitNumber(): string
    {
        $year = date('Y');
        
        // Get last permit number for this year
        $lastBusiness = $this->businessModel
            ->where('business_permit_number LIKE', "BP-{$year}-%")
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastBusiness) {
            $lastNumber = (int) substr($lastBusiness['business_permit_number'], -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('BP-%s-%05d', $year, $nextNumber);
    }

    /**
     * Calculate permit fee based on business type and capital
     */
    protected function calculatePermitFee(array $businessData): float
    {
        $baseFee = 500.00; // Base fee for all businesses
        $capital = (float) ($businessData['capital_amount'] ?? 0);
        
        // Fee structure based on capital amount
        if ($capital <= 10000) {
            $capitalFee = 100.00;
        } elseif ($capital <= 50000) {
            $capitalFee = 300.00;
        } elseif ($capital <= 100000) {
            $capitalFee = 500.00;
        } else {
            $capitalFee = 1000.00;
        }

        // Additional fees based on business type
        $typeFees = [
            'Retail Store' => 200.00,
            'Restaurant' => 500.00,
            'Sari-sari Store' => 100.00,
            'Beauty Salon' => 300.00,
            'Internet Cafe' => 400.00,
            'Repair Shop' => 250.00,
            'Bakery' => 350.00,
            'Pharmacy' => 600.00,
        ];

        $typeFee = $typeFees[$businessData['business_type']] ?? 200.00;

        return $baseFee + $capitalFee + $typeFee;
    }

    /**
     * Create payment record for permit fee
     */
    protected function createPermitPayment(int $businessId, float $amount): void
    {
        $business = $this->businessModel->find($businessId);
        
        $paymentData = [
            'receipt_number' => $this->paymentModel->generateReceiptNumber(),
            'payer_type' => 'business',
            'payer_id' => $businessId,
            'service_type' => 'business_permit',
            'service_id' => $businessId,
            'amount' => $amount,
            'payment_method' => 'cash', // Default, can be updated later
            'status' => 'pending',
            'collected_by' => session()->get('user_id'),
            'notes' => "Business permit fee for {$business['business_name']}",
        ];

        $this->paymentModel->insert($paymentData);
    }

    /**
     * Renew business permit
     */
    public function renewPermit(int $businessId): array
    {
        $business = $this->businessModel->find($businessId);
        
        if (!$business) {
            throw new \Exception('Business not found');
        }

        // Calculate renewal fee (usually same as original fee)
        $renewalFee = $this->calculatePermitFee($business);
        
        // Update expiry date
        $newExpiryDate = date('Y-m-d', strtotime($business['expiry_date'] . ' +1 year'));
        
        $this->businessModel->update($businessId, [
            'expiry_date' => $newExpiryDate,
            'status' => 'active',
            'renewal_reminder_sent' => 0,
        ]);

        // Create renewal payment
        $this->createRenewalPayment($businessId, $renewalFee);
        
        // Send notification
        $this->notifyBusinessOwner($businessId, 'renewal');
        
        return $this->businessModel->find($businessId);
    }

    /**
     * Create renewal payment record
     */
    protected function createRenewalPayment(int $businessId, float $amount): void
    {
        $business = $this->businessModel->find($businessId);
        
        $paymentData = [
            'receipt_number' => $this->paymentModel->generateReceiptNumber(),
            'payer_type' => 'business',
            'payer_id' => $businessId,
            'service_type' => 'permit_renewal',
            'service_id' => $businessId,
            'amount' => $amount,
            'payment_method' => 'cash',
            'status' => 'pending',
            'collected_by' => session()->get('user_id'),
            'notes' => "Permit renewal fee for {$business['business_name']}",
        ];

        $this->paymentModel->insert($paymentData);
    }

    /**
     * Send renewal reminders
     */
    public function sendRenewalReminders(): void
    {
        // Get permits expiring in 30 days
        $expiringPermits = $this->businessModel
            ->where('expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)')
            ->where('status', 'active')
            ->where('renewal_reminder_sent', 0)
            ->findAll();

        foreach ($expiringPermits as $permit) {
            $owner = $this->residentModel->find($permit['owner_resident_id']);
            
            if ($owner && !empty($owner['contact_number'])) {
                $template = $this->notificationService->getTemplate('payment_reminder', [
                    'service' => 'Business Permit',
                    'amount' => number_format($permit['permit_fee'], 2),
                    'due_date' => date('F j, Y', strtotime($permit['expiry_date'])),
                ]);

                $this->notificationService->sendToResident(
                    $owner['id'],
                    'permit_renewal_reminder',
                    $template['title'],
                    $template['message'],
                    ['sms']
                );

                // Mark as reminded
                $this->businessModel->update($permit['id'], ['renewal_reminder_sent' => 1]);
            }
        }
    }

    /**
     * Notify business owner
     */
    protected function notifyBusinessOwner(int $businessId, string $type): void
    {
        $business = $this->businessModel->find($businessId);
        $owner = $this->residentModel->find($business['owner_resident_id']);
        
        if (!$owner || empty($owner['contact_number'])) {
            return;
        }

        $templates = [
            'registration' => [
                'title' => 'Business Permit Approved',
                'message' => "Your business permit for {$business['business_name']} has been approved. Permit #: {$business['business_permit_number']}. Please settle the permit fee of ₱{$business['permit_fee']}.",
            ],
            'renewal' => [
                'title' => 'Business Permit Renewed',
                'message' => "Your business permit for {$business['business_name']} has been renewed until " . date('F j, Y', strtotime($business['expiry_date'])) . ". Thank you for your compliance.",
            ],
            'expiry_warning' => [
                'title' => 'Business Permit Expiring Soon',
                'message' => "Your business permit for {$business['business_name']} will expire on " . date('F j, Y', strtotime($business['expiry_date'])) . ". Please renew to avoid penalties.",
            ],
        ];

        $template = $templates[$type] ?? $templates['registration'];

        $this->notificationService->sendToResident(
            $owner['id'],
            'business_' . $type,
            $template['title'],
            $template['message'],
            ['sms']
        );
    }

    /**
     * Get business statistics
     */
    public function getBusinessStatistics(): array
    {
        $db = \Config\Database::connect();

        // Total businesses
        $totalBusinesses = $this->businessModel->countAll();
        
        // Active businesses
        $activeBusinesses = $this->businessModel->where('status', 'active')->countAllResults();
        
        // Businesses by type
        $businessesByType = $db->query("
            SELECT business_type, COUNT(*) as count 
            FROM business_permits 
            WHERE status = 'active'
            GROUP BY business_type
            ORDER BY count DESC
        ")->getResultArray();

        // Monthly registrations
        $monthlyRegistrations = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM business_permits 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        // Revenue from permits
        $permitRevenue = $db->query("
            SELECT 
                SUM(amount) as total_revenue,
                COUNT(*) as total_payments
            FROM payments 
            WHERE service_type IN ('business_permit', 'permit_renewal')
            AND status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        ")->getRowArray();

        // Expiring permits (next 30 days)
        $expiringPermits = $this->businessModel
            ->where('expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)')
            ->where('status', 'active')
            ->countAllResults();

        return [
            'total_businesses' => $totalBusinesses,
            'active_businesses' => $activeBusinesses,
            'by_type' => $businessesByType,
            'monthly_registrations' => $monthlyRegistrations,
            'permit_revenue' => $permitRevenue,
            'expiring_permits' => $expiringPermits,
        ];
    }

    /**
     * Generate business directory
     */
    public function generateBusinessDirectory(array $filters = []): array
    {
        $builder = $this->businessModel->builder();
        
        $builder->select('
            business_permits.*,
            CONCAT(residents.first_name, " ", residents.last_name) as owner_name,
            residents.contact_number as owner_contact
        ')->join('residents', 'residents.id = business_permits.owner_resident_id')
          ->where('business_permits.status', 'active');

        // Apply filters
        if (!empty($filters['business_type'])) {
            $builder->where('business_permits.business_type', $filters['business_type']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('business_permits.business_name', $filters['search'])
                   ->orLike('business_permits.business_address', $filters['search'])
                   ->orLike('CONCAT(residents.first_name, " ", residents.last_name)', $filters['search'])
                   ->groupEnd();
        }

        return $builder->orderBy('business_permits.business_name')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Check business compliance
     */
    public function checkCompliance(int $businessId): array
    {
        $business = $this->businessModel->find($businessId);
        
        if (!$business) {
            throw new \Exception('Business not found');
        }

        $compliance = [
            'permit_status' => 'valid',
            'payment_status' => 'paid',
            'issues' => [],
            'recommendations' => [],
        ];

        // Check permit expiry
        if (strtotime($business['expiry_date']) < time()) {
            $compliance['permit_status'] = 'expired';
            $compliance['issues'][] = 'Business permit has expired';
            $compliance['recommendations'][] = 'Renew business permit immediately';
        } elseif (strtotime($business['expiry_date']) < strtotime('+30 days')) {
            $compliance['permit_status'] = 'expiring_soon';
            $compliance['issues'][] = 'Business permit expires within 30 days';
            $compliance['recommendations'][] = 'Schedule permit renewal';
        }

        // Check payment status
        $unpaidPayments = $this->paymentModel
            ->where('payer_type', 'business')
            ->where('payer_id', $businessId)
            ->where('status', 'pending')
            ->countAllResults();

        if ($unpaidPayments > 0) {
            $compliance['payment_status'] = 'pending';
            $compliance['issues'][] = "Has {$unpaidPayments} unpaid payment(s)";
            $compliance['recommendations'][] = 'Settle outstanding payments';
        }

        // Check business information completeness
        $requiredFields = ['business_name', 'business_type', 'business_address', 'contact_number'];
        foreach ($requiredFields as $field) {
            if (empty($business[$field])) {
                $compliance['issues'][] = "Missing {$field}";
                $compliance['recommendations'][] = "Update {$field} information";
            }
        }

        $compliance['overall_status'] = empty($compliance['issues']) ? 'compliant' : 'non_compliant';

        return $compliance;
    }

    /**
     * Generate business report
     */
    public function generateBusinessReport(array $dateRange = []): array
    {
        $statistics = $this->getBusinessStatistics();
        
        // Get detailed business list
        $businesses = $this->generateBusinessDirectory();
        
        // Compliance summary
        $complianceIssues = 0;
        foreach ($businesses as $business) {
            $compliance = $this->checkCompliance($business['id']);
            if ($compliance['overall_status'] === 'non_compliant') {
                $complianceIssues++;
            }
        }

        return [
            'report_type' => 'business_summary',
            'generated_at' => date('Y-m-d H:i:s'),
            'date_range' => $dateRange,
            'statistics' => $statistics,
            'compliance_summary' => [
                'total_businesses' => count($businesses),
                'compliant_businesses' => count($businesses) - $complianceIssues,
                'non_compliant_businesses' => $complianceIssues,
                'compliance_rate' => count($businesses) > 0 ? round(((count($businesses) - $complianceIssues) / count($businesses)) * 100, 2) : 0,
            ],
            'businesses' => $businesses,
        ];
    }
}