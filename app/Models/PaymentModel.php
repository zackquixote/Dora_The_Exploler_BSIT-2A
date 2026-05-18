<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PaymentModel
 * Manages payment records and financial transactions
 */
class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'receipt_number',
        'payer_type',
        'payer_id',
        'service_type',
        'service_id',
        'amount',
        'payment_method',
        'reference_number',
        'status',
        'collected_by',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'receipt_number' => 'required|is_unique[payments.receipt_number]|max_length[50]',
        'payer_type' => 'required|in_list[resident,business,external]',
        'payer_id' => 'required|integer',
        'service_type' => 'required|max_length[100]',
        'amount' => 'required|decimal|greater_than[0]',
        'payment_method' => 'required|in_list[cash,gcash,bank_transfer,check,online]',
        'collected_by' => 'required|integer',
    ];

    /**
     * Generate receipt number
     */
    public function generateReceiptNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get last receipt number for this month
        $lastPayment = $this->where('receipt_number LIKE', "OR-{$year}{$month}-%")
                           ->orderBy('id', 'DESC')
                           ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment['receipt_number'], -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('OR-%s%s-%06d', $year, $month, $nextNumber);
    }

    /**
     * Get payments by payer
     */
    public function getByPayer(string $payerType, int $payerId): array
    {
        return $this->where('payer_type', $payerType)
                   ->where('payer_id', $payerId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get payments by service type
     */
    public function getByServiceType(string $serviceType): array
    {
        return $this->where('service_type', $serviceType)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get revenue summary
     */
    public function getRevenueSummary(array $dateRange = []): array
    {
        $builder = $this->builder();
        
        if (!empty($dateRange)) {
            $builder->where('created_at >=', $dateRange['start'])
                   ->where('created_at <=', $dateRange['end']);
        }

        return $builder->select('
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_revenue,
            AVG(CASE WHEN status = "completed" THEN amount ELSE NULL END) as avg_transaction,
            SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_amount
        ')->where('status', 'completed')
          ->get()
          ->getRowArray();
    }

    /**
     * Get daily revenue
     */
    public function getDailyRevenue(int $days = 30): array
    {
        return $this->select('
            DATE(created_at) as date,
            SUM(amount) as daily_revenue,
            COUNT(*) as transaction_count
        ')->where('status', 'completed')
          ->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")))
          ->groupBy('DATE(created_at)')
          ->orderBy('date', 'DESC')
          ->findAll();
    }
}