<?php

namespace App\Http\Controllers\ServiceConnection;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Services\Meter\MeterAssignmentService;
use App\Services\ServiceConnection\ServiceConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceConnectionController extends Controller
{
    public function __construct(
        protected ServiceConnectionService $connectionService,
        protected MeterAssignmentService $meterService
    ) {}

    /**
     * Display service connections list
     */
    public function index(): View
    {
        session(['active_menu' => 'connection-active']);

        $connections = ServiceConnection::with(['customer', 'address.barangay', 'address.purok', 'status', 'accountType', 'serviceApplication'])
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        // Get scheduled applications ready for connection
        $scheduledApplications = ServiceApplication::with(['customer', 'address.barangay'])
            ->where('stat_id', Status::getIdByDescription(Status::SCHEDULED))
            ->orderBy('scheduled_connection_date', 'asc')
            ->get();

        return view('pages.connection.service-connection', compact('connections', 'scheduledApplications'));
    }

    /**
     * Show connection details
     */
    public function show(int $id): View
    {
        $connection = $this->connectionService->getConnectionById($id);

        if (! $connection) {
            abort(404, 'Connection not found');
        }

        $balance = $this->connectionService->getConnectionBalance($id);
        $currentMeter = $this->meterService->getCurrentAssignment($id);
        $meterHistory = $this->meterService->getAssignmentHistory($id);

        return view('pages.connection.service-connection-detail', compact(
            'connection',
            'balance',
            'currentMeter',
            'meterHistory'
        ));
    }

    /**
     * Create connection from scheduled application
     */
    public function completeConnection(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => 'required|integer|exists:ServiceApplication,application_id',
            'account_type_id' => 'required|integer|exists:account_type,at_id',
            'area_id' => 'nullable|integer|exists:area,a_id',
            'meter_serial' => 'required|string|max:100|unique:meter,mtr_serial',
            'meter_brand' => 'required|string|max:100',
            'install_read' => 'required|numeric|min:0',
        ]);

        try {
            $application = ServiceApplication::findOrFail($request->input('application_id'));

            // Create the connection (rates determined by account type)
            $connection = $this->connectionService->createFromApplication(
                $application,
                $request->input('account_type_id'),
                $request->input('area_id')
            );

            // Create and assign the meter (customer-purchased meter)
            $meter = $this->meterService->createAndAssignMeter(
                $connection->connection_id,
                $request->input('meter_serial'),
                $request->input('meter_brand'),
                $request->input('install_read'),
                now()
            );

            return response()->json([
                'success' => true,
                'message' => 'Service connection created successfully',
                'data' => $connection->load(['customer', 'address', 'status', 'accountType']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Suspend a connection
     */
    public function suspend(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $connection = $this->connectionService->suspendConnection(
                $id,
                $request->input('reason'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Connection suspended',
                'data' => $connection->load(['customer', 'status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Disconnect a connection permanently
     */
    public function disconnect(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $connection = $this->connectionService->disconnectConnection(
                $id,
                $request->input('reason'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Connection disconnected',
                'data' => $connection->load(['customer', 'status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reconnect a suspended connection
     */
    public function reconnect(Request $request, int $id): JsonResponse
    {
        try {
            $connection = $this->connectionService->reconnectConnection($id, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Connection reconnected',
                'data' => $connection->load(['customer', 'status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get connection balance (API)
     */
    public function balance(int $id): JsonResponse
    {
        try {
            $balance = $this->connectionService->getConnectionBalance($id);

            return response()->json([
                'success' => true,
                'data' => $balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get connections by status (API)
     */
    public function getByStatus(string $status): JsonResponse
    {
        try {
            $connections = $this->connectionService->getConnectionsByStatus($status);

            return response()->json([
                'success' => true,
                'data' => $connections,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get customer's connections (API)
     */
    public function customerConnections(int $customerId): JsonResponse
    {
        try {
            $connections = $this->connectionService->getCustomerConnections($customerId);

            return response()->json([
                'success' => true,
                'data' => $connections,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Assign or replace meter on connection
     */
    public function assignMeter(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'meter_id' => 'required|integer|exists:meter,mtr_id',
            'install_read' => 'required|numeric|min:0',
            'removal_read' => 'nullable|numeric|min:0',
        ]);

        try {
            // Check if connection has an existing meter
            $currentAssignment = $this->meterService->getCurrentAssignment($id);

            if ($currentAssignment) {
                // Replacing existing meter - removal_read is required
                if ($request->input('removal_read') === null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter the old meter\'s final reading',
                    ], 422);
                }

                // Validate removal_read >= install_read of current meter
                if ($request->input('removal_read') < $currentAssignment->install_read) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Final reading cannot be less than the install reading ('.number_format($currentAssignment->install_read, 3).')',
                    ], 422);
                }

                // Use replaceMeter for atomic replacement
                $result = $this->meterService->replaceMeter(
                    $id,
                    $request->input('meter_id'),
                    $request->input('removal_read'),
                    $request->input('install_read')
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Meter replaced successfully',
                    'data' => $result['new_assignment']->load('meter'),
                ]);
            }

            // Fresh assignment - no existing meter
            $assignment = $this->meterService->assignMeter(
                $id,
                $request->input('meter_id'),
                $request->input('install_read'),
                now()
            );

            return response()->json([
                'success' => true,
                'message' => 'Meter assigned successfully',
                'data' => $assignment->load('meter'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove meter from connection
     */
    public function removeMeter(Request $request, int $assignmentId): JsonResponse
    {
        $request->validate([
            'removal_read' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $assignment = $this->meterService->removeMeter(
                $assignmentId,
                $request->input('removal_read'),
                now(),
                $request->input('reason')
            );

            return response()->json([
                'success' => true,
                'message' => 'Meter removed successfully',
                'data' => $assignment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get available meters for assignment
     */
    public function availableMeters(): JsonResponse
    {
        try {
            $meters = $this->meterService->getAvailableMeters();

            return response()->json([
                'success' => true,
                'data' => $meters,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get account types for connection setup
     */
    public function getAccountTypes(): JsonResponse
    {
        try {
            $accountTypes = \App\Models\AccountType::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
                ->orderBy('at_desc')
                ->get()
                ->map(fn ($type) => [
                    'id' => $type->at_id,
                    'description' => $type->at_desc,
                ]);

            return response()->json([
                'success' => true,
                'data' => $accountTypes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Print account statement
     */
    public function printStatement(int $id): View
    {
        $connection = ServiceConnection::with([
            'customer',
            'address.purok',
            'address.barangay',
            'accountType',
            'status',
        ])->findOrFail($id);

        $balance = $this->connectionService->getConnectionBalance($id);
        $ledgerEntries = $this->connectionService->getStatementLedgerEntries($id);

        return view('pages.connection.service-connection-statement', compact(
            'connection',
            'balance',
            'ledgerEntries'
        ));
    }
}
