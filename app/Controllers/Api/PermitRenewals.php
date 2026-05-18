<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BusinessPermitModel;
use App\Models\PaymentModel;
use App\Models\PermitRenewalModel;
use App\Services\AuditService;

class PermitRenewals extends BaseController
{
    protected BusinessPermitModel $businessPermits;
    protected PermitRenewalModel $renewals;
    protected PaymentModel $payments;
    protected AuditService $audit;

    public function __construct()
    {
        $this->businessPermits = new BusinessPermitModel();
        $this->renewals        = new PermitRenewalModel();
        $this->payments        = new PaymentModel();
        $this->audit           = new AuditService();
    }

    /**
     * GET /api/business-permits/{businessPermitId}/renewals
     */
    public function listByBusiness(int $businessPermitId)
    {
        $bp = $this->businessPermits->find($businessPermitId);
        if (! $bp) {
            return $this->jsonError('Business permit not found', 404);
        }

        $items = $this->renewals->listByBusiness($businessPermitId);
        return $this->jsonSuccess([
            'business_permit' => $bp,
            'renewals'        => $items,
        ]);
    }

    /**
     * POST /api/business-permits/{businessPermitId}/renewals
     * JSON/form: renewal_year(optional), amount_due(optional), remarks(optional)
     */
    public function create(int $businessPermitId)
    {
        if (! $this->canWrite()) {
            return $this->jsonError('Forbidden', 403);
        }

        $bp = $this->businessPermits->find($businessPermitId);
        if (! $bp) {
            return $this->jsonError('Business permit not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $year = (int) ($payload['renewal_year'] ?? date('Y'));
        $amountDue = (float) ($payload['amount_due'] ?? $bp['permit_fee'] ?? 0);

        // Avoid duplicate renewals (unique constraint also protects this)
        $exists = $this->renewals
            ->where('business_permit_id', $businessPermitId)
            ->where('renewal_year', $year)
            ->first();
        if ($exists) {
            return $this->jsonError("Renewal for {$year} already exists", 409, ['renewal' => $exists]);
        }

        $data = [
            'business_permit_id' => $businessPermitId,
            'renewal_year'       => $year,
            'status'             => 'pending',
            'amount_due'         => $amountDue,
            'remarks'            => $payload['remarks'] ?? null,
            'created_by'         => (int) (session()->get('user_id') ?? 0),
        ];

        if (! $this->renewals->insert($data)) {
            return $this->jsonError('Failed to create renewal', 422, $this->renewals->errors());
        }

        $id = (int) $this->renewals->getInsertID();
        $renewal = $this->renewals->find($id);

        $this->audit->log('create', 'permit_renewal', $id, null, $renewal);

        return $this->jsonSuccess(['renewal' => $renewal], 'Renewal created');
    }

    /**
     * GET /api/permit-renewals/{id}
     */
    public function show(int $id)
    {
        $renewal = $this->renewals->find($id);
        if (! $renewal) {
            return $this->jsonError('Renewal not found', 404);
        }

        $bp = $this->businessPermits->find((int) $renewal['business_permit_id']);

        return $this->jsonSuccess([
            'renewal'        => $renewal,
            'business_permit'=> $bp,
        ]);
    }

    /**
     * POST /api/permit-renewals/{id}/pay
     * JSON/form: payment_method, reference_number(optional), notes(optional)
     *
     * Creates a payment row and moves renewal status to "paid".
     */
    public function pay(int $id)
    {
        if (! $this->canWrite()) {
            return $this->jsonError('Forbidden', 403);
        }

        $renewal = $this->renewals->find($id);
        if (! $renewal) {
            return $this->jsonError('Renewal not found', 404);
        }

        if ($renewal['status'] !== 'pending') {
            return $this->jsonError('Only pending renewals can be paid', 409, ['status' => $renewal['status']]);
        }

        $bp = $this->businessPermits->find((int) $renewal['business_permit_id']);
        if (! $bp) {
            return $this->jsonError('Business permit not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $method = (string) ($payload['payment_method'] ?? 'cash');
        if (! in_array($method, ['cash', 'gcash', 'bank_transfer', 'check', 'online'], true)) {
            return $this->jsonError('Invalid payment_method', 422);
        }

        $receipt = $this->payments->generateReceiptNumber();
        $paymentData = [
            'receipt_number'   => $receipt,
            'payer_type'       => 'business',
            'payer_id'         => (int) $bp['id'],
            'service_type'     => 'permit_renewal',
            'service_id'       => (int) $renewal['id'],
            'amount'           => (float) ($renewal['amount_due'] ?? 0),
            'payment_method'   => $method,
            'reference_number' => $payload['reference_number'] ?? null,
            'status'           => 'completed',
            'collected_by'     => (int) (session()->get('user_id') ?? 0),
            'notes'            => $payload['notes'] ?? null,
        ];

        if (! $this->payments->insert($paymentData)) {
            return $this->jsonError('Failed to record payment', 422, $this->payments->errors());
        }

        $paymentId = (int) $this->payments->getInsertID();

        $this->renewals->update($id, [
            'status'     => 'paid',
            'payment_id' => $paymentId,
            'paid_at'    => date('Y-m-d H:i:s'),
        ]);

        $updated = $this->renewals->find($id);
        $this->audit->log('paid', 'permit_renewal', $id, $renewal, $updated);

        return $this->jsonSuccess([
            'renewal' => $updated,
            'payment' => $this->payments->find($paymentId),
        ], 'Payment recorded');
    }

    /**
     * POST /api/permit-renewals/{id}/approve
     * Admin-only for MVP.
     */
    public function approve(int $id)
    {
        if (! $this->isAdmin()) {
            return $this->jsonError('Forbidden', 403);
        }

        $renewal = $this->renewals->find($id);
        if (! $renewal) {
            return $this->jsonError('Renewal not found', 404);
        }

        if ($renewal['status'] !== 'paid') {
            return $this->jsonError('Only paid renewals can be approved', 409, ['status' => $renewal['status']]);
        }

        $bp = $this->businessPermits->find((int) $renewal['business_permit_id']);
        if (! $bp) {
            return $this->jsonError('Business permit not found', 404);
        }

        // Approve renewal
        $this->renewals->update($id, [
            'status'      => 'approved',
            'approved_by' => (int) (session()->get('user_id') ?? 0),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        // Update the underlying business permit validity window
        $year = (int) $renewal['renewal_year'];
        $issue = date('Y-m-d');
        $expiry = sprintf('%04d-12-31', $year);

        $this->businessPermits->update((int) $bp['id'], [
            'issue_date'            => $issue,
            'expiry_date'           => $expiry,
            'status'                => 'active',
            'renewal_reminder_sent' => 0,
        ]);

        $updated = $this->renewals->find($id);
        $this->audit->log('approved', 'permit_renewal', $id, $renewal, $updated);

        return $this->jsonSuccess([
            'renewal'        => $updated,
            'business_permit'=> $this->businessPermits->find((int) $bp['id']),
        ], 'Renewal approved');
    }

    /**
     * POST /api/permit-renewals/{id}/mark-printed
     * Admin-only for MVP.
     */
    public function markPrinted(int $id)
    {
        if (! $this->isAdmin()) {
            return $this->jsonError('Forbidden', 403);
        }

        $renewal = $this->renewals->find($id);
        if (! $renewal) {
            return $this->jsonError('Renewal not found', 404);
        }

        if (! in_array($renewal['status'], ['approved', 'printed'], true)) {
            return $this->jsonError('Only approved renewals can be printed', 409, ['status' => $renewal['status']]);
        }

        $newCount = ((int) ($renewal['print_count'] ?? 0)) + 1;

        $this->renewals->update($id, [
            'status'      => 'printed',
            'printed_by'  => (int) (session()->get('user_id') ?? 0),
            'printed_at'  => date('Y-m-d H:i:s'),
            'print_count' => $newCount,
        ]);

        $updated = $this->renewals->find($id);
        $this->audit->log('printed', 'permit_renewal', $id, $renewal, $updated);

        return $this->jsonSuccess(['renewal' => $updated], 'Marked as printed');
    }

    private function canWrite(): bool
    {
        $role = strtolower((string) (session()->get('role') ?? ''));
        return in_array($role, ['admin', 'staff'], true);
    }

    private function isAdmin(): bool
    {
        return strtolower((string) (session()->get('role') ?? '')) === 'admin';
    }
}

