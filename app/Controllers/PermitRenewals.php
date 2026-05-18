<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BusinessPermitModel;
use App\Models\PermitRenewalModel;

class PermitRenewals extends BaseController
{
    protected BusinessPermitModel $businessPermits;
    protected PermitRenewalModel $renewals;

    public function __construct()
    {
        $this->businessPermits = new BusinessPermitModel();
        $this->renewals        = new PermitRenewalModel();
    }

    /**
     * Printable view
     * GET /permit-renewals/print/{renewalId}
     *
     * Note: call the API endpoint /api/permit-renewals/{id}/mark-printed to update status.
     */
    public function print(int $renewalId)
    {
        $renewal = $this->renewals->find($renewalId);
        if (! $renewal) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Renewal not found');
        }

        $bp = $this->businessPermits->find((int) $renewal['business_permit_id']);

        return view('permit_renewals/print_view', [
            'title'        => 'Business Permit Renewal',
            'renewal'      => $renewal,
            'businessPermit'=> $bp,
        ]);
    }
}

