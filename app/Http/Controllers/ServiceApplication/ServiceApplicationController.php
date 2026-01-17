<?php

namespace App\Http\Controllers\ServiceApplication;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceApplicationController extends Controller
{
    public function __construct(
        protected ServiceApplicationService $applicationService
    ) {}

    /**
     * Display service applications list
     */
    public function index(): View
    {
        session(['active_menu' => 'connection-applications']);

        $applications = ServiceApplication::with(['customer', 'address.purok', 'address.barangay', 'status'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        return view('pages.connection.service-application', compact('applications'));
    }

    /**
     * Show the form for creating a new service application
     * Entry point from Connection Management > New Application
     */
    public function create(): View
    {
        session(['active_menu' => 'connection-new']);

        return view('pages.application.service-application');
    }

    /**
     * Get application fee templates (ChargeItem records for new applications)
     * Used to display fees in the application form before submission
     */
    public function getFeeTemplates(): JsonResponse
    {
        try {
            $fees = $this->applicationService->getApplicationFeeTemplates();

            return response()->json([
                'success' => true,
                'data' => $fees,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a newly created service application
     *
     * Application is auto-verified and charges are generated immediately.
     * Customer can proceed directly to cashier for payment.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customerType' => 'required|in:new,existing',
            'customer' => 'required|array',
            'application' => 'required|array',
            'application.barangay' => 'required',
            'application.purok' => 'required',
            'application.landmark' => 'required|string|max:255',
        ]);

        try {
            $result = $this->applicationService->createApplication(
                $request->input('customerType'),
                $request->input('customer'),
                $request->input('application'),
                Auth::id() // Pass current user for audit trail
            );

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully. Customer may proceed to cashier for payment.',
                'data' => [
                    'applicationNumber' => $result['application']->application_number,
                    'application' => $result['application'],
                    'charges' => $result['charges'] ?? [],
                    'total_amount' => $result['total_amount'] ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Show application details with timeline
     */
    public function show(int $id): View
    {
        $application = $this->applicationService->getApplicationById($id);

        if (! $application) {
            abort(404, 'Application not found');
        }

        $timeline = $this->applicationService->getApplicationTimeline($id);
        $chargesData = $this->applicationService->getApplicationCharges($id);

        return view('pages.connection.service-application-detail', compact('application', 'timeline', 'chargesData'));
    }

    /**
     * Get applications by status (API)
     */
    public function getByStatus(string $status): JsonResponse
    {
        try {
            $applications = $this->applicationService->getApplicationsByStatus($status);

            return response()->json([
                'success' => true,
                'data' => $applications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify a pending application
     *
     * This generates charges and ledger entries
     */
    public function verify(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->applicationService->verifyApplication($id, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Application verified successfully. Charges have been generated.',
                'data' => [
                    'application' => $result['application']->load(['customer', 'status']),
                    'charges' => $result['charges'],
                    'total_amount' => $result['charges']->sum(fn ($c) => $c->total_amount),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get application charges
     */
    public function getCharges(int $id): JsonResponse
    {
        try {
            $chargesData = $this->applicationService->getApplicationCharges($id);

            return response()->json([
                'success' => true,
                'data' => $chargesData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Process payment for a verified application
     *
     * Full payment required
     */
    public function processPayment(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'amount_received' => 'required|numeric|min:0',
        ]);

        try {
            $result = $this->applicationService->processPayment(
                $id,
                (float) $request->input('amount_received'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'receipt_no' => $result['payment']->receipt_no,
                    'total_paid' => $result['total_paid'],
                    'amount_received' => $result['amount_received'],
                    'change' => $result['change'],
                    'payment' => $result['payment'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Print Order of Payment
     */
    public function orderOfPayment(int $id): View
    {
        $application = $this->applicationService->getApplicationById($id);

        if (! $application) {
            abort(404, 'Application not found');
        }

        $chargesData = $this->applicationService->getApplicationCharges($id);

        return view('pages.connection.order-of-payment', compact('application', 'chargesData'));
    }

    /**
     * Schedule connection for a paid application
     */
    public function schedule(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $application = $this->applicationService->scheduleConnection(
                $id,
                \Carbon\Carbon::parse($request->input('scheduled_date')),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Connection scheduled successfully',
                'data' => $application->load(['customer', 'status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reject an application
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $application = $this->applicationService->rejectApplication(
                $id,
                $request->input('reason'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Application rejected',
                'data' => $application->load(['customer', 'status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel an application
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $application = $this->applicationService->cancelApplication(
                $id,
                $request->input('reason')
            );

            return response()->json([
                'success' => true,
                'message' => 'Application cancelled',
                'data' => $application->load(['customer', 'status']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get application timeline (API)
     */
    public function timeline(int $id): JsonResponse
    {
        try {
            $timeline = $this->applicationService->getApplicationTimeline($id);

            return response()->json([
                'success' => true,
                'data' => $timeline,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Print service application form
     * Accessible at any application status
     */
    public function printApplication(int $id): View
    {
        $application = $this->applicationService->getApplicationById($id);

        if (! $application) {
            abort(404, 'Application not found');
        }

        $chargesData = $this->applicationService->getApplicationCharges($id);

        return view('pages.connection.service-application-print', compact('application', 'chargesData'));
    }
}
