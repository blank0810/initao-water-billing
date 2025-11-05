<?php

namespace App\Services\Customers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomerService
{
    /**
     * Get paginated list of customers with filters
     *
     * @param Request $request
     * @return array
     */
    public function getCustomerList(Request $request): array
    {
        $query = Customer::with(['status', 'address.purok', 'address.barangay', 'address.town', 'address.province']);

        // Apply search filter
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('cust_id', 'like', "%{$search}%")
                    ->orWhere('cust_first_name', 'like', "%{$search}%")
                    ->orWhere('cust_middle_name', 'like', "%{$search}%")
                    ->orWhere('cust_last_name', 'like', "%{$search}%")
                    ->orWhere('resolution_no', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if ($request->has('status_filter') && !empty($request->status_filter)) {
            $query->whereHas('status', function (Builder $q) use ($request) {
                $q->where('stat_description', $request->status_filter);
            });
        }

        // Get total count before pagination
        $totalRecords = Customer::count();
        $filteredRecords = $query->count();

        // Apply ordering
        if ($request->has('order')) {
            $orderColumn = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'desc');

            $columns = ['cust_id', 'cust_last_name', 'land_mark', 'create_date', 'stat_id'];

            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        } else {
            $query->orderBy('create_date', 'desc');
        }

        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $customers = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $customers->map(function ($customer) {
            return [
                'cust_id' => $customer->cust_id,
                'customer_name' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
                'first_name' => $customer->cust_first_name,
                'middle_name' => $customer->cust_middle_name,
                'last_name' => $customer->cust_last_name,
                'location' => $this->formatLocation($customer),
                'land_mark' => $customer->land_mark ?? 'N/A',
                'created_at' => $customer->create_date ? $customer->create_date->format('Y-m-d') : 'N/A',
                'status' => $customer->status->stat_description ?? 'Unknown',
                'status_badge' => $this->getStatusBadge($customer->status->stat_description ?? ''),
                'resolution_no' => $customer->resolution_no ?? 'N/A',
                'c_type' => $customer->c_type ?? 'N/A',
            ];
        });

        return [
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    }

    /**
     * Format customer location from address
     *
     * @param Customer $customer
     * @return string
     */
    private function formatLocation(Customer $customer): string
    {
        if (!$customer->address) {
            return 'N/A';
        }

        $parts = [];

        if ($customer->address->purok) {
            $parts[] = $customer->address->purok->p_name;
        }
        if ($customer->address->barangay) {
            $parts[] = $customer->address->barangay->b_name;
        }
        if ($customer->address->town) {
            $parts[] = $customer->address->town->t_name;
        }

        return !empty($parts) ? implode(', ', $parts) : 'N/A';
    }

    /**
     * Get status badge HTML
     *
     * @param string $status
     * @return string
     */
    private function getStatusBadge(string $status): string
    {
        $badges = [
            'PENDING' => '<span class="px-3 py-1 bg-orange-200 dark:bg-orange-600 text-orange-800 dark:text-white rounded-full text-xs font-semibold">Pending</span>',
            'ACTIVE' => '<span class="px-3 py-1 bg-green-200 dark:bg-green-600 text-green-800 dark:text-white rounded-full text-xs font-semibold">Active</span>',
            'INACTIVE' => '<span class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-full text-xs font-semibold">Inactive</span>',
        ];

        return $badges[$status] ?? '<span class="px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">' . $status . '</span>';
    }

    /**
     * Get customer by ID
     *
     * @param int $id
     * @return Customer|null
     */
    public function getCustomerById(int $id): ?Customer
    {
        return Customer::with(['status', 'address'])->find($id);
    }
}
