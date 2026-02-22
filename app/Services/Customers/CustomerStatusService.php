<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\Status;

class CustomerStatusService
{
    public function assertCustomerCanCreateApplication(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if ($status !== Status::ACTIVE) {
            throw new \Exception("Customer must be ACTIVE to create a service application. Current status: {$status}.");
        }
    }

    public function assertCustomerCanProcessPayment(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if (! in_array($status, [Status::ACTIVE, Status::INACTIVE])) {
            throw new \Exception("Customer must be ACTIVE or INACTIVE to process payments. Current status: {$status}.");
        }
    }

    public function assertCustomerCanEdit(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if ($status !== Status::ACTIVE) {
            throw new \Exception("Customer must be ACTIVE to edit. Current status: {$status}.");
        }
    }

    public function assertCustomerCanDelete(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if ($status !== Status::ACTIVE) {
            throw new \Exception("Customer must be ACTIVE to delete. Current status: {$status}.");
        }
    }

    public function getCustomerStatusDescription(Customer $customer): string
    {
        if (! $customer->relationLoaded('status')) {
            $customer->load('status');
        }

        return $customer->status?->stat_desc ?? 'UNKNOWN';
    }

    public function getCustomerAllowedActions(Customer $customer): array
    {
        $status = $this->getCustomerStatusDescription($customer);

        return match ($status) {
            Status::ACTIVE => [
                'view',
                'edit',
                'delete',
                'create_application',
                'process_payment',
            ],
            Status::PENDING => [
                'view',
                'reactivate',
            ],
            Status::INACTIVE, Status::SUSPENDED => [
                'view',
                'process_payment',
                'reactivate',
            ],
            default => ['view'],
        };
    }
}
