<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\ConsumerAddress;
use App\Models\ServiceApplication;
use App\Models\CustomerCharge;
use App\Models\Status;
use App\Http\Helpers\CustomerHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Create customer with service application (Approach B)
     * Creates: Customer + ConsumerAddress + ServiceApplication + CustomerCharges in one transaction
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createCustomerWithApplication(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                // 1. Create service address
                $address = ConsumerAddress::create([
                    'p_id' => $data['p_id'] ?? null,
                    'b_id' => $data['b_id'] ?? null,
                    't_id' => $data['t_id'] ?? null,
                    'prov_id' => $data['prov_id'] ?? null,
                    'stat_id' => Status::getIdByDescription(Status::ACTIVE),
                ]);

                // 2. Create customer
                $customer = Customer::create([
                    'cust_first_name' => strtoupper($data['cust_first_name']),
                    'cust_middle_name' => isset($data['cust_middle_name']) ? strtoupper($data['cust_middle_name']) : null,
                    'cust_last_name' => strtoupper($data['cust_last_name']),
                    'ca_id' => $address->ca_id,
                    'land_mark' => isset($data['land_mark']) ? strtoupper($data['land_mark']) : null,
                    'c_type' => strtoupper($data['c_type']),
                    'resolution_no' => CustomerHelper::generateCustomerResolutionNumber(
                        $data['cust_first_name'],
                        $data['cust_last_name']
                    ),
                    'create_date' => now(),
                    'stat_id' => Status::getIdByDescription(Status::PENDING), // PENDING until application is approved
                ]);

                // 3. Create service application
                $application = ServiceApplication::create([
                    'customer_id' => $customer->cust_id,
                    'address_id' => $address->ca_id, // Service address (same as billing for now)
                    'application_number' => $this->generateApplicationNumber(),
                    'submitted_at' => now(),
                    'stat_id' => Status::getIdByDescription(Status::PENDING),
                ]);

                // 4. Create customer charges if charge items are provided
                if (isset($data['charge_items']) && is_array($data['charge_items'])) {
                    foreach ($data['charge_items'] as $chargeItem) {
                        CustomerCharge::create([
                            'customer_id' => $customer->cust_id,
                            'application_id' => $application->application_id,
                            'connection_id' => null, // No connection yet
                            'charge_item_id' => $chargeItem['charge_item_id'],
                            'description' => $chargeItem['description'] ?? null,
                            'quantity' => $chargeItem['quantity'] ?? 1,
                            'unit_amount' => $chargeItem['unit_amount'],
                            'due_date' => $data['due_date'] ?? now()->addDays(30),
                            'stat_id' => Status::getIdByDescription(Status::PENDING), // PENDING payment
                        ]);
                    }
                }

                return [
                    'success' => true,
                    'customer' => $customer->load(['address', 'status']),
                    'application' => $application->load('status'),
                    'message' => 'Customer and service application created successfully',
                ];
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to create customer with application: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique application number
     *
     * @return string
     */
    private function generateApplicationNumber(): string
    {
        $year = date('Y');
        $lastApplication = ServiceApplication::whereYear('submitted_at', $year)
            ->orderBy('application_id', 'desc')
            ->first();

        $nextNumber = $lastApplication ? ((int) substr($lastApplication->application_number, -5)) + 1 : 1;

        return 'APP-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
