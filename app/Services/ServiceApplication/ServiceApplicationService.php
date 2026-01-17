<?php

namespace App\Services\ServiceApplication;

use App\Http\Helpers\CustomerHelper;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\ServiceApplication;
use App\Models\Status;
use App\Services\Charge\ApplicationChargeService;
use App\Services\Ledger\LedgerService;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ServiceApplicationService
{
    public function __construct(
        protected ApplicationChargeService $chargeService,
        protected LedgerService $ledgerService,
        protected PaymentService $paymentService
    ) {}

    /**
     * Create a new service application
     *
     * Auto-verifies and generates charges immediately so customer can proceed to cashier.
     *
     * @param  string  $customerType  'new' or 'existing'
     * @param  array  $customerData  Customer form data (camelCase) - includes home address fields
     * @param  array  $applicationData  Application form data (camelCase) - service/connection address
     * @param  int|null  $userId  User creating the application (for audit trail)
     * @return array Contains 'customer', 'application', 'charges'
     */
    public function createApplication(string $customerType, array $customerData, array $applicationData, ?int $userId = null): array
    {
        return DB::transaction(function () use ($customerType, $customerData, $applicationData, $userId) {
            // Transform customer data from camelCase to snake_case
            $transformedCustomer = $this->transformCustomerData($customerData);

            // Transform application data from camelCase to snake_case
            $transformedApplication = $this->transformApplicationData($applicationData);

            // Get status IDs - Application goes directly to VERIFIED (auto-verify workflow)
            $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
            $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
            $pendingStatusId = Status::getIdByDescription(Status::PENDING);

            // Create SERVICE ADDRESS (where the water connection will be installed)
            // This is separate from the customer's home/contact address
            $serviceAddress = ConsumerAddress::create([
                'p_id' => $transformedApplication['p_id'],
                'b_id' => $transformedApplication['b_id'],
                't_id' => 1, // Initao
                'prov_id' => 1, // Misamis Oriental
                'stat_id' => $activeStatusId,
            ]);

            // Handle customer based on type
            if ($customerType === 'new') {
                // Generate resolution number
                $resolutionNo = CustomerHelper::generateCustomerResolutionNumber(
                    $transformedCustomer['cust_first_name'],
                    $transformedCustomer['cust_last_name']
                );

                // Check if customer has separate home address fields
                $customerBarangay = $customerData['barangay'] ?? null;
                $customerPurok = $customerData['purok'] ?? null;
                $customerLandmark = $customerData['landmark'] ?? null;

                // Create customer's home/contact address (separate from service address)
                if ($customerBarangay && $customerPurok) {
                    $customerAddress = ConsumerAddress::create([
                        'p_id' => $customerPurok,
                        'b_id' => $customerBarangay,
                        't_id' => 1, // Initao
                        'prov_id' => 1, // Misamis Oriental
                        'stat_id' => $activeStatusId,
                    ]);
                    $customerAddressId = $customerAddress->ca_id;
                } else {
                    // Fallback: use service address if no separate customer address provided
                    $customerAddressId = $serviceAddress->ca_id;
                    $customerLandmark = $transformedApplication['land_mark'];
                }

                // Create new customer with their HOME address
                $customer = Customer::create([
                    'cust_first_name' => $transformedCustomer['cust_first_name'],
                    'cust_middle_name' => $transformedCustomer['cust_middle_name'] ?? null,
                    'cust_last_name' => $transformedCustomer['cust_last_name'],
                    'contact_number' => $transformedCustomer['contact_number'] ?? null,
                    'id_type' => $transformedCustomer['id_type'] ?? null,
                    'id_number' => $transformedCustomer['id_number'] ?? null,
                    'ca_id' => $customerAddressId, // Customer's HOME address
                    'land_mark' => $customerLandmark ?? null,
                    'stat_id' => $pendingStatusId,
                    'c_type' => $transformedCustomer['c_type'] ?? 'RESIDENTIAL',
                    'resolution_no' => $resolutionNo,
                    'create_date' => now(),
                ]);
            } else {
                // Find existing customer - DO NOT update their home address
                // The customer's ca_id (home address) remains unchanged
                // Only the service application gets the new service address
                $customer = Customer::findOrFail($customerData['customerId'] ?? $customerData['customer_id'] ?? $customerData['id']);
            }

            // Generate application number
            $applicationNumber = 'APP-'.date('Y').'-'.str_pad(
                ServiceApplication::count() + 1,
                5,
                '0',
                STR_PAD_LEFT
            );

            // Create service application with VERIFIED status (auto-verify workflow)
            // Customer can proceed directly to cashier for payment
            $application = ServiceApplication::create([
                'customer_id' => $customer->cust_id,
                'address_id' => $serviceAddress->ca_id,
                'application_number' => $applicationNumber,
                'submitted_at' => now(),
                'verified_at' => now(), // Auto-verified on submission
                'verified_by' => $userId,
                'processed_by' => $userId, // Set the processing officer
                'stat_id' => $verifiedStatusId, // Skip PENDING, go directly to VERIFIED
                'remarks' => $applicationData['remarks'] ?? null,
            ]);

            // Generate charges immediately so customer can pay at cashier
            $charges = $this->chargeService->generateApplicationCharges($application);

            // Record charges in ledger as DEBIT entries
            $this->ledgerService->recordCharges($charges, $userId ?? 1);

            return [
                'customer' => $customer->fresh(['address', 'status']),
                'application' => $application->fresh(['customer', 'address', 'status', 'customerCharges']),
                'charges' => $charges,
                'total_amount' => $charges->sum(fn ($c) => $c->total_amount),
            ];
        });
    }

    /**
     * Transform customer data from camelCase to database columns
     */
    protected function transformCustomerData(array $data): array
    {
        return [
            'cust_first_name' => $data['firstName'] ?? $data['cust_first_name'] ?? null,
            'cust_middle_name' => $data['middleName'] ?? $data['cust_middle_name'] ?? null,
            'cust_last_name' => $data['lastName'] ?? $data['cust_last_name'] ?? null,
            'contact_number' => $data['phone'] ?? $data['contact_number'] ?? null,
            'id_type' => $data['idType'] ?? $data['id_type'] ?? null,
            'id_number' => $data['idNumber'] ?? $data['id_number'] ?? null,
            'c_type' => $data['customerType'] ?? $data['c_type'] ?? 'RESIDENTIAL',
        ];
    }

    /**
     * Transform application data from camelCase to database columns
     */
    protected function transformApplicationData(array $data): array
    {
        return [
            'b_id' => $data['barangay'] ?? $data['b_id'] ?? null,
            'p_id' => $data['purok'] ?? $data['p_id'] ?? null,
            'land_mark' => $data['landmark'] ?? $data['land_mark'] ?? null,
        ];
    }

    /**
     * Verify a pending application (documents reviewed)
     *
     * This also generates charges and ledger entries
     */
    public function verifyApplication(int $applicationId, int $verifiedBy): array
    {
        $application = ServiceApplication::findOrFail($applicationId);

        if ($application->stat_id !== Status::getIdByDescription(Status::PENDING)) {
            throw new \Exception('Only PENDING applications can be verified');
        }

        return DB::transaction(function () use ($application, $verifiedBy) {
            // Update application status to VERIFIED
            $application->update([
                'stat_id' => Status::getIdByDescription(Status::VERIFIED),
                'verified_at' => now(),
                'verified_by' => $verifiedBy,
            ]);

            // Generate charges for the application
            $charges = $this->chargeService->generateApplicationCharges($application);

            // Record charges in ledger as DEBIT entries
            $ledgerEntries = $this->ledgerService->recordCharges($charges, $verifiedBy);

            return [
                'application' => $application->fresh(),
                'charges' => $charges,
                'ledger_entries' => $ledgerEntries,
            ];
        });
    }

    /**
     * Process payment for a verified application
     *
     * Creates Payment, PaymentAllocations, and updates application status
     */
    public function processPayment(int $applicationId, float $amountReceived, int $userId): array
    {
        return $this->paymentService->processApplicationPayment($applicationId, $amountReceived, $userId);
    }

    /**
     * Get application charges summary
     */
    public function getApplicationCharges(int $applicationId): array
    {
        return $this->chargeService->getApplicationChargesTotal($applicationId);
    }

    /**
     * Schedule connection for a paid application
     */
    public function scheduleConnection(int $applicationId, Carbon $scheduledDate, int $scheduledBy): ServiceApplication
    {
        $application = ServiceApplication::findOrFail($applicationId);

        if ($application->stat_id !== Status::getIdByDescription(Status::PAID)) {
            throw new \Exception('Only PAID applications can be scheduled');
        }

        $application->update([
            'stat_id' => Status::getIdByDescription(Status::SCHEDULED),
            'scheduled_at' => now(),
            'scheduled_connection_date' => $scheduledDate,
            'scheduled_by' => $scheduledBy,
        ]);

        return $application->fresh();
    }

    /**
     * Mark application as connected (called by ServiceConnectionService)
     */
    public function markAsConnected(int $applicationId, int $connectionId): ServiceApplication
    {
        $application = ServiceApplication::findOrFail($applicationId);

        if ($application->stat_id !== Status::getIdByDescription(Status::SCHEDULED)) {
            throw new \Exception('Only SCHEDULED applications can be connected');
        }

        $application->update([
            'stat_id' => Status::getIdByDescription(Status::CONNECTED),
            'connected_at' => now(),
            'connection_id' => $connectionId,
        ]);

        return $application->fresh();
    }

    /**
     * Reject an application
     */
    public function rejectApplication(int $applicationId, string $reason, int $rejectedBy): ServiceApplication
    {
        $application = ServiceApplication::findOrFail($applicationId);

        $allowedStatuses = [
            Status::getIdByDescription(Status::PENDING),
            Status::getIdByDescription(Status::VERIFIED),
        ];

        if (! in_array($application->stat_id, $allowedStatuses)) {
            throw new \Exception('Cannot reject application in current status');
        }

        $application->update([
            'stat_id' => Status::getIdByDescription(Status::REJECTED),
            'rejected_at' => now(),
            'rejected_by' => $rejectedBy,
            'rejection_reason' => $reason,
        ]);

        return $application->fresh();
    }

    /**
     * Cancel an application
     */
    public function cancelApplication(int $applicationId, string $reason): ServiceApplication
    {
        $application = ServiceApplication::findOrFail($applicationId);

        $connectedStatusId = Status::getIdByDescription(Status::CONNECTED);

        if ($application->stat_id === $connectedStatusId) {
            throw new \Exception('Cannot cancel connected application');
        }

        $application->update([
            'stat_id' => Status::getIdByDescription(Status::CANCELLED),
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return $application->fresh();
    }

    /**
     * Get applications by status
     */
    public function getApplicationsByStatus(string $status): Collection
    {
        return ServiceApplication::with(['customer', 'address', 'status'])
            ->where('stat_id', Status::getIdByDescription($status))
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get application timeline for audit trail
     */
    public function getApplicationTimeline(int $applicationId): array
    {
        $application = ServiceApplication::with([
            'verifier',
            'scheduler',
            'rejecter',
            'payment',
            'serviceConnection',
        ])->findOrFail($applicationId);

        $timeline = [];

        $timeline[] = [
            'status' => 'SUBMITTED',
            'date' => $application->submitted_at,
            'user' => null,
            'notes' => 'Application submitted',
        ];

        if ($application->verified_at) {
            $timeline[] = [
                'status' => 'VERIFIED',
                'date' => $application->verified_at,
                'user' => $application->verifier?->name,
                'notes' => 'Documents verified',
            ];
        }

        if ($application->paid_at) {
            $timeline[] = [
                'status' => 'PAID',
                'date' => $application->paid_at,
                'user' => null,
                'notes' => 'Payment received (Receipt: '.($application->payment?->receipt_no ?? 'N/A').')',
            ];
        }

        if ($application->scheduled_at) {
            $timeline[] = [
                'status' => 'SCHEDULED',
                'date' => $application->scheduled_at,
                'user' => $application->scheduler?->name,
                'notes' => 'Scheduled for: '.$application->scheduled_connection_date?->format('Y-m-d'),
            ];
        }

        if ($application->connected_at) {
            $timeline[] = [
                'status' => 'CONNECTED',
                'date' => $application->connected_at,
                'user' => null,
                'notes' => 'Service connected (Account: '.($application->serviceConnection?->account_no ?? 'N/A').')',
            ];
        }

        if ($application->rejected_at) {
            $timeline[] = [
                'status' => 'REJECTED',
                'date' => $application->rejected_at,
                'user' => $application->rejecter?->name,
                'notes' => $application->rejection_reason,
            ];
        }

        if ($application->cancelled_at) {
            $timeline[] = [
                'status' => 'CANCELLED',
                'date' => $application->cancelled_at,
                'user' => null,
                'notes' => $application->cancellation_reason,
            ];
        }

        usort($timeline, fn ($a, $b) => $a['date'] <=> $b['date']);

        return $timeline;
    }

    /**
     * Get application with all related data
     */
    public function getApplicationById(int $applicationId): ?ServiceApplication
    {
        return ServiceApplication::with([
            'customer',
            'address.purok',
            'address.barangay',
            'address.town',
            'status',
            'customerCharges',
            'verifier',
            'scheduler',
            'payment',
            'processedBy',
            'serviceConnection.accountType',
            'serviceConnection.meterAssignment.meter',
        ])->find($applicationId);
    }

    /**
     * Get application fee templates (for displaying in form before submission)
     *
     * Returns ChargeItem records that will be applied to new applications
     */
    public function getApplicationFeeTemplates(): array
    {
        $fees = \App\Models\ChargeItem::applicationFees()->get();

        $items = $fees->map(fn ($fee) => [
            'name' => $fee->name,
            'description' => $fee->description,
            'amount' => (float) $fee->default_amount,
        ])->toArray();

        $total = $fees->sum('default_amount');

        return [
            'items' => $items,
            'total' => (float) $total,
        ];
    }
}
