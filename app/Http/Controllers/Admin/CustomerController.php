<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Customers\CustomerService;
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
}
