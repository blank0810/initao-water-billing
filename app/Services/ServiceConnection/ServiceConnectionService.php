<?php

namespace App\Services\ServiceConnection;

use App\Models\Barangay;
use App\Models\CustomerLedger;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Services\Charge\ApplicationChargeService;
use App\Services\Notification\NotificationService;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceConnectionService
{
    private const MAX_ACCOUNT_NUMBER_RETRIES = 5;

    public function __construct(
        protected ServiceApplicationService $applicationService,
        protected NotificationService $notificationService,
        protected ApplicationChargeService $chargeService
    ) {}

    /**
     * Generate account number in format: YYYY-BCODE-NNNNN
     * Example: 2025-POBA-00001
     *
     * Note: This method should be called within a transaction with FOR UPDATE lock
     * to prevent race conditions. Use generateAccountNumberWithLock() for safe generation.
     */
    public function generateAccountNumber(int $barangayId): string
    {
        $barangay = Barangay::findOrFail($barangayId);

        return $this->generateAccountNumberForBarangay($barangay);
    }

    /**
     * Generate account number with row-level locking to prevent duplicates
     */
    protected function generateAccountNumberWithLock(int $barangayId): string
    {
        $barangay = Barangay::findOrFail($barangayId);
        $year = now()->year;
        $code = $barangay->b_code ?? 'UNKN';

        // Use FOR UPDATE to lock the rows being read, preventing concurrent reads
        $lastConnection = ServiceConnection::query()
            ->where('account_no', 'like', "{$year}-{$code}-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(account_no, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        if ($lastConnection) {
            $lastSequence = (int) substr($lastConnection->account_no, -5);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return sprintf('%d-%s-%05d', $year, $code, $newSequence);
    }

    /**
     * Generate account number for a barangay (internal helper)
     */
    protected function generateAccountNumberForBarangay(Barangay $barangay): string
    {
        $year = now()->year;
        $code = $barangay->b_code ?? 'UNKN';

        $lastConnection = ServiceConnection::query()
            ->where('account_no', 'like', "{$year}-{$code}-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(account_no, '-', -1) AS UNSIGNED) DESC")
            ->first();

        if ($lastConnection) {
            $lastSequence = (int) substr($lastConnection->account_no, -5);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return sprintf('%d-%s-%05d', $year, $code, $newSequence);
    }

    /**
     * Create service connection from approved application
     *
     * Uses retry logic to handle race conditions when generating account numbers.
     * If a unique constraint violation occurs, retries with a new account number.
     *
     * Note: Water rates are now determined automatically by account type (tiered rate system).
     */
    public function createFromApplication(
        ServiceApplication $application,
        int $accountTypeId,
        ?int $areaId = null
    ): ServiceConnection {
        if ($application->stat_id !== Status::getIdByDescription(Status::SCHEDULED)) {
            throw new \Exception('Application must be in SCHEDULED status to create connection');
        }

        $attempts = 0;
        $lastException = null;

        while ($attempts < self::MAX_ACCOUNT_NUMBER_RETRIES) {
            $attempts++;

            try {
                return DB::transaction(function () use ($application, $accountTypeId, $areaId) {
                    // Get barangay from application's address
                    $barangayId = $application->address->b_id;

                    // Generate account number with lock to prevent race conditions
                    $accountNumber = $this->generateAccountNumberWithLock($barangayId);

                    // Create service connection (rates determined by account_type_id)
                    $connection = ServiceConnection::create([
                        'account_no' => $accountNumber,
                        'customer_id' => $application->customer_id,
                        'address_id' => $application->address_id,
                        'account_type_id' => $accountTypeId,
                        'area_id' => $areaId,
                        'started_at' => now(),
                        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
                    ]);

                    // Mark application as connected
                    $this->applicationService->markAsConnected(
                        $application->application_id,
                        $connection->connection_id
                    );

                    // Transfer application charges and ledger entries to the new connection
                    $this->chargeService->transferChargesToConnection(
                        $application->application_id,
                        $connection->connection_id
                    );

                    // Notify about completed connection
                    $application->load('customer');
                    $this->notificationService->notifyApplicationConnected($application, $connection);

                    return $connection;
                });
            } catch (QueryException $e) {
                $lastException = $e;

                // Check if this is a unique constraint violation (MySQL error 1062)
                if ($this->isUniqueConstraintViolation($e)) {
                    // Log the retry attempt
                    Log::warning("Account number generation retry attempt {$attempts}", [
                        'application_id' => $application->application_id,
                        'error' => $e->getMessage(),
                    ]);

                    // Small delay before retry to reduce contention
                    usleep(50000 * $attempts); // 50ms * attempt number

                    continue;
                }

                // Re-throw if it's not a unique constraint violation
                throw $e;
            }
        }

        // All retries exhausted
        throw new \Exception(
            "Failed to generate unique account number after {$attempts} attempts. ".
            'Please try again or contact support.',
            0,
            $lastException
        );
    }

    /**
     * Check if the exception is a unique constraint violation
     */
    protected function isUniqueConstraintViolation(QueryException $e): bool
    {
        // MySQL error code 1062 = Duplicate entry
        // MySQL error code 23000 = Integrity constraint violation
        $errorCode = $e->errorInfo[1] ?? null;

        return $errorCode === 1062 || str_contains($e->getMessage(), 'Duplicate entry');
    }

    /**
     * Suspend a connection temporarily
     */
    public function suspendConnection(
        int $connectionId,
        string $reason,
        int $suspendedBy
    ): ServiceConnection {
        $connection = ServiceConnection::findOrFail($connectionId);

        if ($connection->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
            throw new \Exception('Only ACTIVE connections can be suspended');
        }

        $connection->update([
            'stat_id' => Status::getIdByDescription(Status::SUSPENDED),
        ]);

        $connection = $connection->fresh('customer');

        $this->notificationService->notifyConnectionSuspended($connection, $suspendedBy);

        return $connection;
    }

    /**
     * Disconnect a connection permanently
     */
    public function disconnectConnection(
        int $connectionId,
        string $reason,
        int $disconnectedBy
    ): ServiceConnection {
        $connection = ServiceConnection::findOrFail($connectionId);

        $allowedStatuses = [
            Status::getIdByDescription(Status::ACTIVE),
            Status::getIdByDescription(Status::SUSPENDED),
        ];

        if (! in_array($connection->stat_id, $allowedStatuses)) {
            throw new \Exception('Connection must be ACTIVE or SUSPENDED to disconnect');
        }

        $connection->update([
            'stat_id' => Status::getIdByDescription(Status::DISCONNECTED),
            'ended_at' => now(),
        ]);

        $connection = $connection->fresh('customer');

        $this->notificationService->notifyConnectionDisconnected($connection, $disconnectedBy);

        return $connection;
    }

    /**
     * Reconnect a suspended connection
     */
    public function reconnectConnection(int $connectionId, int $reconnectedBy): ServiceConnection
    {
        $connection = ServiceConnection::findOrFail($connectionId);

        if ($connection->stat_id !== Status::getIdByDescription(Status::SUSPENDED)) {
            throw new \Exception('Only SUSPENDED connections can be reconnected');
        }

        $connection->update([
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $connection = $connection->fresh('customer');

        $this->notificationService->notifyConnectionReconnected($connection, $reconnectedBy);

        return $connection;
    }

    /**
     * Get connection balance summary
     */
    public function getConnectionBalance(int $connectionId): array
    {
        $connection = ServiceConnection::with(['customerLedgerEntries'])->findOrFail($connectionId);

        $ledgerEntries = $connection->customerLedgerEntries;

        $totalBills = $ledgerEntries
            ->where('source_type', 'BILL')
            ->sum('debit');

        $totalCharges = $ledgerEntries
            ->where('source_type', 'CHARGE')
            ->sum('debit');

        $totalPayments = $ledgerEntries
            ->sum('credit');

        $balance = $totalBills + $totalCharges - $totalPayments;

        return [
            'connection_id' => $connectionId,
            'account_no' => $connection->account_no,
            'total_bills' => round($totalBills, 2),
            'total_charges' => round($totalCharges, 2),
            'total_payments' => round($totalPayments, 2),
            'balance' => round($balance, 2),
            'status' => $connection->status?->stat_desc,
        ];
    }

    /**
     * Get all connections for a customer
     */
    public function getCustomerConnections(int $customerId): Collection
    {
        return ServiceConnection::with([
            'address.purok',
            'address.barangay',
            'accountType',
            'status',
        ])
            ->where('customer_id', $customerId)
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * Get connections by status
     */
    public function getConnectionsByStatus(string $status): Collection
    {
        return ServiceConnection::with(['customer', 'address', 'status'])
            ->where('stat_id', Status::getIdByDescription($status))
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * Get connection with all related data
     */
    public function getConnectionById(int $connectionId): ?ServiceConnection
    {
        return ServiceConnection::with([
            'customer',
            'address.purok',
            'address.barangay',
            'address.town',
            'accountType',
            'status',
            'meterAssignments.meter',
            'customerLedgerEntries',
            'serviceApplication',
        ])->find($connectionId);
    }

    /**
     * Get active connections count by barangay
     */
    public function getActiveConnectionsCountByBarangay(): Collection
    {
        return ServiceConnection::query()
            ->join('ConsumerAddress', 'ServiceConnection.address_id', '=', 'ConsumerAddress.ca_id')
            ->join('barangay', 'ConsumerAddress.b_id', '=', 'barangay.b_id')
            ->where('ServiceConnection.stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->groupBy('barangay.b_id', 'barangay.b_desc')
            ->select('barangay.b_id', 'barangay.b_desc', DB::raw('COUNT(*) as connection_count'))
            ->get();
    }

    /**
     * Get ledger entries for account statement
     *
     * @param  int  $connectionId  The connection ID
     * @param  int  $limit  Maximum number of entries to return
     * @return Collection Ledger entries ordered by transaction date and post timestamp
     */
    public function getStatementLedgerEntries(int $connectionId, int $limit = 50): Collection
    {
        return CustomerLedger::where('connection_id', $connectionId)
            ->orderBy('txn_date', 'desc')
            ->orderBy('post_ts', 'desc')
            ->limit($limit)
            ->get();
    }
}
