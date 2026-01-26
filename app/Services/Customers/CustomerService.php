<?php

namespace App\Services\Customers;

use App\Http\Helpers\CustomerHelper;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\ServiceApplication;
use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Get paginated list of customers with filters
     */
    public function getCustomerList(Request $request): array
    {
        $query = Customer::with([
            'status',
            'address.purok',
            'address.barangay',
            'address.town',
            'address.province',
            'serviceConnections.meterAssignment.meter',
            'customerLedgerEntries',
        ]);

        // Apply search filter (support both DataTables and direct search)
        $search = $request->input('search');
        if (is_array($search) && ! empty($search['value'])) {
            // DataTables format
            $searchValue = $search['value'];
        } elseif (is_string($search) && ! empty($search)) {
            // Direct search format (from Flowbite)
            $searchValue = $search;
        }

        if (isset($searchValue)) {
            $query->where(function (Builder $q) use ($searchValue) {
                $q->where('cust_id', 'like', "%{$searchValue}%")
                    ->orWhere('cust_first_name', 'like', "%{$searchValue}%")
                    ->orWhere('cust_middle_name', 'like', "%{$searchValue}%")
                    ->orWhere('cust_last_name', 'like', "%{$searchValue}%")
                    ->orWhere('resolution_no', 'like', "%{$searchValue}%");
            });
        }

        // Apply status filter (support both formats)
        $statusFilter = $request->input('status_filter') ?? $request->input('status');
        if (! empty($statusFilter)) {
            $query->whereHas('status', function (Builder $q) use ($statusFilter) {
                $q->where('stat_desc', $statusFilter);
            });
        }

        // Get total count before pagination
        $totalRecords = Customer::count();
        $filteredRecords = $query->count();

        // Apply ordering (support both DataTables and direct sort)
        if ($request->has('order')) {
            // DataTables format
            $orderColumn = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'desc');
            $columns = ['cust_id', 'cust_last_name', 'land_mark', 'create_date', 'stat_id'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        } elseif ($request->has('sort_column')) {
            // Direct sort format (from Flowbite)
            $sortColumn = $request->input('sort_column', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            // Map frontend column names to database columns
            $columnMap = [
                'cust_id' => 'cust_id',
                'name' => 'cust_last_name',
                'created_at' => 'create_date',
            ];

            $dbColumn = $columnMap[$sortColumn] ?? 'create_date';
            $query->orderBy($dbColumn, $sortDirection);
        } else {
            $query->orderBy('create_date', 'desc');
        }

        // Apply pagination
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', $request->input('length', 10));

        $customers = $query->paginate($perPage, ['*'], 'page', $page);

        // Format data
        $data = $customers->map(function ($customer) {
            $statusDescription = $customer->status?->stat_desc ?? 'UNKNOWN';

            return [
                'cust_id' => $customer->cust_id,
                'customer_name' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
                'cust_first_name' => $customer->cust_first_name,
                'cust_middle_name' => $customer->cust_middle_name ?? '',
                'cust_last_name' => $customer->cust_last_name,
                'contact_number' => $customer->contact_number ?? '',
                'location' => $this->formatLocation($customer),
                'land_mark' => $customer->land_mark ?? '',
                'created_at' => $customer->create_date ? $customer->create_date->format('Y-m-d') : 'N/A',
                'status' => $statusDescription,
                'status_badge' => $this->getStatusBadge($statusDescription),
                'resolution_no' => $customer->resolution_no ?? 'N/A',
                'c_type' => $customer->c_type ?? 'N/A',
                'meter_no' => $this->getCustomerMeterNumber($customer),
                'current_bill' => $this->getCustomerCurrentBill($customer),
            ];
        });

        // Return format supports both DataTables and Laravel pagination
        if ($request->has('draw')) {
            // DataTables format
            return [
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ];
        } else {
            // Laravel pagination format
            return [
                'data' => $data,
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem(),
            ];
        }
    }

    /**
     * Format customer location from address
     */
    private function formatLocation(Customer $customer): string
    {
        if (! $customer->address) {
            return 'N/A';
        }

        $parts = [];

        if ($customer->address->purok) {
            $parts[] = $customer->address->purok->p_desc;
        }
        if ($customer->address->barangay) {
            $parts[] = $customer->address->barangay->b_desc;
        }
        if ($customer->address->town) {
            $parts[] = $customer->address->town->t_desc;
        }

        return ! empty($parts) ? implode(', ', $parts) : 'N/A';
    }

    /**
     * Get customer's meter number from active service connection
     */
    private function getCustomerMeterNumber(Customer $customer): string
    {
        // Find the customer's active ServiceConnection
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $activeConnection = $customer->serviceConnections
            ->where('stat_id', $activeStatusId)
            ->first();

        if (! $activeConnection) {
            return 'N/A';
        }

        // Get the current meter assignment
        $meterAssignment = $activeConnection->meterAssignment;

        if (! $meterAssignment || ! $meterAssignment->meter) {
            return 'N/A';
        }

        // Return meter serial number
        return $meterAssignment->meter->mtr_serial ?? 'N/A';
    }

    /**
     * Get customer's current unpaid bill amount
     */
    private function getCustomerCurrentBill(Customer $customer): string
    {
        // Calculate total unpaid amount from CustomerLedger
        // Debit/Credit accounting: unpaid balance = sum(debits) - sum(credits)
        // where debits are from BILL entries and credits are from PAYMENT entries

        $totalDebits = $customer->customerLedgerEntries
            ->where('source_type', 'BILL')
            ->sum('debit');

        $totalCredits = $customer->customerLedgerEntries
            ->where('source_type', 'PAYMENT')
            ->sum('credit');

        $unpaidBalance = max(0, $totalDebits - $totalCredits);

        // Format as Philippine Peso
        return '₱'.number_format($unpaidBalance, 2, '.', ',');
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge(string $status): string
    {
        $badges = [
            'PENDING' => '<span class="inline-flex px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300 rounded-full text-xs font-semibold">Pending</span>',
            'ACTIVE' => '<span class="inline-flex px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full text-xs font-semibold">Active</span>',
            'INACTIVE' => '<span class="inline-flex px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full text-xs font-semibold">Inactive</span>',
            'UNKNOWN' => '<span class="inline-flex px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full text-xs font-semibold">Unknown</span>',
        ];

        return $badges[$status] ?? '<span class="inline-flex px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded-full text-xs font-semibold">'.ucfirst(strtolower($status)).'</span>';
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById(int $id): ?Customer
    {
        return Customer::with(['status', 'address'])->find($id);
    }

    /**
     * Search customers by name, phone, or ID for service application
     */
    public function searchCustomers(string $query): array
    {
        $customers = Customer::with(['status', 'serviceConnections'])
            ->where(function (Builder $q) use ($query) {
                $q->where('cust_first_name', 'like', "%{$query}%")
                    ->orWhere('cust_middle_name', 'like', "%{$query}%")
                    ->orWhere('cust_last_name', 'like', "%{$query}%")
                    ->orWhere('resolution_no', 'like', "%{$query}%")
                    ->orWhereRaw("CONCAT(cust_first_name, ' ', COALESCE(cust_middle_name, ''), ' ', cust_last_name) LIKE ?", ["%{$query}%"]);
            })
            ->whereHas('status', function (Builder $q) {
                $q->where('stat_desc', '!=', 'INACTIVE');
            })
            ->limit(10)
            ->get();

        return $customers->map(function ($customer) {
            return [
                'id' => $customer->cust_id,
                'fullName' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
                'phone' => $customer->contact_number ?? 'N/A',
                'type' => $customer->c_type ?? 'RESIDENTIAL',
                'connectionsCount' => $customer->serviceConnections ? $customer->serviceConnections->count() : 0,
            ];
        })->toArray();
    }

    /**
     * Create customer with service application (Approach B)
     * Creates: Customer + ConsumerAddress + ServiceApplication + CustomerCharges in one transaction
     *
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
            throw new \Exception('Failed to create customer with application: '.$e->getMessage());
        }
    }

    /**
     * Generate unique application number
     */
    private function generateApplicationNumber(): string
    {
        $year = date('Y');
        $lastApplication = ServiceApplication::whereYear('submitted_at', $year)
            ->orderBy('application_id', 'desc')
            ->first();

        $nextNumber = $lastApplication ? ((int) substr($lastApplication->application_number, -5)) + 1 : 1;

        return 'APP-'.$year.'-'.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get customer's service applications
     */
    public function getCustomerApplications(int $customerId): array
    {
        $customer = Customer::with('status')->find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        $applications = ServiceApplication::where('customer_id', $customerId)
            ->with('status')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $formattedApplications = $applications->map(function ($app) {
            $statusDesc = $app->status->stat_desc ?? 'Unknown';

            return [
                'application_id' => $app->application_id,
                'application_number' => $app->application_number,
                'submitted_at' => $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i') : 'N/A',
                'status_text' => $statusDesc,
                'status_class' => $this->getApplicationStatusClass($statusDesc),
            ];
        });

        return [
            'customer' => [
                'cust_id' => $customer->cust_id,
                'customer_name' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
            ],
            'applications' => $formattedApplications,
        ];
    }

    /**
     * Get status class for application badge
     */
    private function getApplicationStatusClass(string $status): string
    {
        $classes = [
            'PENDING' => 'px-2.5 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded dark:bg-orange-900 dark:text-orange-300',
            'ACTIVE' => 'px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded dark:bg-green-900 dark:text-green-300',
            'APPROVED' => 'px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded dark:bg-green-900 dark:text-green-300',
            'INACTIVE' => 'px-2.5 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded dark:bg-gray-700 dark:text-gray-300',
            'REJECTED' => 'px-2.5 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded dark:bg-red-900 dark:text-red-300',
        ];

        return $classes[$status] ?? 'px-2.5 py-0.5 bg-gray-100 text-gray-800 text-xs font-medium rounded';
    }

    /**
     * Check if customer can be deleted
     */
    public function canDeleteCustomer(int $customerId): array
    {
        $customer = Customer::find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        // Check for active service applications
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $approvedStatusId = Status::getIdByDescription('APPROVED');

        $activeApplicationsCount = ServiceApplication::where('customer_id', $customerId)
            ->whereIn('stat_id', [$activeStatusId, $approvedStatusId])
            ->count();

        if ($activeApplicationsCount > 0) {
            return [
                'can_delete' => false,
                'message' => "Cannot delete customer. There are {$activeApplicationsCount} active/approved service application(s). Please deactivate or reject them first.",
            ];
        }

        return [
            'can_delete' => true,
            'message' => 'Customer can be safely deleted.',
        ];
    }

    /**
     * Update customer information
     */
    public function updateCustomer(int $customerId, array $data): Customer
    {
        $customer = Customer::find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        $customer->update([
            'cust_first_name' => strtoupper($data['cust_first_name']),
            'cust_middle_name' => isset($data['cust_middle_name']) ? strtoupper($data['cust_middle_name']) : null,
            'cust_last_name' => strtoupper($data['cust_last_name']),
            'c_type' => strtoupper($data['c_type']),
            'land_mark' => isset($data['land_mark']) ? strtoupper($data['land_mark']) : null,
        ]);

        return $customer->fresh(['status', 'address']);
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(int $customerId): bool
    {
        return DB::transaction(function () use ($customerId) {
            $customer = Customer::find($customerId);

            if (! $customer) {
                throw new \Exception('Customer not found');
            }

            // Delete related service applications (only if they're in PENDING status)
            $pendingStatusId = Status::getIdByDescription(Status::PENDING);
            ServiceApplication::where('customer_id', $customerId)
                ->where('stat_id', $pendingStatusId)
                ->delete();

            // Delete customer
            $customer->delete();

            return true;
        });
    }

    /**
     * Get customer statistics for dashboard cards
     */
    public function getCustomerStats(): array
    {
        // 1. Total Customers
        $totalCustomers = Customer::count();

        // 2. Residential Count
        $residentialCount = Customer::where('c_type', 'RESIDENTIAL')->count();

        // 3. Total Current Bill - Sum of unpaid bills from CustomerLedger
        // In CustomerLedger, bills are recorded as debits (source_type = 'BILL')
        // To find unpaid bills, we need to check if the bill amount has been credited (paid)
        // We'll sum all bill debits and subtract all payment credits for bills
        $totalBillDebits = DB::table('CustomerLedger')
            ->where('source_type', 'BILL')
            ->sum('debit');

        $totalBillCredits = DB::table('CustomerLedger')
            ->where('source_type', 'PAYMENT')
            ->sum('credit');

        $totalCurrentBill = max(0, $totalBillDebits - $totalBillCredits);

        // 4. Overdue Count - Count distinct customers with unpaid bills past due date
        // Join CustomerLedger with WaterBillHistory to check due_date
        // source_id points to bill_id in water_bill_history when source_type = 'BILL'
        $overdueCount = DB::table('CustomerLedger as cl')
            ->join('water_bill_history as wbh', function ($join) {
                $join->on('cl.source_id', '=', 'wbh.bill_id')
                    ->where('cl.source_type', '=', 'BILL');
            })
            ->where('wbh.due_date', '<', now())
            ->whereNotExists(function ($query) {
                // Check if this bill has been fully paid
                $query->select(DB::raw(1))
                    ->from('CustomerLedger as payment')
                    ->whereColumn('payment.customer_id', 'cl.customer_id')
                    ->whereColumn('payment.source_id', 'cl.source_id')
                    ->where('payment.source_type', 'PAYMENT')
                    ->whereRaw('payment.credit >= cl.debit');
            })
            ->distinct('cl.customer_id')
            ->count('cl.customer_id');

        return [
            'total_customers' => $totalCustomers,
            'residential_count' => $residentialCount,
            'total_current_bill' => (float) $totalCurrentBill,
            'overdue_count' => $overdueCount,
        ];
    }

    /**
     * Get comprehensive customer details for details page
     *
     * @param  int  $customerId
     * @return array
     *
     * @throws \Exception
     */
    public function getCustomerDetails(int $customerId): array
    {
        // Query customer with all necessary relationships
        $customer = Customer::with([
            'status',
            'address.purok',
            'address.barangay',
            'address.town',
            'address.province',
            'serviceConnections' => function ($query) {
                $query->with([
                    'status',
                    'accountType',
                    'meterAssignments.meter',
                    'area',
                ]);
            },
            'customerLedgerEntries',
        ])->find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        // Build response data
        return [
            'customer_info' => $this->buildCustomerInfo($customer),
            'meter_billing' => $this->buildMeterBilling($customer),
            'account_status' => $this->buildAccountStatus($customer),
            'service_connections' => $this->buildServiceConnections($customer),
        ];
    }

    /**
     * Build customer information section
     *
     * @param  Customer  $customer
     * @return array
     */
    private function buildCustomerInfo(Customer $customer): array
    {
        $fullName = trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}");
        $address = $this->formatLocation($customer);

        return [
            'cust_id' => $customer->cust_id,
            'customer_code' => $customer->resolution_no ?? "CUST-{$customer->cust_id}",
            'full_name' => $fullName,
            'first_name' => $customer->cust_first_name,
            'middle_name' => $customer->cust_middle_name ?? '',
            'last_name' => $customer->cust_last_name,
            'address' => $address,
        ];
    }

    /**
     * Build meter and billing section
     *
     * @param  Customer  $customer
     * @return array
     */
    private function buildMeterBilling(Customer $customer): array
    {
        // Get active service connection
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $activeConnection = $customer->serviceConnections
            ->where('stat_id', $activeStatusId)
            ->first();

        if (! $activeConnection) {
            return [
                'meter_no' => 'Not Assigned',
                'rate_class' => 'N/A',
                'total_bill' => '0.00',
                'total_bill_formatted' => '₱0.00',
                'has_active_connection' => false,
            ];
        }

        // Get meter information from latest meter assignment
        $latestAssignment = $activeConnection->meterAssignments()
            ->orderBy('installed_at', 'desc')
            ->first();

        $meterNo = ($latestAssignment && $latestAssignment->meter)
            ? $latestAssignment->meter->mtr_serial
            : 'Not Assigned';

        // Get rate class from account type
        $rateClass = $activeConnection->accountType
            ? $activeConnection->accountType->at_description
            : 'N/A';

        // Calculate total unpaid bills
        $totalBill = $this->calculateTotalUnpaidBills($customer);

        return [
            'meter_no' => $meterNo,
            'rate_class' => $rateClass,
            'total_bill' => number_format($totalBill, 2, '.', ''),
            'total_bill_formatted' => '₱'.number_format($totalBill, 2, '.', ','),
            'has_active_connection' => true,
            'connection_id' => $activeConnection->connection_id,
            'account_no' => $activeConnection->account_no,
        ];
    }

    /**
     * Build account status section
     *
     * @param  Customer  $customer
     * @return array
     */
    private function buildAccountStatus(Customer $customer): array
    {
        $status = $customer->status?->stat_desc ?? 'UNKNOWN';
        $ledgerBalance = $this->calculateLedgerBalance($customer);
        $lastUpdated = $customer->updated_at ?? $customer->create_date;

        return [
            'status' => $status,
            'status_badge' => $this->getStatusBadgeData($status),
            'ledger_balance' => number_format($ledgerBalance, 2, '.', ''),
            'ledger_balance_formatted' => '₱'.number_format($ledgerBalance, 2, '.', ','),
            'last_updated' => $lastUpdated ? $lastUpdated->format('Y-m-d') : 'N/A',
            'last_updated_formatted' => $lastUpdated ? $lastUpdated->format('M d, Y') : 'N/A',
            'created_at' => $customer->create_date ? $customer->create_date->format('M d, Y') : 'N/A',
        ];
    }

    /**
     * Build service connections list
     *
     * @param  Customer  $customer
     * @return array
     */
    private function buildServiceConnections(Customer $customer): array
    {
        return $customer->serviceConnections->map(function ($connection) {
            $latestAssignment = $connection->meterAssignments()
                ->orderBy('installed_at', 'desc')
                ->first();

            $meterNo = ($latestAssignment && $latestAssignment->meter)
                ? $latestAssignment->meter->mtr_serial
                : 'Not Assigned';

            return [
                'connection_id' => $connection->connection_id,
                'account_no' => $connection->account_no,
                'connection_type' => $connection->accountType?->at_description ?? 'N/A',
                'meter_no' => $meterNo,
                'status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                'status_badge' => $this->getStatusBadgeData($connection->status?->stat_desc ?? 'UNKNOWN'),
                'started_at' => $connection->started_at ? $connection->started_at->format('M d, Y') : 'N/A',
                'area' => $connection->area?->a_desc ?? 'N/A',
            ];
        })->toArray();
    }

    /**
     * Calculate total unpaid bills
     *
     * @param  Customer  $customer
     * @return float
     */
    private function calculateTotalUnpaidBills(Customer $customer): float
    {
        $totalDebits = $customer->customerLedgerEntries
            ->where('source_type', 'BILL')
            ->sum('debit');

        $totalCredits = $customer->customerLedgerEntries
            ->where('source_type', 'PAYMENT')
            ->sum('credit');

        return max(0, $totalDebits - $totalCredits);
    }

    /**
     * Calculate ledger balance (all entries)
     *
     * @param  Customer  $customer
     * @return float
     */
    private function calculateLedgerBalance(Customer $customer): float
    {
        $totalDebits = $customer->customerLedgerEntries->sum('debit');
        $totalCredits = $customer->customerLedgerEntries->sum('credit');

        return $totalDebits - $totalCredits;
    }

    /**
     * Get status badge data for frontend
     *
     * @param  string  $status
     * @return array
     */
    private function getStatusBadgeData(string $status): array
    {
        $badges = [
            'ACTIVE' => [
                'text' => 'Active',
                'color' => 'green',
                'classes' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
            ],
            'PENDING' => [
                'text' => 'Pending',
                'color' => 'orange',
                'classes' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300',
            ],
            'INACTIVE' => [
                'text' => 'Inactive',
                'color' => 'gray',
                'classes' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
            ],
        ];

        $statusUpper = strtoupper($status);

        return $badges[$statusUpper] ?? [
            'text' => 'Unknown',
            'color' => 'gray',
            'classes' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
        ];
    }

    /**
     * Get all documents from all service connections for a customer
     *
     * @param  int  $customerId
     * @return array
     *
     * @throws \Exception
     */
    public function getCustomerDocuments(int $customerId): array
    {
        $customer = Customer::with([
            'serviceConnections' => function ($query) {
                $query->with(['accountType', 'status', 'serviceApplication']);
            },
        ])->find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        $documents = [];

        // Aggregate documents from all service connections
        foreach ($customer->serviceConnections as $connection) {
            $connectionDocs = $this->getConnectionDocuments($connection);

            foreach ($connectionDocs as $doc) {
                $documents[] = [
                    'connection_id' => $connection->connection_id,
                    'account_no' => $connection->account_no,
                    'connection_type' => $connection->accountType?->at_description ?? 'N/A',
                    'connection_status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                    'document_type' => $doc['type'],
                    'document_name' => $doc['name'],
                    'generated_at' => $doc['date'],
                    'generated_at_formatted' => \Carbon\Carbon::parse($doc['date'])->format('M d, Y'),
                    'view_url' => $doc['view_url'],
                    'print_url' => $doc['print_url'],
                    'icon' => $doc['icon'],
                    'status_badge' => $this->getStatusBadgeData($connection->status?->stat_desc ?? 'UNKNOWN'),
                ];
            }
        }

        // Sort by date descending (most recent first)
        usort($documents, function ($a, $b) {
            return strtotime($b['generated_at']) - strtotime($a['generated_at']);
        });

        return [
            'connections' => $this->buildConnectionsForFilter($customer),
            'documents' => $documents,
            'total_documents' => count($documents),
            'total_connections' => $customer->serviceConnections->count(),
        ];
    }

    /**
     * Get available documents for a service connection
     *
     * @param  \App\Models\ServiceConnection  $connection
     * @return array
     */
    private function getConnectionDocuments($connection): array
    {
        $docs = [];
        $application = $connection->serviceApplication;

        // Only show documents if service application exists
        if (! $application) {
            return $docs;
        }

        $applicationId = $application->application_id;

        // Service Application (always available if application exists)
        $docs[] = [
            'type' => 'application',
            'name' => 'Service Application',
            'date' => $application->created_at->format('Y-m-d'),
            'view_url' => url("/connection/service-application/{$applicationId}"),
            'print_url' => url("/connection/service-application/{$applicationId}/print"),
            'icon' => 'fa-file-alt',
        ];

        // Service Contract (if approved)
        if ($application->approved_at) {
            $docs[] = [
                'type' => 'contract',
                'name' => 'Service Contract',
                'date' => $application->approved_at->format('Y-m-d'),
                'view_url' => url("/connection/service-application/{$applicationId}/contract"),
                'print_url' => url("/connection/service-application/{$applicationId}/contract"),
                'icon' => 'fa-file-contract',
            ];
        }

        // Order of Payment (always available if application exists)
        $docs[] = [
            'type' => 'payment_order',
            'name' => 'Order of Payment',
            'date' => $application->created_at->format('Y-m-d'),
            'view_url' => url("/connection/service-application/{$applicationId}/order-of-payment"),
            'print_url' => url("/connection/service-application/{$applicationId}/order-of-payment"),
            'icon' => 'fa-money-bill',
        ];

        // Connection Statement (available for active connections)
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        if ($connection->stat_id == $activeStatusId) {
            $docs[] = [
                'type' => 'statement',
                'name' => 'Connection Statement',
                'date' => now()->format('Y-m-d'),
                'view_url' => url("/customer/service-connection/{$connection->connection_id}/statement"),
                'print_url' => url("/customer/service-connection/{$connection->connection_id}/statement"),
                'icon' => 'fa-file-invoice',
            ];
        }

        return $docs;
    }

    /**
     * Build connections list for filter dropdown
     *
     * @param  Customer  $customer
     * @return array
     */
    private function buildConnectionsForFilter(Customer $customer): array
    {
        return $customer->serviceConnections->map(function ($connection) {
            return [
                'connection_id' => $connection->connection_id,
                'account_no' => $connection->account_no,
                'connection_type' => $connection->accountType?->at_description ?? 'N/A',
                'status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                'display_label' => "{$connection->account_no} ({$connection->accountType?->at_description})",
            ];
        })->toArray();
    }
}
