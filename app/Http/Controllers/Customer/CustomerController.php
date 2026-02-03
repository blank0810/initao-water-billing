<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customers\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     * Returns JSON for DataTables
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data = $this->customerService->getCustomerList($request);

            return response()->json($data);
        }

        // If not AJAX, return the view
        return view('pages.customer.customer-list');
    }

    /**
     * Get customer statistics for dashboard/list page
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(): JsonResponse
    {
        $stats = $this->customerService->getCustomerStats();

        return response()->json($stats);
    }

    /**
     * Validation rules for customer creation with service application
     */
    protected function validationRules(): array
    {
        return [
            // Customer fields
            'cust_first_name' => ['required', 'string', 'max:50'],
            'cust_middle_name' => ['nullable', 'string', 'max:50'],
            'cust_last_name' => ['required', 'string', 'max:50'],
            'c_type' => ['required', 'string', 'max:50'],
            'land_mark' => ['nullable', 'string', 'max:100'],

            // Address fields
            'prov_id' => ['required', 'integer', 'exists:province,prov_id'],
            't_id' => ['required', 'integer', 'exists:town,t_id'],
            'b_id' => ['required', 'integer', 'exists:barangay,b_id'],
            'p_id' => ['required', 'integer', 'exists:purok,p_id'],

            // Service application fields
            'account_type_id' => ['required', 'integer', 'exists:account_type,at_id'],
            'rate_id' => ['required', 'integer', 'exists:water_rates,wr_id'],
        ];
    }

    /**
     * Store a newly created customer with service application (Approach B)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate($this->validationRules());

            // Create customer with service application
            $result = $this->customerService->createCustomerWithApplication($validated);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'customer' => $result['customer'],
                'application' => $result['application'],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search customers by name, phone, or ID
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $customers = $this->customerService->searchCustomers($query);

            return response()->json($customers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $customer = $this->customerService->getCustomerById($id);

            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return response()->json($customer, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer's service applications
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplications($id)
    {
        try {
            $data = $this->customerService->getCustomerApplications($id);

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if customer can be deleted
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function canDelete($id)
    {
        try {
            $result = $this->customerService->canDeleteCustomer($id);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'cust_first_name' => ['required', 'string', 'max:50'],
                'cust_middle_name' => ['nullable', 'string', 'max:50'],
                'cust_last_name' => ['required', 'string', 'max:50'],
                'c_type' => ['required', 'string', 'max:50'],
                'land_mark' => ['nullable', 'string', 'max:100'],
            ]);

            $customer = $this->customerService->updateCustomer($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'customer' => $customer,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Check if customer can be deleted
            $canDelete = $this->customerService->canDeleteCustomer($id);

            if (! $canDelete['can_delete']) {
                return response()->json([
                    'success' => false,
                    'message' => $canDelete['message'],
                ], 400);
            }

            $this->customerService->deleteCustomer($id);

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer statistics for dashboard cards
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->customerService->getCustomerStats();

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer details for details page
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails(int $id): JsonResponse
    {
        try {
            $details = $this->customerService->getCustomerDetails($id);

            return response()->json([
                'success' => true,
                'data' => $details,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getMessage() === 'Customer not found' ? 404 : 500);
        }
    }

    /**
     * Get customer documents from all service connections
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocuments(int $id): JsonResponse
    {
        try {
            $documents = $this->customerService->getCustomerDocuments($id);

            return response()->json([
                'success' => true,
                'data' => $documents,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getMessage() === 'Customer not found' ? 404 : 500);
        }
    }

    /**
     * Get customer ledger data with filters
     *
     * @param  int  $id  Customer ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLedger(int $id, Request $request): JsonResponse
    {
        try {
            $filters = [
                'connection_id' => $request->query('connection_id'),
                'period_id' => $request->query('period_id'),
                'source_type' => $request->query('source_type'),
                'per_page' => $request->query('per_page', 20),
                'page' => $request->query('page', 1),
            ];

            $data = $this->customerService->getLedgerData($id, $filters);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get ledger entry details with source document information
     *
     * @param  int  $entryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLedgerEntryDetails(int $entryId): JsonResponse
    {
        try {
            $data = $this->customerService->getLedgerEntryDetails($entryId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
