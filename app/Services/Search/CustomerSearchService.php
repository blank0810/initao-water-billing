<?php

namespace App\Services\Search;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class CustomerSearchService
{
    public function search(string $query): array
    {
        if (strlen(trim($query)) < 2) {
            return [];
        }

        $query = trim($query);

        $customers = Customer::query()
            ->select('customer.cust_id', 'customer.cust_first_name', 'customer.cust_middle_name', 'customer.cust_last_name', 'customer.cust_suffix', 'customer.resolution_no', 'customer.ca_id', 'customer.stat_id')
            ->with([
                'status:stat_id,stat_desc',
                'address.barangay:b_id,b_desc',
                'serviceConnections' => function ($q) {
                    $q->select('connection_id', 'customer_id', 'account_no')
                        ->with(['meterAssignment' => function ($q) {
                            $q->select('assignment_id', 'connection_id', 'meter_id')
                                ->with('meter:mtr_id,mtr_serial');
                        }]);
                },
            ])
            ->where(function (Builder $q) use ($query) {
                // FULLTEXT search on name columns
                $q->whereRaw(
                    'MATCH(cust_first_name, cust_last_name) AGAINST(? IN BOOLEAN MODE)',
                    [$query.'*']
                )
                // Fallback LIKE for partial/middle name matches
                    ->orWhere('cust_first_name', 'like', "{$query}%")
                    ->orWhere('cust_last_name', 'like', "{$query}%")
                // Code/serial lookups (prefix match)
                    ->orWhere('resolution_no', 'like', "{$query}%")
                // Search by account_no or meter serial via relationships
                    ->orWhereHas('serviceConnections', function (Builder $sq) use ($query) {
                        $sq->where('account_no', 'like', "{$query}%")
                            ->orWhereHas('meterAssignment', function (Builder $mq) use ($query) {
                                $mq->whereHas('meter', function (Builder $mtq) use ($query) {
                                    $mtq->where('mtr_serial', 'like', "{$query}%");
                                });
                            });
                    });
            })
            ->limit(10)
            ->get();

        return $customers->map(function ($customer) {
            $connection = $customer->serviceConnections->first();
            $meter = $connection?->meterAssignment?->meter;
            $barangay = $customer->address?->barangay;

            return [
                'customer_id' => $customer->cust_id,
                'name' => $customer->full_name,
                'resolution_no' => $customer->resolution_no ?? '',
                'account_no' => $connection?->account_no ?? '',
                'meter_serial' => $meter?->mtr_serial ?? '',
                'barangay' => $barangay?->b_desc ?? '',
                'status' => $customer->status?->stat_desc ?? '',
            ];
        })->toArray();
    }
}
