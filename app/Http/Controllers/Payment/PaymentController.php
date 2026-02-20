<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WaterBillHistory;
use App\Services\Payment\PaymentManagementService;
use App\Services\Payment\PaymentService;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentManagementService $paymentManagementService,
        protected ServiceApplicationService $applicationService,
        protected PaymentService $paymentService
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
     * Get collections for server-side DataTables (Billing Management tab)
     */
    public function getCollections(Request $request): JsonResponse
    {
        $data = $this->paymentManagementService->getCollections($request->all());

        return response()->json($data);
    }

    /**
     * Get current cashier's transactions for a specific date
     */
    public function getMyTransactions(Request $request): JsonResponse
    {
        $date = $request->input('date');

        $data = $this->paymentManagementService->getCashierTransactions(
            auth()->id(),
            $date
        );

        return response()->json([
            'success' => true,
            'data' => $data,
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
     * Show water bill payment processing form
     * Shows only the clicked bill and its period-matched charges
     */
    public function processWaterBillPayment(int $id)
    {
        $bill = $this->paymentService->getWaterBillDetails($id);

        if (! $bill) {
            abort(404, 'Bill not found or already paid.');
        }

        $connection = $bill->serviceConnection;

        if (! $connection) {
            abort(404, 'Service connection not found for this bill.');
        }

        $outstandingItems = $this->paymentService->getBillOutstandingItems($id);

        return view('pages.payment.process-water-bill', [
            'bill' => $bill,
            'connection' => $connection,
            'outstandingItems' => $outstandingItems,
            'selectedBillId' => $bill->bill_id,
        ]);
    }

    /**
     * Process single water bill + period-matched charges payment
     * Supports both AJAX (JSON) and form submission
     */
    public function storeWaterBillPayment(int $id, Request $request)
    {
        $request->validate([
            'amount_received' => 'required|numeric|min:0.01',
            'connection_id' => 'required|integer|exists:ServiceConnection,connection_id',
        ]);

        // Validate that the route bill belongs to the provided connection
        $bill = WaterBillHistory::where('bill_id', $id)
            ->where('connection_id', $request->connection_id)
            ->first();

        if (! $bill) {
            abort(403, 'Bill does not belong to this connection.');
        }

        try {
            $result = $this->paymentService->processWaterBillPayment(
                $id,
                (float) $request->amount_received,
                auth()->id(),
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'data' => [
                        'payment' => $result['payment'],
                        'receipt_no' => $result['payment']->receipt_no,
                        'total_paid' => $result['total_paid'],
                        'total_due' => $result['total_due'],
                        'remaining_balance' => $result['remaining_balance'],
                        'amount_received' => $result['amount_received'],
                        'change' => $result['change'],
                    ],
                ]);
            }

            return redirect()
                ->route('payment.receipt', $result['payment']->payment_id)
                ->with('success', 'Payment processed successfully. Change: ₱'.number_format($result['change'], 2));

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show payment receipt
     * Enterprise-style receipt for printing
     */
    public function showReceipt(int $paymentId): View
    {
        $payment = Payment::with(['user', 'payer.address.purok', 'payer.address.barangay', 'status', 'paymentAllocations'])->findOrFail($paymentId);

        // Determine payment type from allocations
        $hasBillAllocation = $payment->paymentAllocations->contains('target_type', 'BILL');
        $hasChargeAllocation = $payment->paymentAllocations->contains('target_type', 'CHARGE');

        // Check if this is an application payment (charges linked to an application)
        $isApplicationPayment = false;
        if ($hasChargeAllocation && ! $hasBillAllocation) {
            $chargeIds = $payment->paymentAllocations->where('target_type', 'CHARGE')->pluck('target_id');
            $isApplicationPayment = \App\Models\CustomerCharge::whereIn('charge_id', $chargeIds)
                ->whereNotNull('application_id')
                ->exists();
        }

        if ($isApplicationPayment) {
            return $this->showApplicationReceipt($payment, $paymentId);
        }

        return $this->showWaterBillReceipt($payment);
    }

    /**
     * Render receipt for application payments
     */
    private function showApplicationReceipt(Payment $payment, int $paymentId): View
    {
        $applicationId = $payment->payer?->serviceApplications?->first()?->application_id;
        $application = $applicationId
            ? $this->applicationService->getApplicationById($applicationId)
            : null;

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

    /**
     * Render receipt for water bill payments
     */
    private function showWaterBillReceipt(Payment $payment): View
    {
        $allocations = $payment->paymentAllocations;

        // Build line items from allocations
        $lineItems = collect();
        $totalDue = 0;

        foreach ($allocations as $allocation) {
            if ($allocation->target_type === 'BILL') {
                $bill = WaterBillHistory::with('period')->find($allocation->target_id);
                $lineItems->push([
                    'description' => 'Water Bill - '.($bill?->period?->per_name ?? 'Unknown'),
                    'amount' => (float) $allocation->amount_applied,
                ]);
            } elseif ($allocation->target_type === 'CHARGE') {
                $charge = \App\Models\CustomerCharge::find($allocation->target_id);
                $lineItems->push([
                    'description' => $charge?->description ?? 'Charge',
                    'amount' => (float) $allocation->amount_applied,
                ]);
            }
            $totalDue += (float) $allocation->amount_applied;
        }

        return view('pages.payment.water-bill-receipt', compact('payment', 'lineItems', 'totalDue'));
    }

    /**
     * Export cashier's transactions as CSV
     */
    public function exportMyTransactionsCsv(Request $request): StreamedResponse
    {
        $date = $request->input('date');
        $data = $this->paymentManagementService->getCashierTransactions(
            auth()->id(),
            $date
        );

        $filename = 'my-transactions-'.$data['date'].'.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Receipt #',
                'Customer Name',
                'Customer Code',
                'Payment Type',
                'Amount',
                'Status',
                'Time',
            ]);

            // Data rows
            foreach ($data['transactions'] as $tx) {
                fputcsv($handle, [
                    $tx['receipt_no'],
                    $tx['customer_name'],
                    $tx['customer_code'],
                    $tx['payment_type_label'],
                    $tx['amount'],
                    $tx['is_cancelled'] ? 'CANCELLED' : 'ACTIVE',
                    $tx['time'],
                ]);
            }

            // Summary footer
            fputcsv($handle, []);
            fputcsv($handle, ['Summary']);
            fputcsv($handle, ['Net Collected', $data['summary']['total_collected']]);
            fputcsv($handle, ['Transaction Count', $data['summary']['transaction_count']]);
            if ($data['summary']['cancelled_count'] > 0) {
                fputcsv($handle, ['Cancelled Amount', $data['summary']['cancelled_amount']]);
                fputcsv($handle, ['Cancelled Count', $data['summary']['cancelled_count']]);
            }

            foreach ($data['summary']['by_type'] as $type) {
                fputcsv($handle, [$type['type'], $type['amount']]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export cashier's transactions as printable PDF report
     */
    public function exportMyTransactionsPdf(Request $request): View
    {
        $date = $request->input('date');
        $data = $this->paymentManagementService->getCashierTransactions(
            auth()->id(),
            $date
        );

        $cashierName = auth()->user()->name;

        return view('pages.payment.my-transactions-report', compact('data', 'cashierName'));
    }

    /**
     * Cancel a payment (ADMIN/SUPER_ADMIN only)
     */
    public function cancelPayment(int $paymentId, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $result = $this->paymentService->cancelPayment(
                $paymentId,
                $request->input('reason'),
                auth()->id()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
