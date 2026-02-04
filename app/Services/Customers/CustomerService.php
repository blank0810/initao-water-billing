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
            ? $activeConnection->accountType->at_desc
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

            // Get date installed from the latest meter assignment
            $dateInstalled = $latestAssignment?->installed_at
                ? $latestAssignment->installed_at->format('M d, Y')
                : 'N/A';

            // Get meter reader from area's active assignment
            $meterReader = 'N/A';
            if ($connection->area) {
                $activeAssignment = $connection->area->activeAreaAssignments()
                    ->with('user')
                    ->first();
                if ($activeAssignment && $activeAssignment->user) {
                    $meterReader = $activeAssignment->user->name;
                }
            }

            $areaName = $connection->area?->a_desc ?? 'N/A';

            return [
                'connection_id' => $connection->connection_id,
                'account_no' => $connection->account_no,
                'account_type' => $connection->accountType?->at_desc ?? 'N/A',
                'meter_reader' => $meterReader,
                'area' => $areaName,
                'meter_reader_area' => $meterReader !== 'N/A' ? "{$meterReader} - {$areaName}" : $areaName,
                'meter_no' => $meterNo,
                'date_installed' => $dateInstalled,
                'status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                'status_badge' => $this->getStatusBadgeData($connection->status?->stat_desc ?? 'UNKNOWN'),
                'started_at' => $connection->started_at ? $connection->started_at->format('M d, Y') : 'N/A',
            ];
        })->toArray();
    }

    /**
     * Calculate total unpaid bills
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
     */
    private function calculateLedgerBalance(Customer $customer): float
    {
        $totalDebits = $customer->customerLedgerEntries->sum('debit');
        $totalCredits = $customer->customerLedgerEntries->sum('credit');

        return $totalDebits - $totalCredits;
    }

    /**
     * Get status badge data for frontend
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
                    'connection_type' => $connection->accountType?->at_desc ?? 'N/A',
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

        // Service Contract (if approved or scheduled)
        // Contract is available if application has been approved OR scheduled
        // (scheduled implies approval, even if approved_at is not set)
        if ($application->approved_at || $application->scheduled_at) {
            $contractDate = $application->approved_at
                ? $application->approved_at->format('Y-m-d')
                : $application->scheduled_at->format('Y-m-d');

            $docs[] = [
                'type' => 'contract',
                'name' => 'Service Contract',
                'date' => $contractDate,
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
     */
    private function buildConnectionsForFilter(Customer $customer): array
    {
        return $customer->serviceConnections->map(function ($connection) {
            return [
                'connection_id' => $connection->connection_id,
                'account_no' => $connection->account_no,
                'connection_type' => $connection->accountType?->at_desc ?? 'N/A',
                'status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                'display_label' => "{$connection->account_no} ({$connection->accountType?->at_desc})",
            ];
        })->toArray();
    }

    /**
     * Get customer ledger data with filters and pagination
     *
     * @param  array  $filters  ['connection_id', 'period_id', 'source_type', 'per_page', 'page']
     */
    public function getLedgerData(int $customerId, array $filters = []): array
    {
        $perPage = $filters['per_page'] ?? 20;
        $connectionId = $filters['connection_id'] ?? null;
        $periodId = $filters['period_id'] ?? null;
        $sourceType = $filters['source_type'] ?? null;

        // Build query with filters
        $query = CustomerLedger::with([
            'serviceConnection',
            'period',
            'status',
            'user',
        ])
            ->where('customer_id', $customerId);

        if ($connectionId) {
            $query->where('connection_id', $connectionId);
        }

        if ($periodId) {
            $query->where('period_id', $periodId);
        }

        if ($sourceType) {
            $query->where('source_type', $sourceType);
        }

        // Order by date descending for display (newest first)
        // Include ledger_entry_id DESC to ensure consistent ordering within same timestamp
        // This makes balance flow naturally when reading top-to-bottom (balance increases going down = going back in time)
        $entries = $query->orderBy('txn_date', 'desc')
            ->orderBy('post_ts', 'desc')
            ->orderBy('ledger_entry_id', 'desc')
            ->paginate($perPage);

        // Calculate running balance (oldest to newest for calculation)
        $allEntries = CustomerLedger::where('customer_id', $customerId)
            ->orderBy('txn_date', 'asc')
            ->orderBy('post_ts', 'asc')
            ->orderBy('ledger_entry_id', 'asc')
            ->get();

        $runningBalances = $this->calculateRunningBalances($allEntries);

        // Map entries with running balance
        $entriesWithBalance = $entries->getCollection()->map(function ($entry) use ($runningBalances) {
            return $this->formatLedgerEntry($entry, $runningBalances[$entry->ledger_entry_id] ?? 0);
        });

        // Calculate summary
        $summary = $this->calculateLedgerSummary($customerId);

        // Get filter options
        $filterOptions = $this->getLedgerFilterOptions($customerId);

        return [
            'entries' => $entriesWithBalance,
            'pagination' => [
                'current_page' => $entries->currentPage(),
                'per_page' => $entries->perPage(),
                'total' => $entries->total(),
                'last_page' => $entries->lastPage(),
            ],
            'summary' => $summary,
            'filters' => $filterOptions,
        ];
    }

    /**
     * Calculate running balances for all entries
     */
    private function calculateRunningBalances($entries): array
    {
        $balances = [];
        $runningBalance = 0;

        foreach ($entries as $entry) {
            $runningBalance += ($entry->debit - $entry->credit);
            $balances[$entry->ledger_entry_id] = $runningBalance;
        }

        return $balances;
    }

    /**
     * Format a single ledger entry for API response
     */
    private function formatLedgerEntry(CustomerLedger $entry, float $runningBalance): array
    {
        return [
            'ledger_entry_id' => $entry->ledger_entry_id,
            'txn_date' => $entry->txn_date->format('Y-m-d'),
            'txn_date_formatted' => $entry->txn_date->format('M d, Y'),
            'post_ts' => $entry->post_ts?->format('Y-m-d H:i:s'),
            'source_type' => $entry->source_type,
            'source_type_label' => $this->getSourceTypeLabel($entry->source_type),
            'source_type_badge' => $this->getSourceTypeBadge($entry->source_type),
            'source_id' => $entry->source_id,
            'description' => $entry->description ?? $this->getDefaultDescription($entry),
            'debit' => (float) $entry->debit,
            'debit_formatted' => $entry->debit > 0 ? '₱'.number_format($entry->debit, 2) : '-',
            'credit' => (float) $entry->credit,
            'credit_formatted' => $entry->credit > 0 ? '₱'.number_format($entry->credit, 2) : '-',
            'running_balance' => $runningBalance,
            'running_balance_formatted' => '₱'.number_format($runningBalance, 2),
            'balance_class' => $runningBalance > 0 ? 'text-red-600' : ($runningBalance < 0 ? 'text-blue-600' : 'text-green-600'),
            'connection' => $entry->serviceConnection ? [
                'connection_id' => $entry->serviceConnection->connection_id,
                'account_no' => $entry->serviceConnection->account_no ?? 'N/A',
            ] : null,
            'period' => $entry->period ? [
                'per_id' => $entry->period->per_id,
                'period_label' => $entry->period->per_month.' '.$entry->period->per_year,
            ] : null,
        ];
    }

    /**
     * Get source type display label
     */
    private function getSourceTypeLabel(string $sourceType): string
    {
        return match ($sourceType) {
            'BILL' => 'Water Bill',
            'CHARGE' => 'Charge',
            'PAYMENT' => 'Payment',
            'REFUND' => 'Refund',
            'ADJUST' => 'Adjustment',
            'WRITE_OFF' => 'Write-Off',
            'TRANSFER' => 'Transfer',
            'REVERSAL' => 'Reversal',
            default => $sourceType,
        };
    }

    /**
     * Get source type badge CSS classes
     */
    private function getSourceTypeBadge(string $sourceType): array
    {
        return match ($sourceType) {
            'BILL' => ['bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-800 dark:text-blue-300'],
            'CHARGE' => ['bg' => 'bg-orange-100 dark:bg-orange-900', 'text' => 'text-orange-800 dark:text-orange-300'],
            'PAYMENT' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300'],
            'REFUND' => ['bg' => 'bg-purple-100 dark:bg-purple-900', 'text' => 'text-purple-800 dark:text-purple-300'],
            'ADJUST' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300'],
            'WRITE_OFF' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300'],
            default => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300'],
        };
    }

    /**
     * Get default description based on source type
     */
    private function getDefaultDescription(CustomerLedger $entry): string
    {
        $periodLabel = $entry->period ? $entry->period->per_month.' '.$entry->period->per_year : '';

        return match ($entry->source_type) {
            'BILL' => "Water Bill - {$periodLabel}",
            'CHARGE' => 'Service Charge',
            'PAYMENT' => 'Payment Received',
            'REFUND' => 'Refund Issued',
            'ADJUST' => 'Balance Adjustment',
            'WRITE_OFF' => 'Amount Written Off',
            default => $entry->source_type,
        };
    }

    /**
     * Calculate ledger summary totals
     */
    private function calculateLedgerSummary(int $customerId): array
    {
        $totals = CustomerLedger::where('customer_id', $customerId)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $totalDebit = (float) ($totals->total_debit ?? 0);
        $totalCredit = (float) ($totals->total_credit ?? 0);
        $netBalance = $totalDebit - $totalCredit;

        return [
            'total_debit' => $totalDebit,
            'total_debit_formatted' => '₱'.number_format($totalDebit, 2),
            'total_credit' => $totalCredit,
            'total_credit_formatted' => '₱'.number_format($totalCredit, 2),
            'net_balance' => $netBalance,
            'net_balance_formatted' => '₱'.number_format($netBalance, 2),
            'balance_class' => $netBalance > 0 ? 'text-red-600' : ($netBalance < 0 ? 'text-blue-600' : 'text-green-600'),
        ];
    }

    /**
     * Get filter options for ledger dropdown filters
     */
    private function getLedgerFilterOptions(int $customerId): array
    {
        // Get unique connections for this customer's ledger
        $connections = CustomerLedger::where('customer_id', $customerId)
            ->whereNotNull('connection_id')
            ->with('serviceConnection')
            ->get()
            ->pluck('serviceConnection')
            ->filter()
            ->unique('connection_id')
            ->map(fn ($conn) => [
                'connection_id' => $conn->connection_id,
                'account_no' => $conn->account_no ?? "Connection #{$conn->connection_id}",
            ])
            ->values();

        // Get unique periods for this customer's ledger
        $periods = CustomerLedger::where('customer_id', $customerId)
            ->whereNotNull('period_id')
            ->with('period')
            ->get()
            ->pluck('period')
            ->filter()
            ->unique('per_id')
            ->sortByDesc('per_id')
            ->map(fn ($period) => [
                'per_id' => $period->per_id,
                'label' => $period->per_month.' '.$period->per_year,
            ])
            ->values();

        // Get unique source types
        $types = CustomerLedger::where('customer_id', $customerId)
            ->distinct()
            ->pluck('source_type')
            ->map(fn ($type) => [
                'value' => $type,
                'label' => $this->getSourceTypeLabel($type),
            ]);

        return [
            'connections' => $connections,
            'periods' => $periods,
            'types' => $types,
        ];
    }

    /**
     * Get detailed ledger entry with source document information
     *
     *
     * @throws \Exception
     */
    public function getLedgerEntryDetails(int $entryId): array
    {
        $entry = CustomerLedger::with([
            'serviceConnection.customer',
            'serviceConnection.accountType',
            'period',
            'status',
            'user',
        ])->find($entryId);

        if (! $entry) {
            throw new \Exception('Ledger entry not found');
        }

        $sourceDetails = $this->getSourceDocumentDetails($entry);

        return [
            'entry' => $this->formatLedgerEntry($entry, 0), // Balance not needed for detail view
            'source_details' => $sourceDetails,
            'connection_details' => $entry->serviceConnection ? [
                'connection_id' => $entry->serviceConnection->connection_id,
                'account_no' => $entry->serviceConnection->account_no,
                'customer_name' => $entry->serviceConnection->customer
                    ? trim("{$entry->serviceConnection->customer->cust_first_name} {$entry->serviceConnection->customer->cust_last_name}")
                    : 'N/A',
                'account_type' => $entry->serviceConnection->accountType?->at_desc ?? 'N/A',
            ] : null,
            'audit_info' => [
                'created_by' => $entry->user?->name ?? 'System',
                'created_at' => $entry->created_at?->format('M d, Y H:i:s') ?? 'N/A',
                'post_timestamp' => $entry->post_ts?->format('M d, Y H:i:s.u') ?? 'N/A',
            ],
        ];
    }

    /**
     * Get source document details based on source_type
     */
    private function getSourceDocumentDetails(CustomerLedger $entry): ?array
    {
        return match ($entry->source_type) {
            'BILL' => $this->getBillDetails($entry->source_id),
            'CHARGE' => $this->getChargeDetails($entry->source_id),
            'PAYMENT' => $this->getPaymentDetails($entry->source_id),
            default => null,
        };
    }

    /**
     * Get water bill details
     */
    private function getBillDetails(int $billId): ?array
    {
        $bill = \App\Models\WaterBillHistory::with(['period', 'serviceConnection', 'currentReading', 'previousReading'])
            ->find($billId);

        if (! $bill) {
            return null;
        }

        return [
            'type' => 'BILL',
            'bill_id' => $bill->bill_id,
            'period' => $bill->period ? $bill->period->per_month.' '.$bill->period->per_year : 'N/A',
            'consumption' => number_format($bill->consumption, 3).' m³',
            'water_amount' => '₱'.number_format($bill->water_amount, 2),
            'adjustment_total' => '₱'.number_format($bill->adjustment_total ?? 0, 2),
            'total_amount' => '₱'.number_format($bill->total_amount, 2),
            'due_date' => $bill->due_date?->format('M d, Y') ?? 'N/A',
            'prev_reading' => $bill->previousReading?->reading_value ?? 'N/A',
            'curr_reading' => $bill->currentReading?->reading_value ?? 'N/A',
        ];
    }

    /**
     * Get charge details
     */
    private function getChargeDetails(int $chargeId): ?array
    {
        $charge = CustomerCharge::with(['chargeItem', 'serviceConnection'])
            ->find($chargeId);

        if (! $charge) {
            return null;
        }

        return [
            'type' => 'CHARGE',
            'charge_id' => $charge->charge_id,
            'charge_item' => $charge->chargeItem?->name ?? 'Service Charge',
            'description' => $charge->description,
            'quantity' => number_format($charge->quantity, 3),
            'unit_amount' => '₱'.number_format($charge->unit_amount, 2),
            'total_amount' => '₱'.number_format($charge->total_amount, 2),
            'due_date' => $charge->due_date?->format('M d, Y') ?? 'N/A',
        ];
    }

    /**
     * Get payment details
     */
    private function getPaymentDetails(int $paymentId): ?array
    {
        $payment = \App\Models\Payment::with(['payer', 'user', 'paymentAllocations'])
            ->find($paymentId);

        if (! $payment) {
            return null;
        }

        return [
            'type' => 'PAYMENT',
            'payment_id' => $payment->payment_id,
            'receipt_no' => $payment->receipt_no,
            'payment_date' => $payment->payment_date?->format('M d, Y') ?? 'N/A',
            'amount_received' => '₱'.number_format($payment->amount_received, 2),
            'payer_name' => $payment->payer
                ? trim("{$payment->payer->cust_first_name} {$payment->payer->cust_last_name}")
                : 'N/A',
            'processed_by' => $payment->user?->name ?? 'System',
            'allocations_count' => $payment->paymentAllocations->count(),
        ];
    }

    /**
     * Get ledger data formatted for statement export (PDF/CSV)
     *
     * @param  array  $filters  Optional filters (date_from, date_to, connection_id)
     *
     * @throws \Exception
     */
    public function getLedgerStatementData(int $customerId, array $filters = []): array
    {
        $customer = Customer::with(['address.purok', 'address.barangay'])
            ->find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        // Build query with optional filters
        $query = CustomerLedger::with(['serviceConnection', 'period'])
            ->where('customer_id', $customerId);

        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        $connectionId = $filters['connection_id'] ?? null;

        if ($dateFrom) {
            $query->where('txn_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('txn_date', '<=', $dateTo);
        }

        if ($connectionId) {
            $query->where('connection_id', $connectionId);
        }

        // Get all entries ordered for calculation (oldest first)
        $allEntries = CustomerLedger::where('customer_id', $customerId)
            ->orderBy('txn_date', 'asc')
            ->orderBy('post_ts', 'asc')
            ->orderBy('ledger_entry_id', 'asc')
            ->get();

        $runningBalances = $this->calculateRunningBalances($allEntries);

        // Get entries for statement (newest first for display)
        $entries = $query->orderBy('txn_date', 'desc')
            ->orderBy('post_ts', 'desc')
            ->orderBy('ledger_entry_id', 'desc')
            ->get();

        // Calculate summary
        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');
        $debitCount = $entries->where('debit', '>', 0)->count();
        $creditCount = $entries->where('credit', '>', 0)->count();

        // Get current balance (from all entries, not just filtered)
        $netBalance = $allEntries->sum('debit') - $allEntries->sum('credit');

        // Format entries for statement
        $formattedEntries = $entries->map(function ($entry) use ($runningBalances) {
            return [
                'ledger_entry_id' => $entry->ledger_entry_id,
                'txn_date' => $entry->txn_date->format('Y-m-d'),
                'txn_date_formatted' => $entry->txn_date->format('M d, Y'),
                'time' => $entry->post_ts?->format('h:i A') ?? '-',
                'source_type' => $entry->source_type,
                'source_type_label' => $this->getSourceTypeLabel($entry->source_type),
                'description' => $entry->description ?? $this->getDefaultDescription($entry),
                'debit' => (float) $entry->debit,
                'credit' => (float) $entry->credit,
                'running_balance' => $runningBalances[$entry->ledger_entry_id] ?? 0,
            ];
        })->toArray();

        // Customer info
        $customerInfo = [
            'customer_code' => $customer->cust_id,
            'name' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
            'address' => $this->formatLocation($customer),
        ];

        // Period info
        $periodInfo = [
            'from' => $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : ($entries->last()?->txn_date?->format('M d, Y') ?? null),
            'to' => $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : ($entries->first()?->txn_date?->format('M d, Y') ?? null),
        ];

        return [
            'customer' => $customerInfo,
            'period' => $periodInfo,
            'summary' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'net_balance' => $netBalance,
                'entry_count' => $entries->count(),
                'debit_count' => $debitCount,
                'credit_count' => $creditCount,
            ],
            'entries' => $formattedEntries,
        ];
    }
}
