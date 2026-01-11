<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payment\PaymentManagementService;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentManagementService $paymentManagementService,
        protected ServiceApplicationService $applicationService
    ) {}

    /**
     * Display payment management dashboard
     */
    public function index(Request $request): View
    {
        session(['active_menu' => 'payment-management']);

        $stats = $this->paymentManagementService->getStatistics();
        $paymentTypes = $this->paymentManagementService->getPaymentTypes();

        return view('pages.payment.payment-management', compact('stats', 'paymentTypes'));
    }

    /**
     * Get pending payments (API endpoint for table)
     */
    public function getPendingPayments(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $search = $request->input('search');

        $payments = $this->paymentManagementService->getPendingPayments($type, $search);

        return response()->json([
            'success' => true,
            'data' => $payments,
            'count' => $payments->count(),
        ]);
    }

    /**
     * Get payment statistics (API endpoint)
     */
    public function getStatistics(): JsonResponse
    {
        $stats = $this->paymentManagementService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Show create payment form
     */
    public function create($customerCode = null): View
    {
        session(['active_menu' => 'payment-management']);

        return view('pages.payment.create-payment', [
            'customerCode' => $customerCode,
        ]);
    }

    /**
     * Show payment processing page for an application
     * Fintech-style payment capture portal for cashiers
     */
    public function processApplicationPayment(int $applicationId): View
    {
        session(['active_menu' => 'payment-management']);

        $application = $this->applicationService->getApplicationById($applicationId);

        if (! $application) {
            abort(404, 'Application not found');
        }

        // Check if application is in VERIFIED status (ready for payment)
        if ($application->status?->stat_desc !== 'VERIFIED') {
            abort(403, 'This application is not ready for payment processing');
        }

        $chargesData = $this->applicationService->getApplicationCharges($applicationId);

        return view('pages.payment.process-payment', compact('application', 'chargesData'));
    }

    /**
     * Show payment receipt
     * Enterprise-style receipt for printing
     */
    public function showReceipt(int $paymentId): View
    {
        $payment = Payment::with(['user', 'payer', 'status'])->findOrFail($paymentId);

        // Get the associated application from payment
        $application = $this->applicationService->getApplicationById($payment->payer?->serviceApplications?->first()?->application_id);

        // If application not found via customer, try to find via payment_id on ServiceApplication
        if (! $application) {
            $application = \App\Models\ServiceApplication::with([
                'customer',
                'address.purok',
                'address.barangay',
            ])->where('payment_id', $paymentId)->first();
        }

        if (! $application) {
            abort(404, 'Associated application not found');
        }

        $chargesData = $this->applicationService->getApplicationCharges($application->application_id);

        return view('pages.payment.payment-receipt', compact('payment', 'application', 'chargesData'));
    }
}
