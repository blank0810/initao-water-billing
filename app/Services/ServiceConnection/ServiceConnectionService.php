<?php

namespace App\Services\ServiceConnection;

use App\Models\Barangay;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ServiceConnectionService
{
    public function __construct(
        protected ServiceApplicationService $applicationService
    ) {}

    /**
     * Generate account number in format: YYYY-BCODE-NNNNN
     * Example: 2025-POBA-00001
     */
    public function generateAccountNumber(int $barangayId): string
    {
        $barangay = Barangay::findOrFail($barangayId);
        $year = now()->year;
        $code = $barangay->b_code ?? 'UNKN';

        // Get the last sequence number for this barangay and year
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
     */
    public function createFromApplication(
        ServiceApplication $application,
        int $accountTypeId,
        int $rateId
    ): ServiceConnection {
        if ($application->stat_id !== Status::getIdByDescription(Status::SCHEDULED)) {
            throw new \Exception('Application must be in SCHEDULED status to create connection');
        }

        return DB::transaction(function () use ($application, $accountTypeId, $rateId) {
            // Get barangay from application's address
            $barangayId = $application->address->b_id;

            // Generate account number
            $accountNumber = $this->generateAccountNumber($barangayId);

            // Create service connection
            $connection = ServiceConnection::create([
                'account_no' => $accountNumber,
                'customer_id' => $application->customer_id,
                'address_id' => $application->address_id,
                'account_type_id' => $accountTypeId,
                'rate_id' => $rateId,
                'started_at' => now(),
                'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            ]);

            // Mark application as connected
            $this->applicationService->markAsConnected(
                $application->application_id,
                $connection->connection_id
            );

            return $connection;
        });
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

        // TODO: Add audit logging for suspension with reason and suspendedBy

        return $connection->fresh();
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

        return $connection->fresh();
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

        return $connection->fresh();
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
            'rate',
            'status',
            'meterAssignments.meter',
            'customerLedgerEntries',
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
}
