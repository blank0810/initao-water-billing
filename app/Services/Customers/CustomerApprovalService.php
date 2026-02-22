<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\Status;

class CustomerApprovalService
{
    /**
     * Reactivate a non-ACTIVE customer directly to ACTIVE.
     * Accepts INACTIVE, SUSPENDED, or PENDING customers.
     *
     * @throws \Exception
     */
    public function reactivateCustomer(int $customerId): Customer
    {
        $customer = Customer::findOrFail($customerId);

        $allowedStatuses = [
            Status::getIdByDescription(Status::INACTIVE),
            Status::getIdByDescription(Status::SUSPENDED),
            Status::getIdByDescription(Status::PENDING),
        ];

        if (! in_array($customer->stat_id, $allowedStatuses)) {
            throw new \Exception('Only INACTIVE, SUSPENDED, or PENDING customers can be reactivated.');
        }

        $customer->update([
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        return $customer->fresh('status');
    }
}
