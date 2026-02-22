<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Services\Customers\CustomerStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomerStatus
{
    public function __construct(
        protected CustomerStatusService $customerStatusService
    ) {}

    /**
     * Validate customer status before allowing the action.
     *
     * Usage: Route::middleware('customer.status:edit')
     *
     * @param  string  $action  The action being attempted (create_application, process_payment, edit, delete)
     */
    public function handle(Request $request, Closure $next, string $action): Response
    {
        $customerId = $request->route('id')
            ?? $request->input('customer_id')
            ?? $request->route('customerId');

        if (! $customerId) {
            return $next($request);
        }

        $customer = Customer::with('status')->find($customerId);

        if (! $customer) {
            return $next($request);
        }

        $allowedActions = $this->customerStatusService->getCustomerAllowedActions($customer);

        if (! in_array($action, $allowedActions)) {
            $status = $this->customerStatusService->getCustomerStatusDescription($customer);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "This action is not allowed for customers with {$status} status.",
                ], 403);
            }

            abort(403, "This action is not allowed for customers with {$status} status.");
        }

        return $next($request);
    }
}
